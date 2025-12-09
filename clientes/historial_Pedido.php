<?php include '../model/conexion.php'; ?>
<?php include '../plantilla/sesion.php'; ?>
<?php include '../plantilla/topClientes.php';
include '../plantilla/paginacion.php';

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

$filtroEstado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : '';
$filtroEmpleado = isset($_GET['empleado']) && $_GET['empleado'] !== '' ? $_GET['empleado'] : '';
$filtroFecha = isset($_GET['fecha']) && $_GET['fecha'] !== '' ? $_GET['fecha'] : '';

$baseQuery = "FROM pedidos p INNER JOIN usuarios cli ON p.Cedula_Cliente = cli.Cedula LEFT JOIN usuarios emp ON p.Empleado_Encargado = emp.Cedula INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material WHERE p.Cedula_Cliente = :Cedula_Cliente";
if ($q !== '') {
    $baseQuery .= " AND (p.Id_pedido LIKE :q OR p.Estado_Pedido LIKE :q)";
}
if ($filtroEstado !== '') {
    $baseQuery .= " AND p.Estado_Pedido = :estado";
}

if ($filtroEmpleado !== '') {
    $baseQuery .= " AND emp.Cedula = :empleado";
}

if ($filtroFecha !== '') {
    $baseQuery .= " AND DATE(p.Fecha_Solicitud) = :fecha";
}

$countStmt = $db->prepare("SELECT COUNT(*) as total " . $baseQuery);
$countStmt->bindValue(':Cedula_Cliente', $CedulaSesion);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
if ($filtroEstado !== '') $countStmt->bindValue(':estado', $filtroEstado, PDO::PARAM_STR);
if ($filtroEmpleado !== '') $countStmt->bindValue(':empleado', $filtroEmpleado,   PDO::PARAM_STR);
if ($filtroFecha !== '') $countStmt->bindValue(':fecha', $filtroFecha, PDO::PARAM_STR); // Correcto
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

$sql = "SELECT p.Id_pedido, p.Estado_Pedido, p.Centimetros, p.Cantidades, p.Costo, p.Fecha_Solicitud, p.Fecha_Entrega, CONCAT(cli.Nombre, ' ', cli.Apellido, ' C.I:', p.Cedula_Cliente) as ClientePedido, CASE WHEN p.Empleado_Encargado IS NULL THEN NULL ELSE CONCAT(emp.Nombre,' ',emp.Apellido,' ',p.Empleado_Encargado) END as EmpleadoPedido, CONCAT(m.Nombre_material,' ',m.Precio_CM,' bs/c')AS ELMATERIAL " . $baseQuery . " ORDER BY p.Fecha_Solicitud DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':Cedula_Cliente', $CedulaSesion);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
if ($filtroEstado !== '') $stmt->bindValue(':estado', $filtroEstado, PDO::PARAM_STR);
if ($filtroEmpleado !== '') $stmt->bindValue(':empleado', $filtroEmpleado, PDO::PARAM_STR);
if ($filtroFecha !== '') $stmt->bindValue(':fecha', $filtroFecha, PDO::PARAM_STR);

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$Pedidos = $stmt->fetchAll(PDO::FETCH_OBJ);



$SQLEmpleados = "SELECT u.Cedula as Cedula, CONCAT(u.Nombre,' ',u.Apellido)  as NombreEmpleado FROM usuarios u WHERE u.Tipo_Usuario='2';";
$BusquedaEmpleados = $db->prepare($SQLEmpleados);
$BusquedaEmpleados->execute();
$Empleados = $BusquedaEmpleados->fetchAll(PDO::FETCH_OBJ);

?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Historial de pedidos</h1>
                <div class="d-flex align-items-center">
                    <!-- Filtro por estado -->
                    <form class="d-flex me-2" method="GET" action="historial_Pedido.php">
                        <select name="estado" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente" <?php echo $filtroEstado === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="Produccion" <?php echo $filtroEstado === 'Produccion' ? 'selected' : ''; ?>>Produccion</option>
                            <option value="Finalizado" <?php echo $filtroEstado === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                            <option value="Rechazado" <?php echo $filtroEstado === 'Rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                            <option value="Verificado" <?php echo $filtroEstado === 'Verificado' ? 'selected' : ''; ?>>Verificado</option>
                        </select>

                        <?php if ($q !== ''): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <?php endif; ?>
                    </form>

                    <!-- Filtro por empleado -->
                    <form class="d-flex me-2" method="GET" action="historial_Pedido.php">
                        <select name="empleado" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                            <option value="">Todos los empleados</option>

                            <?php foreach ($Empleados as $Empleado) { ?>

                                <option value="<?php echo $Empleado->Cedula ?>" <?php echo $filtroEmpleado === $Empleado->Cedula ? 'selected' : ''; ?>> <?php echo $Empleado->NombreEmpleado ?></option>

                            <?php } ?>
                        </select>

                        <?php if ($q !== ''): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <?php endif; ?>
                    </form>

                    <!-- Filtro por fecha -->
                    <form class="d-flex me-2" method="GET" action="historial_Pedido.php">
                        <input type="date" name="fecha" class="form-control form-control-sm me-2" value="<?php echo htmlspecialchars($filtroFecha); ?>" onchange="this.form.submit()">

                        <?php if ($q !== ''): ?>
                            <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <?php endif; ?>
                        <?php if ($filtroEstado !== ''): ?><input type="hidden" name="estado" value="<?php echo htmlspecialchars($filtroEstado); ?>">
                        <?php endif; ?>
                        <?php if ($filtroEmpleado !== ''): ?><input type="hidden" name="empleado" value="<?php echo htmlspecialchars($filtroEmpleado); ?>">
                        <?php endif; ?>
                    </form>

                    <form class="d-flex" method="GET" action="historial_Pedido.php">
                        <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar..." value="<?php echo htmlspecialchars($q); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

            </div>
            <p>Desde aca podras visualizar tus pedidos</p>
        </div>
        <div class="d-flex justify-content-end mb-3">

        </div>
        <!-- Tabla de usuarios -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-truck"></i> Lista de Pedidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark">
                        <thead>
                            <tr>
                                <th scope="col">N° de Pedido</th>
                                <th scope="col">Fecha de solictud</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Empleado encargado</th>
                                <th scope="col">Material</th>
                                <th scope="col">Centimetros</th>
                                <th scope="col">Cantidad de Diseños</th>
                                <th scope="col">Fecha de entrega</th>
                                <th scope="col">Total Bs.</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($Pedidos as $Pedido) { ?>
                                <tr>
                                    <style>
                                        .bg-purple {
                                            background-color: #6f42c1 !important;
                                            color: #fff;
                                        }
                                    </style>

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
                                                            case 'Produccion':
                                                                echo ('badge bg-primary');
                                                                break;
                                                            case 'Pendiente':
                                                                echo ('badge bg-warning text-dark');
                                                                break;
                                                            case 'Verificado':
                                                                echo ('badge bg-purple');
                                                                break;
                                                        };
                                                        ?>"><?php echo $Pedido->Estado_Pedido ?></span></td>
                                    <td><?php if($Pedido->EmpleadoPedido!=null){echo $Pedido->EmpleadoPedido;}else{echo "Aun no asignado";}?></td>
                                    <td><?php echo $Pedido->ELMATERIAL ?></td>
                                    <td><?php echo $Pedido->Centimetros ?></td>
                                    <td><?php echo $Pedido->Cantidades ?></td>
                                    <td><?php if($Pedido->Fecha_Entrega!=null){echo $Pedido->Fecha_Entrega;}else{echo "Aun no definida";}?></td>
                                    <td><?php echo $Pedido->Costo ?></td>
                                    <td class="text-center">
                                        <a href="historial_PedidoDiseño.php?Codigo=<?php echo $Pedido->Id_pedido ?>" class="btn btn-sm btn-primary me-1" title="Ver diseños">
                                            <i class="fa-solid fa-file-image"></i> Diseños
                                        </a>

                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo renderPagination($total, $perPage, $page); ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../plantilla/bot.php'; ?>