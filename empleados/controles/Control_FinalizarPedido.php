<?php
include '../../model/conexion.php';
include '../../plantilla/sesion.php';

$Codigo = $_GET['Codigo'];

// ACTUALIZACIÓN:
// 1. Estado -> Finalizado
// 2. Fecha_Finalizacion -> NOW() (Momento real en que se terminó)
// 3. Fecha_Entrega -> NO SE TOCA (Ya se estableció antes)

$sentencia = $db->prepare("UPDATE pedidos SET Estado_Pedido = 'Finalizado' WHERE Id_pedido = :Codigo");
$sentencia->bindParam(':Codigo', $Codigo);
$sentencia->execute();

// --- LÓGICA DE CORREO (SE MANTIENE IGUAL) ---

// preparar y enviar correo de pedido finalizado al cliente
$stmtDetalle = $db->prepare('SELECT p.Id_pedido, p.Cantidades, p.Costo, p.Fecha_Entrega, p.Centimetros, p.Estado_Pedido, u.Nombre, u.Apellido, u.Correo FROM pedidos p INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :IdPedido');
$stmtDetalle->bindParam(':IdPedido', $Codigo);
$stmtDetalle->execute();
$detalle = $stmtDetalle->fetch(PDO::FETCH_ASSOC);

// obtener imagenes/disenos asociados (si existen)
$stmtImgs = $db->prepare('SELECT d.Nombre_Diseno, d.URL_Diseno, d.Cantidad FROM disenos d INNER JOIN pedido_diseno pd ON pd.id_Diseno = d.ID_Diseno WHERE pd.id_Pedido = :IdPedido');
$stmtImgs->bindParam(':IdPedido', $Codigo);
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
	$detalle['NombreCliente'] = ($detalle['Nombre'] ?? '') . ' ' . ($detalle['Apellido'] ?? '');
}

// enviar correo
require_once __DIR__ . '/../../email/pedido_finalizado.php';
if (!empty($detalle['Correo'])) {
    // Usamos la fecha de entrega que ya estaba en la base de datos para el correo
    $fechaEntrega = $detalle['Fecha_Entrega']; 
	$sent = enviarCorreoPedidoFinalizado($detalle['Correo'], $fechaEntrega, $detalle);
	if (!$sent) {
		// registrar fallo de envío para depuración
		$logDir = __DIR__ . '/../../logs';
		if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
		$logFile = $logDir . '/mail_errors.log';
		$msg = "[" . date('Y-m-d H:i:s') . "] Fallo al enviar correo pedido_finalizado para pedido {$Codigo} al correo {$detalle['Correo']}\n";
		$msg .= "Detalle: " . print_r($detalle, true) . "\n\n";
		@file_put_contents($logFile, $msg, FILE_APPEND);
		$_SESSION['EstadoPedido'] = 'El pedido fue finalizado pero no se pudo notificar por correo. Se registró el error.';
	} else {
		$_SESSION['EstadoPedido'] = 'Se le notificara al cliente que el pedido ha sido finalizado';
	}
} else {
	$_SESSION['EstadoPedido'] = 'Pedido finalizado. No se encontró correo del cliente.';
}
header("Location: ".$URL."empleados/PedidosProduccion.php");
?>