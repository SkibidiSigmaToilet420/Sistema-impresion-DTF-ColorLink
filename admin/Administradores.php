<?php include '../model/conexion.php';
include '../plantilla/topAdmin.php';
include '../plantilla/paginacion.php';

$params = getPaginationParams(5);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

// construimos la parte base de la consulta
$baseWhere = "Estado = 'ACTIVO' AND Tipo_Usuario = 3";
if($q !== ''){
    $baseWhere .= " AND (Cedula LIKE :q OR Correo LIKE :q OR CONCAT(Nombre,' ',Apellido) LIKE :q)";
}

// total
$countSql = "SELECT COUNT(*) as total FROM usuarios WHERE " . $baseWhere;
$stmtCount = $db->prepare($countSql);
if($q !== ''){ $stmtCount->bindValue(':q','%'.$q.'%'); }
$stmtCount->execute();
$total = (int) $stmtCount->fetch(PDO::FETCH_OBJ)->total;

// datos paginados
$sql = "SELECT Cedula, Correo, CONCAT(Nombre, ' ', Apellido) as NombreCompleto, NumeroTLF, Fecha_Registro ";
$sql .= " FROM usuarios WHERE " . $baseWhere . " ORDER BY Fecha_Registro DESC LIMIT :limit OFFSET :offset";
$sentencia = $db->prepare($sql);
if($q !== ''){ $sentencia->bindValue(':q','%'.$q.'%'); }
$sentencia->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$sentencia->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$sentencia->execute();
$Administradores = $sentencia->fetchAll(PDO::FETCH_OBJ);

$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                <h1 class="h2 text-light">Administradores</h1>
                <p class="">Desde aquí podra visualizar y gestionar los administradores del sistema</p></div>
                <a href="AgregarUsuarios.php" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus me-2"></i> Agregar Usuario
                </a>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-crown"></i> Lista de Administradores</h5>
                <form class="d-flex" method="GET" action="Administradores.php">
                    <input id="searchInput" type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar" value="<?php echo htmlspecialchars($q); ?>">
                    <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
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
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="administradoresTbody">
                            <?php foreach($Administradores as $Administrador){ ?>
                            <tr>
                                <td><?php echo $Administrador ->Fecha_Registro ?></td>
                                <td><?php echo $Administrador->Cedula ?></td>
                                <td><?php echo $Administrador->Correo ?></td>
                                <td><?php echo $Administrador->NombreCompleto ?></td>
                                <td><?php echo $Administrador->NumeroTLF ?></td>
                                <td class="text-center">
                                    <a href="EditarUsuario.php?Codigo=<?php echo $Administrador->Cedula?>" class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a onclick="confirmarEliminacionUsuarios('<?php echo $Administrador->Cedula?>')" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-3">
                <?php echo renderPagination($total, $perPage, $page, ['q' => $q]); ?>
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
