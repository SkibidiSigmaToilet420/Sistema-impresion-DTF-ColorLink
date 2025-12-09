<?php 
include('../plantilla/sesion.php');


if(isset($_SESSION['incio_sesion'])) {
}else{
    header("location:".$URL."login.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | ColorLink</title>
    <!-- Icono -->
    <link rel="icon" href="<?php echo $URL;?>/estilizado/imagenes/images.ico">
    <!-- BootStrap 5 -->
    <link href="<?php echo $URL;?>/assets/BootStrap 5.0.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- BootStrap 5 icons -->
    <!-- <link href="assets/BootStrap 5.0.2/icons/bootstrap-icons.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="<?php echo $URL;?>/assets/fontawesome-free-6.7.2-web/css/all.min.css">
    <!-- Estilos -->
    <link rel="stylesheet" href="<?php echo $URL;?>/estilizado/index.css">
    <!-- SweetAlert 2 -->
    <link rel="stylesheet" href="<?php echo $URL;?>/assets/SweetAlert 2/sweetalert2.min.css">
    <!-- modal_notificaciones removed: using inline dropdown scroll -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" id="header">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo $URL;?>empleados/index.php">
            <img src="<?php echo $URL; ?>estilizado/imagenes/Logo.png" 
                 alt="ColorLink Logo" 
                 class="me-2"
                 style="height: 40px; width: auto;">
            <span>Color</span><span>Link</span>
        </a>

        <a class="navbar-brand fw-bold">
            <span></span><span>Empleado</span>
        </a>
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <?php include_once '../empleados/Controles/Control_notificaciones.php'; ?>
                <button class="btn btn-link position-relative me-3" type="button" id="dropdownNotificaciones" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                        <?php echo $count; ?>
                    </span>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end p-0 dropdown-menu-notif" aria-labelledby="dropdownNotificaciones" style="min-width:340px; max-width:95vw;">
                    <li class="nav-link text-light d-flex justify-content-between align-items-center">
                        <span>Notificaciones</span>
                        <form action="<?php echo $URL; ?>empleados/Controles/Borrar_notificaciones.php" method="post" style="display:inline; margin-left: 10px;">
                            <button type="submit" class="btn btn-sm btn-danger" title="Borrar notificaciones">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider m-0"></li>
                    <li>
                        <div>
                            <div id="notifContainer" style="max-height:360px; overflow-y:auto; width:100%; padding:8px;">
                                <?php if ($count > 0): ?>
                                    <?php foreach ($notificaciones as $row): ?>
                                        <div class="dropdown-item notification-item d-flex align-items-center" style="background-color: var(--notification-bg-color, #1a3a6a);">
                                            <a href="<?php echo $URL; ?>empleados/VerificarDise%C3%B1os.php?Codigo=<?php echo $row['Id_pedido']; ?>" class="text-decoration-none w-100">
                                                <span class="me-2"><i class="fa fa-check-circle text-success"></i></span>
                                                <span class="message-description small" style="color: var(--notification-text-color, #ffffff); font-weight: 500;">
                                                    Pedido ID: <b style="color: var(--notification-highlight-color, #bbdefb);"> <?php echo htmlspecialchars($row['Id_pedido']); ?></b><br>
                                                    Estado: <b style="color: var(--notification-highlight-color, #bbdefb);"> <?php echo htmlspecialchars($row['Estado_Pedido']); ?></b><br>
                                                    Cliente: <b style="color: var(--notification-highlight-color, #bbdefb);"> <?php echo htmlspecialchars($row['Nombre'] . ' ' . $row['Apellido']); ?></b><br>
                                                    Fecha: <span class="text-muted" style="color: var(--notification-muted-color, #90caf9);"> <?php echo date('d/m/Y H:i', strtotime($row['Fecha_Solicitud'])); ?></span>
                                                </span>
                                            </a>
                                        </div>
                                        <div><hr class="dropdown-divider m-0"></div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="dropdown-item text-muted notification-item text-center">No hay notificaciones</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-circle-user"></i>
                    <span id="nombreUsuario"><?php echo($nombreSesion)?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo $URL;?>/usuarios/perfil.php"><i class="fa-solid fa-user"></i> Perfil</a></li>
                    <li><a class="dropdown-item" href="#" id="themeToggle"><i class=""></i> Tema</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo $URL?>cerrarsesion.php" id="logoutBtn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Cerrar sesión</a></li>
                </ul>
                
            </div>
        </div>
    </div>
    
</nav>
<div class="sidebar">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 bg-dark-blue sidebar">
                <div class="d-flex flex-column flex-shrink-0 p-3 h-100">
<ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item mb-2">
                            <a href="<?php echo $URL;?>empleados/index.php" class="nav-link text-light">
                               <i class="fa-solid fa-house"></i> Inicio
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo $URL;?>empleados/Solicitudes.php" class="nav-link text-light">
                               <i class="fa-solid fa-clock"></i> Solicitudes en espera
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo $URL;?>empleados/PedidosEncargados.php" class="nav-link text-light">
                                <i class="fa-solid fa-list-check"></i> Pedidos encargados
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo $URL;?>empleados/PedidosProduccion.php" class="nav-link text-light">
                                <i class="fa-solid fa-print"></i> Pedidos en producción
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="<?php echo $URL;?>empleados/historialPedidos.php" class="nav-link text-light">
                                <i class="fa-solid fa-clock-rotate-left"></i> Historial de pedidos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo $URL;?>usuarios/ayuda.php" class="nav-link text-light">
                                <i class="fa-solid fa-circle-question"></i> Ayuda
                            </a>
                        </li>
                    </ul>
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
        </div>
    </div>       
</div>