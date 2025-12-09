<?php
include('../../model/conexion.php');
include('../../plantilla/sesion.php');

if (isset($_GET['Codigo'])) {
    $codigo = $_GET['Codigo'];
    echo $codigo;


    $sentencia = $db->prepare("UPDATE materiales SET Estado_Material = 'INACTIVO' WHERE materiales.CODIGO_Material = :codigo;");
    $sentencia->bindParam(':codigo', $codigo);
    $sentencia->execute();

    if ($sentencia->execute()) {
        $_SESSION['MaterialEliminado'] = "Material desactivado correctamente";
        header("Location: ../Material.php");
        exit;
    } else {
        $_SESSION['MaterialError'] = "Error al desactivar el material";
        header("Location:".$URL."admin/Material.php");
        exit;
    }
}
?>
?>