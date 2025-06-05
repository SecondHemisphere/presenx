<form action="<?= $form_action ?>" method="POST" novalidate>
    <!-- Grupo: Datos de asistencia -->
    <div class="grupo-campos">
        <!-- Empleado -->
        <div class="campo <?= isset($errors['id_empleado']) ? 'error-input' : '' ?>">
            <label for="id_empleado">Empleado*</label>
            <select name="id_empleado" id="id_empleado" required>
                <option value="">Seleccione un empleado</option>
                <?php foreach ($empleados as $empleado): ?>
                    <option value="<?= $empleado->id ?>" <?= ($asistencia->id_empleado ?? '') == $empleado->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($empleado->nombre . ' ' . $empleado->apellido) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['id_empleado'])): ?>
                <span class="error"><?= $errors['id_empleado'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Fecha -->
        <div class="campo <?= isset($errors['fecha']) ? 'error-input' : '' ?>">
            <label for="fecha">Fecha*</label>
            <input type="date" name="fecha" id="fecha"
                value="<?= htmlspecialchars($_POST['fecha'] ?? date('Y-m-d')) ?>">
            <?php if (isset($errors['fecha'])): ?>
                <span class="error"><?= $errors['fecha'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Entrada -->
        <div class="campo <?= isset($errors['entrada']) ? 'error-input' : '' ?>">
            <label for="entrada">Hora de Entrada*</label>
            <input type="time" name="entrada" id="entrada"
                value="<?= htmlspecialchars($_POST['entrada'] ?? '08:00') ?>">
            <?php if (isset($errors['entrada'])): ?>
                <span class="error"><?= $errors['entrada'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Salida -->
        <div class="campo <?= isset($errors['salida']) ? 'error-input' : '' ?>">
            <label for="salida">Hora de Salida</label>
            <input type="time" name="salida" id="salida"
                value="<?= htmlspecialchars($_POST['salida'] ?? '17:00') ?>">
            <?php if (isset($errors['salida'])): ?>
                <span class="error"><?= $errors['salida'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Observaciones -->
        <div class="campo">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="2"><?= htmlspecialchars($asistencia->observaciones ?? '') ?></textarea>
        </div>

        <!-- Estado -->
        <div class="campo <?= isset($errors['estado']) ? 'error-input' : '' ?>">
            <label for="estado">Estado*</label>
            <select name="estado" id="estado" required>
                <option value="puntual" <?= ($asistencia->estado ?? '') === 'puntual' ? 'selected' : '' ?>>Puntual</option>
                <option value="tarde" <?= ($asistencia->estado ?? '') === 'tarde' ? 'selected' : '' ?>>Tarde</option>
                <option value="falta" <?= ($asistencia->estado ?? '') === 'falta' ? 'selected' : '' ?>>Falta</option>
            </select>
            <?php if (isset($errors['estado'])): ?>
                <span class="error"><?= $errors['estado'] ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Botones -->
    <div class="contenedor-botones">
        <a href="/asistencias" class="boton boton-cancelar">Cancelar</a>
        <button type="submit" class="boton boton-registrar">Guardar</button>
    </div>
</form>