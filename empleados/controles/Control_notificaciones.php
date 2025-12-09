<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$count = 0;
$notificaciones = [];
$ultimoId = 0;

$file = __DIR__ . "/ultimo_notif_empleado.txt";
if (file_exists($file)) {
    $ultimoId = (int)file_get_contents($file);
    $_SESSION['ultimo_pedido_notificado_empleado'] = $ultimoId;
    if (isset($db) && $db instanceof PDO) {
        $stmt = $db->prepare("SELECT p.Id_pedido, p.Estado_Pedido, p.Fecha_Solicitud, u.Nombre, u.Apellido, u.Cedula
            FROM pedidos p JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido > ? ORDER BY p.Fecha_Solicitud DESC LIMIT 20");
        $stmt->execute([$ultimoId]);
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($notificaciones);
    }
} else if (isset($_SESSION['ultimo_pedido_notificado_empleado'])) {
    $ultimoId = $_SESSION['ultimo_pedido_notificado_empleado'];
    if (isset($db) && $db instanceof PDO) {
        $stmt = $db->prepare("SELECT p.Id_pedido, p.Estado_Pedido, p.Fecha_Solicitud, u.Nombre, u.Apellido, u.Cedula
            FROM pedidos p JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido > ? ORDER BY p.Fecha_Solicitud DESC LIMIT 20");
        $stmt->execute([$ultimoId]);
        $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $count = count($notificaciones);
    }
} else if (isset($db) && $db instanceof PDO) {
    $stmt = $db->prepare("SELECT p.Id_pedido, p.Estado_Pedido, p.Fecha_Solicitud, u.Nombre, u.Apellido, u.Cedula
        FROM pedidos p JOIN usuarios u ON p.Cedula_Cliente = u.Cedula ORDER BY p.Fecha_Solicitud DESC LIMIT 20");
    $stmt->execute();
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = count($notificaciones);
}
?>
