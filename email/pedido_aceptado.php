<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

function enviarCorreoPedidoAceptado($correoDestino, $detalle = null, $imagenes = [], $estado = 'aceptado') {
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
        if (strtolower($estado) === 'revision') {
            $mail->Subject = 'Tu pedido está en revisión';
            // incluir detalles mínimos del pedido en el cuerpo
            $body = '<p>Su pedido se va a mandar a produccion, confirme en la pagina si quiere que la produccion continue o quiere cambiarlo antes.</p>';
            if ($detalle) {
                $body .= '<ul>';
                $body .= '<li><strong>Numero del pedido:</strong> ' . htmlspecialchars($detalle['Id_pedido']) . '</li>';
                $body .= '<li><strong>Centímetros:</strong> ' . htmlspecialchars($detalle['Centimetros']) . '</li>';
                $body .= '<li><strong>Total Bs:</strong> ' . htmlspecialchars($detalle['Costo']) . '</li>';
                $body .= '</ul>';
            }
            // AltBody con resumen de información
            $alt = 'Tu pedido está en revisión. ID: ' . ($detalle['Id_pedido'] ?? '') . ' - Centímetros: ' . ($detalle['Centimetros'] ?? '') . ' - Total Bs: ' . ($detalle['Costo'] ?? '');
            $mail->AltBody = $alt;
        } else {
            $mail->Subject = 'Enhorabuena, tu pedido ha sido aceptado';
            $title = 'Enhorabuena, tu pedido ha sido aceptado';
            $body = '<p>' . $title . '. A continuación los detalles:</p>';
        }
        if ($detalle) {
            $body .= '<ul>';
            $body .= '<li><strong>Numero del pedido:</strong> ' . htmlspecialchars($detalle['Id_pedido']) . '</li>';
            $body .= '<li><strong>Centímetros:</strong> ' . htmlspecialchars($detalle['Centimetros']) . '</li>';
            $body .= '<li><strong>Total Bs:</strong> ' . htmlspecialchars($detalle['Costo']) . '</li>';
            $body .= '</ul>';
        }

        if (!empty($imagenes)) {
            // adjuntar la primera imagen si existe
            $first = $imagenes[0];
            $imagePath = __DIR__ . '/../clientes/imagenes/' . $first['Nombre_Diseno'];
            if (file_exists($imagePath)) {
                $mail->addAttachment($imagePath);
                $body .= '<p>Adjuntamos el diseño asociado al pedido.</p>';
            }
        }

        // aseguremos AltBody por si el cliente no soporta HTML
        if (empty($mail->AltBody)) {
            $alt = "Pedido: " . ($detalle['Id_pedido'] ?? '') . " - Centímetros: " . ($detalle['Centimetros'] ?? '') . " - Total Bs: " . ($detalle['Costo'] ?? '');
            $mail->AltBody = strip_tags($body) ?: $alt;
        }
        $mail->Body = $body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        // error_log("No se pudo enviar el correo: {$mail->ErrorInfo}");
        return false;
    }
}
