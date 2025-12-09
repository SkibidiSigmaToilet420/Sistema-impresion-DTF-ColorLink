<?php

include '../../../model/conexion.php';
require_once '../../../lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ---------------------------------------------
// 2. OBTENER FILTROS Y CONSTRUIR CONSULTA SQL
// ---------------------------------------------
$q = isset($_GET['q']) ? $_GET['q'] : '';
$filtroEstado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : '';
$filtroEmpleado = isset($_GET['empleado']) && $_GET['empleado'] !== '' ? $_GET['empleado'] : '';
$filtroFecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : '';

// --- LOGICA DE TEXTO DE FILTROS ---
$filtrosAplicados = [];
if ($q !== '') $filtrosAplicados[] = "<b>Búsqueda:</b> " . htmlspecialchars($q);
if ($filtroEstado !== '') $filtrosAplicados[] = "<b>Estado:</b> " . htmlspecialchars($filtroEstado);
if ($filtroEmpleado !== '') $filtrosAplicados[] = "<b>Cédula Empleado:</b> " . htmlspecialchars($filtroEmpleado);
if ($filtroFecha !== '') {
    $fechaFormateada = date('d/m/Y', strtotime($filtroFecha));
    $filtrosAplicados[] = "<b>Fecha Solicitud:</b> " . $fechaFormateada;
}
$textoFiltros = empty($filtrosAplicados) ? "Listado general (Sin filtros aplicados)" : implode(" &nbsp;|&nbsp; ", $filtrosAplicados);

// Base de la consulta
$baseQuery = "FROM pedidos p 
              INNER JOIN usuarios cli ON p.Cedula_Cliente = cli.Cedula 
              LEFT JOIN usuarios emp ON p.Empleado_Encargado = emp.Cedula 
              INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material 
              WHERE 1=1";

if ($q !== '') $baseQuery .= " AND (cli.Nombre LIKE :q OR cli.Apellido LIKE :q OR p.Id_pedido LIKE :q OR p.Estado_Pedido LIKE :q)";
if ($filtroEstado !== '') $baseQuery .= " AND p.Estado_Pedido = :estado";
if ($filtroEmpleado !== '') $baseQuery .= " AND emp.Cedula = :empleado";
if ($filtroFecha !== '') $baseQuery .= " AND DATE(p.Fecha_Solicitud) = :fecha";

// Modificamos el SELECT para manejar nulos en SQL si se prefiere, o lo hacemos en PHP.
// Aquí traemos los datos crudos y los formateamos abajo.
$sql = "SELECT 
            p.Id_pedido, 
            DATE(p.Fecha_Solicitud) as Fecha_Solicitud_Simple, 
            p.Fecha_Entrega, 
            p.Estado_Pedido, 
            CONCAT(cli.Nombre, ' ', cli.Apellido) as Cliente, 
            CONCAT(emp.Nombre,' ',emp.Apellido) as Nombre_Empleado,
            p.Empleado_Encargado, 
            m.Nombre_material as Material, 
            p.Centimetros, 
            p.Cantidades, 
            p.Costo 
        " . $baseQuery . " ORDER BY p.Fecha_Solicitud DESC";

$stmt = $db->prepare($sql);

if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
if ($filtroEstado !== '') $stmt->bindValue(':estado', $filtroEstado, PDO::PARAM_STR);
if ($filtroEmpleado !== '') $stmt->bindValue(':empleado', $filtroEmpleado, PDO::PARAM_STR);
if ($filtroFecha !== '') $stmt->bindValue(':fecha', $filtroFecha, PDO::PARAM_STR);

$stmt->execute();
$Pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------------------------------------------
// 3. GENERAR EL HTML DEL REPORTE
// ---------------------------------------------

$html = '
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        h1 { text-align: center; margin: 0 0 5px 0; font-size: 24px; }
        .filtros-info { text-align: center; font-size: 10px; color: #555; margin-bottom: 15px; background-color: #f9f9f9; padding: 5px; border: 1px solid #eee; }
        .color-text { color: white; } 
        .link-text { color: #0D47A1; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: left; font-size: 9px; }
        th { background-color: #0D47A1; color: white; text-align: center; font-weight: bold; }
        
        /* ESTILOS DE ESTADO */
        .estado-Finalizado { background-color: #d4edda; color: #155724; font-weight: bold; text-align: center; }
        .estado-Pendiente { background-color: #fff3cd; color: #856404; font-weight: bold; text-align: center; }
        .estado-Rechazado { background-color: #f8d7da; color: #721c24; font-weight: bold; text-align: center; }
        .estado-Produccion { background-color: #cce5ff; color: #004085; font-weight: bold; text-align: center; }
        /* NUEVO: ESTILO PARA VERIFICADO (Morado) */
        .estado-Verificado { background-color: #e2d9f3; color: #5f259f; font-weight: bold; text-align: center; }
        .estado-RevisionCliente { background-color: #e2d9f3; color: #5f259f; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
    
    <h1>
        <span class="color-text" style="background-color: #0D47A1; padding: 0 5px;">Color</span><span class="link-text" style="padding: 0 5px;">Link</span>
        <br>
        <span style="font-size: 14px; display: block; color: #333; margin-top: 5px;">Reporte de Pedidos</span>
    </h1>

    <p style="font-size: 10px; text-align: center; margin-bottom: 5px;">Generado el: ' . date('d/m/Y H:i:s') . '</p>
    
    <div class="filtros-info">
        ' . $textoFiltros . '
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nro.</th>
                <th>Fecha Solicitud</th>
                <th>Fecha Entrega</th> <th>Estado</th>
                <th>Cliente</th>
                <th>Empleado Encargado</th>
                <th>Material</th>
                <th>Centimetros</th>
                <th>Cant. Disenos</th> <th>Costo</th>
            </tr>
        </thead>
        <tbody>';

foreach ($Pedidos as $pedido) {
    // Limpieza para clase CSS (quita espacios y acentos simples si hubiera)
    $estadoClass = 'estado-' . str_replace([' ', 'ó'], ['', 'o'], $pedido['Estado_Pedido']);
    
    // Lógica para Empleado
    $empleadoTexto = !empty($pedido['Empleado_Encargado']) ? htmlspecialchars($pedido['Nombre_Empleado']) : '<em style="color:#999">Sin asignar</em>';

    // Lógica para Fecha de Entrega
    $fechaEntregaTexto = !empty($pedido['Fecha_Entrega']) ? date('d/m/Y', strtotime($pedido['Fecha_Entrega'])) : '<em style="color:#999">No definida</em>';

    $html .= '
        <tr>
            <td style="text-align: center;">' . htmlspecialchars($pedido['Id_pedido']) . '</td>
            <td>' . date('d/m/Y', strtotime($pedido['Fecha_Solicitud_Simple'])) . '</td>
            <td>' . $fechaEntregaTexto . '</td>
            <td class="' . $estadoClass . '">' . htmlspecialchars($pedido['Estado_Pedido']) . '</td>
            <td>' . htmlspecialchars($pedido['Cliente']) . '</td>
            <td>' . $empleadoTexto . '</td>
            <td>' . htmlspecialchars($pedido['Material']) . '</td>
            <td style="text-align: right;">' . htmlspecialchars($pedido['Centimetros']) . '</td>
            <td style="text-align: center;">' . htmlspecialchars($pedido['Cantidades']) . '</td>
            <td style="text-align: right;">' . htmlspecialchars($pedido['Costo']) . ' Bs.</td> </tr>';
}

$html .= '
        </tbody>
    </table>
</body>
</html>';

$options = new Options();
$options->set('defaultFont', 'Helvetica');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');
$dompdf->loadHtml($html);
$dompdf->render();

$filename = 'Pedidos_Reporte_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ["Attachment" => true]);
exit;
?>