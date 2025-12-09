<?php
include '../model/conexion.php';
include '../plantilla/topAdmin.php';
include '../plantilla/paginacion.php';
$codigo = isset($_GET['Codigo']) ? $_GET['Codigo'] : null;

$params = getPaginationParams(12);
$q = $params['q'];
$page = $params['page'];
$perPage = $params['perPage'];
$offset = $params['offset'];

$baseQuery = "FROM disenos d INNER JOIN pedido_diseno pd ON pd.id_Diseno = d.ID_Diseno LEFT JOIN pedidos p ON p.Id_pedido = pd.id_pedido INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula WHERE p.Id_pedido = :Codigo";
if ($q !== '') {
    $baseQuery .= " AND (d.Nombre_Diseno LIKE :q OR d.ID_Diseno LIKE :q)";
}

$countStmt = $db->prepare("SELECT COUNT(*) as total " . $baseQuery);
$countStmt->bindValue(':Codigo', $codigo, PDO::PARAM_INT);
if ($q !== '') $countStmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();

$sql = "SELECT d.ID_Diseno, d.Nombre_Diseno, d.URL_Diseno, d.Cantidad, p.Cedula_Cliente, p.Cantidades, CONCAT(u.Nombre,' ',u.Apellido) AS NOMBRECLIENTE, p.Id_pedido " . $baseQuery . " ORDER BY d.ID_Diseno DESC LIMIT :limit OFFSET :offset";
$stmt = $db->prepare($sql);
$stmt->bindValue(':Codigo', $codigo, PDO::PARAM_INT);
if ($q !== '') $stmt->bindValue(':q', "%$q%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$Diseños = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
    
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
                <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                <h1 class="h2 text-light">Diseños del pedido N° <?php echo isset($Diseños[0]) ? $Diseños[0]->Id_pedido : $codigo;?></h1>
                <p class="">Explora los diseños de este pedido</p></div>
                <a href="Pedidos.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <!-- Galería de diseños -->
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
                                <strong>N° del pedido:</strong> <?php echo $Diseño->Id_pedido ?>
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
                                <strong>Cantidad de impresiones:</strong> <?php echo $Diseño->Cantidad ?>
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
        <div class="mt-3">
            <?php echo renderPagination($total, $perPage, $page); ?>
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
</html>

<?php include '../plantilla/bot.php';?>

<script>
    function showImageModal(imageSrc, imageTitle) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalImageTitle').textContent = imageTitle;
    }
</script>