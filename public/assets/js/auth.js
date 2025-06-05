document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Validación del formulario de login
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const usuario = document.getElementById('usuario').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!usuario || !password) {
                e.preventDefault();
                mostrarMensaje('Por favor complete todos los campos.', 'error');
            }
        });
    }

    // Validación del formulario de registro
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();

            if (!name || !email || !password || !confirmPassword) {
                e.preventDefault();
                mostrarMensaje('Por favor complete todos los campos.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                mostrarMensaje('Las contraseñas no coinciden.', 'error');
                return;
            }

            if (password.length < 8) {
                e.preventDefault();
                mostrarMensaje('La contraseña debe tener al menos 8 caracteres.', 'error');
            }
        });
    }

    function mostrarMensaje(mensaje, tipo) {
        let mensajeContainer = document.querySelector('.auth-mensaje-js');

        if (!mensajeContainer) {
            mensajeContainer = document.createElement('div');
            mensajeContainer.className = 'auth-mensaje-js auth-mensaje auth-' + tipo;
            document.querySelector('.auth-contenedor').prepend(mensajeContainer);
        }

        mensajeContainer.textContent = mensaje;

        // Eliminar después de 5 segundos
        setTimeout(() => mensajeContainer.remove(), 5000);
    }
});
