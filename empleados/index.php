<?php
include('../model/conexion.php');
include('../plantilla/topEmpleados.php');

if (isset($_SESSION['incio_sesion'])) {
} else {
    header("location:" . $URL . "login.php");
}

try {
    $sqlPendienteNoempleado = "SELECT COUNT(*) as pendientesNoempleado FROM pedidos WHERE Estado_Pedido='Pendiente' AND Empleado_Encargado IS NULL;";
    $stmt_Pendientes_Noempleado = $db->prepare($sqlPendienteNoempleado);
    $stmt_Pendientes_Noempleado->execute();
    $pendiente_Noempleados_data = $stmt_Pendientes_Noempleado->fetch(PDO::FETCH_ASSOC);

    $sql_pendientes = "SELECT COUNT(*) as pendientes FROM pedidos WHERE Estado_Pedido = 'Pendiente' AND Empleado_Encargado = :cedula";
    $stmt_pendientes = $db->prepare($sql_pendientes);
    $stmt_pendientes->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_pendientes->execute();
    $pendientes_data = $stmt_pendientes->fetch(PDO::FETCH_ASSOC);

    $sql_finalizados_hoy = "SELECT COUNT(*) as finalizados_hoy FROM pedidos WHERE Estado_Pedido = 'Finalizado' AND Empleado_Encargado = :cedula AND DATE(Fecha_Entrega) = CURDATE()";
    $stmt_hoy = $db->prepare($sql_finalizados_hoy);
    $stmt_hoy->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_hoy->execute();
    $hoy_data = $stmt_hoy->fetch(PDO::FETCH_ASSOC);

    $sql_finalizados_mes = "SELECT COUNT(*) as finalizados_mes FROM pedidos WHERE Estado_Pedido = 'Finalizado' AND Empleado_Encargado = :cedula AND MONTH(Fecha_Entrega) = MONTH(CURDATE()) AND YEAR(Fecha_Entrega) = YEAR(CURDATE())";
    $stmt_mes = $db->prepare($sql_finalizados_mes);
    $stmt_mes->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_mes->execute();
    $mes_data = $stmt_mes->fetch(PDO::FETCH_ASSOC);

    $sql_aceptados = "SELECT COUNT(*) as aceptados FROM pedidos WHERE Estado_Pedido = 'Produccion' AND Empleado_Encargado = :cedula";
    $stmt_aceptados = $db->prepare($sql_aceptados);
    $stmt_aceptados->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_aceptados->execute();
    $aceptados_data = $stmt_aceptados->fetch(PDO::FETCH_ASSOC);

    $sql_total_finalizados = "SELECT COUNT(*) as total_finalizados FROM pedidos WHERE Estado_Pedido = 'Finalizado' AND Empleado_Encargado = :cedula";
    $stmt_total_finalizados = $db->prepare($sql_total_finalizados);
    $stmt_total_finalizados->bindValue(':cedula', $CedulaSesion, PDO::PARAM_STR);
    $stmt_total_finalizados->execute();
    $total_finalizados_data = $stmt_total_finalizados->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error en consultas del dashboard empleado: " . $e->getMessage());
    $pendiente_Noempleados_data = ['pendientesNoempleado' => 0];
    $pendientes_data = ['pendientes' => 0];
    $hoy_data = ['finalizados_hoy' => 0];
    $mes_data = ['finalizados_mes' => 0];
    $aceptados_data = ['aceptados' => 0];
    $total_finalizados_data = ['total_finalizados' => 0];
}
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Bienvenido, <span id="welcomeUser"><?php echo $nombreSesion . " " . $ApellidoSesion; ?></span></h1>
            </div>
        </div>

        <div class="dashboard mb-5">
            <h3 class="text-light mb-4">Mis Estadísticas de Pedidos</h3>

            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-warning text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Pendientes</h5>
                                    <h2 class="mb-0"><?php echo $pendiente_Noempleados_data['pendientesNoempleado']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-clock fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Requieren que se le asigne un empleado</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-warning text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Asignados</h5>
                                    <h2 class="mb-0"><?php echo $pendientes_data['pendientes']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-list-check fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Requieren verifcar su diseño</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-primary text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Producción</h5>
                                    <h2 class="mb-0"><?php echo $aceptados_data['aceptados']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-print fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>En proceso de producción</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-success text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Entregados Hoy</h5>
                                    <h2 class="mb-0"><?php echo $hoy_data['finalizados_hoy']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-calendar-day fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Pedidos entregados el día de hoy</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-info text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Finalizados Mes</h5>
                                    <h2 class="mb-0"><?php echo $mes_data['finalizados_mes']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-calendar fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Completados este mes</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card bg-dark text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Finalizados</h5>
                                    <h2 class="mb-0"><?php echo $total_finalizados_data['total_finalizados']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-trophy fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Total de pedidos completados</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Existencia de Materiales (Inventario)</h5>
                        </div>
                        <div class="card-body text-center">
                            <canvas id="graficaMateriales"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Total de Pedidos Encargados: <span id="totalPedidosAsignados" class="badge bg-primary">Cargando...</span></h5>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="graficaPedidosEmpleado" style="max-height: 400px;"></canvas>
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

<!-- Grafica de los materiales -->
<script>
    // --------------------------------------------------------------------------------
    // 1. CONSTANTES Y PLUGIN (Mantenemos la solución de color fijo)
    // --------------------------------------------------------------------------------
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
        // 1. Obtener los datos del script PHP de materiales
        // NOTA: La ruta debe ser relativa desde la ubicación del dashboard (ej: admin/)
        fetch('../empleados/graficas/datos_material.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const ctxMateriales = document.getElementById('graficaMateriales').getContext('2d');

                new Chart(ctxMateriales, {
                    type: 'bar', // Gráfico de Barras
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Cantidad Disponible',
                            data: data.data,
                            backgroundColor: '#17A2B8', // Color azul-celeste para las barras
                            borderColor: '#007BFF',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false // Ocultar leyenda para gráficos de una sola serie
                            },
                            title: {
                                display: true,
                                text: 'Inventario por Tipo de Material en Centimetros',
                                color: FIXED_TEXT_COLOR
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Cantidad (Unidades/Cm/M)',
                                    color: FIXED_TEXT_COLOR
                                },
                                ticks: {
                                    color: FIXED_TEXT_COLOR
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            x: {
                                ticks: {
                                    color: FIXED_TEXT_COLOR
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            }
                        },
                        plugins: {
                            // 3. Aplica el fondo fijo
                            customCanvasBackgroundColor: {
                                backgroundColor: FIXED_BACKGROUND_COLOR,
                            },
                            legend: {
                                labels: {
                                    // 4. Color de la Leyenda
                                    color: FIXED_TEXT_COLOR
                                }
                            },
                        }
                    },
                    plugins: [chartBackgroundPlugin]
                });
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de materiales:', error);
                document.getElementById('graficaMateriales').innerHTML = '<p class="text-danger">Error al cargar la gráfica de materiales.</p>';
            });
    });
</script>

<!-- Grafica estado pedido empleados -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const estadoColores = {
            'Pendiente': '#FFC107',
            'Rechazado': '#DC3545',
            'Produccion': '#0D6EFD',
            'Finalizado': '#198754',
            'Verificado': '#6F42C1'
        };

        fetch('../empleados/graficas/datos_pedidos.php')
            .then(response => {
                // ... (Código para verificar respuesta OK)
                if (!response.ok) {
                    throw new Error('Network response was not ok. Status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {

                // 🚀 NUEVO: MOSTRAR EL TOTAL DE PEDIDOS
                const totalElement = document.getElementById('totalPedidosAsignados');
                if (totalElement) {
                    totalElement.textContent = data.total_pedidos;
                }

                if (data.data.length === 0) {
                    const parent = document.getElementById('graficaPedidosEmpleado').parentNode.parentNode; // Ajustar la referencia si usaste el HTML sugerido
                    parent.innerHTML = '<p class="text-light mt-3">No tienes pedidos asignados actualmente o no eres Empleado.</p>';
                    return;
                }

                const ctxEmpleado = document.getElementById('graficaPedidosEmpleado').getContext('2d');
                const backgroundColors = data.labels.map(label => estadoColores[label] || '#CCCCCC');

                // ... (Inicialización de la gráfica con Chart.js)
                new Chart(ctxEmpleado, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: data.data,
                            backgroundColor: backgroundColors,
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
                                text: 'Distribución de Mis Pedidos',
                                color: FIXED_TEXT_COLOR
                            }
                        }
                    },
                    plugins: [chartBackgroundPlugin]
                });
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de pedidos del empleado:', error);
                // Si falla, también puedes actualizar el elemento del total
                const totalElement = document.getElementById('totalPedidosAsignados');
                if (totalElement) {
                    totalElement.textContent = 'Error';
                    totalElement.className = 'badge bg-danger';
                }
                document.getElementById('graficaPedidosEmpleado').parentNode.innerHTML = '<p class="text-danger">Error al cargar la gráfica de trabajo. Verifique la sesión y las rutas.</p>';
            });
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
}
