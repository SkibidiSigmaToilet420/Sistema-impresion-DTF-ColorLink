<?php 
include '../model/conexion.php';
include '../plantilla/sesion.php';
$cedula = $_GET['Codigo'];

$sentencia = $db -> prepare('SELECT u.Cedula, u.Correo, u.Password_usuario, u.Nombre, u.Apellido, u.NumeroTLF, u.Fecha_Registro, t.Codigo, t.Tipo
                             FROM usuarios u, tipousuario t 
                             WHERE Cedula = :Cedula AND u.Tipo_Usuario = t.Codigo');
$sentencia -> bindParam(':Cedula', $cedula);
$sentencia -> execute();
$usuarioEditar = $sentencia -> fetch(PDO::FETCH_ASSOC);

$sentenciaRoles = $db -> prepare('SELECT Tipo, Codigo FROM tipousuario;');
$sentenciaRoles -> execute();
$Roles = $sentenciaRoles -> fetchAll(PDO::FETCH_OBJ);

include '../plantilla/topAdmin.php';
?>
<main>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-light">Editar usuarios</h1>
                    <p class="text">Desde este menú podrás editar el usuario seleccionado</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-user-plus me-2"></i>Datos del usuario</h5>
            </div>
            <div class="card-body">
                <form id="formNuevoUsuario" action="Controles/Control_EditarU.php" method="post">
                    <div class="row g-3">
                        <input type="hidden" id="cedula" name="cedula" value="<?php echo $usuarioEditar['Cedula'];?>" required>

                        <div class="col-md-6">
                            <label for="nombre" class="form-label text-light">Nombre</label>
                            <input type="text" class="form-control bg-light text-dark border-secondary" id="nombre" name="nombre" value="<?php echo $usuarioEditar['Nombre'];?>" required>
                            <div class="invalid-feedback">
                                Por favor ingresa el nombre del usuario.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="apellido" class="form-label text-light">Apellido</label>
                            <input type="text" class="form-control bg-light text-dark border-secondary" id="apellido" name="apellido" value="<?php echo $usuarioEditar['Apellido'];?>" required>
                            <div class="invalid-feedback">
                                Por favor ingresa el apellido del usuario.
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="correo" class="form-label text-light">Correo electrónico</label>
                            <input type="email" class="form-control bg-light text-dark border-secondary" id="correo" name="correo" value="<?php echo $usuarioEditar['Correo'];?>" required>
                            <div class="invalid-feedback">
                                Por favor ingresa un correo electrónico válido.
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="tipoUsuario" class="form-label text-light">Tipo de usuario</label>
                            <select class="form-select bg-light text-dark border-secondary" id="tipoUsuario" name="tipoUsuario" required>
                                <?php foreach($Roles as $Rol){ ?>
                                <option value="<?php echo $Rol->Codigo?>" <?php if($Rol->Codigo == $usuarioEditar['Codigo']){echo "selected";}?> ><?php echo  $Rol->Tipo?></option>
                                <?php }?>
                            </select>
                            <div class="invalid-feedback">
                                Por favor selecciona un tipo de usuario.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="cedula" class="form-label text-light">Número de télefono</label>
                            <input type="text" class="form-control bg-light text-dark border-secondary" id="TLF" name="TLF" pattern="[0-9]{11}" title="Solo números, 11 dígitos" value="<?php echo $usuarioEditar['NumeroTLF'];?>">
                            <div class="invalid-feedback">
                                Por favor ingresa una número de télefono válida (solo números, 11 dígitos).
                            </div>
                        </div>
                        
                        
                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label text-light">Contraseña Actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control bg-light text-dark border-secondary" id="pass" name="pass" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Las contraseñas no coinciden.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="confirmPassword" class="form-label text-light">Contraseña Nueva</label>
                            <div class="input-group">
                                <input type="password" class="form-control bg-light text-dark border-secondary" id="repass" name="repass">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Las contraseñas no coinciden.
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-6">
                        <button class="btn btn-secondary me-2" onclick="window.history.back();">
                            <i class="fa-solid fa-arrow-left"></i>Volver
                        </button>                     
                        <button type="button" class="btn btn-outline-light me-2" onclick="limpiar()">
                            <i class="fa-solid fa-eraser me-2"></i>Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-2"></i>Editar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</main>

<script src="EditarUsuarios.js">
</script>
<?php include '../plantilla/bot.php';?>


<?php
        if(isset($_SESSION['contraseñaserror'])) {
            $respuesta = $_SESSION['contraseñaserror'];
            unset($_SESSION['contraseñaserror']);
    ?>
        <script>
            Swal.fire({
                toast: true, // Activa el modo toast
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                },
                icon: 'error',
                title: 'Aviso',
                text: '<?php echo $respuesta; ?>',
            });
        </script>
    <?php
        }
    ?>

<?php
        if(isset($_SESSION['correo_duplicado'])) {
            $respuesta = $_SESSION['correo_duplicado'];
            unset($_SESSION['correo_duplicado']);
    ?>
        <script>
            Swal.fire({
                toast: true, // Activa el modo toast
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                },
                icon: 'error',
                title: 'Aviso',
                text: '<?php echo $respuesta; ?>',
            });
        </script>
    <?php
        }
    ?>       