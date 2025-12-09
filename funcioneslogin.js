function abrirRestablecer(){
    Swal.fire({
        title: 'Restablecer contraseña',
        input: 'email',
        inputLabel: 'Ingresa tu correo electrónico',
        inputPlaceholder: 'usuario@gmail.com',
        showCancelButton: true,
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        preConfirm: (email) => {
            if(!email){
                Swal.showValidationMessage('Por favor ingresa un correo');
            }
            return fetch('usuarios/solicitar_restablecer.php',{
                method: 'POST',
                headers: {'Accept':'application/json'},
                body: new URLSearchParams({correo: email})
            }).then(response => {
                if(!response.ok) throw new Error(response.statusText);
                return response.json();
            }).catch(() => {
                return {status: 'error'};
            });
        }
    }).then((result) => {
        if(result.isConfirmed){
            const resp = result.value;
            if(resp && resp.status === 'ok'){
                Swal.fire({icon: 'success', title: 'Correo enviado', text: 'Revisa tu correo para restablecer la contraseña.'});
            } else if(resp && resp.status === 'noexiste'){
                Swal.fire({icon: 'error', title: 'Correo no encontrado', text: 'El correo no existe en el sistema.'});
            } else {
                Swal.fire({icon: 'error', title: 'Error', text: 'Ocurrió un error, intenta más tarde.'});
            }
        }
    });
}