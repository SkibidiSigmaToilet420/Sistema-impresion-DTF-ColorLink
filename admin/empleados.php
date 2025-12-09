<?php include '../model/conexion.php';
include '../plantilla/topAdmin.php';
include '../plantilla/paginacion.php';

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

$baseQuery = "FROM usuarios u JOIN empleados e ON e.Cedula_Empleado = u.Cedula WHERE u.Estado = 'ACTIVO' AND u.Tipo_Usuario = 2";
if ($q !== '') {
    $baseQuery .= " AND (u.Nombre LIKE :q OR u.Apellido LIKE :q OR u.Correo LIKE :q OR u.Cedula LIKE :q)";
}

$countStmt = $db->prepare("SELECT COUNT(*) as total " . $baseQuery);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

$sql = "SELECT u.Cedula, u.Correo, CONCAT(u.Nombre, ' ', u.Apellido) as NombreCompleto, u.NumeroTLF, u.Fecha_Registro, e.Cargo, (SELECT COUNT(*) FROM pedidos p WHERE p.Empleado_Encargado = u.Cedula) AS TotalPedidos " . $baseQuery . " ORDER BY u.Fecha_Registro DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$Empleados = $stmt->fetchAll(PDO::FETCH_OBJ);

$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                <h1 class="h2 text-light">Empleados</h1>
                <p class="text">Desde aquí podras visualizar y gestionar los empleados del sistema</p></div>
                <a href="AgregarUsuarios.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus me-2"></i> Agregar Usuario
                </a>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-user-tie"></i> Lista de Empleados</h5>
                    <form class="d-flex" method="GET" action="empleados.php">
                        <input id="searchInput" type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar..." value="<?php echo htmlspecialchars($q); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark">
                        <thead>
                            <tr>
                                <th scope="col">Fecha de registro</th>
                                <th scope="col">Documento de identidad</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Pedidos Asignados</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Cargo</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="empleadosTbody">
                            <?php foreach($Empleados as $Empleado){ ?>
                            <tr>
                                <td><?php echo $Empleado ->Fecha_Registro ?></td>
                                <td><?php echo $Empleado->Cedula ?></td>
                                <td><?php echo $Empleado->Correo ?></td>
                                <td><?php echo $Empleado->TotalPedidos ?></td>    
                                <td><?php echo $Empleado->NombreCompleto ?></td>
                                <td><?php echo $Empleado->Cargo?></td>
                                <td><?php echo $Empleado->NumeroTLF ?></td>
                                <td class="text-center">
                                    <a href="EditarUsuario.php?Codigo=<?php echo $Empleado->Cedula?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a onclick="confirmarEliminacionUsuarios('<?php echo $Empleado->Cedula?>')" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                    <button onclick="asginarCargo('<?php echo $Empleado->Cedula?>')" class="btn btn-sm btn-primary" title="Asignar cargo al empleado"> 
                                        <i class="fa-solid fa-address-card"></i> Asignar cargo
                                    </button>

                                </td>
                            </tr>
                            <?php }?>
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
<?php include '../plantilla/bot.php';?>

<?php
        if(isset($_SESSION['UsuarioEditar'])) {
            $respuesta = $_SESSION['UsuarioEditar'];
            unset($_SESSION['UsuarioEditar']);
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

<?php
        if(isset($_SESSION['Usuario_Eliminado'])) {
            $respuesta = $_SESSION['Usuario_Eliminado'];
            unset($_SESSION['Usuario_Eliminado']);
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

<?php
        if(isset($_SESSION['borrar_mismo_usuario'])) {
            $respuesta = $_SESSION['borrar_mismo_usuario'];
            unset($_SESSION['borrar_mismo_usuario']);
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
                title: 'Atencion',
                text: '<?php echo $respuesta; ?>',
            });
        </script>
<?php
    }
?>  

<script>
    function confirmarEliminacionUsuarios(Cedula) {
        Swal.fire({
                title: '¿Estás seguro?',
                text: "¡Esta apunto de eliminar este usuario!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminarlo',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'Controles/Control_BorrarU.php?Codigo=' + Cedula;
                }
            });
    }

</script>

<script>
    function asginarCargo(Cedula) {
        Swal.fire({
            title: 'Asignar Cargo',
            text: 'Ingrese un cargo para el empleado',
            input: 'text',
            inputPlaceholder: 'Ingrese un cargo',
            showCancelButton: true,
            confirmButtonText: 'Asignar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                window.location.href = 'Controles/Control_AsginarCargo.php?Codigo=' + Cedula + '&Cargo=' + result.value;
            }
        });
    }
</script>

<?php
        if(isset($_SESSION['CargoAsignado'])) {
            $respuesta = $_SESSION['CargoAsignado'];
            unset($_SESSION['CargoAsignado']);
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
                title: 'Atencion',
                text: '<?php echo $respuesta; ?>',
            });
        </script>
<?php
    }
?>  
