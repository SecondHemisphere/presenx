<div class="auth-contenedor">
    <h2 class="auth-titulo">Iniciar Sesión</h2>

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
    <form id="loginForm" action="/auth/login" method="POST" class="auth-formulario">
        <div class="auth-grupo">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="auth-grupo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="auth-boton auth-boton-principal">Ingresar</button>
    </form>

    <p class="auth-texto-secundario">
        ¿No tienes una cuenta?
        <a href="/register" class="auth-enlace">Regístrate aquí</a>
    </p>
</div>

<script src="/assets/js/auth.js"></script>