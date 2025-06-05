<div class="auth-contenedor">
    <h2 class="auth-titulo">Crear Cuenta</h2>

    <!-- Mensaje de error -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="auth-mensaje auth-error">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Mensaje de éxito -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="auth-mensaje auth-exito">
            <?= $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de inicio de sesión -->
    <form id="registerForm" action="/auth/register" method="POST" class="auth-formulario">
        <div class="auth-grupo">
            <label for="name">Nombre completo</label>
            <input type="text" id="name" name="name" placeholder="Ej: Juan Pérez" required>
        </div>

        <div class="auth-grupo">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
        </div>

        <div class="auth-grupo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
        </div>

        <div class="auth-grupo">
            <label for="confirm_password">Confirmar contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required>
        </div>

        <button type="submit" class="auth-boton auth-boton-principal">Registrarse</button>
    </form>

    <!-- Enlace para usuarios que ya tienen cuenta -->
    <p class="auth-texto-secundario">
        ¿Ya tienes una cuenta?
        <a href="/login" class="auth-enlace">Inicia Sesión</a>
    </p>
</div>

<script src="/assets/js/auth.js"></script>