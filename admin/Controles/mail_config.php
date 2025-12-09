<?php
// Configuración de PHPMailer para reusar en controladores
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../../vendor/autoload.php';

function enviarCorreoClienteTipo($correo, $tipo) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'colorlink609@gmail.com';
        $mail->Password   = 'pcpo wunj vuhc uzrz';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('colorlink609@gmail.com', 'ColorLink');
        $mail->addAddress($correo);
        $mail->isHTML(true);
        $mail->Subject = '¡Felicidades! Ahora eres un cliente ' . $tipo;
        $mail->Body    = 'Felicidades, ahora eres un cliente <b>' . htmlspecialchars($tipo) . '</b> en ColorLink.';
        $mail->send();
        return true;
    } catch (Exception $e) {
        // error_log("No se pudo enviar el correo: {$mail->ErrorInfo}");
        return false;
    }
}
