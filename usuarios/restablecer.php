<?php
include('../model/conexion.php'); // Asegúrate de que tenga los paréntesis
include('token_helper.php');

$token = isset($_GET['token']) ? $_GET['token'] : '';
$val = validar_token($token);
if (!$val) {
?>
    <script src="../assets/SweetAlert 2/sweetalert2.all.min.js"></script>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Enlace inválido',
            text: 'El enlace de restablecimiento no es válido o expiró.'
        }).then(() => {
            window.location = '../login.php'
        });
    </script>
<?php
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <link href="<?php echo $URL; ?>assets/BootStrap 5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo $URL; ?>estilizado/imagenes/images.ico">
    <link rel="stylesheet" href="<?php echo $URL; ?>estilizado/login.css">
    <link rel="stylesheet" href="<?php echo $URL; ?>assets/SweetAlert 2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?php echo $URL; ?>/assets/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        /* Estilos existentes */
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6) !important;
        }

        .form-control {
            color: #fff !important;
        }

        /* AÑADIDO: ESTILOS PARA EL FONDO DTF */
        body {
            /* Capa negra al 40% y 60% sobre la imagen para oscurecerla */
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('<?php echo $URL; ?>assets/img/dtf_background.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: rgba(0, 0, 0, 0.7) !important;
        }

        .card {
            /* Puedes añadir un color de fondo semi-transparente a la tarjeta */
            background-color: rgba(17, 34, 64, 0.9);
            /* Un fondo oscuro */
        }
    </style>
</head>

<body class="bg-dark text-light">
    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card bg-dark-blue border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="text-center mb-4 text-light">Restablecer contraseña</h3>
                        </div>
                        <form id="formRestablecer" action="procesar_restablecer.php" method="post">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                            <div class="mb-3">
                                <label class="form-label text-light">Nueva contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="nueva" name="nueva" class="form-control bg-dark border-secondary text-light" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('nueva', 'icon-nueva')">
                                        <i class="fa-solid fa-eye" id="icon-nueva"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-light">Confirmar contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="confirmar" name="confirmar" class="form-control bg-dark border-secondary text-light" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmar', 'icon-confirmar')">
                                        <i class="fa-solid fa-eye" id="icon-confirmar"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/BootStrap 5.0.2/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/SweetAlert 2/sweetalert2.all.min.js"></script>
    <script src="<?php echo $URL; ?>assets/fontawesome-free-6.7.2-web/js/all.min.js"></script>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Tu validación existente de coincidencia
        document.getElementById('formRestablecer').addEventListener('submit', function(e) {
            var nueva = this.querySelector('input[name="nueva"]').value;
            var confirmar = this.querySelector('input[name="confirmar"]').value;
            if (nueva !== confirmar) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden.'
                });
                return false;
            }
        });
    </script>
    <script>
        document.getElementById('formRestablecer').addEventListener('submit', function(e) {
            var nueva = this.querySelector('input[name="nueva"]').value;
            var confirmar = this.querySelector('input[name="confirmar"]').value;
            if (nueva !== confirmar) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden.'
                });
                return false;
            }
        });
    </script>
</body>

</html>