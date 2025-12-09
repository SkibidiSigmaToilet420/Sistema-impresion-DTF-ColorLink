<?php include '../model/conexion.php';
include '../plantilla/topAdmin.php';

$sentencia = $db->prepare('SELECT Tipo, Codigo FROM tipousuario;');
$sentencia->execute();
$Roles = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>


<main>
    <main>
        <div class="col-lg-10 ms-sm-auto px-4 py-4">
            <div class="title">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                    <div>
                        <h1 class="h2 text-light">Agregar usuarios</h1>
                        <p class="text">Desde este menú podrás añadir nuevos usuarios al sistema</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark-blue text-light">
                    <h5 class="mb-0"><i class="fa-solid fa-user-plus me-2"></i>Datos del nuevo usuario</h5>
                </div>
                <div class="card-body">
                    <form id="formNuevoUsuario" action="Controles/Control_AgregarU.php" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cedula" class="form-label text-light">Documento de Identidad</label>
                                <div class="input-group">
                                    <select class="form-select bg-light text-dark border-secondary" id="tipo_documento" name="tipo_documento" style="max-width: 80px;" required>
                                        <option value="V" selected>V</option>
                                        <option value="E">E</option>
                                        <option value="J">J</option>
                                    </select>
                                    <input type="text" class="form-control bg-light text-dark border-secondary" id="cedula" name="cedula" placeholder="12345678" required>
                                </div>
                                <small id="helpCedula" class="text-muted" style="font-size: 0.8em; color: #bbb !important;">Máximo 8 dígitos</small>
                            </div>

                            <div class="col-md-6">
                                <label for="nombre" class="form-label text-light">Nombre</label>
                                <input type="text" class="form-control bg-light text-dark border-secondary" id="nombre" name="nombre" required>
                                <div class="invalid-feedback">
                                    Por favor ingresa el nombre del usuario.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="apellido" class="form-label text-light">Apellido</label>
                                <input type="text" class="form-control bg-light text-dark border-secondary" id="apellido" name="apellido" required>
                                <div class="invalid-feedback">
                                    Por favor ingresa el apellido del usuario.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="correo" class="form-label text-light">Correo electrónico</label>
                                <input type="email" class="form-control bg-light text-dark border-secondary" id="correo" name="correo" required>
                                <div class="invalid-feedback">
                                    Por favor ingresa un correo electrónico válido.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="tipoUsuario" class="form-label text-light">Tipo de usuario</label>
                                <select class="form-select bg-light text-dark border-secondary" id="tipoUsuario" name="tipoUsuario" required>
                                    <option value="" selected disabled>Selecciona un tipo...</option>
                                    <?php foreach ($Roles as $Rol) { ?>
                                        <option value="<?php echo $Rol->Codigo ?>"><?php echo  $Rol->Tipo ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback">
                                    Por favor selecciona un tipo de usuario.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="cedula" class="form-label text-light">Número de télefono</label>
                                <input type="text" class="form-control bg-light text-light border-secondary" id="TLF" name="TLF" pattern="[0-9]{11}" title="Solo números, 11 dígitos">
                                <div class="invalid-feedback">
                                    Por favor ingresa una número de télefono válida (solo números, 11 dígitos).
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label text-light">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-light text-dark border-secondary" id="pass" name="pass" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Por favor ingresa una contraseña.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="confirmPassword" class="form-label text-light">Confirmar contraseña</label>
                                <div class="input-group">
                                    <input type="password" class="form-control bg-light text-dark border-secondary" id="repass" name="repass" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Las contraseñas no coinciden.
                                </div>
                            </div>

                            <div class="col-md-12 d-none" id="divDireccion">
                                <label for="direccion" class="form-label text-light">Dirección (Solo para Clientes)</label>
                                <textarea class="form-control bg-light text-dark border-secondary" id="direccion" name="direccion" rows="2" style="resize: none;" placeholder="Ingrese la dirección..."></textarea>
                            </div>
                        </div><br>

                        <div class="d-flex justify-content-end mt-6">
                            <a href="usuarios.php" type="button" class="btn btn-secondary me-2">
                                <i class="fa-solid fa-arrow-left"></i>Volver
                            </a>
                            <button type="button" class="btn btn-outline-light me-2" onclick="limpiar()">
                                <i class="fa-solid fa-eraser me-2"></i>Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-2"></i>Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</main>

<script>
    function limpiar() {
        document.getElementById('formNuevoUsuario').reset();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // LÓGICA PARA MOSTRAR/OCULTAR DIRECCIÓN
        const selectTipoUsuario = document.getElementById('tipoUsuario');
        const divDireccion = document.getElementById('divDireccion');
        const inputDireccion = document.getElementById('direccion');

        if (selectTipoUsuario && divDireccion) {
            selectTipoUsuario.addEventListener('change', function() {
                // Asumimos que el value '1' es Cliente (según tu base de datos)
                if (this.value == 1) {
                    divDireccion.classList.remove('d-none'); // Mostrar
                    inputDireccion.setAttribute('required', 'true'); // Hacerlo obligatorio
                } else {
                    divDireccion.classList.add('d-none'); // Ocultar
                    inputDireccion.removeAttribute('required'); // Quitar obligatorio
                    inputDireccion.value = ''; // Limpiar si cambian de opinión
                }
            });
        }



        // --- LÓGICA CÉDULA ---
        const tipoDocSelect = document.getElementById('tipo_documento');
        const cedulaInput = document.getElementById('cedula');
        const helpCedula = document.getElementById('helpCedula');

        if (tipoDocSelect && cedulaInput) {
            function actualizarReglas() {
                const tipo = tipoDocSelect.value;
                cedulaInput.value = '';
                if (tipo === 'V') {
                    cedulaInput.setAttribute('pattern', '[0-9]{7,8}');
                    cedulaInput.setAttribute('maxlength', '8');
                    helpCedula.textContent = "Máximo 8 dígitos";
                } else if (tipo === 'E') {
                    cedulaInput.setAttribute('pattern', '[0-9]{5,15}');
                    cedulaInput.setAttribute('maxlength', '15');
                    helpCedula.textContent = "Solo números";
                } else if (tipo === 'J') {
                    cedulaInput.setAttribute('pattern', '[0-9]{8,10}');
                    cedulaInput.setAttribute('maxlength', '10');
                    helpCedula.textContent = "Hasta 10 dígitos (RIF)";
                }
            }
            tipoDocSelect.addEventListener('change', actualizarReglas);
            actualizarReglas(); // Iniciar reglas
        }

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('pass');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    if (icon) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                } else {
                    passwordInput.type = 'password';
                    if (icon) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        }

        // Solo declara una vez la variable aquí
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('repass');
        if (toggleConfirmPassword && confirmPasswordInput) {
            toggleConfirmPassword.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (confirmPasswordInput.type === 'password') {
                    confirmPasswordInput.type = 'text';
                    if (icon) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }
                } else {
                    confirmPasswordInput.type = 'password';
                    if (icon) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                }
            });
        }
    });
</script>
<?php include '../plantilla/bot.php'; ?>


<?php
if (isset($_SESSION['ContraseñaDiferente'])) {
    $respuesta = $_SESSION['ContraseñaDiferente'];
    unset($_SESSION['ContraseñaDiferente']);
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
if (isset($_SESSION['cedula_duplicada'])) {
    $respuesta = $_SESSION['cedula_duplicada'];
    unset($_SESSION['cedula_duplicada']);
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
if (isset($_SESSION['correo_duplicado'])) {
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