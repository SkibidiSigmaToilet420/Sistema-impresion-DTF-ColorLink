<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

function enviarCorreoPedidoCliente($correoDestino) {
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
        $mail->addAddress($correoDestino);
        $mail->isHTML(true);
        $mail->Subject = '¡Tu pedido ha sido recibido!';
        $mail->Body    = 'Hola, hemos recibido tu pedido correctamente. Pronto nos pondremos en contacto contigo.<br><br>Gracias por confiar en ColorLink.';
        $mail->send();
        return true;
    } catch (Exception $e) {
        // error_log("No se pudo enviar el correo: {$mail->ErrorInfo}");
        return false;
    }
}
