<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

function enviarCorreoPedidoRechazado($correoDestino, $motivo, $detalle = null) {
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
        $mail->Subject = 'Tu pedido ha sido rechazado';

        $mensaje = '';
        $mensaje .= '<div style="font-family: Arial, Helvetica, sans-serif; color: #222; background: #f6f7f9; padding: 18px;">';
        $mensaje .= '<p style="margin:0 0 12px; font-size:15px; color:#333;">Su pedido ha sido rechazado por la siguiente razón:</p>';
        $mensaje .= '<div style="background:#fff7f8;border-radius:6px;padding:10px;margin:10px 0;border:1px solid #f1c0c4">';
        $mensaje .= '<p style="margin:0;color:#b02a37;font-size:14px"><strong>Motivo:</strong><br>' . nl2br(htmlspecialchars($motivo)) . '</p>';
        $mensaje .= '</div>';

        if ($detalle && is_array($detalle)) {
            $mensaje .= '<div style="background:#ffffff;border-radius:8px;padding:12px;margin:12px 0;border:1px solid #e1e4ea">';
            $mensaje .= '<h4 style="margin:0 0 8px;color:#1f3b73;font-size:16px">Numero del pedido: ' . htmlspecialchars($detalle['Id_pedido'] ?? '') . '</h4>';
            $mensaje .= '<div style="font-size:14px;color:#444;margin-bottom:8px">';
            if (!empty($detalle['Cantidades'])) $mensaje .= '<div><strong>Numero de impresiones:</strong> ' . htmlspecialchars($detalle['Cantidades']) . '</div>';
            if (!empty($detalle['Centimetros'])) $mensaje .= '<div><strong>Centímetros:</strong> ' . htmlspecialchars($detalle['Centimetros']) . '</div>';
            if (!empty($detalle['Costo'])) $mensaje .= '<div><strong>Total Bs:</strong> ' . htmlspecialchars($detalle['Costo']) . '</div>';
            if (!empty($detalle['Fecha_Entrega'])) $mensaje .= '<div><strong>Fecha de entrega:</strong> ' . htmlspecialchars($detalle['Fecha_Entrega']) . '</div>';
            if (!empty($detalle['Estado_Pedido'])) $mensaje .= '<div><strong>Estado:</strong> ' . htmlspecialchars($detalle['Estado_Pedido']) . '</div>';
            if (!empty($detalle['NombreCliente'])) $mensaje .= '<div><strong>Cliente:</strong> ' . htmlspecialchars($detalle['NombreCliente']) . '</div>';
            $mensaje .= '</div>';

            // Incluir imágenes si vienen en el detalle (misma lógica que en pedido_revision)
            $imgItems = [];
            if (!empty($detalle['Imagenes']) && is_array($detalle['Imagenes'])) {
                $imgItems = $detalle['Imagenes'];
            }

            if (!empty($imgItems)) {
                $mensaje .= '<table role="presentation" style="width:100%;border-collapse:collapse;margin-top:8px"><tr>';
                foreach ($imgItems as $imgItem) {
                    if (is_array($imgItem)) {
                        $imgRel = $imgItem['url'] ?? ($imgItem['URL_Diseno'] ?? null);
                        $cantidadDiseno = $imgItem['cantidad'] ?? ($imgItem['Cantidad'] ?? null);
                    } else {
                        $imgRel = $imgItem;
                        $cantidadDiseno = null;
                    }
                    if (!$imgRel) continue;
                    $localPath = realpath(__DIR__ . '/../' . $imgRel);
                    $cid = null;
                    if ($localPath && file_exists($localPath)) {
                        $cid = 'img' . uniqid();
                        try {
                            $mail->addEmbeddedImage($localPath, $cid);
                        } catch (Exception $e) {
                            $cid = null;
                        }
                    }
                    if ($cid) {
                        $imgTag = '<img src="cid:' . $cid . '" style="max-width:180px;width:100%;height:auto;border:1px solid #ddd;padding:4px;border-radius:4px" alt="Diseño">';
                    } else {
                        global $URL;
                        $imgUrl = (isset($URL) ? $URL : '') . $imgRel;
                        $imgTag = '<img src="' . htmlspecialchars($imgUrl) . '" style="max-width:180px;width:100%;height:auto;border:1px solid #ddd;padding:4px;border-radius:4px" alt="Diseño">';
                    }

                    $mensaje .= '<td style="padding:6px;vertical-align:top;text-align:center;width:200px">';
                    if ($cantidadDiseno !== null) {
                        $mensaje .= '<div style="font-size:13px;color:#333;margin-bottom:6px"><strong>Impresiones</strong><br>' . htmlspecialchars($cantidadDiseno) . '</div>';
                    }
                    $mensaje .= $imgTag;
                    $mensaje .= '</td>';
                }
                $mensaje .= '</tr></table>';
            }

            $mensaje .= '</div>';
        }

        $mensaje .= '</div>'; // wrapper

        $mail->Body = $mensaje;
        $mail->AltBody = strip_tags($mensaje);
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
