<?php
include '../model/conexion.php';
include '../plantilla/sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $TipoSesion == 1) {
    $direccion = trim($_POST['Direccion']);
    
    $sentenciaActualizacion = $db->prepare("UPDATE clientes SET Direccion = :direccion WHERE Cedula_Cliente = :cedula");
    $sentenciaActualizacion->bindParam(':direccion', $direccion);
    $sentenciaActualizacion->bindParam(':cedula', $CedulaSesion);
    $resultado = $sentenciaActualizacion->execute();

    if ($resultado) {
        header("Location: perfil.php?actualizacion_cliente=ok");
    } else {
        header("Location: perfil.php?actualizacion_cliente=error");
    }
    exit;
}
header("Location: perfil.php");
exit;
?>