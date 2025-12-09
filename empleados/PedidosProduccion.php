<?php
include('../model/conexion.php');
include('../plantilla/topEmpleados.php');
include('../plantilla/paginacion.php');

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

$filtroFecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : '';

$baseQuery = "FROM pedidos p INNER JOIN usuarios cli ON p.Cedula_Cliente = cli.Cedula LEFT JOIN usuarios emp ON p.Empleado_Encargado = emp.Cedula INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material WHERE p.Empleado_Encargado = :Empleado_Encargado AND p.Estado_Pedido = 'Produccion'";
if ($q !== '') {
    $baseQuery .= " AND (cli.Nombre LIKE :q OR cli.Apellido LIKE :q OR p.Id_pedido LIKE :q)";
}

if ($filtroFecha !== '') {
    $baseQuery .= " AND DATE(p.Fecha_Solicitud) = :fecha";
}

$countStmt = $db->prepare("SELECT COUNT(*) as total " . $baseQuery);
$countStmt->bindValue(':Empleado_Encargado', $CedulaSesion);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
if ($filtroFecha !== '') $countStmt->bindValue(':fecha', $filtroFecha, PDO::PARAM_STR); // Correcto
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

$sql = "SELECT p.Id_pedido, p.Estado_Pedido, p.Centimetros, p.Cantidades, p.Costo, p.Fecha_Solicitud, p.Fecha_Entrega, CONCAT(cli.Nombre, ' ', cli.Apellido, ' ', p.Cedula_Cliente) as ClientePedido, CASE WHEN p.Empleado_Encargado IS NULL THEN NULL ELSE CONCAT(emp.Nombre,' ',emp.Apellido,' ',p.Empleado_Encargado) END as EmpleadoPedido, CONCAT(m.Nombre_material,' ',m.Precio_CM,' bs/c')AS ELMATERIAL " . $baseQuery . " ORDER BY p.Fecha_Solicitud ASC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':Empleado_Encargado', $CedulaSesion);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
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
                <h1 class="h2 text-light">Pedidos en producción</h1>
                <div class="d-flex align-items-center">
                    <!-- Filtro por fecha -->
                    <form class="d-flex me-2" method="GET" action="PedidosProduccion.php">
                        <input type="date" name="fecha" class="form-control form-control-sm me-2" value="<?php echo htmlspecialchars($filtroFecha); ?>" onchange="this.form.submit()">

                        <?php if ($q !== ''): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <?php endif; ?>
                    </form>

                    <form class="d-flex" method="GET" action="PedidosProduccion.php">
                        <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar cliente o pedido" value="<?php echo htmlspecialchars($q); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>


                </div>
            </div>
            <p class="">Desde aqui podrás verificar los pedidos en producción y marcarlos como finalizados</p>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-print"></i> Lista de pedidos en producción<?php if (count($Pedidos) == 0) {
                                                                                                        echo ', (No hay pedidos en producción actualmente)';
                                                                                                    } ?></p>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark">
                        <thead>
                            <tr>
                                <th scope="col">N° del pedido</th>
                                <th scope="col">Fecha de solictud</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Material</th>
                                <th scope="col">Centimetros</th>
                                <th scope="col">N° Diseños</th>
                                <th scope="col">Total Bs.</th>
                                <th scope="col">Fecha de entrega</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($Pedidos as $Pedido) { ?>
                                <tr>
                                    <td><?php echo $Pedido->Id_pedido ?></td>
                                    <td><?php echo $Pedido->Fecha_Solicitud ?></td>
                                    <td><span class="badge bg-primary"><?php echo $Pedido->Estado_Pedido ?></span></td>
                                    <td><?php echo $Pedido->ClientePedido ?></td>
                                    <td><?php echo $Pedido->ELMATERIAL ?></td>
                                    <td><?php echo $Pedido->Centimetros ?></td>
                                    <td><?php echo $Pedido->Cantidades ?></td>
                                    <td><?php echo $Pedido->Costo ?></td>
                                    <td><?php echo $Pedido->Fecha_Entrega ?></td>
                                    <td class="text-center align-middle">
                                        <button onclick="confirmarFinalizar(<?php echo $Pedido->Id_pedido ?>)" class="btn btn-sm btn-success" style="white-space: nowrap;">
                                            <i class="fa-solid fa-check"></i> Finalizar pedido
                                        </button>
                                    </td>
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
            text: "El pedido pasará a estado 'Finalizado' y se notificará al cliente.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754", // Verde (Success)
            cancelButtonColor: "#6c757d", // Gris
            confirmButtonText: "Sí, finalizar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                // Ya no enviamos fecha, solo el código del pedido
                window.location.href = "<?php echo $URL ?>empleados/controles/Control_FinalizarPedido.php?Codigo=" + idpedido;
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
