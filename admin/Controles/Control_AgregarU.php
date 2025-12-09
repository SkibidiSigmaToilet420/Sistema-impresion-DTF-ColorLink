<?php 
include ('../../model/conexion.php');
include ('../../plantilla/sesion.php');

// 1. RECIBIR Y CONCATENAR CÉDULA
$tipo_doc = $_POST['tipo_documento'];
$numero_cedula = $_POST['cedula'];
$cedula_completa = $tipo_doc . "-" . $numero_cedula;

$Nombre = $_POST['nombre'];
$Apellido = $_POST['apellido'];
$Correo = $_POST['correo'];
$tipoUsuario = $_POST['tipoUsuario'];
$Telefono = isset($_POST['TLF']) && $_POST['TLF'] !== '' ? $_POST['TLF'] : null;

$Direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null; // Recibir dirección

$pass = $_POST['pass'];
$repass = $_POST['repass'];
$fechaRegistro = $fechaHora;

// Validar passwords
if($pass != $repass){
    $_SESSION['ContraseñaDiferente']="Las contraseñas ingresadas no son iguales";
    header("Location:".$URL."admin/AgregarUsuarios.php");
    exit();
}

// Validar Cédula Duplicada (Usando la variable concatenada)
$confirmacionCedula = $db->prepare("SELECT * FROM usuarios WHERE Cedula = :Cedula");
$confirmacionCedula->bindParam(':Cedula', $cedula_completa);
$confirmacionCedula->execute();

if ($confirmacionCedula->rowCount()>0){
    $_SESSION['cedula_duplicada'] = 'Esta cedula ya está registrada.';
    header("Location:".$URL."admin/AgregarUsuarios.php");
    exit();
}

// Validar Correo
$confirmacion = $db->prepare("SELECT * FROM usuarios WHERE Correo = :Correo");
$confirmacion->bindParam(':Correo', $Correo);
$confirmacion->execute();

if ($confirmacion->rowCount()>0){
    $_SESSION['correo_duplicado'] = 'El correo ya está registrado.';
    header("Location:".$URL."admin/AgregarUsuarios.php");
    exit();
}

// INSERTAR
$pass = password_hash($pass, PASSWORD_DEFAULT);
$sentencia = $db->prepare('INSERT INTO usuarios (Cedula, Correo, Password_usuario, Nombre, Apellido, NumeroTLF, Fecha_Registro, Tipo_Usuario) VALUES (:Cedula, :Correo, :Password_usuario, :Nombre, :Apellido, :NumeroTLF, :Fecha_Registro, :Tipo_Usuario);');
$sentencia->bindParam(':Cedula', $cedula_completa); // Usar completa
$sentencia->bindParam(':Correo', $Correo); 
$sentencia->bindParam(':Password_usuario', $pass); 
$sentencia->bindParam(':Nombre', $Nombre);
$sentencia->bindParam(':Apellido', $Apellido);      
$sentencia->bindParam(':NumeroTLF', $Telefono);
$sentencia->bindParam(':Fecha_Registro', $fechaHora);      
$sentencia->bindParam(':Tipo_Usuario', $tipoUsuario);
$sentencia->execute();

// TABLAS ESPECÍFICAS
switch($tipoUsuario){
    case 1: // Cliente
        // Aquí guardamos la dirección
        $sentenciaCliente =  $db->prepare('INSERT INTO clientes (Cedula_Cliente, Direccion) VALUES (:Cedula_Cliente, :Direccion);');
        $sentenciaCliente->bindParam(':Cedula_Cliente', $cedula_completa);
        $sentenciaCliente->bindParam(':Direccion', $Direccion);
        $sentenciaCliente->execute();
        break;
    case 2: // Empleado
        $sentenciaEmpleado =  $db->prepare('INSERT INTO empleados (Cedula_Empleado, Cedula_Administrador) VALUES (:Cedula_Empleado, :Cedula_Administrador);');
        $sentenciaEmpleado->bindParam(':Cedula_Empleado', $cedula_completa);
        $sentenciaEmpleado->bindParam(':Cedula_Administrador', $CedulaSesion);
        $sentenciaEmpleado->execute();
        break;
    case 3: // Admin
        $sentenciaAdministrador =  $db->prepare('INSERT INTO administradores (Cedula_Administrador) VALUES (:Cedula_Administrador);');
        $sentenciaAdministrador->bindParam(':Cedula_Administrador', $cedula_completa);
        $sentenciaAdministrador->execute();
        break;
}

$_SESSION['UsuarioRegistrado']="El usuario ha sido registrado con exito!";
header("Location:".$URL."admin/usuarios.php"); 
?>