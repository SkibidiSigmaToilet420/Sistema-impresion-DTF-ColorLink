<?php 
include ('../../model/conexion.php');
include ('../../plantilla/sesion.php');

$Cedula = $_POST['cedula'];
$Nombre = $_POST['nombre'];
$Apellido = $_POST['apellido'];
$Correo = $_POST['correo'];
$tipoUsuario = $_POST['tipoUsuario'];
$Telefono = $_POST['TLF'];
$pass = $_POST['pass'];
$repass = $_POST['repass'];
$fechaEditar = $fechaHora;

$validarContraseña = $db -> prepare("SELECT Password_usuario FROM usuarios WHERE Cedula = :cedula");
$validarContraseña -> bindParam(':cedula', $Cedula);
$validarContraseña -> execute();
$contraseñaBD = $validarContraseña -> fetch(PDO::FETCH_ASSOC);

echo $Cedula." ".$Nombre." ".$Apellido." ".$Correo." ".$tipoUsuario." ".$Telefono." ".$pass." ".$repass." ".$contraseñaBD['Password_usuario'];

    if (password_verify($pass, $contraseñaBD['Password_usuario'])){
        $confirmacionCorreo = $db->prepare("SELECT * FROM usuarios WHERE Correo = :correo AND Cedula != :Cedula");
        $confirmacionCorreo->bindParam(':correo', $Correo);
        $confirmacionCorreo->bindParam(':Cedula', $Cedula);
        $confirmacionCorreo->execute();

        if($confirmacionCorreo ->rowCount() > 0){
            echo "El correo ya está siendo utilizado por otro usuario";
            $_SESSION['correo_duplicado'] = 'El correo ya está siendo utilizado, por favor elija otro correo';
            header("Location:". $URL."admin/EditarUsuario.php?Codigo=".$Cedula);
            exit();
            
        }else{

            if (empty($repass)) {
                $passwordEncriptada = $contraseñaBD['Password_usuario'];
            } else {
                $passwordEncriptada = password_hash($repass, PASSWORD_DEFAULT);
            }

            $sentencia = $db -> prepare('UPDATE usuarios 
                                        SET Correo = :Correo, Password_usuario = :Password_usuario, Nombre = :Nombre, Apellido = :Apellido, NumeroTLF = :NumeroTLF, Fecha_Registro = :Fecha_Registro, Tipo_Usuario = :Tipo_Usuario 
                                        WHERE usuarios.Cedula = :Cedula;');
            $sentencia -> bindParam(':Correo', $Correo);
            $sentencia -> bindParam(':Password_usuario', $passwordEncriptada);
            $sentencia -> bindParam(':Nombre', $Nombre);
            $sentencia -> bindParam(':Apellido', $Apellido);
            $sentencia -> bindParam(':NumeroTLF', $Telefono);
            $sentencia -> bindParam(':Fecha_Registro', $fechaEditar);
            $sentencia -> bindParam(':Tipo_Usuario', $tipoUsuario);
            $sentencia -> bindParam(':Cedula', $Cedula);
            $sentencia -> execute();
            $_SESSION['UsuarioEditar']="El usuario ha sido editado con éxito!";
            $redirect_url = $_SESSION['previous_page'] ?? '../usuarios.php';
            header("Location: " . $redirect_url);
            exit();
        }
        

    }else{
        $_SESSION['contraseñaserror']="La contraseña ingresada no es correcta";
        header("Location:".$URL."admin/EditarUsuario.php?Codigo=$Cedula");
    } 


