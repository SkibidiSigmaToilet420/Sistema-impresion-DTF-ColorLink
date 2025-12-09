<?php 
include '../../model/conexion.php';
include '../../plantilla/sesion.php';
$Cedula = $_GET['Codigo'];
$Cargo = $_GET['Cargo'];

echo $Cargo." ".$Cedula;

$sentencia = $db -> prepare('UPDATE empleados SET Cargo = :Cargo WHERE Cedula_Empleado = :Cedula');
$sentencia -> bindParam(':Cargo', $Cargo);
$sentencia -> bindParam(':Cedula', $Cedula);
$sentencia -> execute();
$_SESSION['CargoAsignado'] = 'Cargo asignado exitosamente';
header('Location:'.$URL.'admin/empleados.php');