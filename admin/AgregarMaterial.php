<?php
include '../model/conexion.php';
include '../plantilla/topAdmin.php';
?>

<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-light">Agregar material</h1>
                    <p class="text">Desde este menú podrás añadir nuevos materiales al sistema</p>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Datos del nuevo material</h5>
            </div>
            <div class="card-body">
                <form id="formNuevoMaterial" action="Controles/Control_AgregarM.php" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="Nombre" class="form-label text-light">Nombre</label>
                            <input type="text" class="form-control bg-light text-dark border-secondary" id="Nombre" name="Nombre" maxlength="100" required>
                            <div class="invalid-feedback">
                                Por favor ingresa el nombre del material.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="Cantidad" class="form-label text-light">Cantidad en metros</label>
                            <input type="number" class="form-control bg-light text-dark border-secondary" id="Cantidad" name="Cantidad" step='0.01' required>
                            <div class="invalid-feedback">
                                Por favor ingresa una cantidad válida (mayor o igual a 0).
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="Precio_CM" class="form-label text-light">Precio por centimetro</label>
                            <input type="number" class="form-control bg-light text-dark border-secondary" id="Precio_CM" name="Precio_CM" step='0.01' required>
                            <div class="invalid-feedback">
                                Por favor ingresa un código válido de exactamente 5 dígitos.
                            </div>
                        </div>
                    </div><br>
                    <div class="d-flex justify-content-end mt-6">
                        <a href="Material.php" type="button" class="btn btn-secondary me-2">
                            <i class="fa-solid fa-arrow-left"></i>Volver
                        </a>                     
                        <button type="button" class="btn btn-outline-light me-2" onclick="limpiar()">
                            <i class="fa-solid fa-eraser me-2"></i>Limpiar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-2"></i>Guardar Material
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    function limpiar() {
        document.getElementById('formNuevoMaterial').reset();
    }
    // Limitar a exactamente 5 caracteres en el input de código
    document.addEventListener('DOMContentLoaded', function() {
        const codigoInput = document.getElementById('CODIGO_Material');
        codigoInput.addEventListener('input', function() {
            // Solo permitir 5 dígitos
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
            // Si el valor es menor a 10000, lo limpia
            if (this.value.length > 0 && this.value.length < 5) {
                this.setCustomValidity('El código debe tener exactamente 5 dígitos.');
            } else {
                this.setCustomValidity('');
            }
        });
    });
</script>
<?php include '../plantilla/bot.php'; ?>

<?php
if(isset($_SESSION['MaterialRegistrado'])) {
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
        }).then(() => {
            window.location.href = 'Material.php';
        });
    </script>
<?php }
if(isset($_SESSION['MaterialError'])) {
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
            title: 'Aviso',
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php } ?>
