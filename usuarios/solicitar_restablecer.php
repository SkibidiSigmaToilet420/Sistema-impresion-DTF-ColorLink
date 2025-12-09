<?php
include '../model/conexion.php';
require_once __DIR__ . '/../vendor/autoload.php';
include 'token_helper.php';
use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo json_encode(['status' => 'error']);
    exit;
}

$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
if(empty($correo)){
    echo json_encode(['status' => 'error']);
    exit;
}

$sent = $db->prepare('SELECT Correo FROM usuarios WHERE Correo = :correo');
$sent->bindParam(':correo', $correo);
$sent->execute();
$user = $sent->fetch(PDO::FETCH_ASSOC);

if(!$user){
    echo json_encode(['status' => 'noexiste']);
    exit;
}

$token = generar_token($correo);
$urlRestablecer = $URL . 'usuarios/restablecer.php?token=' . urlencode($token);

$mail = new PHPMailer(true);
try{
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'colorlink609@gmail.com';
    $mail->Password = 'pcpo wunj vuhc uzrz';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('colorlink609@gmail.com', 'ColorLink');
    $mail->addAddress($correo);
    $mail->isHTML(true);
    $mail->Subject = 'Restablecer contraseña - ColorLink';
    $mail->Body = 'Para restablecer tu contraseña, haz click en el siguiente enlace: <a href="' . htmlspecialchars($urlRestablecer) . '">Restablecer contraseña</a> . Si no solicitaste este cambio, ignora este correo.';
    $mail->send();
    echo json_encode(['status' => 'ok']);
    exit;
}catch(Exception $e){
    echo json_encode(['status' => 'error']);
    exit;
}
?>