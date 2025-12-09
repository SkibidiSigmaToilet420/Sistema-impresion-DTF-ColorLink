<?php
// procesar_pedido.php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

$alto = $_POST['alto'];
$Detalles = isset($_POST['notas']) ? trim($_POST['notas']) : '';
$IDmaterial = $_POST['material'];


$sentenciaPrecioCM =  $db -> prepare('SELECT Precio_CM FROM materiales WHERE CODIGO_Material = :CODIGO_Material');
$sentenciaPrecioCM -> bindParam(':CODIGO_Material', $IDmaterial);
$sentenciaPrecioCM -> execute();
$PrecioCM = $sentenciaPrecioCM -> fetch(PDO::FETCH_COLUMN);
echo $PrecioCM;
$precio = $alto * $PrecioCM;

$imagenes = [];
$cantidad_imagenes = 0;

$sentencia = $db -> prepare('INSERT INTO pedidos (Cedula_Cliente, Material_Pedido, Centimetros, Detalles, Costo, Fecha_Solicitud) 
                            VALUES (:Cedula_Cliente, :Material_Pedido, :Centimetros, :Detalles, :Costo, :Fecha_Solicitud);');
$sentencia -> bindParam(':Cedula_Cliente', $CedulaSesion);
$sentencia -> bindParam(':Material_Pedido', $IDmaterial);
$sentencia -> bindParam(':Centimetros', $alto);
$sentencia -> bindParam(':Detalles', $Detalles);
$sentencia -> bindParam(':Costo', $precio);
$sentencia -> bindParam(':Fecha_Solicitud', $fechaHora);
$sentencia -> execute();
$_SESSION['PedidoCreado'] = 'Pedido creado exitosamente';




if(isset($_FILES['imagenes'])) {
    $cantidad_imagenes = count($_FILES['imagenes']['name']);
    
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
            
            $SentenciaDiseño = $db->prepare('INSERT INTO disenos (Nombre_Diseno, URL_Diseno) VALUES (:Nombre_Diseno, :URL_Diseno)');
            $SentenciaDiseño->bindParam(':Nombre_Diseno', $file_name);
            $SentenciaDiseño->bindParam(':URL_Diseno', $url_relativa);
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

            header('Location:'.$URL.'clientes/index.php');

        }
    }
}



//echo "Alto: ".$alto." Precio: ".$precio." Cantidad de imagenes: ".$cantidad_imagenes." Detalles: ".$Detalles." Precio calculado: ".$precioCm;






