<?php
include('../model/conexion.php');
include('../plantilla/topEmpleados.php');

$sentencia = $db->prepare("SELECT p.Id_pedido, p.Estado_Pedido, p.Centimetros, p.Cantidades, p.Costo, p.Fecha_Solicitud, p.Fecha_Entrega, 
                            CONCAT(cli.Nombre, ' ', cli.Apellido, ' ', p.Cedula_Cliente) as ClientePedido, 
                            CASE WHEN p.Empleado_Encargado IS NULL THEN NULL ELSE CONCAT(emp.Nombre,' ',emp.Apellido,' C.I:',p.Empleado_Encargado) END as EmpleadoPedido, CONCAT(m.Nombre_material,' ',m.Precio_CM,' bs/c')AS ELMATERIAL 
                            FROM pedidos p 
                            INNER JOIN usuarios cli ON p.Cedula_Cliente = cli.Cedula 
                            LEFT JOIN usuarios emp ON p.Empleado_Encargado = emp.Cedula 
                            INNER JOIN materiales m ON p.Material_Pedido = m.CODIGO_Material
                            WHERE p.Empleado_Encargado = :Empleado_Encargado AND p.Estado_Pedido = 'Pendiente'
                            ORDER BY p.Fecha_Solicitud ASC");
$sentencia->bindParam(':Empleado_Encargado', $CedulaSesion);
$sentencia->execute();
$Pedidos = $sentencia->fetchAll(PDO::FETCH_OBJ);
?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Pedidos encargados</h1>

            </div>
            <p class="">Desde aqui podrás verificar los diseños y aceptar o rechazar las solicitudes que tienes asignadas</p>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-clock"></i> Lista de Solicitudes en espera<?php if (count($Pedidos) == 0) {
                                                                                                        echo ', (No hay solicitudes pendientes actualmente)';
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
                                <th scope="col">Fecha de entrega</th>
                                <th scope="col">Total Bs.</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($Pedidos as $Pedido) { ?>
                                <tr>
                                    <td><?php echo $Pedido->Id_pedido ?></td>
                                    <td><?php echo $Pedido->Fecha_Solicitud ?></td>
                                    <td><span class="badge bg-warning text-dark"><?php echo $Pedido->Estado_Pedido ?></span></td>
                                    <td><?php echo $Pedido->ClientePedido ?></td>
                                    <td><?php echo $Pedido->ELMATERIAL ?></td>
                                    <td><?php echo $Pedido->Centimetros ?></td>
                                    <td><?php echo $Pedido->Cantidades ?></td>
                                    <td><?php echo $Pedido->Fecha_Entrega ?></td>
                                    <td><?php echo $Pedido->Costo ?></td>
                                    <td class="text-center align-middle d-flex flex-column gap-3">
                                        <a class="btn btn-sm btn-primary" href="VerificarDiseños.php?Codigo=<?php echo $Pedido->Id_pedido; ?>">
                                            <i class="fa-solid fa-image"></i> Verificar diseños
                                        </a>
                                        <button onclick="confirmarActualizar(<?php echo $Pedido->Id_pedido ?>)" class="btn btn-sm btn-warning" style="white-space: nowrap;">
                                            <i class="fa-solid fa-arrows-rotate"></i> Actualizar estado
                                        </button>
                                        <button onclick="confirmarLiberacion(<?php echo $Pedido->Id_pedido ?>)" class="btn btn-sm btn-secondary" style="white-space: nowrap;">
                                            <i class="fa-solid fa-unlock"></i> Liberar pedido
                                        </button>
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
<?php include('../plantilla/bot.php'); ?>

<script>
    function confirmarActualizar(idpedido) {
        Swal.fire({
            title: "Actualizar Estado",
            text: "Seleccione el nuevo estado para esta solicitud:",
            icon: "question",
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonColor: "#56b321ff", // Verde para Verificado
            denyButtonColor: "#d33", // Rojo para Rechazado
            confirmButtonText: "Verificado",
            denyButtonText: `Rechazado`,
            cancelButtonText: "Cancelar",
        }).then((result) => {

            // --- CASO 1: VERIFICADO (Pedir Fecha) ---
            if (result.isConfirmed) {
                // Obtener fecha de hoy en formato YYYY-MM-DD para el atributo min
                const today = new Date().toISOString().split('T')[0];

                Swal.fire({
                    title: 'Establecer Fecha Estimada de Entrega',
                    html: `
                        <p class="text-muted">Indique cuándo estará listo este pedido para que el cliente apruebe.</p>
                        <input type="date" id="fechaEntrega" class="swal2-input" min="${today}">
                    `,
                    confirmButtonText: 'Confirmar y Verificar',
                    showCancelButton: true,
                    preConfirm: () => {
                        const fecha = document.getElementById('fechaEntrega').value;
                        if (!fecha) {
                            Swal.showValidationMessage('Debe seleccionar una fecha de entrega');
                            return false;
                        }
                        if (fecha < today) {
                            Swal.showValidationMessage('La fecha no puede ser anterior a hoy');
                            return false;
                        }
                        return fecha;
                    }
                }).then((dateResult) => {
                    if (dateResult.isConfirmed) {
                        const fechaSeleccionada = dateResult.value;
                        // Enviamos la fecha como parámetro GET adicional
                        window.location.href = `<?php echo $URL ?>empleados/controles/Control_EstadoPedido.php?Codigo=${idpedido}&Estado=Verificado&fecha=${fechaSeleccionada}`;
                    }
                });

                // --- CASO 2: RECHAZADO (Pedir Motivo) ---
            } else if (result.isDenied) {
                Swal.fire({
                    title: "Ingrese el motivo del rechazo",
                    icon: "info",
                    input: "text",
                    showCancelButton: true,
                    confirmButtonText: "Enviar",
                    cancelButtonText: "Cancelar",
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Debe ingresar un motivo para rechazar el pedido.';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const motivo = encodeURIComponent(result.value);
                        window.location.href = `<?php echo $URL ?>empleados/controles/Control_EstadoPedido.php?Codigo=${idpedido}&Estado=Rechazado&motivo_rechazo=${motivo}`;
                    }
                });
            }
        });
    }



    function confirmarLiberacion(idpedido) {
        Swal.fire({
            title: "¿Estás seguro de liberar este pedido?",
            icon: "question",
            text: "Al hacer esto, el pedido será liberado y no estará más en tu lista de pendientes.",
            showCancelButton: true,
            confirmButtonColor: "#56b321ff",
            confirmButtonText: "Liberar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?php echo $URL ?>empleados/controles/Control_LiberarPedido.php?Codigo=${idpedido}`;
            }
        })
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
            icon: 'info',
            title: 'Estado de la solicitud actualizado!',
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php
} ?>

<?php
if (isset($_SESSION['LiberarPedido'])) {
    $respuesta = $_SESSION['LiberarPedido'];
    unset($_SESSION['LiberarPedido']);
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
            icon: 'info',
            title: 'El pedido ha sido liberado!',
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php
} ?>