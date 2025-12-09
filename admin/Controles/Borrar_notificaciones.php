<?php
session_start();
include ('../../model/conexion.php');
$ultimoId = 0;

if (isset($db) && $db instanceof PDO) {
    $stmt = $db->query("SELECT MAX(Id_pedido) FROM pedidos");
    $ultimoId = $stmt->fetchColumn();
}
$_SESSION['ultimo_pedido_notificado_admin'] = $ultimoId;
// Persist the ultimoId to a file so the choice survives logout
$file = __DIR__ . "/ultimo_notif_admin.txt";
@file_put_contents($file, (int)$ultimoId);
echo '<script>window.location.replace(document.referrer || "../../index.php");</script>';
exit;
