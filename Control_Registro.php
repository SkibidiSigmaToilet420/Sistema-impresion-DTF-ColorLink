<?php 
include ('model/conexion.php');
include ('plantilla/sesion.php');

// 1. RECIBIMOS LOS DATOS NUEVOS
$tipo_doc = $_POST['tipo_documento']; // V, J, E
$numero_cedula = $_POST['cedula'];    // El número

// 2. CONCATENAMOS PARA CREAR LA CÉDULA COMPLETA (Ej: V-12345678)
$cedula_completa = $tipo_doc . "-" . $numero_cedula;

$Nombre = $_POST['nombre'];
$Apellido = $_POST['apellido'];
$Correo = strtolower($_POST['correo']);
$tipoUsuario = $_POST['tipoUsuario'];
$Telefono = isset($_POST['TLF']) && $_POST['TLF'] !== '' ? $_POST['TLF'] : null;
$pass = $_POST['pass'];
$repass = $_POST['repass'];

// 3. RECIBIMOS LA DIRECCIÓN (Si no la envían, queda NULL)
$Direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;

$fechaRegistro = $fechaHora;

if($pass != $repass){
    session_start();
    $_SESSION['ContraseñaDiferente']="Las contraseñas ingresadas no son iguales";
    header("Location:".$URL."registro.php");
    exit(); // IMPORTANTE: Usar exit después de header
}

// VERIFICAMOS SI LA CÉDULA COMPLETA YA EXISTE
$confirmacionCedula = $db->prepare("SELECT * FROM usuarios WHERE Cedula = :Cedula");
$confirmacionCedula->bindParam(':Cedula', $cedula_completa); // Usamos la variable concatenada
$confirmacionCedula->execute();

if ($confirmacionCedula->rowCount() > 0){
    session_start();
    $_SESSION['cedula_duplicada'] = 'Este documento de identidad ya está registrado.';
    header("Location:".$URL."registro.php");
    exit();
}

// VERIFICAMOS EL CORREO
$confirmacion = $db->prepare("SELECT * FROM usuarios WHERE Correo = :Correo");
$confirmacion->bindParam(':Correo', $Correo);
$confirmacion->execute();

if ($confirmacion->rowCount() > 0){
    session_start();
    $_SESSION['correo_duplicado'] = 'El correo ya está registrado, por favor ingrese otro.';
    header("Location:".$URL."registro.php");
    exit();
}

// SI TODO ESTÁ BIEN, REGISTRAMOS
$pass = password_hash($pass, PASSWORD_DEFAULT);

// Insertamos en la tabla USUARIOS (La general)
$sentencia = $db->prepare('INSERT INTO usuarios (Cedula, Correo, Password_usuario, Nombre, Apellido, NumeroTLF, Fecha_Registro, Tipo_Usuario)
                            VALUES (:Cedula, :Correo, :Password_usuario, :Nombre, :Apellido, :NumeroTLF, :Fecha_Registro, :Tipo_Usuario);');
$sentencia->bindParam(':Cedula', $cedula_completa); // OJO: Variable concatenada
$sentencia->bindParam(':Correo', $Correo); 
$sentencia->bindParam(':Password_usuario', $pass); 
$sentencia->bindParam(':Nombre', $Nombre);
$sentencia->bindParam(':Apellido', $Apellido);      
$sentencia->bindParam(':NumeroTLF', $Telefono);
$sentencia->bindParam(':Fecha_Registro', $fechaHora);      
$sentencia->bindParam(':Tipo_Usuario', $tipoUsuario);
$sentencia->execute();

// Insertamos en las tablas específicas según el ROL
switch($tipoUsuario){
    case 1: // CLIENTE
        // AQUÍ AGREGAMOS LA DIRECCIÓN
        $sentenciaCliente = $db->prepare('INSERT INTO clientes (Cedula_Cliente, Direccion) VALUES (:Cedula_Cliente, :Direccion);');
        $sentenciaCliente->bindParam(':Cedula_Cliente', $cedula_completa);
        $sentenciaCliente->bindParam(':Direccion', $Direccion); // Guardamos la dirección
        $sentenciaCliente->execute();
        break;
        
    case 2: // EMPLEADO
        $sentenciaEmpleado = $db->prepare('INSERT INTO empleados (Cedula_Empleado, Cedula_Administrador) VALUES (:Cedula_Empleado, :Cedula_Administrador);');
        $sentenciaEmpleado->bindParam(':Cedula_Empleado', $cedula_completa);
        $sentenciaEmpleado->bindParam(':Cedula_Administrador', $CedulaSesion); // Asegúrate que $CedulaSesion venga del include session
        $sentenciaEmpleado->execute();
        break;
        
    case 3: // ADMINISTRADOR
        $sentenciaAdministrador = $db->prepare('INSERT INTO administradores (Cedula_Administrador) VALUES (:Cedula_Administrador);');
        $sentenciaAdministrador->bindParam(':Cedula_Administrador', $cedula_completa);
        $sentenciaAdministrador->execute();
        break;
}

// ENVIAR CORREO Y FINALIZAR
require_once __DIR__ . '/email/bienvenida_usuario.php';

try {
    enviarCorreoBienvenida($Correo, $Nombre); 
} catch (Exception $e) {
    error_log("Fallo al ENVIAR correo de bienvenida: " . $e->getMessage());
}

session_start();
$_SESSION['UsuarioRegistrado'] = "El usuario ha sido registrado con éxito!";
header("Location:".$URL."login.php"); 
exit();
?>