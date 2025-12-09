<?php 
session_start();
include ('model/conexion.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColorLink | Servicio de impresión DTFS <?php echo $URL;?></title>
    <!-- Icono -->
    <link rel="icon" href="<?php echo $URL;?>estilizado/imagenes/images.ico">
    <!-- BootStrap 5 -->
    <link href="<?php echo $URL; ?>assets/BootStrap 5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos -->
    <link rel="stylesheet" href="<?php echo $URL; ?>estilizado/login.css">
    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="<?php echo $URL; ?>assets/SweetAlert 2/sweetalert2.min.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="<?php echo $URL; ?>assets/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
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
            background-color: rgba(18, 25, 36, 0.9) !important; /* Ligeramente más opaco */
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

        .btn-volver-colorlink {
            background-color: #495057 !important;
            transition: background-color 0.3s ease;
            width: 50px; 
            height: 50px; 
            padding: 0; 
            -height: 50px; 
            text-align: center; 
            font-size: 1.2rem; 
            z-index: 1000; 
            border: none
        }

        .btn-volver-colorlink:hover {
            background-color: #6c757d !important;
            transform: scale(1.05);

        }
    </style>
</head>
<body class="bg-dark text-light">
    <a href="index.php"
        class="btn btn-dark rounded-circle position-fixed top-0 start-0 m-4 shadow btn-volver-colorlink"
        style="width: 50px; height: 50px; padding: 0; line-height: 50px; text-align: center; font-size: 1.2rem; z-index: 1000; border: none;"
        title="Volver a ColorLink">
        <i class="fas fa-arrow-left text-white"></i>
    </a>
    <a href="https://wa.link/qd7vkh" 
       target="_blank" 
       class="whatsapp-button">
        <i class="fa-brands fa-whatsapp"></i> Contáctanos
    </a>

    <div class="container-fluid min-vh-100 d-flex flex-column justify-content-center">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card bg-dark-blue border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <!-- Logo y nombre de la empresa -->
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <img src="estilizado/imagenes/Logo.png" 
                                     alt="ColorLink Logo" 
                                     class="me-3"
                                     style="height: 100px; width: auto;">
                                <h1 class="display-4 fw-bold mb-0">
                                    <span style="color: #ffffff;">Color</span><span style="color: #4a8cff;">Link</span>
                                </h1>
                            </div>
                            <h3 class="text-primary">Colors That Give Life</h3>
                        </div>

                        <div class="text-center mb-4">
                            <h2 class="text-light">Servicio de Impresión DTF</h2>
                        </div>

                        <div class="login-form">
                            <h3 class="text-center mb-4 text-light">Iniciar Sesión</h3>
                            <form id="loginForm" action="controlLogin.php" method="post">
                                <div class="mb-3">
                                    <input type="email" id="correo" name="correo" class="form-control bg-dark border-secondary text-light" 
                                           placeholder="Usuario" required>
                                </div>
                                <div class="mb-4">
                                    <input type="password" id="pass" name="pass" class="form-control bg-dark border-secondary text-light" 
                                           placeholder="Contraseña" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="submit" id="loginBtn" class="btn btn-primary w-100 py-2 fw-bold">
                                            Iniciar Sesión
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="registro.php" class="btn btn-secondary w-100 py-2 fw-bold"> Registro</a>
                                    </div>
                                </div>

                                
                            </form>
                            <div class="text-center mt-3">
                                <a href="#" onclick="abrirRestablecer()" class="text-light">Olvidé mi contraseña</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo $URL; ?>funcioneslogin.js"></script>
    <!-- BootStrap 5 -->
    <script src="<?php echo $URL; ?>assets/BootStrap 5.0.2/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert 2 -->
    <script src="<?php echo $URL; ?>assets/SweetAlert 2/sweetalert2.all.min.js"></script>
    <!-- FontAwesome -->    
    <script src="<?php echo $URL; ?>assets/fontawesome-free-6.7.2-web/js/all.min.js"></script>

    <!-- mensajes de inicio de sesion -->

    <?php
        if(isset($_SESSION['error_sesion'])) {
            $respuesta = $_SESSION['error_sesion'];
            unset($_SESSION['error_sesion']);
    ?>
        <script>
            Swal.fire({
            icon: "error",
            title: "Antención",
            text: <?php echo json_encode($respuesta); ?>,
            footer: '<a href="">Desea registrarse?</a>'
            });
        </script>
    <?php
        }
    ?>

    <?php
        if(isset($_SESSION['UsuarioRegistrado'])) {
            $respuesta = $_SESSION['UsuarioRegistrado'];
            unset($_SESSION['UsuarioRegistrado']);
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
                icon: 'success',
                title: 'Aviso',
                text: <?php echo json_encode($respuesta); ?>,
            });
        </script>
<?php
    }
?>

    <?php
        if(isset($_SESSION['restablecer_ok'])) {
            $respuesta = $_SESSION['restablecer_ok'];
            unset($_SESSION['restablecer_ok']);
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
                icon: 'success',
                title: 'Listo',
                text: <?php echo json_encode($respuesta); ?>,
            }).then(()=>{window.location = 'login.php'});
        </script>
    <?php
        }
    ?>

    <?php
        if(isset($_SESSION['restablecer_error'])) {
            $respuesta = $_SESSION['restablecer_error'];
            unset($_SESSION['restablecer_error']);
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
                title: 'Error',
                text: <?php echo json_encode($respuesta); ?>,
            });
        </script>
    <?php
        }
    ?>



<?php
// En login.php - después de session_start();
if (isset($_GET['inactividad']) && $_GET['inactividad'] == 1) {
    echo '<script>
        Swal.fire({
            icon: "info",
            title: "¡Atención!",
            text: "Se ha cerrado su sesión por inactividad",
        });
    </script>';
}
?>

    
</body>
</html>