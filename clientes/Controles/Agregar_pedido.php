<?php
// procesar_pedido.php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

$alto = $_POST['alto'];
$IDmaterial = $_POST['material'];

// Validar y procesar cantidades
$cantidades_disenos = [];
if(isset($_POST['cantidades']) && is_array($_POST['cantidades'])) {
    foreach($_POST['cantidades'] as $index => $cantidad) {
        $cantidad_entero = intval($cantidad);
        if($cantidad_entero < 1) {
            $_SESSION['error'] = "Error: La cantidad para el diseño " . ($index + 1) . " debe ser al menos 1";
            header('Location:'.$URL.'clientes/Pedido.php');
            exit;
        }
        $cantidades_disenos[] = $cantidad_entero;
    }
} else {
    $_SESSION['error'] = "Error: Debe especificar las cantidades para cada diseño";
    header('Location:'.$URL.'clientes/Pedido.php');
    exit;
}

// La cantidad de diseños es automáticamente igual al número de imágenes
$cantidad_disenos = count($cantidades_disenos);

// SOLO el número de diseños para el campo Cantidades en pedidos
$detalles_cantidades = $cantidad_disenos;

// --- 4. VALIDACIÓN DE STOCK Y CÁLCULO DE PRECIO ---

// Preparamos una sola consulta para obtener el precio Y la cantidad disponible
$sentenciaMaterial = $db->prepare('SELECT Precio_CM, Cantidad FROM materiales WHERE CODIGO_Material = :CODIGO_Material');
$sentenciaMaterial->bindParam(':CODIGO_Material', $IDmaterial);
$sentenciaMaterial->execute();
$material_info = $sentenciaMaterial->fetch(PDO::FETCH_ASSOC);

if (!$material_info) {
    $_SESSION['error'] = "Error: El material seleccionado no existe.";
    header('Location:'.$URL.'clientes/Pedido.php');
    exit;
}

$PrecioCM = $material_info['Precio_CM'];
$cantidad_disponible = $material_info['Cantidad'];

// --- ¡LA VALIDACIÓN CLAVE! ---
if ($alto > $cantidad_disponible) {
    $_SESSION['error'] = "No hay suficiente material disponible. Solicitado: " . $alto . "cm Disponible: " . $cantidad_disponible . "cm";
    header('Location:'.$URL.'clientes/Pedido.php');
    exit;
}

// Si la validación pasa, calculamos el precio
$precio = $alto * $PrecioCM;

$imagenes = [];
$cantidad_imagenes = 0;

$sentencia = $db -> prepare('INSERT INTO pedidos (Cedula_Cliente, Material_Pedido, Centimetros, Cantidades, Costo, Fecha_Solicitud) 
                            VALUES (:Cedula_Cliente, :Material_Pedido, :Centimetros, :Cantidades, :Costo, :Fecha_Solicitud);');
$sentencia -> bindParam(':Cedula_Cliente', $CedulaSesion);
$sentencia -> bindParam(':Material_Pedido', $IDmaterial);
$sentencia -> bindParam(':Centimetros', $alto);
$sentencia -> bindParam(':Cantidades', $detalles_cantidades); // Solo el número de diseños
$sentencia -> bindParam(':Costo', $precio);
$sentencia -> bindParam(':Fecha_Solicitud', $fechaHora);
$sentencia -> execute();
$_SESSION['PedidoCreado'] = 'Pedido creado exitosamente';

$sentenciaMaterial = $db -> prepare('UPDATE materiales SET Cantidad = Cantidad-:Cantidad WHERE CODIGO_Material = :Material;');
$sentenciaMaterial -> bindParam(':Cantidad', $alto);
$sentenciaMaterial -> bindParam(':Material', $IDmaterial);
$sentenciaMaterial -> execute();


if(isset($_FILES['imagenes'])) {
    $cantidad_imagenes = count($_FILES['imagenes']['name']);
    
    // Validar que la cantidad de imágenes coincida con la cantidad de cantidades
    if($cantidad_imagenes !== count($cantidades_disenos)) {
        $_SESSION['error'] = "Error: La cantidad de imágenes no coincide con las cantidades especificadas";
        header('Location:'.$URL.'clientes/Pedido.php');
        exit;
    }
    
    $upload_dir = __DIR__ . '/../imagenes/';
    if(!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $sentenciaIDUltimoPedido = $db -> prepare('SELECT MAX(Id_pedido) FROM pedidos WHERE Cedula_Cliente = :Cedula_Cliente;');
    $sentenciaIDUltimoPedido -> bindParam(':Cedula_Cliente', $CedulaSesion);
    $sentenciaIDUltimoPedido -> execute();
    $ultimoIdPedido = $sentenciaIDUltimoPedido -> fetchColumn();
    
    for($i = 0; $i < $cantidad_imagenes; $i++) {
        if($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['imagenes']['type'][$i];
            if($file_type != 'image/png') {
                continue;
            }
            
            // Generar nombre único
            $file_ext = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);
            $file_name = $nombreSesion .'_'. $CedulaSesion .  '_' . $ultimoIdPedido .  '_' . $i . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            $url_relativa = 'clientes/imagenes/' . $file_name;
            
            // Almacenar información del diseño incluyendo la cantidad específica
            $cantidad_actual = $cantidades_disenos[$i];
            $SentenciaDiseño = $db->prepare('INSERT INTO disenos (Nombre_Diseno, URL_Diseno, Cantidad) VALUES (:Nombre_Diseno, :URL_Diseno, :Cantidad)');
            $SentenciaDiseño->bindParam(':Nombre_Diseno', $file_name);
            $SentenciaDiseño->bindParam(':URL_Diseno', $url_relativa);
            $SentenciaDiseño->bindParam(':Cantidad', $cantidad_actual);
            $SentenciaDiseño->execute();

            $sentenciaIDUltimoDiseno = $db -> prepare('SELECT MAX(ID_Diseno) FROM disenos;');
            $sentenciaIDUltimoDiseno -> execute();
            $ultimoIdDiseno = $sentenciaIDUltimoDiseno -> fetchColumn();

            $SentenciaPedidoDiseño =  $db -> prepare('INSERT INTO pedido_diseno (ID_Pedido, ID_Diseno) VALUES (:ID_Pedido, :ID_Diseno);');
            $SentenciaPedidoDiseño -> bindParam(':ID_Pedido', $ultimoIdPedido);
            $SentenciaPedidoDiseño -> bindParam(':ID_Diseno', $ultimoIdDiseno);
            $SentenciaPedidoDiseño -> execute();

            if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $file_path)) {
                $imagenes[] = $file_path; // Guardar ruta en array
            }
        }
    }
}

// Envío de correo al cliente con PHPMailer

// Obtener el correo del cliente desde la base de datos usando la cédula de sesión
$correoUsuario = null;
$sentenciaCorreo = $db->prepare('SELECT correo FROM usuarios WHERE cedula = :cedula');
$sentenciaCorreo->bindParam(':cedula', $CedulaSesion);
$sentenciaCorreo->execute();
$correoUsuario = $sentenciaCorreo->fetchColumn();

// Incluir función modularizada para enviar correo de pedido
require_once __DIR__ . '/../../email/pedido_cliente.php';
if ($correoUsuario) {
    enviarCorreoPedidoCliente($correoUsuario);
}

header('Location:'.$URL.'clientes/index.php');
exit;
?>