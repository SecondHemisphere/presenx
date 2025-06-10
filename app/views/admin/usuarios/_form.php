<form action="<?= $form_action ?>" method="POST" novalidate>
    <div class="grupo-campos">
        <!-- Nombre -->
        <div class="campo <?= isset($errors['nombre']) ? 'error-input' : '' ?>">
            <label for="nombre">Nombre*</label>
            <input type="text" name="nombre" id="nombre"
                value="<?= htmlspecialchars($usuario->nombre ?? $_POST['nombre'] ?? '') ?>" required>
            <?php if (isset($errors['nombre'])): ?>
                <span class="error"><?= $errors['nombre'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="campo <?= isset($errors['email']) ? 'error-input' : '' ?>">
            <label for="email">Email*</label>
            <input type="email" name="email" id="email"
                value="<?= htmlspecialchars($usuario->email ?? $_POST['email'] ?? '') ?>" required>
            <?php if (isset($errors['email'])): ?>
                <span class="error"><?= $errors['email'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Contraseña -->
        <div class="campo <?= isset($errors['password']) ? 'error-input' : '' ?>">
            <label for="password">Contraseña<?= isset($usuario) ? '' : '*' ?></label>
            <input type="password" name="password" id="password" <?= isset($usuario) ? '' : 'required' ?>>
            <?php if (isset($errors['password'])): ?>
                <span class="error"><?= $errors['password'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Rol -->
        <div class="campo <?= isset($errors['rol']) ? 'error-input' : '' ?>">
            <label for="rol">Rol*</label>
            <select name="rol" id="rol" required>
                <option value="Administrador" <?= ($usuario->rol ?? '') === 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                <option value="Usuario" <?= ($usuario->rol ?? '') === 'Usuario' ? 'selected' : '' ?>>Usuario</option>
            </select>
            <?php if (isset($errors['rol'])): ?>
                <span class="error"><?= $errors['rol'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Estado -->
        <div class="campo <?= isset($errors['estado']) ? 'error-input' : '' ?>">
            <label for="estado">Estado*</label>
            <select name="estado" id="estado" required>
                <option value="activo" <?= ($usuario->estado ?? '') === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= ($usuario->estado ?? '') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
            <?php if (isset($errors['estado'])): ?>
                <span class="error"><?= $errors['estado'] ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones -->
    <div class="contenedor-botones">
        <a href="/usuarios" class="boton boton-cancelar">Cancelar</a>
        <button type="submit" class="boton boton-registrar">Guardar</button>
    </div>
</form>
