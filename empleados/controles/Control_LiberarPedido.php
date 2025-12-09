<?php
include '../../model/conexion.php';
include '../../plantilla/sesion.php';

$Codigo = $_GET['Codigo'];

$setencia = $db -> prepare("UPDATE `pedidos` SET `Empleado_Encargado` = NULL WHERE `pedidos`.`Id_pedido` = $Codigo;");
$setencia -> execute();
$_SESSION['LiberarPedido'] = 'Se le notificara al administrador que el pedido ha sido liberado';
header("Location: ".$URL."empleados/PedidosEncargados.php");