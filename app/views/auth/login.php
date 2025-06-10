<div class="auth-contenedor">
    <h2 class="auth-titulo">Iniciar Sesión</h2>

    <!-- Mensaje de éxito -->
    <?php if (isset($_SESSION['exito'])): ?>
        <div class="auth-mensaje auth-exito">
            <?= $_SESSION['exito'];
            unset($_SESSION['exito']); ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de inicio sesión -->
    <form action="/auth/login" method="POST" class="auth-formulario" novalidate>
        <div class="auth-grupo <?= isset($errores['email']) ? 'error-input' : '' ?>">
            <label for="email">Correo electrónico*</label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required
                value="<?= htmlspecialchars($datos['email'] ?? '') ?>">
            <?php if (isset($errores['email'])): ?>
                <span class="error"><?= htmlspecialchars($errores['email']) ?></span>
            <?php endif; ?>
        </div>

        <div class="auth-grupo <?= isset($errores['password']) ? 'error-input' : '' ?>">
            <label for="password">Contraseña*</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($errores['password'])): ?>
                <span class="error"><?= htmlspecialchars($errores['password']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="auth-boton auth-boton-principal">Iniciar Sesión</button>
    </form>

    <p class="auth-texto-secundario">
        ¿No tienes una cuenta?
        <a href="/register" class="auth-enlace">Registrarse</a>
    </p>
</div>