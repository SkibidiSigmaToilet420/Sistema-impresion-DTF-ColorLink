<?php
include ('../model/conexion.php');
$contadorSesion = 0;


$usuarios_data = [
    'total_usuarios' => 0,
    'administradores' => 0,
    'empleados' => 0,
    'clientes' => 0
];
$pendientes_data = ['pendientes' => 0];
$hoy_data = ['finalizados_hoy' => 0];
$mes_data = ['finalizados_mes' => 0];

try {
    $sql_usuarios = "SELECT 
        COUNT(*) as total_usuarios,
        SUM(u.Tipo_Usuario = 3) as administradores,
        SUM(u.Tipo_Usuario = 2) as empleados,
        SUM(u.Tipo_Usuario = 1) as clientes
    FROM usuarios u 
    WHERE u.Estado = 'ACTIVO'";

    $result_usuarios = $db->query($sql_usuarios);
    if ($result_usuarios) {
        $usuarios_data = $result_usuarios->fetch(PDO::FETCH_ASSOC);
    }

    $sql_pedidos_pendientes = "SELECT COUNT(*) as pendientes FROM pedidos WHERE Estado_Pedido = 'Pendiente'";
    $result_pendientes = $db->query($sql_pedidos_pendientes);
    if ($result_pendientes) {
        $pendientes_data = $result_pendientes->fetch(PDO::FETCH_ASSOC);
    }

    $sql_pedidos_hoy = "SELECT COUNT(*) as finalizados_hoy FROM pedidos WHERE Estado_Pedido = 'Finalizado' AND DATE(Fecha_Entrega) = CURDATE()";
    $result_hoy = $db->query($sql_pedidos_hoy);
    if ($result_hoy) {
        $hoy_data = $result_hoy->fetch(PDO::FETCH_ASSOC);
    }

    $sql_pedidos_mes = "SELECT COUNT(*) as finalizados_mes FROM pedidos WHERE Estado_Pedido = 'Finalizado' AND MONTH(Fecha_Entrega) = MONTH(CURDATE()) AND YEAR(Fecha_Entrega) = YEAR(CURDATE())";
    $result_mes = $db->query($sql_pedidos_mes);
    if ($result_mes) {
        $mes_data = $result_mes->fetch(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error en consultas del dashboard: " . $e->getMessage());
}

include '../plantilla/topAdmin.php';
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Bienvenido, <span id="welcomeUser"><?php echo $nombreSesion . " " . $ApellidoSesion; ?></span></h1>
            </div>
        </div>

        <div class="dashboard mb-5">
            <h3 class="text-light mb-4">Dashboard de Estadísticas</h3>

            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card bg-primary text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Total Usuarios</h5>
                                    <h2 class="mb-0"><?php echo $usuarios_data['total_usuarios']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-users fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>
                                    <i class="fa-solid fa-user-shield text-danger"></i> <span class="text-danger">Admin:</span> <?php echo $usuarios_data['administradores']; ?><br>
                                    <i class="fa-solid fa-user-tie text-primary"></i> <span class="text-primary">Empleados:</span> <?php echo $usuarios_data['empleados']; ?><br>
                                    <i class="fa-solid fa-user text-success"></i> <span class="text-success">Clientes:</span> <?php echo $usuarios_data['clientes']; ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card bg-warning text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Pedidos Pendientes</h5>
                                    <h2 class="mb-0"><?php echo $pendientes_data['pendientes']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa-solid fa-clock fa-2x"></i>
                                </div>
                            </div>
                            <div class="mt-3">
                                <small>Requieren atención inmediata</small>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6 col-lg-3 mb-4">
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


                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card bg-info text-light h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">Finalizados en el Mes</h5>
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
            </div>

            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">
                                Total de Pedidos:
                                <span id="totalPedidosAdmin" class="badge bg-primary rounded-pill">0</span>
                            </h5>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="graficaEstados" style="max-height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Existencia de Materiales (Inventario)</h5>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="graficaMateriales" style="max-height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5 d-flex align-items-stretch">
                <div class="col-lg-6">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Producción Mensual (Centímetros DTF)</h5>
                        </div>
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <canvas id="graficaProduccion" style="max-height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Ingresos Mensuales (Bs)</h5>
                        </div>
                        <div class="card-body text-center">
                            <div style="position: relative; height: 400px;">
                                <canvas id="graficaIngresos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5 d-flex align-items-stretch">
                <div class="col-lg-12">
                    <div class="card bg-dark-blue-light text-light h-100">
                        <div class="card-header border-secondary">
                            <h5 class="card-title mb-0">Carga de Trabajo por Empleado (Pedidos Activos)</h5>
                        </div>
                        <div class="card-body text-center">
                            <div style="position: relative; height: 450px;">
                                <canvas id="graficaEmpleadosCarga"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Grafica estados pedidos -->
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

        const estadoColores = {
            'Pendiente': '#FFC107',
            'Rechazado': '#DC3545',
            'Produccion': '#0D6EFD',
            'Finalizado': '#198754',
            'Verificado': '#6F42C1'
        };

        // 1. Obtener los datos del script PHP
        fetch('Graficas/datos_estados.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {

                // 🚀 NUEVO: CALCULAR EL TOTAL DE PEDIDOS SUMANDO TODOS LOS ESTADOS
                const totalPedidos = data.data.reduce((sum, current) => sum + current, 0);

                // 🚀 NUEVO: MOSTRAR EL TOTAL EN EL SPAN
                const totalElement = document.getElementById('totalPedidosAdmin');
                if (totalElement) {
                    totalElement.textContent = totalPedidos;
                }

                // ... (Lógica de colores de fondo, se mantiene igual)
                const colors = data.labels.map(label => estadoColores[label] || '#6C757D');
                // ...

                const ctx = document.getElementById('graficaEstados').getContext('2d');

                var graficaEstadosChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Número de Pedidos',
                            data: data.data,
                            backgroundColor: colors,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            customCanvasBackgroundColor: {
                                backgroundColor: FIXED_BACKGROUND_COLOR,
                            },
                            legend: {
                                position: 'top',
                                labels: {
                                    color: FIXED_TEXT_COLOR
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed;
                                        }
                                        return label;
                                    }
                                }
                            }
                        }
                    },
                    plugins: [chartBackgroundPlugin]
                });

                window.activeCharts = window.activeCharts || [];
                window.activeCharts.push(graficaEstadosChart);
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de estados:', error);
                // Manejar error en el elemento de total
                const totalElement = document.getElementById('totalPedidosAdmin');
                if (totalElement) {
                    totalElement.textContent = 'Error';
                    totalElement.className = 'badge bg-danger rounded-pill';
                }
                document.getElementById('graficaEstados').parentNode.innerHTML = '<p class="text-danger">Error al cargar la gráfica. Verifique el archivo datos_estados.php.</p>';
            });
    });
</script>

<!-- Grafica de los materiales -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Obtener los datos del script PHP de materiales
        // NOTA: La ruta debe ser relativa desde la ubicación del dashboard (ej: admin/)
        fetch('Graficas/datos_materiales.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const ctxMateriales = document.getElementById('graficaMateriales').getContext('2d');

                var graficaMaterialesChart = new Chart(ctxMateriales, {
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
                                color: 'white'
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
                // Guardar instancia para actualización dinámica de colores
                window.activeCharts = window.activeCharts || [];
                window.activeCharts.push(graficaMaterialesChart);
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de materiales:', error);
                document.getElementById('graficaMateriales').innerHTML = '<p class="text-danger">Error al cargar la gráfica de materiales.</p>';
            });
    });
</script>

<!-- Grafica producción de centimetros mensuales -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Obtener los datos del script PHP de producción
        fetch('Graficas/datos_produccion_mensual.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const ctxProduccion = document.getElementById('graficaProduccion').getContext('2d');

                var graficaProduccionChart = new Chart(ctxProduccion, {
                    type: 'bar', // Tipo de Gráfico de Barras
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Centímetros Impresos',
                            data: data.data,
                            backgroundColor: 'rgba(255, 99, 132, 0.7)', // Color rojo suave
                            borderColor: 'rgb(255, 99, 132)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Volumen de Producción (últimos 12 meses)',
                                color: 'white'
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: FIXED_TEXT_COLOR,
                                    // NUEVO: Rota las etiquetas 45 grados si hay muchas.
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },

                            y: {
                                ticks: {
                                    color: FIXED_TEXT_COLOR,
                                    // NUEVO: Rota las etiquetas 45 grados si hay muchas.
                                    maxRotation: 45,
                                    minRotation: 45
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
                // Guardar instancia para actualización dinámica de colores
                window.activeCharts = window.activeCharts || [];
                window.activeCharts.push(graficaProduccionChart);
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de producción mensual:', error);
                document.getElementById('graficaProduccion').innerHTML = '<p class="text-danger">Error al cargar la gráfica de producción.</p>';
            });
    });
</script>

<!-- Grafica ingresos mensuales -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Obtener los datos del script PHP de ingresos
        fetch('Graficas/datos_ingresos_mensuales.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const ctxIngresos = document.getElementById('graficaIngresos').getContext('2d');

                var graficaIngresosChart = new Chart(ctxIngresos, {
                    type: 'line', // Gráfico de Líneas para tendencia
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Ingresos Totales (Bs)',
                            data: data.data,
                            backgroundColor: 'rgba(25, 135, 84, 0.2)', // Verde Suave
                            borderColor: '#198754', // Verde Fuerte
                            borderWidth: 3,
                            tension: 0.3, // Curvas suaves
                            fill: true, // Rellenar área bajo la línea
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Tendencia de Ingresos (últimos 6 meses)',
                                color: 'white'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Ingresos (Bs)',
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
                                    color: 'white',
                                    maxRotation: 45,
                                    minRotation: 45
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
                // Guardar instancia para actualización dinámica de colores
                window.activeCharts = window.activeCharts || [];
                window.activeCharts.push(graficaIngresosChart);
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de ingresos:', error);
                document.getElementById('graficaIngresos').innerHTML = '<p class="text-danger">Error al cargar la gráfica de ingresos.</p>';
            });
    });
</script>

<!-- Grafica carga de trabajo por empleado -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Obtener los datos del script PHP de carga de empleados
        fetch('Graficas/datos_empleados_carga.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const ctxEmpleados = document.getElementById('graficaEmpleadosCarga').getContext('2d');

                var graficaEmpleadosCargaChart = new Chart(ctxEmpleados, {
                    type: 'bar', // Gráfico de Barras
                    data: {
                        labels: data.labels, // Nombres de los empleados
                        datasets: data.datasets // Series de datos por estado
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true, // ¡Clave para que las barras se apilen!
                                ticks: {
                                    color: 'white',
                                    maxRotation: 45,
                                    minRotation: 45
                                },
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                }
                            },
                            y: {
                                stacked: true, // ¡Clave para que las barras se apilen!
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Pedidos',
                                    color: 'white'
                                },
                                ticks: {
                                    color: 'white',
                                    precision: 0 // Mostrar solo números enteros
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
                                position: 'top',
                                labels: {
                                    color: FIXED_TEXT_COLOR
                                }
                            },
                            title: {
                                display: true,
                                text: 'Pedidos Activos Asignados por Estado',
                                color: FIXED_BACKGROUND_COLOR
                            }
                        }
                    },
                    plugins: [chartBackgroundPlugin]
                });
                // Guardar instancia para actualización dinámica de colores
                window.activeCharts = window.activeCharts || [];
                window.activeCharts.push(graficaEmpleadosCargaChart);
            })
            .catch(error => {
                console.error('Error al cargar la gráfica de empleados:', error);
                document.getElementById('graficaEmpleadosCarga').innerHTML = '<p class="text-danger">Error al cargar la gráfica de carga de empleados.</p>';
            });
    });
</script>


<?php include '../plantilla/bot.php'; ?>



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
