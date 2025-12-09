<?php
include '../model/conexion.php';
include '../plantilla/topAdmin.php';
include '../plantilla/paginacion.php';

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

$baseQuery = 'FROM materiales m INNER JOIN usuarios u ON m.Ingresado_Modificado_por = u.Cedula WHERE m.Estado_Material = "ACTIVO" OR m.Estado_Material = "INACTIVO"';
if ($q !== '') {
    $baseQuery .= ' AND (m.Nombre_material LIKE :q OR m.CODIGO_Material LIKE :q)';
}

$countStmt = $db->prepare('SELECT COUNT(*) as total ' . $baseQuery);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

$sql = 'SELECT m.CODIGO_Material, m.Fecha_Ultima_Modificacion, m.Motivo_Cambio, m.Nombre_material, m.Cantidad, m.Estado_Material, m.Precio_CM, m.Ingresado_Modificado_por, u.Nombre, u.Apellido ' . $baseQuery . ' ORDER BY m.Nombre_material ASC LIMIT :limit OFFSET :offset';
$stmt = $db->prepare($sql);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$materialesBD = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<style>
    /* ... tus otros estilos ... */

    /* Estilo para la celda de Motivo */
    .celda-motivo {
        max-width: 200px;
        /* Ancho máximo de la columna */
        white-space: nowrap;
        /* Evita que el texto salte de línea */
        overflow: hidden;
        /* Oculta lo que se salga del ancho */
        text-overflow: ellipsis;
        /* Agrega los "..." al final */
        font-style: italic;
        /* Letra cursiva para diferenciarlo */
        color: #ced4da;
        /* Un gris claro (bueno para fondo oscuro) */
        cursor: help;
        /* Cambia el cursor para indicar que hay info */
    }

    /* Efecto opcional: al pasar el mouse, el color se aclara más */
    .celda-motivo:hover {
        color: #ffffff;
    }
</style>

<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-light">Materiales</h1>
                    <p class="text">Desde aquí puedes visualizar y gestionar los materiales disponibles</p>
                </div>
                <a href="AgregarMaterial.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i> Agregar Material
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Lista de Materiales</h5>
                    <form class="d-flex" method="GET" action="Material.php">
                        <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar..." value="<?php echo htmlspecialchars($q); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark">
                        <thead>
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Cantidad en metros</th>
                                <th scope="col">Precio por cm</th>
                                <th scope="col">Ingresado/Modificado por</th>
                                <th scope="col">Fecha ultima modificación</th>
                                <th scope="col">Motivo ultima modificación</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materialesBD as $material) { ?>
                                <tr>
                                    <td><?php echo $material->Nombre_material ?></td>
                                    <td><span class="<?php
                                                        switch ($material->Estado_Material) {
                                                            case "ACTIVO":
                                                                echo ('badge bg-success');
                                                                break;
                                                            case "INACTIVO":
                                                                echo ('badge bg-danger');
                                                                break;
                                                        };
                                                        ?>"><?php echo $material->Estado_Material ?></span></td>
                                    <td><?php echo $material->Cantidad ?></td>
                                    <td><?php echo $material->Precio_CM ?></td>
                                    <td><?php echo $material->Ingresado_Modificado_por . " " . $material->Nombre . " " . $material->Apellido ?></td>
                                    <td><?php echo $material->Fecha_Ultima_Modificacion ?></td>
                                    <td class="celda-motivo" title="<?php echo htmlspecialchars($material->Motivo_Cambio ?? 'No ha habido modificación todavía'); ?>">
                                        <?php
                                        if ($material->Motivo_Cambio != null) {
                                            echo $material->Motivo_Cambio;
                                        } else {
                                            // Usamos un texto más discreto para cuando no hay datos
                                            echo '<span class="text-muted small">Sin cambios</span>';
                                        }
                                        ?>
                                    </td>

                                    <td class="text-center">
                                        <?php if ($material->Estado_Material == "ACTIVO") { ?>
                                            <a href="./EditarMaterial.php?Codigo=<?php echo $material->CODIGO_Material; ?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button onclick="confirmarEliminacionMaterial('<?php echo $material->CODIGO_Material ?>')" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        <?php } else { ?>
                                            <a onclick="confirmarReactivacionMaterial(<?php echo $material->CODIGO_Material ?>)" class="btn btn-sm btn-success" title="Reactivar">
                                                <i class="fa-solid fa-power-off"></i>
                                            </a>
                                        <?php } ?>
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

<script>
    function confirmarEliminacionMaterial(Codigo) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás recuperar este material!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, Desactivarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'Controles/Control_BorrarM.php?Codigo=' + Codigo;
            }
        });
    }
</script>

<script>
    function confirmarReactivacionMaterial(Cedula) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Desea reactivar este material!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, reactivarlo',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'Controles/control_reactivarM.php?Codigo=' + Cedula;
            }
        });
    }
</script>

<?php
if (isset($_SESSION['MaterialRegistrado'])) {
    $respuesta = $_SESSION['MaterialRegistrado'];
    unset($_SESSION['MaterialRegistrado']);
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
            title: '¡Material Agregado!',
            text: '<?php echo $respuesta; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php
}





if (isset($_SESSION['MaterialEditado'])) {
    $respuesta = $_SESSION['MaterialEditado'];
    unset($_SESSION['MaterialEditado']);
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
            title: '¡Material modificado!',
            text: '<?php echo $respuesta; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php
}
if (isset($_SESSION['MaterialEliminado'])) {
    $respuesta = $_SESSION['MaterialEliminado'];
    unset($_SESSION['MaterialEliminado']);
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
            title: '¡Material Desactivado!',
            text: '<?php echo $respuesta; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php
}
if (isset($_SESSION['MaterialError'])) {
    $respuesta = $_SESSION['MaterialError'];
    unset($_SESSION['MaterialError']);
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
            icon: 'error',
            title: 'Error',
            text: '<?php echo $respuesta; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php
}

if (isset($_SESSION['Material_reactivado'])) {
    $respuesta = $_SESSION['Material_reactivado'];
    unset($_SESSION['Material_reactivado']);
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
            title: '¡Material reactivado!',
            text: '<?php echo $respuesta; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php
}
