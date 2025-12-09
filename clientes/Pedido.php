<?php
include('../model/conexion.php');
include('../plantilla/sesion.php');
include('../plantilla/topClientes.php');

$sentencia = $db -> prepare("SELECT * FROM materiales WHERE Estado_Material = 'ACTIVO'");
$sentencia -> execute();
$materiales = $sentencia -> fetchAll(PDO::FETCH_ASSOC);
?>
<main>
<div class="col-lg-10 ms-sm-auto px-4 py-4">
        <div class="title">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
                <h1 class="h2 text-light">Nuevo pedido</h1>
            </div>
            <p class="text-light">Desde aquí podrás realizar un nuevo pedido</p>
        </div>

        <!-- Instrucciones -->
        <div class="card bg-dark-blue-light">
            <div class="card-body">
                <h5 class="card-title text-light">Instrucciones</h5>
                <ul class="text-light">
                    <li>Ingresa las dimensiones del largo de tu pedido en centímetros, el ancho debe ser de 57 cm</li>
                    <li>Sube tus imágenes en formato PNG con fondo transparente si es necesario</li>
                    <li>Puedes subir múltiples imágenes para el mismo pedido</li>
                    <li>Para cada imagen, especifica la cantidad de veces que deseas imprimir ese diseño (mínimo 1)</li>
                    <li>La cantidad de diseños será automáticamente igual al número de imágenes subidas</li>
                    <li>Una vez finalizado el proceso, sera notificado via correo electronico</li>
                </ul>
            </div>
        </div><br>

        <div class="card bg-dark-blue-light mb-4">
            <div class="card-body">
                <h5 class="card-title text-light">Especificaciones del pedido</h5>
                
                <form id="pedidoForm" enctype="multipart/form-data" method="post" action="Controles/Agregar_pedido.php">
                    <div class="row mb-6">
                        <div class="col-md-4">
                            <label class="form-label text-light">Ancho (fijo)</label>
                            <input type="text" class="form-control bg-dark text-light" value="57 cm" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="alto" class="form-label text-light">Alto (cm)</label>
                            <input type="number" class="form-control bg-dark text-light" id="alto" name="alto" min="1" step="0.1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="material" class="form-label text-light">Material</label>
                            <select name="material" id="material" class="form-select" required>
                                <option value="" selected disabled>Selecciona un material</option>
                                <?php foreach($materiales as $material){ ?>
                                <option value="<?php echo $material['CODIGO_Material']; ?>" data-precio="<?php echo $material['Precio_CM']; ?>">
                                    <?php echo $material['Nombre_material']?> - <?php echo $material['Precio_CM']; ?> Bs/cm
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        
                    </div><br>
                    
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Precio estimado:</span>
                            <strong id="precioCalculado">0,00 Bs</strong>
                        </div>
                        <small class="text-muted">( Bs por cada cm de alto)</small>
                    </div>
                    
                    <div class="mb-4">
                        <label for="imagenes" class="form-label text-light">Subir imágenes (PNG)</label>
                        <input class="form-control bg-dark text-light" type="file" id="imagenes" name="imagenes[]" accept=".png" multiple required>
                        <div class="form-text text-light">Puedes seleccionar múltiples archivos PNG (máx. 5MB cada uno)</div>
                    </div>
                    
                    <div class="mb-4" id="previewContainer" style="display: none;">
                        <h6 class="text-light mb-3">Tus imágenes:</h6>
                        <div class="row g-3" id="imagePreviews"></div>
                    </div>
                    
                    <!-- Campo para cantidades por diseño - Se genera automáticamente -->
                    <div class="mb-4">
                        <label class="form-label text-light">Cantidad de impresiones por diseño</label>
                        <div id="cantidadesContainer">
                            <p class="text-light small">Selecciona las imágenes primero para especificar las cantidades de impresión.</p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">Enviar pedido</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    </div>   
</main>
<?php 
include('../plantilla/bot.php');
?>

<script>
    let precioPorCm = 0;

    // Calcular precio en tiempo real
    function calcularPrecio() {
        const alto = parseFloat(document.getElementById('alto').value) || 0;
        const precioTotal = (alto * precioPorCm).toFixed(2);
        document.getElementById('precioCalculado').textContent = `${precioTotal} Bs`;
    }

    // Evento para cambios en el material
    document.getElementById('material').addEventListener('change', function(e) {
        const valorSeleccionado = this.options[this.selectedIndex];
        precioPorCm = parseFloat(valorSeleccionado.getAttribute('data-precio')) || 0;
        calcularPrecio();
        
        // Actualizar el texto del precio por cm
        document.querySelector('.text-muted').textContent = `(Bs ${precioPorCm.toFixed(2)} por cada cm de alto)`;
    });

    // Evento para cambios en el alto
    document.getElementById('alto').addEventListener('input', calcularPrecio);

    // Inicializar con el primer material seleccionado (opcional)
    document.addEventListener('DOMContentLoaded', function() {
        const materialSelect = document.getElementById('material');
        if (materialSelect.options.length > 1) {
            materialSelect.selectedIndex = 1; // Selecciona el primer material (no el placeholder)
            materialSelect.dispatchEvent(new Event('change'));
        }
    });

    // Vista previa mejorada de imágenes y generación de campos de cantidad
    document.getElementById('imagenes').addEventListener('change', function(e) {
        const previewContainer = document.getElementById('previewContainer');
        const imagePreviews = document.getElementById('imagePreviews');
        const cantidadesContainer = document.getElementById('cantidadesContainer');
        
        // Limpiar vistas previas y cantidades anteriores
        imagePreviews.innerHTML = '';
        cantidadesContainer.innerHTML = '';
        
        if (this.files.length > 0) {
            previewContainer.style.display = 'block';
            
            // Mostrar mensaje de cantidad de imágenes
            const countInfo = document.createElement('p');
            countInfo.className = 'text-light small mb-3';
            countInfo.textContent = `${this.files.length} diseño(s) seleccionado(s) - Para cada diseño especifica cuántas veces quieres imprimirlo:`;
            imagePreviews.appendChild(countInfo);
            
            // Procesar cada imagen
            Array.from(this.files).forEach((file, index) => {
                if (!file.type.match('image.*')) return;
                
                // Crear vista previa de imagen
                const col = document.createElement('div');
                col.className = 'col-md-4 col-lg-3';
                
                const card = document.createElement('div');
                card.className = 'card bg-dark border-secondary h-100';
                
                const imgContainer = document.createElement('div');
                imgContainer.className = 'card-img-top p-2 d-flex align-items-center';
                imgContainer.style.height = '200px';
                imgContainer.style.overflow = 'hidden';
                
                const img = document.createElement('img');
                img.style.maxHeight = '100%';
                img.style.maxWidth = '100%';
                img.style.objectFit = 'contain';
                
                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-2';
                
                const fileName = document.createElement('small');
                fileName.className = 'd-block text-light text-truncate';
                fileName.textContent = `Diseño ${index + 1}: ${file.name}`;
                
                const fileSize = document.createElement('small');
                fileSize.className = 'd-block text-muted';
                fileSize.textContent = `${(file.size / 1024 / 1024).toFixed(2)} MB`;
                
                // Leer y mostrar la imagen
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                }
                reader.readAsDataURL(file);
                
                // Construir la tarjeta
                imgContainer.appendChild(img);
                cardBody.appendChild(fileName);
                cardBody.appendChild(fileSize);
                
                card.appendChild(imgContainer);
                card.appendChild(cardBody);
                col.appendChild(card);
                imagePreviews.appendChild(col);
                
                // Crear campo de cantidad para esta imagen
                const cantidadGroup = document.createElement('div');
                cantidadGroup.className = 'mb-3';
                
                const label = document.createElement('label');
                label.className = 'form-label text-light';
                label.textContent = `Cantidad de impresiones para "Diseño ${index + 1}":`;
                label.htmlFor = `cantidad_${index}`;
                
                const inputGroup = document.createElement('div');
                inputGroup.className = 'input-group';
                
                const input = document.createElement('input');
                input.type = 'number';
                input.className = 'form-control bg-dark text-light';
                input.id = `cantidad_${index}`;
                input.name = `cantidades[]`;
                input.min = '1';
                input.value = '1';
                input.required = true;
                
                const span = document.createElement('span');
                span.className = 'input-group-text bg-light text-dark';
                span.textContent = 'impresiones';
                
                inputGroup.appendChild(input);
                inputGroup.appendChild(span);
                cantidadGroup.appendChild(label);
                cantidadGroup.appendChild(inputGroup);
                cantidadesContainer.appendChild(cantidadGroup);
            });
        } else {
            previewContainer.style.display = 'none';
            cantidadesContainer.innerHTML = '<p class="text-dark small">Selecciona las imágenes primero para especificar las cantidades de impresión.</p>';
        }
    });
</script>

<?php
if (isset($_SESSION['error'])) {
    $respuesta = $_SESSION['error'];
    unset($_SESSION['error']);
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
            title: 'Error',
            text: '<?php echo $respuesta; ?>',
        });
    </script>
<?php
} ?>
