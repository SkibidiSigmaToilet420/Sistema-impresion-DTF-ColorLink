<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración del tiempo de expiración (en segundos)
$tiempo_inactividad = 300; 

// Verificar si existe el tiempo de última actividad
if (isset($_SESSION['ultima_actividad'])) {
    // Calcular el tiempo transcurrido
    $tiempo_transcurrido = time() - $_SESSION['ultima_actividad'];
    
    // Si ha pasado más tiempo del permitido, cerrar sesión
    if ($tiempo_transcurrido > $tiempo_inactividad) {
        // PRIMERO guardar el mensaje en la sesión
        
        // LUEGO destruir la sesión y redirigir
        session_unset();     // Eliminar todas las variables de sesión
        session_destroy();   // Destruir la sesión
        
        // Redirigir al login
        header("Location: " . $URL . "login.php?inactividad=1");
        exit();
    }
}

// Actualizar el tiempo de última actividad
$_SESSION['ultima_actividad'] = time();

$nombreSesion =  "Invitado";
if(isset($_SESSION['incio_sesion'])){
    $correoUsuario = $_SESSION['incio_sesion'];
    $sentencia = $db -> prepare('SELECT Nombre, Apellido, Cedula, Tipo_Usuario FROM usuarios WHERE correo = :correo');
    $sentencia ->bindParam(':correo', $correoUsuario);
    $sentencia -> execute();
    $resultado = $sentencia -> fetch(PDO::FETCH_ASSOC);
    $cedulaSesionData= $resultado['Cedula'];
}
?>