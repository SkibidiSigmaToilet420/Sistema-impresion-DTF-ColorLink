<?php
include '../model/conexion.php';
include '../plantilla/sesion.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

switch($TipoSesion){
    case 1:
        include '../plantilla/topClientes.php';
        $titulo = "Ayuda para Clientes";
        $instrucciones = '
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-house"></i> <b> Inicio:</b> Visualiza el panel principal y novedades.</li>

                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-arrow-up-from-bracket"></i> <b> Ingresar y Comprar:</b> Ingresa la medida de tu impresion tus diseños y la cantidad de impresion por diseños, para realizar un nuevo pedido.</li>

                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-thumbs-up"></i><b> Aprobar pedidos:</b> Verifica tus pedidos antes de ser enviados a producción y realiza cambios si son necesarios, de estar todo correcto aprueba para ser enviado a producción.</li>
                
                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-truck"></i><b> Mis diseños:</b> Sube, consulta y administra tus diseños personalizados para impresión.</li>

                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-file-image"></i><b> Historial de pedidos:</b> Consulta el estado y detalles de todos tus pedidos realizados.</li>

                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-circle-question"></i><b> Ayuda:</b> Consulta esta sección para instrucciones y soporte.</li>

                <li class="list-group-item bg-transparent text-light"><i class="fa-solid fa-right-from-bracket"></i><b> Cerrar sesión:</b> Haz clic en tu nombre y selecciona <b>"Cerrar sesión"</b>.</li>
            </ul>
        ';
        $botones = '
            <div class="d-flex gap-3 mb-3">
                <a href="https://wa.link/qd7vkh" target="_blank" class="btn btn-success btn-lg shadow">
                    <i class="fa-brands fa-whatsapp me-2"></i>Contactar por WhatsApp
                </a>
                <a href="https://workspace.google.com/intl/es-419/gmail/" target="_blank" class="btn btn-primary btn-lg shadow">
                    <i class="fa-solid fa-envelope me-2"></i>Contactar por Correo
                </a>
            </div>
            <p class="text-muted">Para soporte técnico o dudas, comunícate con nuestro equipo.</p>
        ';
        break;
    case 2: 
        include '../plantilla/topEmpleados.php';
        $titulo = "Ayuda para Empleados";
        $instrucciones = '
            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item bg-transparent text-light"><b>Inicio:</b> Visualiza el panel principal y novedades.</li>
                <li class="list-group-item bg-transparent text-light"><b>Solicitudes en espera:</b> Consulta y gestiona las solicitudes pendientes de los clientes.</li>
                <li class="list-group-item bg-transparent text-light"><b>Pedidos encargados:</b> Revisa y administra los pedidos que tienes asignados.</li>
                <li class="list-group-item bg-transparent text-light"><b>Ayuda:</b> Consulta esta sección para instrucciones y soporte.</li>
                <li class="list-group-item bg-transparent text-light"><b>Cerrar sesión:</b> Haz clic en tu nombre y selecciona <b>"Cerrar sesión"</b>.</li>
            </ul>
        ';
        $botones = '';
        break;
    case 3: 
        include '../plantilla/topAdmin.php';
        $titulo = "Ayuda para Administradores";
        $instrucciones = '
            <div class="mb-3">
                <p class="text-light">Como administrador tienes acceso a los siguientes módulos:</p>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-transparent text-light"><b>Inicio:</b> Panel principal con resumen del sistema.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Usuarios:</b> Gestiona todos los usuarios del sistema.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Usuarios borrados:</b> Gestiona y reactiva los usuarios borrados del sistema.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Administradores:</b> Administra cuentas de otros administradores.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Empleados:</b> Gestiona cuentas y permisos de empleados.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Clientes:</b> Consulta y administra la información de los clientes.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Pedidos:</b> Supervisa y gestiona todos los pedidos realizados.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Diseños:</b> Administra los diseños disponibles para impresión.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Material:</b> Controla el inventario de materiales.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Material:</b> Gestiona y reactiva los materiales borrados.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Ayuda:</b> Consulta esta sección para instrucciones y soporte.</li>
                    <li class="list-group-item bg-transparent text-light"><b>Cerrar sesión:</b> Haz clic en tu nombre y selecciona <b>"Cerrar sesión"</b>.</li>
                </ul>
                <p class="text-light">Utiliza el menú lateral para navegar entre los módulos y gestionar eficientemente el sistema.</p>
            </div>
        ';
        $botones = '';
        break;
    default:
        $titulo = "Ayuda";
        $instrucciones = "<p>No se pudo determinar el tipo de usuario.</p>";
        $botones = '';
}
?>

<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light"><?php echo $titulo; ?></h1>
            </div>
            <div class="mb-3">
                <?php echo $instrucciones; ?>
            </div>
            <?php echo $botones; ?>
        </div>
    </div>
</main>
<?php include '../plantilla/bot.php'; ?>