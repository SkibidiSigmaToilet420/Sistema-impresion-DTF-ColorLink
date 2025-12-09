<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once $_SERVER['DOCUMENT_ROOT'] . '/Sistema impresion DTF ColorLink/model/conexion.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Sistema impresion DTF ColorLink/plantilla/sesion.php';
$count = 0;
$notificaciones = [];

$file = __DIR__ . "/ultimo_notif_cliente_{$CedulaSesion}.txt";
if (file_exists($file)) {
    $ultimoId = (int)file_get_contents($file);
    $_SESSION['ultimo_pedido_notificado_cliente'] = $ultimoId;
    if (isset($CedulaSesion) && $CedulaSesion && isset($db) && $db instanceof PDO) {
        $stmt = $db->prepare("SELECT * FROM pedidos WHERE Cedula_Cliente = ? AND Id_pedido > ? ORDER BY Fecha_Solicitud DESC LIMIT 20");
        $stmt->execute([$CedulaSesion, $ultimoId]);
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($notificaciones);
    }
} else if (isset($_SESSION['ultimo_pedido_notificado_cliente'])) {
    $ultimoId = $_SESSION['ultimo_pedido_notificado_cliente'];
    if (isset($CedulaSesion) && $CedulaSesion && isset($db) && $db instanceof PDO) {
        $stmt = $db->prepare("SELECT * FROM pedidos WHERE Cedula_Cliente = ? AND Id_pedido > ? ORDER BY Fecha_Solicitud DESC LIMIT 20");
        $stmt->execute([$CedulaSesion, $ultimoId]);
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($notificaciones);
    }
} else if (isset($CedulaSesion) && $CedulaSesion && isset($db) && $db instanceof PDO) {
    $stmt = $db->prepare("SELECT * FROM pedidos WHERE Cedula_Cliente = ? ORDER BY Fecha_Solicitud DESC LIMIT 20");
    $stmt->execute([$CedulaSesion]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = count($notificaciones);
}
