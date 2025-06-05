<!-- Formulario para crear o editar un empleado -->
<form action="<?= $form_action ?>" method="POST" enctype="multipart/form-data" novalidate>
    <!-- Grupo: Datos personales -->
    <div class="grupo-campos">
        <div class="campo <?= isset($errors['nombre']) ? 'error-input' : '' ?>">
            <label for="nombre">Nombre*</label>
            <input type="text" name="nombre" id="nombre" value="<?= htmlspecialchars($empleado->nombre ?? '') ?>" required>
            <?php if (isset($errors['nombre'])): ?>
                <span class="error"><?= $errors['nombre'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo <?= isset($errors['apellido']) ? 'error-input' : '' ?>">
            <label for="apellido">Apellido*</label>
            <input type="text" name="apellido" id="apellido" value="<?= htmlspecialchars($empleado->apellido ?? '') ?>" required>
            <?php if (isset($errors['apellido'])): ?>
                <span class="error"><?= $errors['apellido'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo <?= isset($errors['cedula']) ? 'error-input' : '' ?>">
            <label for="cedula">Cédula*</label>
            <input type="text" name="cedula" id="cedula" value="<?= htmlspecialchars($empleado->cedula ?? '') ?>" required pattern="\d{10}">
            <?php if (isset($errors['cedula'])): ?>
                <span class="error"><?= $errors['cedula'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo <?= isset($errors['genero']) ? 'error-input' : '' ?>">
            <label for="genero">Género*</label>
            <select name="genero" id="genero" required>
                <option value="">Seleccione</option>
                <option value="M" <?= ($empleado->genero ?? '') === 'M' ? 'selected' : '' ?>>Masculino</option>
                <option value="F" <?= ($empleado->genero ?? '') === 'F' ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro" <?= ($empleado->genero ?? '') === 'Otro' ? 'selected' : '' ?>>Otro</option>
            </select>
            <?php if (isset($errors['genero'])): ?>
                <span class="error"><?= $errors['genero'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?= htmlspecialchars($empleado->fecha_nacimiento ?? '') ?>">
        </div>
    </div>

    <!-- Grupo: Contacto1 -->
    <div class="grupo-campos">
        <div class="campo">
            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($empleado->email ?? '') ?>">
        </div>

        <div class="campo">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?= htmlspecialchars($empleado->telefono ?? '') ?>">
        </div>
    </div>

    <!-- Grupo: Contacto2 -->
    <div class="grupo-campos">
        <div class="campo">
            <label for="direccion">Dirección</label>
            <textarea name="direccion" id="direccion" rows="2"><?= htmlspecialchars($empleado->direccion ?? '') ?></textarea>
        </div>
    </div>

    <!-- Grupo: Laboral -->
    <div class="grupo-campos">
        <div class="campo <?= isset($errors['fecha_ingreso']) ? 'error-input' : '' ?>">
            <label for="fecha_ingreso">Fecha de Ingreso*</label>
            <input type="date" name="fecha_ingreso" id="fecha_ingreso" value="<?= htmlspecialchars($empleado->fecha_ingreso ?? '') ?>" required>
            <?php if (isset($errors['fecha_ingreso'])): ?>
                <span class="error"><?= $errors['fecha_ingreso'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo <?= isset($errors['id_cargo']) ? 'error-input' : '' ?>">
            <label for="id_cargo">Cargo*</label>
            <select name="id_cargo" id="id_cargo" required>
                <option value="">Seleccione un cargo</option>
                <?php foreach ($cargos as $cargo): ?>
                    <option value="<?= $cargo->id ?>" <?= ($empleado->id_cargo ?? '') == $cargo->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cargo->nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['id_cargo'])): ?>
                <span class="error"><?= $errors['id_cargo'] ?></span>
            <?php endif; ?>
        </div>

        <div class="campo <?= isset($errors['id_empresa']) ? 'error-input' : '' ?>">
            <label for="id_empresa">Empresa*</label>
            <select name="id_empresa" id="id_empresa" required>
                <option value="">Seleccione una empresa</option>
                <?php foreach ($empresas as $empresa): ?>
                    <option value="<?= $empresa->id ?>" <?= ($empleado->id_empresa ?? '') == $empresa->id ? 'selected' : '' ?>>
                        <?= htmlspecialchars($empresa->nombre) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['id_empresa'])): ?>
                <span class="error"><?= $errors['id_empresa'] ?></span>
            <?php endif; ?>
        </div>

        <!-- Campo: Estado del estudiante (activo/inactivo) -->
        <div class="campo">
            <label for="estado">Estado</label>
            <select name="estado" id="estado" required>
                <option value="1" <?= (isset($empleado->estado) && $empleado->estado == 1) ? 'selected' : '' ?>>Activo</option>
                <option value="0" <?= (isset($empleado->estado) && $empleado->estado == 0) ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
    </div>

    <!-- Grupo: Foto -->
    <div class="grupo-campos">
        <div class="campo">
            <label for="foto">Foto</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <?php if (!empty($empleado->foto)): ?>
                <div class="foto-actual-display">
                    <p>Foto actual:</p>
                    <img src="/uploads/<?= htmlspecialchars($empleado->foto) ?>"
                        alt="Foto del empleado"
                        class="foto-empleado-preview">
                </div>
            <?php endif; ?>

            <!-- Contenedor para vista previa -->
            <div id="preview-container" style="display: none; margin-top: 10px;">
                <p>Vista previa:</p>
                <img id="preview-image" src="#" alt="Vista previa de la imagen"
                    style="max-width: 200px; border: 1px solid #ccc; padding: 4px;">
            </div>
        </div>
    </div>

    <!-- Botones -->
    <div class="contenedor-botones">
        <a href="/empleados" class="boton boton-cancelar">Cancelar</a>
        <button type="submit" class="boton boton-registrar">Guardar</button>
    </div>
</form>

<script src="/assets/js/vista-previa-imagen.js"></script>