<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
// Ajusta las rutas de inclusión según tu estructura real en el servidor
include '../../model/conexion.php';
include '../../admin/Controles/mail_config.php';

$Codigo = isset($_POST['Codigo']) ? intval($_POST['Codigo']) : 0;
$fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : ''; // Esta es la NUEVA fecha
$motivo = isset($_POST['motivo']) ? trim($_POST['motivo']) : '';

if ($Codigo <= 0 || $fecha === '' || $motivo === '') {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

// Validate date server-side
$today = date('Y-m-d');
if ($fecha < $today) {
    echo json_encode(['success' => false, 'message' => 'La fecha no puede ser menor a la fecha actual.']);
    exit;
}

try {
    // ---------------------------------------------------------
    // 1. OBTENER LA FECHA ANTERIOR (ANTES DE ACTUALIZAR)
    // ---------------------------------------------------------
    $stmtOld = $db->prepare("SELECT Fecha_Entrega FROM pedidos WHERE Id_pedido = :id LIMIT 1");
    $stmtOld->bindValue(':id', $Codigo, PDO::PARAM_INT);
    $stmtOld->execute();
    $datoPrevio = $stmtOld->fetch(PDO::FETCH_ASSOC);
    
    // Si no hay fecha previa, ponemos 'No definida'
    $fechaAnterior = ($datoPrevio && !empty($datoPrevio['Fecha_Entrega'])) ? $datoPrevio['Fecha_Entrega'] : 'No definida';


    // ---------------------------------------------------------
    // 2. ACTUALIZAR LA BASE DE DATOS CON LA NUEVA FECHA
    // ---------------------------------------------------------
    $sentenciapostergarentrega = $db->prepare("UPDATE pedidos SET Fecha_Entrega = :fecha WHERE pedidos.Id_pedido = :Codigo;");
    $sentenciapostergarentrega->bindParam(':fecha', $fecha);
    $sentenciapostergarentrega->bindParam(':Codigo', $Codigo);
    
    if (!$sentenciapostergarentrega->execute()) {
        throw new Exception("Error al actualizar la fecha en la base de datos.");
    }

    // ---------------------------------------------------------
    // 3. OBTENER DATOS DEL CLIENTE PARA EL CORREO
    // ---------------------------------------------------------
    $stmt = $db->prepare("SELECT u.Correo, u.Nombre, u.Apellido FROM pedidos p JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :id LIMIT 1");
    $stmt->bindValue(':id', $Codigo, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !filter_var($user['Correo'], FILTER_VALIDATE_EMAIL)) {
        // Si no hay correo válido, devolvemos éxito parcial (se actualizó la fecha pero no se envió correo)
        echo json_encode(['success' => true, 'message' => 'Fecha actualizada, pero no se encontró correo válido para notificar.']);
        exit;
    }

    $correo = $user['Correo'];
    $nombre = $user['Nombre'] . ' ' . $user['Apellido'];

    // ---------------------------------------------------------
    // 4. PREPARAR Y ENVIAR CORREO
    // ---------------------------------------------------------
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        // ... (Configuración SMTP igual que antes) ...
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'colorlink609@gmail.com';
        $mail->Password   = 'pcpo wunj vuhc uzrz';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom('colorlink609@gmail.com', 'ColorLink');
        $mail->addAddress($correo, $nombre);
        $mail->isHTML(true);
        $mail->Subject = "Notificación sobre su pedido #" . htmlspecialchars($Codigo) . " - Pospuesto";

        // Obtener detalles adicionales (Costos, cantidades, etc.)
        $detalle = [];
        $stmt2 = $db->prepare("SELECT p.Id_pedido, p.Cantidades, p.Centimetros, p.Costo, p.Estado_Pedido, CONCAT(u.Nombre,' ',u.Apellido) as NombreCliente FROM pedidos p JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :id LIMIT 1");
        $stmt2->bindValue(':id', $Codigo, PDO::PARAM_INT);
        $stmt2->execute();
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($row) { $detalle = $row; }

        // Obtener imágenes
        $stmtImg = $db->prepare("SELECT d.URL_Diseno AS URL_Diseno, d.Cantidad AS Cantidad FROM disenos d JOIN pedido_diseno pd ON d.ID_Diseno = pd.id_Diseno WHERE pd.id_pedido = :id LIMIT 6");
        $stmtImg->bindValue(':id', $Codigo, PDO::PARAM_INT);
        $stmtImg->execute();
        $imagenes = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($imagenes)) { $detalle['Imagenes'] = $imagenes; }

        // Construcción del HTML del mensaje
        $mensaje = '';
        $mensaje .= '<div style="font-family: Arial, Helvetica, sans-serif; color: #222; background: #f6f7f9; padding: 18px;">';
        $mensaje .= '<p style="margin:0 0 12px; font-size:15px; color:#333;">Hola <strong>' . htmlspecialchars($nombre) . '</strong>,</p>';
        $mensaje .= '<p style="margin:0 0 12px; font-size:15px; color:#333;">Le informamos que la fecha de entrega de su pedido ha sido modificada por el siguiente motivo:</p>';
        
        $mensaje .= '<div style="background:#fff7f8;border-radius:6px;padding:10px;margin:10px 0;border:1px solid #f1c0c4">';
        $mensaje .= '<p style="margin:0;color:#b02a37;font-size:14px"><strong>Motivo:</strong><br>' . nl2br(htmlspecialchars($motivo)) . '</p>';
        $mensaje .= '</div>';
        
        $mensaje .= '<div style="background:#ffffff;border-radius:8px;padding:12px;margin:12px 0;border:1px solid #e1e4ea">';
        $mensaje .= '<h4 style="margin:0 0 8px;color:#1f3b73;font-size:16px">Numero del pedido: ' . htmlspecialchars($Codigo) . '</h4>';
        
        // --- AQUÍ ESTÁ EL CAMBIO SOLICITADO ---
        $mensaje .= '<div style="font-size:14px;color:#444;margin-bottom:8px">';
        
        // Mostrar Fecha Anterior (La que obtuvimos al principio)
        $mensaje .= '<div style="margin-bottom: 4px;"><strong>Fecha de entrega anterior:</strong> <span style="color: #777;">' . htmlspecialchars($fechaAnterior) . '</span></div>';
        
        // Mostrar Nueva Fecha (La variable $fecha que viene del formulario)
        $mensaje .= '<div style="color: #198754; font-size: 15px;"><strong>Nueva fecha de entrega:</strong> ' . htmlspecialchars($fecha) . '</div>';
        
        if (!empty($detalle['Cantidades'])) $mensaje .= '<div style="margin-top:8px;"><strong>Numero de impresiones:</strong> ' . htmlspecialchars($detalle['Cantidades']) . '</div>';
        if (!empty($detalle['Centimetros'])) $mensaje .= '<div><strong>Centímetros:</strong> ' . htmlspecialchars($detalle['Centimetros']) . '</div>';
        if (!empty($detalle['Costo'])) $mensaje .= '<div><strong>Total Bs:</strong> ' . htmlspecialchars($detalle['Costo']) . '</div>';
        if (!empty($detalle['Estado_Pedido'])) $mensaje .= '<div><strong>Estado:</strong> ' . htmlspecialchars($detalle['Estado_Pedido']) . '</div>';
        $mensaje .= '</div>';
        // --------------------------------------

        if (!empty($detalle['Imagenes']) && is_array($detalle['Imagenes'])) {
            $mensaje .= '<table role="presentation" style="width:100%;border-collapse:collapse;margin-top:8px"><tr>';
            foreach ($detalle['Imagenes'] as $imgItem) {
                $imgRel = $imgItem['URL_Diseno'] ?? null;
                $cantidadDiseno = $imgItem['Cantidad'] ?? null;
                if (!$imgRel) continue;
                $localPath = realpath(__DIR__ . '/../../' . $imgRel);
                $cid = null;
                if ($localPath && file_exists($localPath)) {
                    $cid = 'img' . uniqid();
                    try { $mail->addEmbeddedImage($localPath, $cid); } catch (Exception $e) { $cid = null; }
                }
                if ($cid) {
                    $imgTag = '<img src="cid:' . $cid . '" style="max-width:180px;width:100%;height:auto;border:1px solid #ddd;padding:4px;border-radius:4px" alt="Diseño">';
                } else {
                    global $URL;
                    $imgUrl = (isset($URL) ? $URL : '') . $imgRel;
                    $imgTag = '<img src="' . htmlspecialchars($imgUrl) . '" style="max-width:180px;width:100%;height:auto;border:1px solid #ddd;padding:4px;border-radius:4px" alt="Diseño">';
                }
                $mensaje .= '<td style="padding:6px;vertical-align:top;text-align:center;width:200px">';
                if ($cantidadDiseno !== null) $mensaje .= '<div style="font-size:13px;color:#333;margin-bottom:6px"><strong>Impresiones</strong><br>' . htmlspecialchars($cantidadDiseno) . '</div>';
                $mensaje .= $imgTag;
                $mensaje .= '</td>';
            }
            $mensaje .= '</tr></table>';
        }

        $mensaje .= '</div>'; // wrapper

        $mail->Body = $mensaje;
        $mail->AltBody = strip_tags($mensaje);
        $mail->send();
        
        echo json_encode(['success' => true, 'message' => 'Fecha actualizada y correo enviado correctamente.']);
        exit;

    } catch (Exception $e) {
        // Si falla el correo, pero la BD ya se actualizó, informamos éxito con advertencia
        echo json_encode(['success' => true, 'message' => 'Fecha actualizada, pero hubo un error al enviar el correo: ' . $mail->ErrorInfo]);
        exit;
    }

} catch (Exception $ex) {
    echo json_encode(['success' => false, 'message' => 'Error en el servidor: ' . $ex->getMessage()]);
    exit;
}
?>