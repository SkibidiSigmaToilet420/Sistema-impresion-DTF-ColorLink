<?php
session_start();
include '../model/conexion.php';
include 'token_helper.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../login.php');
    exit;
}

$token = isset($_POST['token']) ? $_POST['token'] : '';
$nueva = isset($_POST['nueva']) ? $_POST['nueva'] : '';
$confirmar = isset($_POST['confirmar']) ? $_POST['confirmar'] : '';

$val = validar_token($token);
if(!$val){
    $_SESSION['restablecer_error'] = 'Enlace inválido o expirado.';
    header('Location: ../login.php');
    exit;
}

if($nueva !== $confirmar){
    $_SESSION['restablecer_error'] = 'Las contraseñas no coinciden.';
    header('Location: restablecer.php?token=' . urlencode($token));
    exit;
}

// No minimum ni máximo de longitud: aceptar cualquier cantidad de dígitos/caracteres

$email = $val['email'];
$hash = password_hash($nueva, PASSWORD_DEFAULT);
$sent = $db->prepare('UPDATE usuarios SET Password_usuario = :pass WHERE Correo = :correo');
$sent->bindParam(':pass', $hash);
$sent->bindParam(':correo', $email);
$sent->execute();

$_SESSION['restablecer_ok'] = 'Tu contraseña fue actualizada. Inicia sesión con la nueva contraseña.';
header('Location: ../login.php');
exit;
