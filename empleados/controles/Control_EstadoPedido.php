<?php 
include '../../model/conexion.php';
include '../../plantilla/sesion.php';

// Recibir datos (Compatible con GET y POST)
$codigo = $_REQUEST['Codigo'] ?? null;
$estado = $_REQUEST['Estado'] ?? null;
$motivo = $_REQUEST['motivo_rechazo'] ?? '';
$fechaEntrega = $_REQUEST['fecha'] ?? null; // NUEVO: Recibir fecha

// Validación básica
if (!$codigo || !$estado) {
    header("Location: " . $URL . "empleados/PedidosEncargados.php");
    exit;
}

// Flag para omitir email (opcional)
$no_email = isset($_REQUEST['no_email']) && $_REQUEST['no_email'] == '1';

// --- ACTUALIZACIÓN DE BASE DE DATOS ---

if ($estado === 'Verificado' && $fechaEntrega) {
    // Si es verificado, actualizamos Estado Y Fecha de Entrega
    $sentencia = $db->prepare("UPDATE pedidos SET Estado_Pedido = :Estado, Fecha_Entrega = :Fecha WHERE Id_pedido = :Codigo");
    $sentencia->bindParam(':Estado', $estado);
    $sentencia->bindParam(':Fecha', $fechaEntrega);
    $sentencia->bindParam(':Codigo', $codigo);
} else {
    // Caso normal (Rechazado o sin fecha)
    $sentencia = $db->prepare("UPDATE pedidos SET Estado_Pedido = :Estado WHERE Id_pedido = :Codigo");
    $sentencia->bindParam(':Estado', $estado);
    $sentencia->bindParam(':Codigo', $codigo);
}

if ($sentencia->execute()) {
    
    // --- LÓGICA DE CORREOS ---
    
    // Obtener correo del cliente
    $stmtCorreo = $db->prepare('SELECT u.Correo FROM pedidos p INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :IdPedido');
    $stmtCorreo->bindParam(':IdPedido', $codigo);
    $stmtCorreo->execute();
    $correoCliente = $stmtCorreo->fetchColumn();

    // Solo enviar si hay correo y no está suprimido
    if ($correoCliente && !$no_email) {
        $estadoLower = strtolower($estado);
        
        // CORREO VERIFICADO (REVISIÓN)
        if ($estadoLower === 'verificado') {
            // obtener detalles (incluyendo la nueva fecha que acabamos de guardar)
            $stmtDetalle = $db->prepare('SELECT p.Id_pedido, p.Cantidades, p.Costo, p.Fecha_Entrega, p.Centimetros, p.Estado_Pedido, u.Nombre, u.Apellido, u.Correo FROM pedidos p INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :IdPedido');
            $stmtDetalle->bindParam(':IdPedido', $codigo);
            $stmtDetalle->execute();
            $detalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC);

            // obtener imagenes
            $stmtImgs = $db->prepare('SELECT d.Nombre_Diseno, d.URL_Diseno, d.Cantidad FROM disenos d INNER JOIN pedido_diseno pd ON pd.id_Diseno = d.ID_Diseno WHERE pd.id_Pedido = :IdPedido');
            $stmtImgs->bindParam(':IdPedido', $codigo);
            $stmtImgs->execute();
            $imagenes = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);

            $imagenes_mapped = [];
            foreach ($imagenes as $im) {
                $imagenes_mapped[] = [
                    'url' => $im['URL_Diseno'] ?? ($im['Nombre_Diseno'] ?? ''),
                    'cantidad' => $im['Cantidad'] ?? null,
                    'nombre' => $im['Nombre_Diseno'] ?? null
                ];
            }

            if ($detalle) {
                $detalle['Imagenes'] = $imagenes_mapped;
            }
            require_once __DIR__ . '/../../email/pedido_revision.php';
            enviarCorreoPedidoRevision($correoCliente, $detalle);
            
        // CORREO RECHAZADO
        } elseif ($estadoLower === 'rechazado') {
            $stmtDetalle = $db->prepare('SELECT p.Id_pedido, p.Cantidades, p.Costo, p.Fecha_Entrega, p.Centimetros, p.Estado_Pedido, u.Nombre, u.Apellido, u.Correo FROM pedidos p INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :IdPedido');
            $stmtDetalle->bindParam(':IdPedido', $codigo);
            $stmtDetalle->execute();
            $detalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC);

            $motivoFinal = !empty($motivo) ? $motivo : 'No se proporcionó un motivo específico.';

            $stmtImgs2 = $db->prepare('SELECT d.Nombre_Diseno, d.URL_Diseno, d.Cantidad FROM disenos d INNER JOIN pedido_diseno pd ON pd.id_Diseno = d.ID_Diseno WHERE pd.id_Pedido = :IdPedido');
            $stmtImgs2->bindParam(':IdPedido', $codigo);
            $stmtImgs2->execute();
            $imagenes2 = $stmtImgs2->fetchAll(PDO::FETCH_ASSOC);

            $imagenes_mapped2 = [];
            foreach ($imagenes2 as $im) {
                $imagenes_mapped2[] = [
                    'url' => $im['URL_Diseno'] ?? ($im['Nombre_Diseno'] ?? ''),
                    'cantidad' => $im['Cantidad'] ?? null,
                    'nombre' => $im['Nombre_Diseno'] ?? null
                ];
            }

            if ($detalle) {
                $detalle['Imagenes'] = $imagenes_mapped2;
                $detalle['Motivo_Rechazo'] = $motivoFinal;
            }

            require_once __DIR__ . '/../../email/pedido_rechazado.php';
            enviarCorreoPedidoRechazado($correoCliente, $motivoFinal, $detalle);
        }
    }

    if ($no_email) {
        $_SESSION['EstadoPedido'] = 'El pedido ha sido ' . $estado . '. (sin notificación al cliente)';
    } else {
        $_SESSION['EstadoPedido'] = 'Se le notificará al cliente que el pedido ha sido ' . $estado;
    }

} else {
    $_SESSION['Error'] = 'Hubo un error al actualizar el pedido.';
}

header("Location: " . $URL . "empleados/PedidosEncargados.php");
exit;
?>