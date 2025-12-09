<?php include '../model/conexion.php';
include '../plantilla/topAdmin.php';
include '../plantilla/paginacion.php';

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

// Base query with optional filtering
$baseQuery = "FROM usuarios u INNER JOIN tipousuario t ON u.Tipo_Usuario = t.Codigo WHERE u.Estado = 'ACTIVO'";
if ($q !== '') {
    $baseQuery .= " AND (u.Nombre LIKE :q OR u.Apellido LIKE :q OR u.Correo LIKE :q OR u.Cedula LIKE :q)";
}

// Count total
$countStmt = $db->prepare("SELECT COUNT(*) as total " . $baseQuery);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

// Fetch page
$sql = "SELECT CONCAT(u.Nombre, ' ', u.Apellido) as NombreCompleto, u.Correo, u.Cedula, u.Fecha_Registro, u.NumeroTLF, t.Tipo, t.Codigo " . $baseQuery . " ORDER BY u.Fecha_Registro DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$usuariosBD = $stmt->fetchAll(PDO::FETCH_OBJ);

$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
?>

<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                <h1 class="h2 text-light">Usuarios</h1>
                <p class="">Desde aquí podra visualizar y gestionar los usuarios del sistema</p></div>
                <a href="AgregarUsuarios.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus me-2"></i> Agregar Usuario
                </a>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fa-solid fa-users me-2"></i>Lista de Usuarios</h5>
                    <form class="d-flex" method="GET" action="usuarios.php">
                        <input type="search" id="searchInput" name="q" class="form-control form-control-sm me-2" placeholder="Buscar..." value="<?php echo htmlspecialchars($q); ?>">
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
                                <th scope="col">Nombre</th>
                                <th scope="col">Teléfono</th>
                                <th scope="col">Tipo de Usuario</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usuariosTbody">
                            <?php foreach($usuariosBD as $usuarioBD){ ?>
                            <tr>
                                <td><?php echo $usuarioBD->Fecha_Registro ?></td>
                                <td><?php echo $usuarioBD->Cedula ?></td>
                                <td><?php echo $usuarioBD->Correo ?></td>
                                <td><?php echo $usuarioBD->NombreCompleto ?></td>
                                <td><?php echo $usuarioBD->NumeroTLF ?></td>
                                <td><span class="<?php 
                                switch($usuarioBD->Codigo){
                                    case 1:
                                        echo('badge bg-success');
                                        break;
                                    case 2:
                                        echo('badge bg-primary');
                                        break;
                                    case 3:
                                        echo('badge bg-danger');
                                        break;    
                                };
                                    ?>"><?php echo $usuarioBD->Tipo ?></span></td>
                                <td class="text-center">
                                    <a href="EditarUsuario.php?Codigo=<?php echo $usuarioBD->Cedula?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a onclick="confirmarEliminacionUsuarios('<?php echo $usuarioBD->Cedula?>')" class="btn btn-sm btn-danger me-1" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

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
        

<?php
        if(isset($_SESSION['UsuarioRegistrado'])) {
            $respuesta = $_SESSION['UsuarioRegistrado'];
            unset($_SESSION['UsuarioRegistrado']);
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


