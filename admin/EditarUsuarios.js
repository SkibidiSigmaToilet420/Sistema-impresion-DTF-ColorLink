function limpiar() {
    document.getElementById('formNuevoUsuario').reset();
}

document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/ocultar contraseña actual
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('pass');
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                passwordInput.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }

    // Mostrar/ocultar nueva contraseña
    const toggleNuevaPassword = document.getElementById('toggleNuevaPassword');
    const nuevaPasswordInput = document.getElementById('nuevapass');
    if (toggleNuevaPassword && nuevaPasswordInput) {
        toggleNuevaPassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (nuevaPasswordInput.type === 'password') {
                nuevaPasswordInput.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                nuevaPasswordInput.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }

    // Mostrar/ocultar confirmar contraseña
    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPasswordInput = document.getElementById('repass');
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                if (icon) {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            } else {
                confirmPasswordInput.type = 'password';
                if (icon) {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        });
    }
});