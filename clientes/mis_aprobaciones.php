<?php
include '../model/conexion.php';
include '../plantilla/sesion.php';
include '../plantilla/topClientes.php';
include '../plantilla/paginacion.php';


$sql = "SELECT 
            p.Id_pedido, 
            p.Fecha_Solicitud, 
            m.Nombre_material, 
            p.Centimetros, 
            p.Cantidades, 
            p.Costo,
            p.Fecha_Entrega
        FROM pedidos p
        INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material
        WHERE 
            p.Cedula_Cliente = :cedula 
        AND 
            p.Estado_Pedido = 'Verificado'  -- Este es el filtro clave
        ORDER BY p.Fecha_Solicitud DESC";

$stmt = $db->prepare($sql);
$stmt->bindParam(':cedula', $CedulaSesion);
$stmt->execute();
$pedidosParaAprobar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Aprobar pedidos</h1>
            </div>
            <p>Verifique sus pedidos que ya han sido verificados, para ser enviados a producción</p>
        </div>
        <div class="card shadow-sm mb-4">


            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-thumbs-up"></i> Lista de pedidos por aprobar<?php if (empty($pedidosParaAprobar)) {
                                                                                                        echo ', (No hay pedidos por aprobar)';
                                                                                                    } ?></p>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark">
                        <thead>
                            <tr>
                                <th>Nro. Pedido</th>
                                <th>Fecha Solicitud</th>
                                <th>Material</th>
                                <th>Centimetros</th>
                                <th>Cantidad de diseños</th>
                                <th>Costo (Bs.)</th>
                                <th>Fecha de entrega</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidosParaAprobar as $pedido) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($pedido['Id_pedido']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['Fecha_Solicitud']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['Nombre_material']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['Centimetros']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['Cantidades']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['Costo']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['Fecha_Entrega']); ?></td>
                                    <td>
                                        <a href="<?php echo $URL ?>clientes/Controles/procesar_aprobacion.php?id=<?php echo $pedido['Id_pedido']; ?>" class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-thumbs-up"></i> Confirmar
                                        </a>

                                        <a href="modificar_pedido.php?id=<?php echo $pedido['Id_pedido']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-pencil"></i> Alterar
                                        </a>
                                        <a href="historial_PedidoDiseño.php?Codigo=<?php echo $pedido['Id_pedido']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-file-image"></i> Diseños
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include '../plantilla/bot.php'; ?>

<?php
if (isset($_SESSION['Pedido_Aprobado'])) {
    $respuesta = $_SESSION['Pedido_Aprobado'];
    unset($_SESSION['Pedido_Aprobado']);
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
            text: '<?php echo $respuesta; ?> le informaremos cuando el pedido este finalizado',
        });
    </script>
<?php
}?>