// Modal de notificaciones para admin y empleados
function mostrarModalNotificaciones(notificaciones, borrarUrl) {
    let html = `<div class='modal fade' id='modalNotificaciones' tabindex='-1' aria-labelledby='modalNotificacionesLabel' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='modalNotificacionesLabel'>Todas las notificaciones</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Cerrar'></button>
                </div>
                <div class='modal-body'>
                    <ul class='list-group mb-3'>`;
    if (notificaciones.length > 0) {
        notificaciones.forEach(function(row) {
            html += `<li class='list-group-item d-flex align-items-center'>
                <span class='me-2'><i class='fa fa-check-circle text-success'></i></span>
                <span class='message-description small'>Nuevo pedido enviado por <b>${row.Nombre}</b><br><span class='text-muted'>Fecha: ${row.Fecha_Solicitud}</span></span>
            </li>`;
        });
    } else {
        html += `<li class='list-group-item text-muted text-center'>No hay notificaciones</li>`;
    }
    html += `</ul>
                <form action='${borrarUrl}' method='post'>
                    <button type='submit' class='btn btn-sm btn-primary w-100 text-light'><i class='fa fa-trash'></i> Borrar notificaciones</button>
                </form>
                </div>
            </div>
        </div>
    </div>`;
    // Eliminar si ya existe
    let oldModal = document.getElementById('modalNotificaciones');
    if (oldModal) oldModal.remove();
    document.body.insertAdjacentHTML('beforeend', html);
    let modal = new bootstrap.Modal(document.getElementById('modalNotificaciones'));
    modal.show();
}
