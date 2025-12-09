<?php
include '../../model/conexion.php';
include '../../plantilla/sesion.php';

$codigo = $_POST['CODIGO_Material'];
$nombre = $_POST['Nombre'];
$cantidad = $_POST['Cantidad'];
$usuario = $CedulaSesion;


$sentencia = $db->prepare('UPDATE materiales SET 
                         Nombre_material = :nombre, 
                         Cantidad = :cantidad, 
                         Ingresado_Modificado_por = :usuario 
                         WHERE CODIGO_Material = :codigo');

$sentencia->bindParam(':nombre', $nombre);
$sentencia->bindParam(':cantidad', $cantidad);
$sentencia->bindParam(':usuario', $usuario);
$sentencia->bindParam(':codigo', $codigo);
$sentencia->execute();

$_SESSION['MaterialEditar'] = "Material actualizado correctamente";
header("Location: ../Material.php");
?>