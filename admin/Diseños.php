<?php include '../model/conexion.php';
include '../plantilla/topAdmin.php';

include '../plantilla/paginacion.php';

$params = getPaginationParams(4); // más elementos por página para la galería
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

// base query
$baseFrom = "FROM disenos d 
    INNER JOIN pedido_diseno pd ON pd.id_Diseno = d.ID_Diseno 
    INNER JOIN pedidos p ON p.Id_pedido = pd.id_Pedido 
    INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula";

// si el usuario es cliente, limitar a sus propios diseños
$baseWhere = "1=1";
if (isset($TipoSesion) && $TipoSesion === 'Cliente') {
    $baseWhere .= " AND p.Cedula_Cliente = :CedulaCliente";
}
if($q !== ''){
    $baseWhere .= " AND (d.Nombre_Diseno LIKE :q OR u.Nombre LIKE :q OR u.Apellido LIKE :q OR p.Id_pedido LIKE :q)";
}

// total
$countSql = "SELECT COUNT(*) as total " . $baseFrom . " WHERE " . $baseWhere;
$stmtCount = $db->prepare($countSql);
if($q !== ''){ $stmtCount->bindValue(':q','%'.$q.'%'); }
if (isset($TipoSesion) && $TipoSesion === 'Cliente') { $stmtCount->bindValue(':CedulaCliente', $CedulaSesion); }
$stmtCount->execute();
$total = (int)$stmtCount->fetch(PDO::FETCH_OBJ)->total;

// select paginado
$sql = "SELECT d.ID_Diseno, d.Nombre_Diseno, d.Cantidad, d.URL_Diseno, p.Cedula_Cliente, p.Cantidades, CONCAT(u.Nombre,' ',u.Apellido) AS NOMBRECLIENTE, p.Id_pedido " . $baseFrom . " WHERE " . $baseWhere . " ORDER BY d.ID_Diseno DESC LIMIT :limit OFFSET :offset";
 $sentencia = $db->prepare($sql);
 if($q !== ''){ $sentencia->bindValue(':q','%'.$q.'%'); }
 if (isset($TipoSesion) && $TipoSesion === 'Cliente') { $sentencia->bindValue(':CedulaCliente', $CedulaSesion); }
 $sentencia->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
 $sentencia->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
 $sentencia->execute();
 $Diseños = $sentencia -> fetchAll(PDO::FETCH_OBJ);
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-light">Galería de Diseños</h1>
                    <p class="text">Explora todos los diseños de nuestros clientes</p>
                </div>
                <div class="d-flex align-items-center">
                    <form class="d-flex me-3" method="GET" action="Diseños.php">
                        <input type="search" name="q" class="form-control form-control-sm me-2" placeholder="Buscar diseño, cliente o pedido" value="<?php echo htmlspecialchars($q); ?>">
                        <button class="btn btn-sm btn-outline-light" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                  
                </div>

            </div>

        </div>

    

      
        <div class="row g-4">
            <?php foreach($Diseños as $Diseño){ ?>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <div class="card bg-dark border-secondary h-100 design-card">
                    <div class="design-image-container p-3">
                        <img src="<?php echo '../clientes/imagenes/' . $Diseño->Nombre_Diseno ?>" 
                             alt="<?php echo $Diseño->Nombre_Diseno ?>" 
                             class="img-fluid design-image"
                             data-bs-toggle="modal" 
                             data-bs-target="#imageModal"
                             onclick="showImageModal('<?php echo '../clientes/imagenes/' . $Diseño->Nombre_Diseno ?>', '<?php echo $Diseño->Nombre_Diseno ?>')">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-light"><?php echo $Diseño->Nombre_Diseno ?></h5>
                        <div class="design-details">
                            <p class="card-text text-muted small mb-1">
                                <strong>N° del Pedido:</strong> <?php echo $Diseño->Id_pedido ?>
                            </p>
                            <p class="card-text text-muted small mb-1">
                                <strong>N° del Diseño:</strong> <?php echo $Diseño->ID_Diseno ?>
                            </p>
                            <p class="card-text text-muted small mb-1">
                                <strong>Cliente:</strong> <?php echo $Diseño->NOMBRECLIENTE ?>
                            </p>
                            <p class="card-text text-muted small mb-1">
                                <strong>Documento de identidad:</strong> <?php echo $Diseño->Cedula_Cliente ?>
                            </p>
                            <p class="card-text text-muted small">
                                <strong>Cantidades:</strong> <?php echo $Diseño->Cantidad ?>
                            </p>
                        </div>
                        <a href="../usuarios/descargar_diseno.php?id=<?php echo $Diseño->ID_Diseno; ?>" 
                           class="btn btn-primary btn-sm w-100 mt-3">
                            <i class="fa-solid fa-download"></i> Descargar Diseño
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="mt-3 w-100 d-flex justify-content-center">
            <?php echo renderPagination($total, $perPage, $page, ['q' => $q]); ?>
        </div>
    </div>

    <!-- Modal para vista ampliada -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-light">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-dark" id="modalImageTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" class="img-fluid" style="max-height: 70vh;">
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../plantilla/bot.php';?>

<script>
    function showImageModal(imageSrc, imageTitle) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalImageTitle').textContent = imageTitle;
    }
</script>