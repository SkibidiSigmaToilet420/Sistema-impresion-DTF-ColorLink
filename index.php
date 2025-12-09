<?php
include 'model/conexion.php'
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColorLink - Impresión DTF Profesional</title>
    <!-- Icono -->
    <link rel="icon" href="<?php echo $URL; ?>/estilizado/imagenes/images.ico">

    <link href="assets/BootStrap 5.0.2/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $URL; ?>/assets/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        body {
            background-color: #121924;
            /* Fondo oscuro principal */
        }

        .bg-dark-blue-light {
            background-color: #1a2433;
            /* Un poco más claro */
        }

        .text-primary {
            color: #4a8cff !important;
            /* Azul de tu logo */
        }

        /* --- Sección Hero (Protagonismo de la imagen) --- */
        .hero-section {
            /* Usamos la imagen de fondo de la IA */
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('assets/img/dtf_background.png');
            background-size: cover;
            background-position: center;
            min-height: 85vh;
            /* Alto, para darle protagonismo */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 40px 20px;
        }

        /* Estilo para las tarjetas de valores */
        .value-card {
            background-color: #121924;
            border: 1px solid #333;
            padding: 20px;
            border-radius: 8px;
            height: 100%;
        }

        .value-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        /* Estilo galería de diseños */
        .gallery-img {
            width: 100%;
            height: 300px;
            /* Altura fija para uniformidad */
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .gallery-img:hover {
            transform: scale(1.05);
        }

        /* Footer */
        .footer-social a {
            font-size: 1.8rem;
            color: #aaa;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .footer-social a:hover {
            color: #4a8cff;
            /* Azul al pasar el ratón */
        }

        /* 1. Define la animación: Un simple efecto de aparecer */
        @keyframes fadeIn {
            from {
                opacity: 0;
                /* Opcional: moverlo un poco desde abajo */
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 2. Clase para aplicar la animación a la página */
        .page-fade-in {
            /* Duración de la animación */
            animation: fadeIn 1.2s ease-out forwards;
        }

        /* 3. Estilo por defecto (para que no se vea el contenido antes de la animación) */
        #main-content {
            opacity: 0;
        }
    </style>
</head>

<body>
    <div id="main-content">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark-blue-light shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="estilizado/imagenes/Logo.png" alt="ColorLink Logo" style="height: 40px;" class="me-2">
                <span style="color: #ffffff;">Color</span><span style="color: #4a8cff;">Link</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#nosotros">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#servicios">Objetivos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#disenos">Diseños</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-outline-light">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a href="registro.php" class="btn btn-primary">Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container-fluid">
            <div class="display-3 fw-bold">  
            	<span style="color: #ffffff;">Color</span><span style="color: #4a8cff;">Link</span>
            </div>
            <h2 class="fw-bold"> Colors that give life</h2>
            <p class="lead fs-4">Innovación y servicio para cubrir la alta demanda del mercado textil.</p>
            <a href="login.php" class="btn btn-primary btn-lg mt-3">Comienza tu Pedido Ahora</a>
        </div>
    </header>

    <section id="nosotros" class="py-5">
        <div class="container">
            <div class="row g-5 align-items-start">

                <div class="col-lg-6">
                    <h2 class="display-5 text-primary">¿Quiénes Somos?</h2>
                    <p class="lead text-white">
                        Somo una empresa de servicio en impresion DTF en crecimiento contínuo, pero ya con años de trayectoria satisfaciendo las necesidades de nuestros clientes, como también cubriendo la alta demanda en el mercado textil con el innovador estilo de estampado con transfer DTF.
                    </p>
                </div>

                <div class="col-lg-6">

                    <h2 class="display-5 text-primary mb-3">Misión</h2>
                    <p class="lead text-white">
                        Proveer soluciones de impresión directa a filme (DTF) de alta calidad, utilizando tecnología avanzada y prácticas sostenibles, para satisfacer las necesidades de nuestros clientes.
                    </p>

                    <hr class="my-4 border-secondary">
                    <h2 class="display-5 text-primary mb-3">Visión</h2>
                    <p class="lead text-white">
                        Ser reconocidos como líderes en soluciones de impresión DTF en Venezuela, destacándonos por nuestra innovación compromiso con la sostenibilidad y calidad excepcional.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="servicios" class="py-5 bg-dark-blue-light">
        <div class="container">
            <h2 class="text-center mb-5 display-5 text-primary">Nuestros Objetivos</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="value-card text-center">
                        <i class="fas fa-users text-primary"></i>
                        <h3 class="h5 text-primary">EQUIPO</h3>
                        <p class="lead text-white">Mantener un equipo humano que trabaje con sinergia para lograr objetivos con excelencia.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card text-center">
                        <i class="fas fa-lightbulb text-primary"></i>
                        <h3 class="h5 text-primary">INNOVACIÓN</h3>
                        <p class="lead text-white">Innovar en actividades para todo público, que dinamicen la economía y el entretenimiento.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card text-center">
                        <i class="fas fa-thumbs-up text-primary"></i>
                        <h3 class="h5 text-primary">SERVICIOS</h3>
                        <p class="lead text-white">Velar por el buen servicio de todos nuestros clientes en la creación de proyectos.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card text-center">
                        <i class="fas fa-print text-primary"></i>
                        <h3 class="h5 text-primary">IMPRESIONES</h3>
                        <p class="lead text-white">Cubrir con excelencia todas las demandas del mercado textil a nivel nacional en impresiones DTF.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card text-center">
                        <i class="fas fa-globe text-primary"></i>
                        <h3 class="h5 text-primary">ALCANCE</h3>
                        <p class="lead text-white">Promover un cambio positivo en la perspectiva innovadora del estampado textil.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card text-center">
                        <i class="fas fa-bullhorn text-primary"></i>
                        <h3 class="h5 text-primary">PROMOVER</h3>
                        <p class="lead text-white">Promover las buenas practicas que nos permita optimizar progresivamente los procesos productivos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="disenos" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5 display-5 text-primary">Nuestros Diseños</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <img src="assets/img/ESTAMPADO-ELECTRONICO-min.png" alt="Diseño DTF Electrónico" class="gallery-img">
                </div>
                <div class="col-md-6 col-lg-3">
                    <img src="assets/img/FRANELADAMA2.png" alt="Diseño DTF Rock" class="gallery-img">
                </div>
                <div class="col-md-6 col-lg-3">
                    <img src="assets/img/FRANELA-3.png" alt="Diseño DTF Bless" class="gallery-img">
                </div>
                <div class="col-md-6 col-lg-3">
                    <img src="assets/img/FRANELADAMA3.png" alt="Diseño DTF Streetwear" class="gallery-img">
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 bg-dark">
        <div class="container text-center text-white">
            <h3 class="text-primary">Contacto</h3>
            <p class="lead">¡Síguenos en nuestras redes sociales!</p>
            <div class="footer-social mb-3">
                <a href="https://www.tiktok.com/@colorlinkk" aria-label="TikTok" class="text-white"><i class="fab fa-tiktok"></i></a>
                <a href="https://www.instagram.com/colorlinkk/" aria-label="Instagram" class="text-white"><i class="fab fa-instagram"></i></a>
                <a href="https://wa.link/qd7vkh" aria-label="WhatsApp" class="text-white"><i class="fab fa-whatsapp"></i></a>
            </div>

            <p class="text-secondary">&copy; <?php echo date('Y'); ?> ColorLink. Todos los derechos reservados.</p>
        </div>
    </footer>
</div>
    <script src="<?php echo $URL?>assets/BootStrap 5.0.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const mainContent = document.getElementById('main-content');
            // Al cargar la página, añade la clase de animación
            if (mainContent) {
                mainContent.classList.add('page-fade-in');
            }
        });
    </script>
</body>

</html>