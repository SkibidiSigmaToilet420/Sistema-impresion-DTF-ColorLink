<?php
// Archivo: exportar_excel.php

// 1. INCLUSIONES Y LIBRERÍAS
require '../../../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

include '../../../model/conexion.php'; 

// ---------------------------------------------
// 2. OBTENER FILTROS Y CONSTRUIR CONSULTA
// ---------------------------------------------
// ... (Misma lógica de obtención de filtros que el archivo anterior) ...
$q = isset($_GET['q']) ? $_GET['q'] : '';
$filtroEstado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : '';
$filtroEmpleado = isset($_GET['empleado']) && $_GET['empleado'] !== '' ? $_GET['empleado'] : '';
$filtroFecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : '';

// --- TEXTO DE FILTROS ---
$filtrosAplicados = [];
if ($q !== '') $filtrosAplicados[] = "Búsqueda: " . $q;
if ($filtroEstado !== '') $filtrosAplicados[] = "Estado: " . $filtroEstado;
if ($filtroEmpleado !== '') $filtrosAplicados[] = "Empleado: " . $filtroEmpleado;
if ($filtroFecha !== '') $filtrosAplicados[] = "Fecha: " . date('d/m/Y', strtotime($filtroFecha));

$textoFiltros = empty($filtrosAplicados) ? "Listado general" : "Filtros: " . implode(" | ", $filtrosAplicados);

// Consulta SQL (Igual que en PDF)
$baseQuery = "FROM pedidos p 
              INNER JOIN usuarios cli ON p.Cedula_Cliente = cli.Cedula 
              LEFT JOIN usuarios emp ON p.Empleado_Encargado = emp.Cedula 
              INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material 
              WHERE 1=1";

if ($q !== '') $baseQuery .= " AND (cli.Nombre LIKE :q OR cli.Apellido LIKE :q OR p.Id_pedido LIKE :q OR p.Estado_Pedido LIKE :q)";
if ($filtroEstado !== '') $baseQuery .= " AND p.Estado_Pedido = :estado";
if ($filtroEmpleado !== '') $baseQuery .= " AND emp.Cedula = :empleado";
if ($filtroFecha !== '') $baseQuery .= " AND DATE(p.Fecha_Solicitud) = :fecha";

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
// 3. GENERAR EXCEL
// ---------------------------------------------

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 3.1 ENCABEZADO (Ajustado para 11 columnas ahora)
$mergeRange = 'A1:K1'; 
$mergeRange2 = 'A2:K2';
$mergeRange3 = 'A3:K3';

// Título
$sheet->setCellValue('A1', 'Reporte de Pedidos - ColorLink');
$sheet->mergeCells($mergeRange);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getFont()->getColor()->setARGB('FF007bff'); 
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Fecha
$sheet->setCellValue('A2', 'Fecha de Generación: ' . date('d/m/Y H:i:s'));
$sheet->mergeCells($mergeRange2);
$sheet->getStyle('A2')->getFont()->setItalic(true);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Filtros
$sheet->setCellValue('A3', $textoFiltros);
$sheet->mergeCells($mergeRange3);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3')->getFont()->getColor()->setARGB('FF555555');

$headerRow = 4; 

// 3.2 ENCABEZADOS DE TABLA (Actualizados)
$headers = [
    'Nro.', 'Fecha Solicitud', 'Fecha Entrega', 'Estado', 'Cliente', 
    'Empleado', 'Material', 'Centímetros', 'Cant. Diseños', 'Costo (Bs.)'
];
$sheet->fromArray($headers, NULL, 'A' . $headerRow); 

// 3.3 ESTILOS TABLA
$headerRange = 'A' . $headerRow . ':K' . $headerRow; // Hasta la K
$sheet->getStyle($headerRange)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle($headerRange)->getFill()->getStartColor()->setARGB('FFFFC107'); 
$sheet->getStyle($headerRange)->getFont()->setBold(true);
$sheet->getStyle($headerRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

// 3.4 DATOS
$rowIndex = $headerRow + 1;
foreach ($Pedidos as $pedido) {
    
    // Lógica para textos vacíos
    $fechaEntrega = !empty($pedido['Fecha_Entrega']) ? $pedido['Fecha_Entrega'] : 'No definida';
    $empleado = !empty($pedido['Empleado_Encargado']) ? $pedido['Nombre_Empleado'] : 'Sin asignar';

    $dataRow = [
        $pedido['Id_pedido'],
        $pedido['Fecha_Solicitud_Simple'],
        $fechaEntrega,
        $pedido['Estado_Pedido'],
        $pedido['Cliente'],
        $empleado,
        $pedido['Material'],
        $pedido['Centimetros'],
        $pedido['Cantidades'],
        $pedido['Costo']
    ];
    $sheet->fromArray($dataRow, NULL, 'A' . $rowIndex); 
    $rowIndex++;
}

// 3.5 AUTOSIZE
foreach (range('A', 'K') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// 3.6 DESCARGA
$filename = 'Pedidos_Reporte_Excel_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>