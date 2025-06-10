<div class="auth-contenedor">
    <h2 class="auth-titulo">Crear Cuenta</h2>

    <!-- Formulario de registro -->
    <form action="/auth/register" method="POST" class="auth-formulario" novalidate>
        <div class="auth-grupo <?= isset($errores['nombre']) ? 'error-input' : '' ?>">
            <label for="nombre">Nombre completo*</label>
            <input type="text" id="nombre" name="nombre" placeholder="Ej: Juan Pérez" required
                value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>">
            <?php if (isset($errores['nombre'])): ?>
                <span class="error"><?= htmlspecialchars($errores['nombre']) ?></span>
            <?php endif; ?>
        </div>

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
            <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
            <?php if (isset($errores['password'])): ?>
                <span class="error"><?= htmlspecialchars($errores['password']) ?></span>
            <?php endif; ?>
        </div>

        <div class="auth-grupo <?= isset($errores['confirm_password']) ? 'error-input' : '' ?>">
            <label for="confirm_password">Confirmar contraseña*</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required minlength="8">
            <?php if (isset($errores['confirm_password'])): ?>
                <span class="error"><?= htmlspecialchars($errores['confirm_password']) ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="auth-boton auth-boton-principal">Registrarse</button>
    </form>

    <p class="auth-texto-secundario">
        ¿Ya tienes una cuenta?
        <a href="/login" class="auth-enlace">Inicia Sesión</a>
    </p>
</div>
