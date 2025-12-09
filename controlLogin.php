<?php 
include ('model/conexion.php');
$correo =  $_POST['correo'];
$pass = $_POST['pass'];
echo($correo.$pass);

$sentencia = $db -> prepare("SELECT * FROM usuarios 
WHERE Correo = :correo");
  
$sentencia ->bindParam(':correo', $correo);
$sentencia -> execute();
$datos_usuario = $sentencia -> fetch(PDO::FETCH_ASSOC);
$tipoUsuario = $datos_usuario['Tipo_Usuario'];

if($datos_usuario && (password_verify($pass, $datos_usuario['Password_usuario']))){
    echo('existe el usuario');
    if($datos_usuario['Estado'] != 'ACTIVO'){
        session_start();
        $_SESSION['error_sesion'] =  "El usuario no se encuentra activo, contacte con el administrador"; 
        header("Location:".$URL. "login.php");
        exit();
    }
    echo($correo.$pass.$datos_usuario['Password_usuario']);
    session_start();
    $_SESSION['incio_sesion'] =  $correo;
    $_SESSION['mensaje_sesion'] =  'Se inicio sesión exitosamente!';
    switch($tipoUsuario){
        case 1:
            header("Location:".$URL. "clientes/index.php"); 
            break;
        case 2:
            header("Location:".$URL. "empleados/index.php"); 
            break;
        case 3:
            header("Location:".$URL. "admin/index.php"); 
            break;
    }
     
}else{
    echo($correo.$pass.$datos_usuario['Password_usuario']);
    echo('no existe el usuario');
    session_start();
    $_SESSION['error_sesion'] =  "Los datos ingresados no existen o son incorrectos"; 
    header("Location:".$URL. "login.php");
}