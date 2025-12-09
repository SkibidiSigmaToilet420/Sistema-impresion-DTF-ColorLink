<?php
include('../model/conexion.php');
include('../plantilla/topEmpleados.php');
include('../plantilla/paginacion.php');

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

$filtroEstado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : '';
$filtroFecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : '';

$baseQuery = "FROM pedidos p INNER JOIN usuarios cli ON p.Cedula_Cliente = cli.Cedula LEFT JOIN usuarios emp ON p.Empleado_Encargado = emp.Cedula INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material WHERE p.Empleado_Encargado = :Empleado_Encargado AND p.Estado_Pedido IN ('Rechazado', 'Finalizado')";
if ($q !== '') {
    $baseQuery .= " AND (cli.Nombre LIKE :q OR cli.Apellido LIKE :q OR p.Id_pedido LIKE :q)";
}

if ($filtroEstado !== '') {
    $baseQuery .= " AND p.Estado_Pedido = :estado";
}

if ($filtroFecha !== '') {
    $baseQuery .= " AND DATE(p.Fecha_Solicitud) = :fecha";
}

$countStmt = $db->prepare("SELECT COUNT(*) as total " . $baseQuery);
$countStmt->bindValue(':Empleado_Encargado', $CedulaSesion);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
if ($filtroEstado !== '') $countStmt->bindValue(':estado', $filtroEstado, PDO::PARAM_STR);
if ($filtroFecha !== '') $countStmt->bindValue(':fecha', $filtroFecha, PDO::PARAM_STR); // Correcto
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

$sql = "SELECT p.Id_pedido, p.Estado_Pedido, p.Centimetros, p.Cantidades, p.Costo, p.Fecha_Solicitud, p.Fecha_Entrega, CONCAT(cli.Nombre, ' ', cli.Apellido, ' ', p.Cedula_Cliente) as ClientePedido, CASE WHEN p.Empleado_Encargado IS NULL THEN NULL ELSE CONCAT(emp.Nombre,' ',emp.Apellido,' ',p.Empleado_Encargado) END as EmpleadoPedido, CONCAT(m.Nombre_material,' ',m.Precio_CM,' bs/c')AS ELMATERIAL " . $baseQuery . " ORDER BY p.Fecha_Solicitud ASC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':Empleado_Encargado', $CedulaSesion);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
if ($filtroEstado !== '') $stmt->bindValue(':estado', $filtroEstado, PDO::PARAM_STR);
if ($filtroFecha !== '') $stmt->bindValue(':fecha', $filtroFecha, PDO::PARAM_STR);

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$Pedidos = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Historial de pedidos</h1>

                <div class="d-flex align-items-center">
                    <!-- Filtro por estado -->
                    <form class="d-flex me-2" method="GET" action="historialPedidos.php">
                        <select name="estado" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="Finalizado" <?php echo $filtroEstado === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                            <option value="Rechazado" <?php echo $filtroEstado === 'Rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                        </select>

                        <?php if ($q !== ''): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <?php endif; ?>
                    </form>

                    <!-- Filtro por fecha -->
                    <form class="d-flex me-2" method="GET" action="historialPedidos.php">
                        <input type="date" name="fecha" class="form-control form-control-sm me-2" value="<?php echo htmlspecialchars($filtroFecha); ?>" onchange="this.form.submit()">

                        <?php if ($q !== ''): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <?php endif; ?>
                        <?php if ($filtroEstado !== ''): ?><input type="hidden" name="estado" value="<?php echo htmlspecialchars($filtroEstado); ?>">
                        <?php endif; ?>
                    </form>

                    <form class="d-flex" method="GET" action="historialPedidos.php">
                        <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar cliente o pedido" value="<?php echo htmlspecialchars($q); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>


            </div>

            <p class="">Desde aqui podrás visualizar los pedidos que te han asignado en su estado final</p>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-print"></i> Lista de pedidos en producción<?php if (count($Pedidos) == 0) {
                                                                                                        echo ', (No ha finalizado ningun pedido todavia)';
                                                                                                    } ?></p>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark">
                        <thead>
                            <tr>
                                <th scope="col">N° Pedido</th>
                                <th scope="col">Fecha de solictud</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Material</th>
                                <th scope="col">Centimetros</th>
                                <th scope="col">N° Diseños</th>
                                <th scope="col">Fecha de entrega</th>
                                <th scope="col">Total Bs.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($Pedidos as $Pedido) { ?>
                                <tr>
                                    <td><?php echo $Pedido->Id_pedido ?></td>
                                    <td><?php echo $Pedido->Fecha_Solicitud ?></td>
                                    <td><span class="<?php
                                                        switch ($Pedido->Estado_Pedido) {
                                                            case 'Rechazado':
                                                                echo ('badge bg-danger');
                                                                break;
                                                            case 'Finalizado':
                                                                echo ('badge bg-success');
                                                                break;
                                                        };
                                                        ?>"><?php echo $Pedido->Estado_Pedido ?></span></td>
                                    <td><?php echo $Pedido->ClientePedido ?></td>
                                    <td><?php echo $Pedido->ELMATERIAL ?></td>
                                    <td><?php echo $Pedido->Centimetros ?></td>
                                    <td><?php echo $Pedido->Cantidades ?></td>
                                    <td>
                                        <?php echo $Pedido->Fecha_Entrega ?>

                                        <?php if ($Pedido->Estado_Pedido == 'Finalizado') { ?>
                                            <button onclick="editarRechazo(<?php echo $Pedido->Id_pedido ?>)" class="btn btn-sm btn-warning ms-2" title="Editar fecha/motivo">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        <?php }; ?>

                                    </td>
                                    <td><?php echo $Pedido->Costo ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <?php echo renderPagination($total, $perPage, $page, ['q' => $q]); ?>
        </div>
    </div>
</main>

<?php include('../plantilla/bot.php'); ?>

<script>
    function confirmarFinalizar(idpedido) {
        Swal.fire({
            title: "¿Está seguro de que desea finalizar este pedido?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#56b321ff",
            confirmButtonText: "Si, finalizar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                window.location.href = "<?php echo $URL ?>empleados/controles/Control_FinalizarPedido.php?Codigo=" + idpedido;
            }
        });
    }
</script>

<script>
    function editarRechazo(idpedido) {
        const today = new Date().toISOString().split('T')[0];
        Swal.fire({
            title: 'Ingrese la fecha y el motivo de la postergación',
            html: `<input type="date" id="swalFecha" class="swal2-input" min="${today}">` +
                `<textarea id="swalMotivo" class="swal2-textarea" placeholder="Motivo de la postergación"></textarea>`,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                const fecha = document.getElementById('swalFecha').value;
                const motivo = document.getElementById('swalMotivo').value.trim();
                if (!fecha) {
                    Swal.showValidationMessage('Por favor ingrese una fecha');
                    return false;
                }
                if (fecha < today) {
                    Swal.showValidationMessage('La fecha no puede ser menor a la fecha actual');
                    return false;
                }
                if (!motivo) {
                    Swal.showValidationMessage('Por favor ingrese un motivo');
                    return false;
                }
                return {
                    fecha,
                    motivo
                };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const fd = new FormData();
                fd.append('Codigo', idpedido);
                fd.append('fecha', result.value.fecha);
                fd.append('motivo', result.value.motivo);
                fetch('<?php echo $URL; ?>empleados/controles/EnviarMotivoRechazo.php', {
                    method: 'POST',
                    body: fd
                }).then(res => {
                    if (!res.ok) {
                        return res.text().then(text => {
                            throw new Error('HTTP ' + res.status + ' - ' + text);
                        });
                    }
                    // Intentar parsear JSON; si falla, mostrar el texto crudo
                    return res.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Respuesta inválida del servidor: ' + text);
                        }
                    });
                }).then(data => {
                    if (data.success) {
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
                            title: 'Enviado',
                            text: data.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo enviar el correo.'
                        });
                    }
                }).catch(err => {
                    console.error('Fetch error:', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.message || 'Error de red al enviar la solicitud.'
                    });
                });
            }
        });
    }
</script>

<?php
if (isset($_SESSION['EstadoPedido'])) {
    $respuesta = $_SESSION['EstadoPedido'];
    unset($_SESSION['EstadoPedido']);
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
            title: 'Pedido finalizado!',
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php
}
