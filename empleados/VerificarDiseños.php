<?php 
include('../model/conexion.php');
include('../plantilla/topEmpleados.php');

$Codigo = $_GET['Codigo'];


$sentencia = $db -> prepare("SELECT d.ID_Diseno, d.Nombre_Diseno, d.URL_Diseno, d.Cantidad, p.Cedula_Cliente, p.Cantidades, 
                          CONCAT(u.Nombre,' ',u.Apellido) AS NOMBRECLIENTE, p.Id_pedido 
                          FROM disenos d 
                          INNER JOIN pedido_diseno pd ON pd.id_Diseno = d.ID_Diseno 
                          LEFT JOIN pedidos p ON p.Id_pedido = pd.id_pedido 
                          INNER JOIN usuarios u ON p.Cedula_Cliente = u.Cedula
                          WHERE p.Id_pedido = :Codigo;");
$sentencia->bindParam(':Codigo', $Codigo);
$sentencia -> execute();
$Diseños = $sentencia -> fetchAll(PDO::FETCH_OBJ);
?>

<main>
    
<div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                <h1 class="h2 text-light">Diseños del pedido N° <?php echo $Diseños[0]->Id_pedido;?></h1>
                <p class="text">Explora los diseños de este pedido</p></div>
                <a href="PedidosEncargados.php" class="btn btn-secondary">
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
                                <strong>ID Pedido:</strong> <?php echo $Diseño->Id_pedido ?>
                            </p>
                            <p class="card-text text-muted small mb-1">
                                <strong>ID Diseño:</strong> <?php echo $Diseño->ID_Diseno ?>
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
                        <a href="controles/descargar_Diseños.php?id=<?php echo $Diseño->ID_Diseno; ?>" 
                           class="btn btn-primary btn-sm w-100 mt-3">
                            <i class="fa-solid fa-download"></i> Descargar Diseño
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
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