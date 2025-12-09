<?php
// Archivo: email/bienvenida_usuario.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar el autoload de Composer (ajusta la ruta si 'vendor' no está en la raíz)
require_once __DIR__ . '/../vendor/autoload.php'; 

function enviarCorreoBienvenida($correoUsuario, $nombreUsuario) {
    
    $mail = new PHPMailer(true);

    try {
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'colorlink609@gmail.com';
        $mail->Password = 'pcpo wunj vuhc uzrz'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; 

        $mail->setFrom('colorlink609@gmail.com', 'ColorLink');
        
        $mail->addAddress($correoUsuario, $nombreUsuario); 

        // --- CONTENIDO DEL CORREO ---
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = '¡Bienvenido a ColorLink!';
        $mail->Body    = "
            <html lang='es'>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <div style='max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                    <h2 style='color: #0D47A1; text-align: center;'>¡Hola, " . htmlspecialchars($nombreUsuario) . "!</h2>
                    <p>Te damos la más cordial bienvenida a <strong>ColorLink</strong>.</p>
                    <p>Tu cuenta ha sido creada exitosamente. Ahora puedes iniciar sesión en nuestro sistema y comenzar a gestionar tus pedidos de impresión DTF de alta calidad.</p>
                    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                    <br>
                    <p style='text-align: center;'>
                        <a href='https://colorlink.byethost10.com/login.php' style='background-color: #0D47A1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>
                            Iniciar Sesión
                        </a>
                    </p>
                    <br>
                    <p style='font-size: 0.9em; color: #777;'>Saludos,<br>El equipo de ColorLink</p>
                </div>
            </body>
            </html>";

        $mail->send();
        return true; // El correo se envió

    } catch (Exception $e) {
        // En un entorno de producción, nunca muestres el error al usuario.
        // Guárdalo en un log para depuración.
        error_log("Error al enviar correo de bienvenida: " . $mail->ErrorInfo);
        return false; // El correo falló
    }
}
?>