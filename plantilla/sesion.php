<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tiempo_inactividad = 300; 

if (isset($_SESSION['ultima_actividad'])) {
    $tiempo_transcurrido = time() - $_SESSION['ultima_actividad'];
    if ($tiempo_transcurrido > $tiempo_inactividad) {
        session_unset();
        session_destroy();
        header("Location: " . $URL . "login.php?inactividad=1");
        exit();
    }
}
$_SESSION['ultima_actividad'] = time();

$nombreSesion = "Invitado";
$TipoSesion = 0; 

if(isset($_SESSION['incio_sesion'])){
    $correoUsuario = $_SESSION['incio_sesion'];
    if(isset($db)){
        $sentencia = $db -> prepare('SELECT Nombre, Apellido, Cedula, Tipo_Usuario FROM usuarios WHERE correo = :correo');
        $sentencia ->bindParam(':correo', $correoUsuario);
        $sentencia -> execute();
        $resultado = $sentencia -> fetch(PDO::FETCH_ASSOC);
        
        if($resultado){
            $nombreSesion = $resultado['Nombre'];
            $ApellidoSesion = $resultado['Apellido'];
            $CedulaSesion= $resultado['Cedula'];
            $TipoSesion = $resultado['Tipo_Usuario'];
        }
    }
}


$ruta_actual = $_SERVER['PHP_SELF'];

if (!function_exists('destruir_sesion_y_salir')) {
    function destruir_sesion_y_salir($url_login) {
        session_unset();     // Limpiar variables
        session_destroy();   // Destruir la sesión actual
        header("Location: " . $url_login); // Mandar al login
        exit();
    }
}
// 1. Protección de carpeta ADMIN (Solo Tipo 3)
if (strpos($ruta_actual, '/admin/') !== false) {
    if ($TipoSesion != 3) {
        destruir_sesion_y_salir($URL . "login.php");
    }
}

// 2. Protección de carpeta EMPLEADOS (Solo Tipo 2)
if (strpos($ruta_actual, '/empleados/') !== false) {
    if ($TipoSesion != 2) {
        destruir_sesion_y_salir($URL . "login.php");
    }
}

// 3. Protección de carpeta CLIENTES (Solo Tipo 1)
if (strpos($ruta_actual, '/clientes/') !== false) {
    if ($TipoSesion != 1) {
        destruir_sesion_y_salir($URL . "login.php");
    }
}
?>

<script>
const tiempoInactividad = 5 * 60 * 1000;

let tiempoInactivo = 0;
let contadorInterval;

function reiniciarContador() {
    tiempoInactivo = 0;
    clearInterval(contadorInterval);
    iniciarContador();
}

function iniciarContador() {
    contadorInterval = setInterval(() => {
        tiempoInactivo += 1000; 
        
        if (tiempoInactivo >= tiempoInactividad) {
            window.location.href = '<?php echo $URL ?>login.php?inactividad=1';
        }
    }, 1000);
}

document.addEventListener('mousemove', reiniciarContador);
document.addEventListener('keypress', reiniciarContador);
document.addEventListener('click', reiniciarContador);
document.addEventListener('scroll', reiniciarContador);
document.addEventListener('DOMContentLoaded', iniciarContador);
</script>