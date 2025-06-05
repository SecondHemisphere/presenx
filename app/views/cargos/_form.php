<!-- Formulario para crear o editar un estudiante -->
<form action="<?= $form_action ?>" method="POST" novalidate>
    <!-- Grupo 1: Datos de identificación -->
    <div class="grupo-campos">
        <!-- Campo: Nombre -->
        <div class="campo campo-ancho <?= isset($errors['nombre']) ? 'error-input' : '' ?>">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre"
                value="<?= htmlspecialchars($cargo->nombre ?? '') ?>" required>
            <?php if (isset($errors['nombre'])): ?>
                <span class="error"><?= htmlspecialchars($errors['nombre']) ?></span>
            <?php endif; ?>
        </div>
        <!-- Campo: Descripción -->
        <div class="campo campo-ancho <?= isset($errors['descripcion']) ? 'error-input' : '' ?>">
            <label for="descripcion">Descripción</label>
            <textarea name="descripcion" id="descripcion" rows="3"><?= htmlspecialchars($cargo->descripcion ?? '') ?></textarea>
            <?php if (isset($errors['descripcion'])): ?>
                <span class="error"><?= htmlspecialchars($errors['descripcion']) ?></span>
            <?php endif; ?>
        </div>

    </div>
    <!-- Grupo de botones -->
    <div class="contenedor-botones">
        <a href="/cargos" class="boton boton-cancelar">Cancelar</a>
        <button type="submit" class="boton boton-registrar">Guardar</button>
    </div>
</form>