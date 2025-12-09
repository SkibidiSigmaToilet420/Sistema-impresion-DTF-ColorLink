<?php
include ('../../model/conexion.php');
include ('../../plantilla/sesion.php');
$cedulaBorrar =  $_GET['Codigo'];

if ($cedulaBorrar == $CedulaSesion){
    session_start();
    $_SESSION["borrar_mismo_usuario"]="No se puede eliminar su propio usuario";
    $redirect_url = $_SESSION['previous_page'];
    header("Location: " . $redirect_url);
    exit();
}else{
     '45612378';
    $sentencia = $db -> prepare('UPDATE usuarios SET Estado = "INACTIVO" WHERE Cedula = :Cedula');
    $sentencia -> bindParam(':Cedula', $cedulaBorrar);
    $sentencia -> execute();
    session_start();
    $_SESSION['Usuario_Eliminado'] = "El usuario ha sido eliminado exitosamente!";
    $redirect_url = $_SESSION['previous_page'];
    header("Location: " . $redirect_url);
    exit();
}
