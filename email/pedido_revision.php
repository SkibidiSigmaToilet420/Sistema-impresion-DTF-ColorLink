<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/conexion.php';

function enviarCorreoPedidoRevision($correoDestino, $detalle = null, $intro = null) {
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
        $mail->Subject = 'Tu pedido está en revisión';

    $mensaje = '';
    $mensaje .= '<div style="font-family: Arial, Helvetica, sans-serif; color: #222; background: #f6f7f9; padding: 18px;">';
    // permitir mensaje introductorio personalizado (p. ej. desde Agregar_pedido.php)
    $introMsg = $intro ?? 'Su pedido ha sido revisado, confirme en la pagina si quiere que se envie a producción o quiere cambiarlo antes.';
    $mensaje .= '<p style="margin:0 0 12px; font-size:15px; color:#333;">' . htmlspecialchars($introMsg) . '</p>';

        $ordenes = [];
        if (!$detalle) {
            $ordenes = [];
        } elseif (isset($detalle[0]) && is_array($detalle[0])) {
            $ordenes = $detalle;
        } else {
            $ordenes[] = $detalle;
        }

        if (!empty($ordenes)) {
            foreach ($ordenes as $ord) {
                $mensaje .= '<div style="background:#ffffff;border-radius:8px;padding:12px;margin:12px 0;border:1px solid #e1e4ea">';
                $mensaje .= '<h4 style="margin:0 0 8px;color:#1f3b73;font-size:16px">Numero del pedido: ' . htmlspecialchars($ord['Id_pedido'] ?? '') . '</h4>';
                $mensaje .= '<div style="font-size:14px;color:#444;margin-bottom:8px">';
                if (!empty($ord['Cantidades'])) $mensaje .= '<div><strong>Numero de impresiones:</strong> ' . htmlspecialchars($ord['Cantidades']) . '</div>';
                if (!empty($ord['Centimetros'])) $mensaje .= '<div><strong>Centímetros:</strong> ' . htmlspecialchars($ord['Centimetros']) . '</div>';
                if (!empty($ord['Costo'])) $mensaje .= '<div><strong>Total Bs:</strong> ' . htmlspecialchars($ord['Costo']) . '</div>';
                if (!empty($ord['Fecha_Entrega'])) $mensaje .= '<div><strong>Fecha de entrega:</strong> ' . htmlspecialchars($ord['Fecha_Entrega']) . '</div>';
                if (!empty($ord['Estado_Pedido'])) $mensaje .= '<div><strong>Estado:</strong> ' . htmlspecialchars($ord['Estado_Pedido']) . '</div>';
                if (!empty($ord['NombreCliente'])) $mensaje .= '<div><strong>Cliente:</strong> ' . htmlspecialchars($ord['NombreCliente']) . '</div>';
                $mensaje .= '</div>';

                $imgItems = [];
                if (!empty($ord['Imagenes']) && is_array($ord['Imagenes'])) {
                    $imgItems = $ord['Imagenes'];
                } elseif (!empty($ord['Id_pedido'])) {
                    global $db;
                    $stmtImgs = $db->prepare('SELECT d.URL_Diseno, d.Cantidad FROM disenos d JOIN pedido_diseno pd ON d.ID_Diseno = pd.ID_Diseno WHERE pd.ID_Pedido = :IdPedido');
                    $stmtImgs->bindParam(':IdPedido', $ord['Id_pedido']);
                    $stmtImgs->execute();
                    $rows = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $r) {
                        $imgItems[] = ['url' => $r['URL_Diseno'], 'cantidad' => $r['Cantidad']];
                    }
                }

                if (!empty($imgItems)) {
                    // usar tabla para mayor compatibilidad en clientes de correo
                    $mensaje .= '<table role="presentation" style="width:100%;border-collapse:collapse;margin-top:8px"><tr>';
                    foreach ($imgItems as $imgItem) {
                        if (is_array($imgItem)) {
                            $imgRel = $imgItem['url'] ?? null;
                            $cantidadDiseno = $imgItem['cantidad'] ?? null;
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
        }

    $mensaje .= '</div>'; // cierre del wrapper
    $mail->Body    = $mensaje;
        $mail->AltBody = strip_tags($mensaje);
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
