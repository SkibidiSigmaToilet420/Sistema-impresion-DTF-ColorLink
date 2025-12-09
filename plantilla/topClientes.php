<?php


if (isset($_SESSION['incio_sesion'])) {
} else {
    header("location:" . $URL . "login.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | ColorLink</title>
    <!-- Icono -->
    <link rel="icon" href="<?php echo $URL; ?>/estilizado/imagenes/images.ico">
    <!-- BootStrap 5 -->
    <link href="<?php echo $URL; ?>/assets/BootStrap 5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- BootStrap 5 icons -->
    <!-- <link href="assets/BootStrap 5.0.2/icons/bootstrap-icons.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?php echo $URL; ?>/assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Estilos -->
    <link rel="stylesheet" href="<?php echo $URL; ?>/estilizado/index.css">
    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="<?php echo $URL; ?>/assets/SweetAlert 2/sweetalert2.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" id="header">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo $URL; ?>clientes/index.php">
                <img src="<?php echo $URL; ?>estilizado/imagenes/Logo.png"
                    alt="ColorLink Logo"
                    class="me-2"
                    style="height: 40px; width: auto;">
                <span>Color</span><span>Link</span>
            </a>
            <div class="d-flex align-items-center">

                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user"></i>
                        <span id="nombreUsuario"><?php echo ($nombreSesion) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo $URL; ?>usuarios/perfil.php"><i class="fa-solid fa-user"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="#" id="themeToggle"><i class=""></i> Tema</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?php echo $URL ?>cerrarsesion.php" id="logoutBtn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Highlight current page in sidebar
                try {
                    var links = document.querySelectorAll('.sidebar .nav-link');
                    var current = window.location.href;
                    links.forEach(function(l) {
                        var href = l.href;
                        if (href && current.indexOf(href) !== -1) {
                            l.classList.add('active');
                            l.style.backgroundColor = 'rgba(255,255,255,0.06)';
                        }
                    });
                } catch (e) {
                    console.error(e);
                }
            });
        </script>

    </nav>
    <div class="sidebar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-2 bg-dark-blue sidebar">
                    <div class="d-flex flex-column flex-shrink-0 p-3 h-100">
                        <ul class="nav nav-pills flex-column mb-auto">
                            <li class="nav-item mb-2">
                                <a href="<?php echo $URL; ?>clientes/index.php" class="nav-link text-light">
                                    <i class="fa-solid fa-house"></i> Inicio
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="<?php echo $URL; ?>clientes/Pedido.php" class="nav-link text-light">
                                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Ingresar y Comprar
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="<?php echo $URL; ?>clientes/mis_aprobaciones.php" class="nav-link text-light">
                                    <i class="fa-solid fa-thumbs-up"></i> Aprobar pedidos
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="<?php echo $URL; ?>clientes/historial_Pedido.php" class="nav-link text-light">
                                    <i class="fa-solid fa-truck"></i> Historial de pedidos
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="<?php echo $URL; ?>clientes/diseño.php" class="nav-link text-light">
                                    <i class="fa-solid fa-file-image"></i> Mis diseños
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo $URL; ?>usuarios/ayuda.php" class="nav-link text-light">
                                    <i class="fa-solid fa-circle-question"></i> Ayuda
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>