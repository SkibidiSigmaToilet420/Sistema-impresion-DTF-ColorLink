<?php
include '../model/conexion.php';
include '../plantilla/sesion.php';

// Verificación de parámetro GET
if (!isset($_GET['Codigo'])) {
    header("Location: Material.php");
    exit;
}

$codigo = $_GET['Codigo'];
$sentencia = $db->prepare("SELECT * FROM materiales WHERE CODIGO_Material = :codigo");
$sentencia->bindParam(':codigo', $codigo);
$sentencia->execute();
$material = $sentencia->fetch(PDO::FETCH_OBJ);

if (!$material) {
    header("Location: Material.php");
    exit;
}
?>

<?php include '../plantilla/topAdmin.php'; ?>
<main>
    <div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <div>
                    <h1 class="h2 text-light">Editar Materiales</h1>
                    <p class="text">Desde este menú podrás editar los valores de los materiales disponibles</p>
                </div>
            </div>
        </div>

   <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark-blue text-light">
                <h5 class="mb-0"><i class="fa-solid fa-boxes-stacked me-2"></i>Datos del material a editar</h5>
            </div>
            <div class="card-body">
                <form id="formNuevoMaterial" action="Controles/Control_EditarM.php" method="post">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="hidden" name="CODIGO_Material" value="<?php echo ($material->CODIGO_Material); ?>">
                            <label for="Nombre" class="form-label text-light">Nombre</label>
                            <input type="text" class="form-control bg-light text-dark border-secondary" id="Nombre" name="Nombre" maxlength="100" value="<?php echo ($material->Nombre_material); ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingresa el nombre del material.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="Cantidad" class="form-label text-light">Cantidad en metros</label>
                            <input type="number" class="form-control bg-light text-dark border-secondary" id="Cantidad" name="Cantidad" step='0.01' value="<?php echo ($material->Cantidad);?>">
                            <div class="invalid-feedback">
                                Por favor ingresa una cantidad válida.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="Precio_CM" class="form-label text-light">Precio por centimetro</label>
                            <input type="number" class="form-control bg-light text-dark border-secondary" id="Precio_CM" name="Precio_CM" step='0.01' value="<?php echo ($material->Precio_CM); ?>" required>
                            <div class="invalid-feedback">
                                Por favor ingresa un precio válido.
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="Motivo" class="form-label text-light">Motivo de la Modificación</label>
                            <textarea class="form-control bg-light text-dark border-secondary" 
                                      id="Motivo" 
                                      name="Motivo" 
                                      rows="2" 
                                      required 
                                      placeholder="Explique brevemente por qué está modificando este material (Ej: Ajuste de precio, llegada de inventario...)"></textarea>
                            <div class="invalid-feedback">
                                El motivo es obligatorio para auditoría.
                            </div>
                        </div>

                    </div><br>
                    <div class="d-flex justify-content-end mt-6">
                        <a href="Material.php" type="button" class="btn btn-secondary me-2">
                            <i class="fa-solid fa-arrow-left"></i>Volver
                        </a>                     
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
</main>

<?php include '../plantilla/bot.php'; ?>
</body>
</html>