
<?php
// Borra solo notificaciones para clientes
include_once $_SERVER['DOCUMENT_ROOT'] . '/Sistema impresion DTF ColorLink/model/conexion.php';
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/Sistema impresion DTF ColorLink/model/conexion.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Sistema impresion DTF ColorLink/plantilla/sesion.php';
$ultimoId = 0;
if (isset($db) && $db instanceof PDO && isset($CedulaSesion)) {
    $stmt = $db->prepare("SELECT MAX(Id_pedido) FROM pedidos WHERE Cedula_Cliente = ?");
    $stmt->execute([$CedulaSesion]);
    $ultimoId = $stmt->fetchColumn();
}
$_SESSION['ultimo_pedido_notificado_cliente'] = $ultimoId;
echo '<script>window.location.replace(document.referrer || "../index.php");</script>';
exit;
