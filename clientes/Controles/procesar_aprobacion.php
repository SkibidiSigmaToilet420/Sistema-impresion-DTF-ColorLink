<?php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

$idPedido = $_GET['id'] ?? 0;
$cedulaCliente = $CedulaSesion;

if ($idPedido > 0) {

  $sql = "UPDATE pedidos 
            SET Estado_Pedido = 'Produccion' 
            WHERE Id_pedido = :id 
              AND Cedula_Cliente = :cedula
              AND Estado_Pedido = 'Verificado'";

  $stmt = $db->prepare($sql);
  $stmt->bindParam(':id', $idPedido);
  $stmt->bindParam(':cedula', $cedulaCliente);
  $stmt->execute();

  // Redirigir de vuelta
  $_SESSION['Pedido_Aprobado'] = 'El pedido ha sido aprobado';
  header('Location:' . $URL . 'clientes/mis_aprobaciones.php');
  exit;
}
