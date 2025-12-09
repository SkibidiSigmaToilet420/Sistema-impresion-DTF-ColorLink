<?php
include '../model/conexion.php';
include '../plantilla/sesion.php';

$sentencia = $db->prepare("SELECT * FROM usuarios WHERE Correo = :correo");
$sentencia->bindParam(':correo', $_SESSION['incio_sesion']);
$sentencia->execute();
$usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

if ($usuario && $usuario['Tipo_Usuario'] == 1) {
    $sentencia = $db->prepare("SELECT * FROM clientes WHERE Cedula_Cliente = :cedula");
    $sentencia->bindParam(':cedula', $CedulaSesion);
    $sentencia->execute();
    $cliente = $sentencia->fetch(PDO::FETCH_ASSOC);

}

if ($usuario && $usuario['Tipo_Usuario'] == 2) {
    $sentencia = $db->prepare("SELECT * FROM empleados WHERE Cedula_Empleado = :cedula");
    $sentencia->bindParam(':cedula', $CedulaSesion);
    $sentencia->execute();
    $empleados = $sentencia->fetch(PDO::FETCH_ASSOC);

}

switch($TipoSesion){
    case 1: 
        include '../plantilla/topClientes.php';
        break;
    case 2: 
        include '../plantilla/topEmpleados.php';
        break;
    case 3: 
        include '../plantilla/topAdmin.php';
        break;
}
?>

<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Perfil</h1>
            </div>
            <p class="text-light">Desde este menú podrás verificar y actualizar la información de tu perfil.</p>
        </div>

        <?php if($usuario): ?>
        <div class="card shadow mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Información general</h5>
            </div>
            <div class="card-body">
                <form class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-light">Nombre</label>
                        <input type="text" class="form-control" value="<?php echo $usuario['Nombre']; ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light">Correo</label>
                        <input type="email" class="form-control" value="<?php echo $usuario['Correo']; ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light">Cédula</label>
                        <input type="text" class="form-control" value="<?php echo $usuario['Cedula']; ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light">Tipo de Usuario</label>
                        <input type="text" class="form-control" value="<?php
                            if($usuario['Tipo_Usuario']==1) echo 'Cliente';
                            elseif($usuario['Tipo_Usuario']==2) echo 'Empleado';
                            elseif($usuario['Tipo_Usuario']==3) echo 'Administrador';
                        ?>" disabled>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Solo para clientes: editar dirección y ver tipo de cliente -->
        <?php if($TipoSesion == 1 && $cliente): ?>
        <div class="card shadow mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-address-card me-2"></i>Datos de cliente</h5>
            </div>
            <div class="card-body">
                <form action="procesar_perfil_cliente.php" method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-light">Dirección</label>
                        <input type="text" name="Direccion" class="form-control" value="<?php echo $cliente['Direccion']; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-light">Tipo de cliente</label>
                        <input type="text" name="Direccion" class="form-control" value="<?php echo $cliente['Tipo_de_Cliente']; ?>" disabled>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Actualizar Datos</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Solo para empleados: ver cargo -->
        <?php if($TipoSesion == 2 && $empleados): ?>
        <div class="card shadow mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-address-card me-2"></i>Datos del empleado</h5>
            </div>
            <div class="card-body">
                <form action="procesar_perfil_cliente.php" method="post" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-light">Cargo</label>
                        <input type="text" name="Direccion" class="form-control" value="<?php echo $empleados['Cargo']; ?>" disabled>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cambiar contraseña para todos -->
        <div class="card shadow mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-key me-2"></i>Cambiar contraseña</h5>
            </div>
            <div class="card-body">
                <form action="procesar_cambio_contrasena.php" method="post" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="password" name="contrasena_actual" class="form-control" placeholder="Contraseña actual" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="password" name="nueva_contrasena" class="form-control" placeholder="Nueva contraseña" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="password" name="confirmar_contrasena" class="form-control" placeholder="Confirmar nueva" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-warning">Cambiar contraseña</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if(isset($_GET['cambio_clave']) || isset($_GET['actualizacion_cliente'])): ?>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    <?php if(isset($_GET['cambio_clave']) && $_GET['cambio_clave'] == 'ok'): ?>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña actualizada!',
                        text: 'Tu contraseña se cambió correctamente.',
                        confirmButtonColor: '#3085d6'
                    });
                    <?php elseif(isset($_GET['cambio_clave']) && $_GET['cambio_clave'] == 'error_actual'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La contraseña actual no es correcta.',
                        confirmButtonColor: '#d33'
                    });
                    <?php elseif(isset($_GET['cambio_clave']) && $_GET['cambio_clave'] == 'error_coincidencia'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Las nuevas contraseñas no coinciden.',
                        confirmButtonColor: '#d33'
                    });
                    <?php elseif(isset($_GET['cambio_clave']) && $_GET['cambio_clave'] == 'error_longitud'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La nueva contraseña debe tener al menos 6 caracteres.',
                        confirmButtonColor: '#d33'
                    });
                    <?php endif; ?>

                    <?php if(isset($_GET['actualizacion_cliente']) && $_GET['actualizacion_cliente'] == 'ok'): ?>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Datos actualizados exitosamente!',
                        text: 'La información de tu perfil de cliente ha sido actualizada.',
                        confirmButtonColor: '#3085d6'
                    });
                    <?php elseif(isset($_GET['actualizacion_cliente']) && $_GET['actualizacion_cliente'] == 'error'): ?>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron actualizar los datos del cliente.',
                        confirmButtonColor: '#d33'
                    });
                    <?php endif; ?>
                });
            </script>
        <?php endif; ?>
    </div>
</main>
<?php include '../plantilla/bot.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>