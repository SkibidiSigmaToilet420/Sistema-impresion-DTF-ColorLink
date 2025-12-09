<?php
include('../model/conexion.php');
include('../plantilla/sesion.php');
include('../plantilla/topClientes.php');
$contadorSesion = 0;

// Consultas para el dashboard del cliente
try {
    // 0. Pedidos para ser aprobados por el cliente (NUEVO)
    $sql_aprobacion_cliente = "SELECT COUNT(*) as aprobacion_cliente FROM pedidos WHERE Estado_Pedido = 'Verificado' AND Cedula_Cliente = :cedula";
    $stmt_aprobacion_cliente = $db->prepare($sql_aprobacion_cliente);
    $stmt_aprobacion_cliente->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_aprobacion_cliente->execute();
    $aprobacion_cliente_data = $stmt_aprobacion_cliente->fetch(PDO::FETCH_ASSOC);

    // 1. Pedidos en espera (pendientes) del cliente actual
    $sql_pendientes = "SELECT COUNT(*) as pendientes FROM pedidos WHERE Estado_Pedido = 'Pendiente' AND Cedula_Cliente = :cedula";
    $stmt_pendientes = $db->prepare($sql_pendientes);
    $stmt_pendientes->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_pendientes->execute();
    $pendientes_data = $stmt_pendientes->fetch(PDO::FETCH_ASSOC);

    // 2. Pedidos en Producción (nuevo estado 'Produccion')
    $sql_produccion = "SELECT COUNT(*) as produccion FROM pedidos WHERE Estado_Pedido = 'Produccion' AND Cedula_Cliente = :cedula";
    $stmt_produccion = $db->prepare($sql_produccion);
    $stmt_produccion->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_produccion->execute();
    $produccion_data = $stmt_produccion->fetch(PDO::FETCH_ASSOC);

    // 3. Pedidos finalizados del cliente actual
    $sql_finalizados = "SELECT COUNT(*) as finalizados FROM pedidos WHERE Estado_Pedido = 'Finalizado' AND Cedula_Cliente = :cedula";
    $stmt_finalizados = $db->prepare($sql_finalizados);
    $stmt_finalizados->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_finalizados->execute();
    $finalizados_data = $stmt_finalizados->fetch(PDO::FETCH_ASSOC);

    // 4. Total de pedidos del cliente actual
    $sql_total = "SELECT COUNT(*) as total_pedidos FROM pedidos WHERE Cedula_Cliente = :cedula";
    $stmt_total = $db->prepare($sql_total);
    $stmt_total->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_total->execute();
    $total_data = $stmt_total->fetch(PDO::FETCH_ASSOC);

    // 5. Pedidos rechazados del cliente actual
    $sql_rechazados = "SELECT COUNT(*) as rechazados FROM pedidos WHERE Estado_Pedido = 'Rechazado' AND Cedula_Cliente = :cedula";
    $stmt_rechazados = $db->prepare($sql_rechazados);
    $stmt_rechazados->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_rechazados->execute();
    $rechazados_data = $stmt_rechazados->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error en consultas del dashboard cliente: " . $e->getMessage());
    // Inicializar variables en caso de error
    $aprobacion_cliente_data = ['aprobacion_cliente' => 0]; // NUEVO
    $pendientes_data = ['pendientes' => 0];
    $aceptados_data = ['aceptados' => 0];
    $finalizados_data = ['finalizados' => 0];
    $total_data = ['total_pedidos' => 0];
    $rechazados_data = ['rechazados' => 0];
}


// ----------------------------------------------------
// LÓGICA GRÁFICA 1: PEDIDOS POR ESTADO
// ----------------------------------------------------
$grafica_estados_data = [
    'labels' => [],
    'data' => [],
    'colors' => []
];

// Asignación de colores
$color_map = [
    'Pendiente' => '#FFC107',         // Amarillo
    'Produccion' => '#0D6EFD',        // Azul
    'Revisión Cliente' => '#6F42C1',  // Morado
    'Finalizado' => '#198754',        // Verde
    'Rechazado' => '#DC3545',         // Rojo
];

// Llenamos los datos de la gráfica usando las variables de las tarjetas
// (Asegúrate de haber renombrado 'aceptados' a 'produccion' en la consulta)
if ($aprobacion_cliente_data['aprobacion_cliente'] > 0) {
    $grafica_estados_data['labels'][] = 'Revisado';
    $grafica_estados_data['data'][] = $aprobacion_cliente_data['aprobacion_cliente'];
    $grafica_estados_data['colors'][] = $color_map['Revisión Cliente'];
}
if ($pendientes_data['pendientes'] > 0) {
    $grafica_estados_data['labels'][] = 'Pendiente';
    $grafica_estados_data['data'][] = $pendientes_data['pendientes'];
    $grafica_estados_data['colors'][] = $color_map['Pendiente'];
}
if ($produccion_data['produccion'] > 0) {
    $grafica_estados_data['labels'][] = 'Produccion';
    $grafica_estados_data['data'][] = $produccion_data['produccion'];
    $grafica_estados_data['colors'][] = $color_map['Produccion'];
}
if ($finalizados_data['finalizados'] > 0) {
    $grafica_estados_data['labels'][] = 'Finalizado';
    $grafica_estados_data['data'][] = $finalizados_data['finalizados'];
    $grafica_estados_data['colors'][] = $color_map['Finalizado'];
}
if ($rechazados_data['rechazados'] > 0) {
    $grafica_estados_data['labels'][] = 'Rechazado';
    $grafica_estados_data['data'][] = $rechazados_data['rechazados'];
    $grafica_estados_data['colors'][] = $color_map['Rechazado'];
}
// Convertir los datos a JSON para JavaScript
$json_grafica_estados = json_encode($grafica_estados_data);

?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Bienvenido, <span id="welcomeUser"><?php echo $nombreSesion . " " . $ApellidoSesion; ?></span></h1>
            </div>
        </div>

        <div class="dashboard mb-5">
            <h3 class="text-light mb-4">Mis Pedidos</h3>

            <div class="row">

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-secondary text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Aprobar Diseño</h5>
                                    <h2 class="mb-0"><?php echo $aprobacion_cliente_data['aprobacion_cliente']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-thumbs-up fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Pendientes de su aprobación</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-warning text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">En Espera</h5>
                                    <h2 class="mb-0"><?php echo $pendientes_data['pendientes']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-clock fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Esperando aprobación</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-primary text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">En Producción</h5>
                                    <h2 class="mb-0"><?php echo $produccion_data['produccion']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-gears fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>En proceso de impresión</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-success text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Finalizados</h5>
                                    <h2 class="mb-0"><?php echo $finalizados_data['finalizados']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Listos para entregar</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-info text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Pedidos</h5>
                                    <h2 class="mb-0"><?php echo $total_data['total_pedidos']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-list fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Todos mis pedidos</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-danger text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Rechazados</h5>
                                    <h2 class="mb-0"><?php echo $rechazados_data['rechazados']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-xmark-circle fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Pedidos no aprobados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-lg-6 mx-auto">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Resumen de Mis Pedidos <span class="badge bg-primary"> <?php echo $total_data['total_pedidos']; ?> </span></h5>
                        </div>
                        <div class="card-body text-center">
                            <div style="position: relative; height: 350px;">
                                <canvas id="graficaPedidosCliente"></canvas>
                            </div>
                            <?php if (empty($grafica_estados_data['data'])): ?>
                                <p class="text-light mt-3">No tienes pedidos activos para mostrar.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <p></p>
    </div>
</main>

<?php
include('../plantilla/bot.php');
?>

<script>
    const FIXED_BACKGROUND_COLOR = '#112240';
    const FIXED_TEXT_COLOR = '#FFFFFF';

    const chartBackgroundPlugin = {
        id: 'customCanvasBackgroundColor',
        beforeDraw: (chart, args, options) => {
            const {
                ctx
            } = chart;
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = options.backgroundColor || '#ffffff';
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        }
    };
    document.addEventListener('DOMContentLoaded', function() {
        // Inyectamos el JSON de la Gráfica 1
        const data_estados = <?php echo $json_grafica_estados; ?>;

        if (data_estados.data.length > 0) {
            const ctxEstados = document.getElementById('graficaPedidosCliente').getContext('2d');
            new Chart(ctxEstados, {
                type: 'doughnut',
                data: {
                    labels: data_estados.labels,
                    datasets: [{
                        data: data_estados.data,
                        backgroundColor: data_estados.colors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        // A. Aplica el fondo fijo
                        customCanvasBackgroundColor: {
                            backgroundColor: FIXED_BACKGROUND_COLOR,
                        },
                        legend: {
                            position: 'right',
                            labels: {
                                color: FIXED_TEXT_COLOR,
                                boxWidth: 15
                            }
                        },
                        title: {
                            display: true,
                            text: 'Mis Pedidos por Estado',
                            color: FIXED_TEXT_COLOR
                        }
                    }
                },
                plugins: [chartBackgroundPlugin]
            });
        }
        
    });
</script>


<?php
if (isset($_SESSION['mensaje_sesion'])) {
    $respuesta = $_SESSION['mensaje_sesion'];
    unset($_SESSION['mensaje_sesion']);
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
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php
} ?>

<?php
if (isset($_SESSION['PedidoCreado'])) {
    $respuesta = $_SESSION['PedidoCreado'];
    unset($_SESSION['PedidoCreado']);
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
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php
}
?>