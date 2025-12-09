<?php 
include '../../model/conexion.php';
include '../../plantilla/sesion.php';
$Cedula = $_GET['Codigo'];
$Tipo = $_GET['Tipo'];

echo $Tipo." ".$Cedula;

$sentencia = $db -> prepare('UPDATE clientes SET Tipo_de_Cliente = :Tipo WHERE Cedula_Cliente = :Cedula');
$sentencia -> bindParam(':Tipo', $Tipo);
$sentencia -> bindParam(':Cedula', $Cedula);
$sentencia -> execute();
// Obtener correo del cliente
$sentenciaCorreo = $db->prepare('SELECT Correo FROM usuarios WHERE Cedula = :Cedula');
$sentenciaCorreo->bindParam(':Cedula', $Cedula);
$sentenciaCorreo->execute();
$correoCliente = $sentenciaCorreo->fetchColumn();


require_once __DIR__ . '/../../email/tipo_cliente.php';
if ($correoCliente) {
    enviarCorreoClienteTipo($correoCliente, $Tipo);
}

$_SESSION['TipoAsignado'] = 'Tipo de cliente asignado exitosamente';
header('Location:'.$URL.'admin/Clientes.php');