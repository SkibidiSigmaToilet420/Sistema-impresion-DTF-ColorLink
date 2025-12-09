<?php include 'model/conexion.php';
include 'plantilla/sesion.php';


$sentencia = $db->prepare('SELECT Tipo, Codigo FROM tipousuario;');
$sentencia->execute();
$Roles = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColorLink | Servicio de impresión DTF</title>
    <!-- Icono -->
    <link rel="icon" href="estilizado/imagenes/images.ico">
    <!-- BootStrap 5 -->
    <link href="assets/BootStrap 5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos -->
    <link rel="stylesheet" href="estilizado/login.css">
    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="assets/SweetAlert 2/sweetalert2.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="assets/fontawesome-free-6.7.2-web/css/all.min.css">


    <style>
        /* 1. ESTILOS PARA EL FONDO DTF */
        /* Sobrescribe el body para agregar la imagen de fondo */
        body {
            /* IMPORTANTE: Asegúrate de que esta ruta sea correcta */
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('assets/img/dtf_background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            /* Agregamos una capa oscura para mantener el texto legible si tu body no tiene un fondo definido */
            background-color: rgba(0, 0, 0, 0.7) !important;
        }

        /* Opcional: Para mejorar el contraste del formulario existente */
        .card.bg-dark-blue {
            background-color: rgba(18, 25, 36, 0.9) !important;
            /* Ligeramente más opaco */
        }

        /* 2. ESTILOS PARA EL BOTÓN DE WHATSAPP */
        .whatsapp-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #25D366;
            color: white;
            padding: 12px 18px;
            border-radius: 50px;
            text-align: center;
            font-size: 1.2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            z-index: 1000;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .whatsapp-button:hover {
            background-color: #1DA851;
            color: white;
        }

        .whatsapp-button i {
            margin-right: 8px;
        }
    </style>
</head>

<body class="bg-dark text-light">
    <a href="https://wa.link/qd7vkh"
        target="_blank"
        class="whatsapp-button">
        <i class="fa-brands fa-whatsapp"></i> Contáctanos
    </a>


    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card bg-dark-blue border-0 shadow-lg my-5" style="max-width: 800px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <!-- Logo y nombre de la empresa -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <img src="estilizado/imagenes/Logo.png"
                                    alt="ColorLink Logo"
                                    class="me-3"
                                    style="height: 100px; width: auto;">
                                <h1 class="display-4 fw-bold mb-0">
                                    <span style="color: #ffffffff;">Registro</span>
                                </h1>
                            </div>
                            <h3 class="text-primary">Ingrese sus datos</h3>
                        </div>
                        <div class="card-body">
                            <form id="formNuevoUsuario" action="Control_Registro.php" method="post">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="cedula" class="form-label text-light">Documento de identidad</label>
                                        <div class="input-group">
                                            <select class="form-select bg-light text-dark border-secondary"
                                                id="tipo_documento"
                                                name="tipo_documento"
                                                style="max-width: 80px;"
                                                required>
                                                <option value="V" selected>V</option>
                                                <option value="E">E</option>
                                                <option value="J">J</option>
                                            </select>

                                            <input type="text"
                                                class="form-control bg-light text-dark border-secondary"
                                                id="cedula"
                                                name="cedula"
                                                placeholder="12345678"
                                                required>
                                        </div>
                                        <div class="invalid-feedback">
                                            Formato inválido para el tipo de documento seleccionado.
                                        </div>
                                        <small id="helpCedula" class="text-muted" style="font-size: 0.8em; color: #bbb !important;">
                                            Máximo 8 dígitos numéricos
                                        </small>
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
                                        <label for="cedula" class="form-label text-light">Número de télefono</label>
                                        <input type="text" class="form-control bg-light text-light border-secondary" id="TLF" name="TLF" pattern="[0-9]{11}" title="Solo números, 11 dígitos">
                                        <div class="invalid-feedback">
                                            Por favor ingresa una número de télefono válida (solo números, 11 dígitos).
                                        </div>
                                    </div>

                                    <div class="col-md-6"> <label for="direccion" class="form-label text-light">Dirección</label>
                                        <textarea class="form-control bg-light text-dark border-secondary"
                                            id="direccion"
                                            name="direccion"
                                            rows="2"
                                            placeholder="Indique calle, casa, punto de referencia..."
                                            required></textarea>
                                        <div class="invalid-feedback">
                                            La dirección es obligatoria.
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

                                    <input type="number" class="form-control d-none" id="tipoUsuario" name="tipoUsuario" value=1 hidden>
                                </div><br>

                                <div class="d-flex justify-content-end mt-6">
                                    <a href="login.php" type="button" class="btn btn-secondary me-2">
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
            </div>
        </div>
    </div>
</body>


<script>
    function limpiar() {
        document.getElementById('formNuevoUsuario').reset();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tipoDocSelect = document.getElementById('tipo_documento');
        const cedulaInput = document.getElementById('cedula');
        const helpCedula = document.getElementById('helpCedula');

        if (tipoDocSelect && cedulaInput) {

            // Función para actualizar restricciones
            function actualizarReglasDocumento() {
                const tipo = tipoDocSelect.value;

                // Limpiamos el valor si cambian de tipo para evitar errores de formato cruzado
                cedulaInput.value = '';

                if (tipo === 'V') {
                    // Venezolano: Solo números, máx 8
                    cedulaInput.setAttribute('pattern', '[0-9]{7,8}');
                    cedulaInput.setAttribute('maxlength', '8');
                    cedulaInput.setAttribute('title', 'Solo números, entre 7 y 8 dígitos');
                    helpCedula.textContent = "Máximo 8 dígitos numéricos (Ej: 12345678)";
                } else if (tipo === 'E') {
                    // Extranjero: Números, asumimos hasta 12 dígitos
                    cedulaInput.setAttribute('pattern', '[0-9]{5,15}');
                    cedulaInput.setAttribute('maxlength', '15');
                    cedulaInput.setAttribute('title', 'Solo números');
                    helpCedula.textContent = "Solo números (Cédula de extranjería)";
                } else if (tipo === 'J') {
                    // Jurídico (RIF): Generalmente 9 o 10 dígitos. 
                    // Si quieres permitir guiones usa: pattern="[0-9-]{9,12}"
                    // Aquí lo dejamos solo números hasta 10 como pediste:
                    cedulaInput.setAttribute('pattern', '[0-9]{8,10}');
                    cedulaInput.setAttribute('maxlength', '10');
                    cedulaInput.setAttribute('title', 'Solo números del RIF (sin guiones)');
                    helpCedula.textContent = "Ingrese los números del RIF (hasta 10 dígitos)";
                }
            }

            // Escuchar el cambio en el select
            tipoDocSelect.addEventListener('change', actualizarReglasDocumento);

            // Ejecutar una vez al inicio
            actualizarReglasDocumento();
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
<?php include 'plantilla/bot.php'; ?>


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