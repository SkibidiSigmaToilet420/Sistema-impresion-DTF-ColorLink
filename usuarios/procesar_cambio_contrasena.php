<?php
include '../model/conexion.php';
include '../plantilla/sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contrasenaActual = $_POST['contrasena_actual'];
    $nuevaContrasena = $_POST['nueva_contrasena'];
    $confirmarContrasena = $_POST['confirmar_contrasena'];

    // Verificar contraseña actual
    $sentencia = $db->prepare("SELECT Password_usuario FROM usuarios WHERE Correo = :correo");
    $sentencia->bindParam(':correo', $_SESSION['incio_sesion']);
    $sentencia->execute();
    $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($contrasenaActual, $usuario['Password_usuario'])) {
        header("Location: perfil.php?cambio_clave=error_actual");
        exit;
    }

    if ($nuevaContrasena !== $confirmarContrasena) {
        header("Location: perfil.php?cambio_clave=error_coincidencia");
        exit;
    }

    // No se aplica mínimo ni máximo de longitud: aceptar cualquier cantidad de dígitos/caracteres

    // Actualizar contraseña
    $hashContrasena = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
    $sentencia = $db->prepare("UPDATE usuarios SET Password_usuario = :pass WHERE Correo = :correo");
    $sentencia->bindParam(':pass', $hashContrasena);
    $sentencia->bindParam(':correo', $_SESSION['incio_sesion']);
    $sentencia->execute();

    header("Location: perfil.php?cambio_clave=ok");
    exit;
}

header("Location: perfil.php");
exit;
?>