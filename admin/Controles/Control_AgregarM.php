<?php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

$nombre   = isset($_POST['Nombre']) ? trim($_POST['Nombre']) : null;
$cantidad = isset($_POST['Cantidad']) ? intval($_POST['Cantidad']) : null;
$precioCM = isset($_POST['Precio_CM']) ? trim($_POST['Precio_CM']) : null;
$usuario  = $CedulaSesion;

echo $nombre." ".$cantidad." ".$precioCM." ".$usuario;

// Validación básica antes de continuar
if (is_null($precioCM) || is_null($nombre) || is_null($cantidad) || is_null($usuario)) {
    die('Faltan datos obligatorios del formulario.');
}

    $sentencia = $db->prepare('INSERT INTO materiales (Nombre_material, Cantidad, Precio_CM, Ingresado_Modificado_por) VALUES (:nombre, :cantidad, :PecioCM, :usuario)');
    $sentencia->bindParam(':nombre', $nombre);
    $sentencia->bindParam(':cantidad', $cantidad);
    $sentencia->bindParam(':PecioCM', $precioCM);
    $sentencia->bindParam(':usuario', $usuario);
    $sentencia->execute();
    $_SESSION['MaterialRegistrado'] = 'El material ha sido registrado con exito!';
    header("Location: ../Material.php");
    exit;
?>