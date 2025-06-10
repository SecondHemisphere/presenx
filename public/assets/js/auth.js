document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    // Validación del formulario de login
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            const email = document.getElementById('email')?.value.trim();
            const password = document.getElementById('password')?.value.trim();

            if (!email || !password) {
                e.preventDefault();
                mostrarMensaje('Por favor complete todos los campos.', 'error');
            }
        });
    }

    // Validación del formulario de registro
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            const name = document.getElementById('nombre')?.value.trim();
            const email = document.getElementById('email')?.value.trim();
            const password = document.getElementById('password')?.value.trim();
            const confirmPassword = document.getElementById('confirm_password')?.value.trim();

            if (!name || !email || !password || !confirmPassword) {
                e.preventDefault();
                mostrarMensaje('Por favor complete todos los campos.', 'error');
                return;
            }

            if (!validarEmail(email)) {
                e.preventDefault();
                mostrarMensaje('Ingrese un correo válido.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                mostrarMensaje('Las contraseñas no coinciden.', 'error');
                return;
            }

            if (password.length < 6) {
                e.preventDefault();
                mostrarMensaje('La contraseña debe tener al menos 6 caracteres.', 'error');
            }
        });
    }

    function mostrarMensaje(mensaje, tipo) {
        let mensajeContainer = document.querySelector('.auth-mensaje-js');

        if (!mensajeContainer) {
            mensajeContainer = document.createElement('div');
            mensajeContainer.className = `auth-mensaje-js auth-mensaje auth-${tipo}`;
            document.querySelector('.auth-contenedor')?.prepend(mensajeContainer);
        }

        mensajeContainer.textContent = mensaje;

        setTimeout(() => {
            if (mensajeContainer) {
                mensajeContainer.remove();
            }
        }, 5000);
    }

    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});
