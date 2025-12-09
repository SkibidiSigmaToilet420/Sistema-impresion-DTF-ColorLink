<?php
include ('../../model/conexion.php');
include ('../../plantilla/sesion.php');
$codigoMaterial =  $_GET['Codigo'];


$sentencia = $db -> prepare('UPDATE materiales SET Estado_Material = "ACTIVO" WHERE CODIGO_Material = :CODIGO_Material');
$sentencia -> bindParam(':CODIGO_Material', $codigoMaterial);
$sentencia -> execute();
session_start();
$_SESSION['Material_reactivado'] = "El material ha sido reactivado exitosamente!";
header("Location:".$URL."admin/Material.php");

