<form action="<?= htmlspecialchars($accion_formulario) ?>" method="POST" novalidate>
    <div class="grupo-campos">
        <!-- Nombre -->
        <div class="campo <?= isset($errores['nombre']) ? 'error-input' : '' ?>">
            <label for="nombre">Nombre*</label>
            <input type="text" name="nombre" id="nombre"
                value="<?= htmlspecialchars($usuario->nombre ?? $_POST['nombre'] ?? '') ?>" required>
            <?php if (isset($errores['nombre'])): ?>
                <span class="error"><?= htmlspecialchars($errores['nombre']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Email -->
        <div class="campo <?= isset($errores['email']) ? 'error-input' : '' ?>">
            <label for="email">Email*</label>
            <input type="email" name="email" id="email"
                value="<?= htmlspecialchars($usuario->email ?? $_POST['email'] ?? '') ?>" required>
            <?php if (isset($errores['email'])): ?>
                <span class="error"><?= htmlspecialchars($errores['email']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Contraseña -->
        <div class="campo <?= isset($errores['password']) ? 'error-input' : '' ?>">
            <label for="password">Contraseña<?= !empty($usuario->id) ? '' : '*' ?></label>
            <input type="password" name="password" id="password" <?= !empty($usuario->id) ? '' : 'required' ?>>
            <?php if (isset($errores['password'])): ?>
                <span class="error"><?= htmlspecialchars($errores['password']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Rol -->
        <div class="campo <?= isset($errores['rol']) ? 'error-input' : '' ?>">
            <label for="rol">Rol*</label>
            <select name="rol" id="rol" required>
                <option value="Administrador" <?= (isset($usuario->rol) && $usuario->rol === 'Administrador') ? 'selected' : '' ?>>Administrador</option>
                <option value="Usuario" <?= (isset($usuario->rol) && $usuario->rol === 'Usuario') ? 'selected' : '' ?>>Usuario</option>
            </select>
            <?php if (isset($errores['rol'])): ?>
                <span class="error"><?= htmlspecialchars($errores['rol']) ?></span>
            <?php endif; ?>
        </div>

        <!-- Estado -->
        <div class="campo <?= isset($errores['estado']) ? 'error-input' : '' ?>">
            <label for="estado">Estado*</label>
            <select name="estado" id="estado" required>
                <option value="activo" <?= (isset($usuario->estado) && $usuario->estado === 'activo') ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= (isset($usuario->estado) && $usuario->estado === 'inactivo') ? 'selected' : '' ?>>Inactivo</option>
            </select>
            <?php if (isset($errores['estado'])): ?>
                <span class="error"><?= htmlspecialchars($errores['estado']) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones -->
    <div class="contenedor-botones">
        <a href="/usuarios" class="boton boton-cancelar">Cancelar</a>
        <button type="submit" class="boton boton-registrar">Guardar</button>
    </div>
</form>
