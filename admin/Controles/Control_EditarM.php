<?php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo   = $_POST['CODIGO_Material'];
    $nombre   = $_POST['Nombre'];
    $cantidad = $_POST['Cantidad'];
    $usuario  = $CedulaSesion; // Quien hace el cambio
    $precioCM = $_POST['Precio_CM'];
    
    // NUEVO: Recibimos el motivo
    $motivo   = $_POST['Motivo'];

    // Actualizamos también Fecha y Motivo
    $sentencia = $db->prepare('UPDATE materiales 
                                SET Nombre_material = :nombre, 
                                    Cantidad = :cantidad, 
                                    Precio_CM = :precioCM, 
                                    Ingresado_Modificado_por = :usuario,
                                    Fecha_Ultima_Modificacion = NOW(),
                                    Motivo_Cambio = :motivo
                                WHERE CODIGO_Material = :codigo');
                                
    $sentencia->bindParam(':nombre', $nombre);
    $sentencia->bindParam(':cantidad', $cantidad);
    $sentencia->bindParam(':precioCM', $precioCM); // Faltaba bindear el precio en tu código anterior
    $sentencia->bindParam(':usuario', $usuario);
    $sentencia->bindParam(':motivo', $motivo);
    $sentencia->bindParam(':codigo', $codigo);
    
    if ($sentencia->execute()) {
        $_SESSION['MaterialEditado'] = 'El material ha sido modificado exitosamente';
    } else {
        $_SESSION['Error'] = 'Error al modificar el material';
    }
    
    header("Location:" . $URL . "admin/Material.php");
    exit;
}
?>