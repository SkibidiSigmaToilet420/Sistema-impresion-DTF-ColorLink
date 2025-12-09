<?php
// guardar_modificacion.php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

// --- 1. OBTENER DATOS DEL FORMULARIO ---
// ¡NUEVO! Obtenemos el ID del pedido que estamos editando
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

$alto = $_POST['alto'];
$IDmaterial = $_POST['material'];

$alto_Viejo = $_POST['alto_viejo'];
$id_material_viejo = $_POST['id_material_viejo'];

// Validación de ID de Pedido
if ($id_pedido === 0) {
    $_SESSION['error'] = "Error: ID de pedido no válido.";
    header('Location:' . $URL . 'clientes/mis_aprobaciones.php'); // Redirigir a la página de aprobaciones
    exit;
}

// --- 2. VALIDAR CANTIDADES (Igual que en Agregar_pedido.php) ---
$cantidades_disenos = [];
if (isset($_POST['cantidades']) && is_array($_POST['cantidades'])) {
    foreach ($_POST['cantidades'] as $index => $cantidad) {
        $cantidad_entero = intval($cantidad);
        if ($cantidad_entero < 1) {
            // Redirigir de vuelta a la página de modificación con error
            $_SESSION['error'] = "Error: La cantidad para el diseño " . ($index + 1) . " debe ser al menos 1";
            header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
            exit;
        }
        $cantidades_disenos[] = $cantidad_entero;
    }
} else {
    $_SESSION['error'] = "Error: Debe especificar las cantidades para cada diseño";
    header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
    exit;
}

$cantidad_disenos = count($cantidades_disenos);
$detalles_cantidades = $cantidad_disenos; // Solo el número de diseños

// --- 3. VALIDAR ARCHIVOS (Igual que en Agregar_pedido.php) ---
if (!isset($_FILES['imagenes']) || empty($_FILES['imagenes']['name'][0])) {
    $_SESSION['error'] = "Error: Debe subir los archivos de diseño nuevamente.";
    header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
    exit;
}

$cantidad_imagenes = count($_FILES['imagenes']['name']);

// Validar que la cantidad de imágenes coincida con la cantidad de cantidades
if ($cantidad_imagenes !== count($cantidades_disenos)) {
    $_SESSION['error'] = "Error: La cantidad de imágenes no coincide con las cantidades especificadas";
    header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
    exit;
}

// --- 4. CALCULAR NUEVO PRECIO (Igual que en Agregar_pedido.php) ---
$sentenciaPrecioCM =  $db->prepare('SELECT Precio_CM FROM materiales WHERE CODIGO_Material = :CODIGO_Material');
$sentenciaPrecioCM->bindParam(':CODIGO_Material', $IDmaterial);
$sentenciaPrecioCM->execute();
$PrecioCM = $sentenciaPrecioCM->fetch(PDO::FETCH_COLUMN);

$precio = $alto * $PrecioCM;

// --- 4.B. VALIDACIÓN DE STOCK PARA MODIFICACIÓN ---

// Primero, obtenemos el stock actual del material NUEVO
$sentenciaNuevoMaterial = $db->prepare('SELECT Cantidad FROM materiales WHERE CODIGO_Material = :IDmaterial');
$sentenciaNuevoMaterial->bindParam(':IDmaterial', $IDmaterial);
$sentenciaNuevoMaterial->execute();
$cantidad_disponible_nuevo = $sentenciaNuevoMaterial->fetchColumn();

// Calculamos el stock que realmente necesitamos
$stock_necesario = $alto;

// Si el material es el MISMO que el anterior...
if ($IDmaterial == $id_material_viejo) {
    // ...solo necesitamos la DIFERENCIA de stock.
    // (Restamos el 'alto_Viejo' porque se "devuelve" al inventario)
    $stock_necesario = $alto - $alto_Viejo;
}

// Si la diferencia es negativa (ej. cambió de 100cm a 50cm), no necesitamos stock.
if ($stock_necesario < 0) {
    $stock_necesario = 0;
}

// --- ¡LA VALIDACIÓN CLAVE! ---
if ($stock_necesario > $cantidad_disponible_nuevo) {
    
    $mensaje_error = "No hay suficiente material para esta modificación. Necesario: " . $stock_necesario . "cm adicionales Disponible: " . $cantidad_disponible_nuevo . "cm";
    
    // Si los materiales eran diferentes, el mensaje es más simple
    if ($IDmaterial != $id_material_viejo) {
         $mensaje_error = "No hay suficiente material disponible. Solicitado: " . $alto . "cm Disponible: " . $cantidad_disponible_nuevo . "cm";
    }

    $_SESSION['error'] = $mensaje_error;
    header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
    exit;
}

// --- 5. LÓGICA DE ACTUALIZACIÓN (Todo esto es NUEVO) ---
// Directorio de imágenes. Debe ser la misma ruta que en Agregar_pedido.php
$upload_dir = __DIR__ . '/../imagenes/';

// NUEVO: Aseguramos que el directorio exista. Si no existe, lo crea.
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        // Lanzar una excepción si falla la creación del directorio
        $_SESSION['error'] = "Error: No se pudo crear el directorio de subida de imágenes. Verifique permisos (0755).";
        header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
        exit;
    }
}

try {
    // Iniciamos una transacción para asegurar que todo se haga o nada se haga
    $db->beginTransaction();
    // --- 5.A. OBTENER Y BORRAR DISEÑOS ANTIGUOS ---

    // 1. Encontrar todos los diseños y sus URLs asociados a este pedido
    $sqlGetDisenos = "SELECT d.ID_Diseno, d.URL_Diseno 
                      FROM disenos d 
                      JOIN pedido_diseno pd ON d.ID_Diseno = pd.ID_Diseno 
                      WHERE pd.ID_Pedido = :id_pedido";
    $sentenciaGetDisenos = $db->prepare($sqlGetDisenos);
    $sentenciaGetDisenos->bindParam(':id_pedido', $id_pedido);
    $sentenciaGetDisenos->execute();
    $disenosAntiguos = $sentenciaGetDisenos->fetchAll(PDO::FETCH_ASSOC);

    // 2. Borrar las relaciones en la tabla pivote
    $sentenciaDelPedDis = $db->prepare("DELETE FROM pedido_diseno WHERE ID_Pedido = :id_pedido");
    $sentenciaDelPedDis->bindParam(':id_pedido', $id_pedido);
    $sentenciaDelPedDis->execute();

    // 3. Borrar los registros de diseños
    if (!empty($disenosAntiguos)) {
        $sentenciaDelDis = $db->prepare("DELETE FROM disenos WHERE ID_Diseno = :id_diseno");
        foreach ($disenosAntiguos as $diseno) {
            $sentenciaDelDis->bindParam(':id_diseno', $diseno['ID_Diseno']);
            $sentenciaDelDis->execute();
        }
    }


    $sqlMaterialViejo = "UPDATE materiales SET Cantidad = Cantidad + :alto_viejo WHERE CODIGO_Material = :id_material_viejo";
    $sqlMaterialViejoStmt = $db->prepare($sqlMaterialViejo);
    $sqlMaterialViejoStmt->bindParam(':alto_viejo', $alto_Viejo);
    $sqlMaterialViejoStmt->bindParam(':id_material_viejo', $id_material_viejo);
    $sqlMaterialViejoStmt->execute();
    // 4. Ajustar el inventario del material (devolviendo el alto viejo)

    // --- 5.B. ACTUALIZAR EL PEDIDO PRINCIPAL ---
    // Aquí actualizamos el pedido con la nueva info y lo regresamos a 'Pendiente'
    $sql_update_pedido = "UPDATE pedidos SET 
                              Material_Pedido = :Material_Pedido, 
                              Centimetros = :Centimetros, 
                              Cantidades = :Cantidades, 
                              Costo = :Costo, 
                              Fecha_Solicitud = :Fecha_Solicitud,
                              Estado_Pedido = 'Pendiente' -- ¡CLAVE! Se regresa a revisión
                          WHERE 
                              Id_pedido = :id_pedido 
                          AND 
                              Cedula_Cliente = :Cedula_Cliente";

    $sentenciaUpdate = $db->prepare($sql_update_pedido);
    $sentenciaUpdate->bindParam(':Material_Pedido', $IDmaterial);
    $sentenciaUpdate->bindParam(':Centimetros', $alto);
    $sentenciaUpdate->bindParam(':Cantidades', $detalles_cantidades);
    $sentenciaUpdate->bindParam(':Costo', $precio);
    $sentenciaUpdate->bindParam(':Fecha_Solicitud', $fechaHora); // Actualiza la fecha a la de la modificación
    $sentenciaUpdate->bindParam(':id_pedido', $id_pedido);
    $sentenciaUpdate->bindParam(':Cedula_Cliente', $CedulaSesion);
    $sentenciaUpdate->execute();

    $sentenciaMaterialNuevo = $db->prepare("UPDATE materiales SET Cantidad = Cantidad - :alto WHERE CODIGO_Material = :IDmaterial");
    $sentenciaMaterialNuevo->bindParam(':alto', $alto);
    $sentenciaMaterialNuevo->bindParam(':IDmaterial', $IDmaterial);
    $sentenciaMaterialNuevo->execute();


    // --- 5.C. AGREGAR LOS NUEVOS DISEÑOS (Lógica de 'Agregar_pedido.php' modificada) ---
    // Usamos $id_pedido (que ya tenemos) en lugar de buscar el MAX()

    for ($i = 0; $i < $cantidad_imagenes; $i++) {
        if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['imagenes']['type'][$i];
            if ($file_type != 'image/png') {
                continue; // Omitir si no es PNG
            }

            // 1. Generar nombre y ruta
            $file_ext = pathinfo($_FILES['imagenes']['name'][$i], PATHINFO_EXTENSION);

            $unique_id = uniqid('', true); //

            // Nuevo Nombre: Cliente_Cedula_IDPedido_UniqueID.ext
            $file_name = $nombreSesion . '_' . $CedulaSesion .  '_' . $id_pedido .  '_' . $unique_id . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            $url_relativa = 'clientes/imagenes/' . $file_name; // URL para la BD

            // 2. Mover el archivo subido PRIMERO. Si falla, lanzamos excepción.
            if (!move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $file_path)) {
                // Si mover falla, revertimos toda la DB (el borrado anterior y el UPDATE)
                throw new Exception("Error crítico al mover el archivo de diseño N° " . ($i + 1) . ". Verifique los permisos de escritura en el servidor.");
            }

            // --- 3. INSERCIÓN EN BASE DE DATOS (Solo si la subida fue exitosa) ---

            $cantidad_actual = $cantidades_disenos[$i];
            $SentenciaDiseño = $db->prepare('INSERT INTO disenos (Nombre_Diseno, URL_Diseno, Cantidad) VALUES (:Nombre_Diseno, :URL_Diseno, :Cantidad)');
            $SentenciaDiseño->bindParam(':Nombre_Diseno', $file_name);
            $SentenciaDiseño->bindParam(':URL_Diseno', $url_relativa);
            $SentenciaDiseño->bindParam(':Cantidad', $cantidad_actual);
            $SentenciaDiseño->execute();

            // Obtener el ID del diseño que acabamos de insertar
            $ultimoIdDiseno = $db->lastInsertId(); // Usamos lastInsertId() de forma segura

            // Vincular el nuevo diseño al pedido existente
            $SentenciaPedidoDiseño =  $db->prepare('INSERT INTO pedido_diseno (ID_Pedido, ID_Diseno) VALUES (:ID_Pedido, :ID_Diseno);');
            $SentenciaPedidoDiseño->bindParam(':ID_Pedido', $id_pedido);
            $SentenciaPedidoDiseño->bindParam(':ID_Diseno', $ultimoIdDiseno);
            $SentenciaPedidoDiseño->execute();
        }
    }

    // --- 5.D. BORRAR FÍSICAMENTE LOS ARCHIVOS ANTIGUOS ---
    // Lo hacemos DESPUÉS de que toda la BD se actualizó y los nuevos archivos se movieron.
    foreach ($disenosAntiguos as $diseno) {
        // La URL es 'clientes/imagenes/archivo.png', necesitamos la ruta del servidor
        $rutaArchivoAntiguo = $upload_dir . basename($diseno['URL_Diseno']);
        if (file_exists($rutaArchivoAntiguo)) {
            unlink($rutaArchivoAntiguo);
        }
    }

    // Si todo salió bien, confirmamos la transacción
    $db->commit();

    // --- 6. NOTIFICACIÓN (Igual que en Agregar_pedido.php) ---

    // (Opcional: podrías querer crear un correo diferente "Su pedido fue modificado")
    // Por ahora, re-enviamos la confirmación estándar
    $correoUsuario = null;
    $sentenciaCorreo = $db->prepare('SELECT correo FROM usuarios WHERE cedula = :cedula');
    $sentenciaCorreo->bindParam(':cedula', $CedulaSesion);
    $sentenciaCorreo->execute();
    $correoUsuario = $sentenciaCorreo->fetchColumn();

    require_once __DIR__ . '/../../email/pedido_cliente.php';
    if ($correoUsuario) {
        enviarCorreoPedidoCliente($correoUsuario); // Reutilizamos el correo de confirmación
    }

    // --- 7. REDIRECCIÓN ---
    $_SESSION['PedidoCreado'] = 'Pedido modificado exitosamente. Ha sido enviado a revisión.';
    header('Location:' . $URL . 'clientes/index.php'); // Redirigir al inicio del cliente
    exit;
} catch (Exception $e) {
    // --- MANEJO DE ERRORES ---
    $db->rollBack(); // Revertir todos los cambios de la BD

    // (Opcional: borrar los archivos nuevos que SÍ se hayan subido antes del error)

    $_SESSION['error'] = "Error fatal al modificar el pedido: " . $e->getMessage();
    header('Location:' . $URL . 'clientes/modificar_pedido.php?id=' . $id_pedido);
    exit;
}
