<div class="contenedor-empresa">
    <div class="encabezado-configuracion">
        <h2>Configuración de la Empresa</h2>
        <p class="subtitulo">Administra la información general de tu empresa registrada en el sistema.</p>
        <hr>
    </div>

    <form action="<?= $form_action ?>" method="POST" class="formulario-empresa" novalidate>
        <div class="grupo-campos">
            <div class="campo <?= isset($errors['nombre']) ? 'error-input' : '' ?>">
                <label for="nombre">Nombre*</label>
                <input type="text" name="nombre" id="nombre"
                    maxlength="150"
                    value="<?= htmlspecialchars($empresa->nombre ?? '') ?>"
                    <?= $modo === 'ver' ? 'disabled' : 'required' ?>>
                <?php if (isset($errors['nombre'])): ?>
                    <span class="error"><?= $errors['nombre'] ?></span>
                <?php endif; ?>
            </div>

            <div class="campo <?= isset($errors['ruc']) ? 'error-input' : '' ?>">
                <label for="ruc">RUC*</label>
                <input type="text" name="ruc" id="ruc"
                    pattern="\d{13}" maxlength="13"
                    title="Debe contener exactamente 13 dígitos"
                    value="<?= htmlspecialchars($empresa->ruc ?? '') ?>"
                    <?= $modo === 'ver' ? 'disabled' : 'required' ?>>
                <?php if (isset($errors['ruc'])): ?>
                    <span class="error"><?= $errors['ruc'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="grupo-campos">
            <div class="campo <?= isset($errors['telefono']) ? 'error-input' : '' ?>">
                <label for="telefono">Teléfono</label>
                <input type="text" name="telefono" id="telefono"
                    maxlength="20"
                    value="<?= htmlspecialchars($empresa->telefono ?? '') ?>"
                    <?= $modo === 'ver' ? 'disabled' : '' ?>>
                <?php if (isset($errors['telefono'])): ?>
                    <span class="error"><?= $errors['telefono'] ?></span>
                <?php endif; ?>
            </div>

            <div class="campo <?= isset($errors['email']) ? 'error-input' : '' ?>">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" id="email"
                    maxlength="100"
                    value="<?= htmlspecialchars($empresa->email ?? '') ?>"
                    <?= $modo === 'ver' ? 'disabled' : '' ?>>
                <?php if (isset($errors['email'])): ?>
                    <span class="error"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="grupo-campos">
            <div class="campo <?= isset($errors['ubicacion']) ? 'error-input' : '' ?>">
                <label for="ubicacion">Ubicación</label>
                <input type="text" name="ubicacion" id="ubicacion"
                    maxlength="255"
                    value="<?= htmlspecialchars($empresa->ubicacion ?? '') ?>"
                    <?= $modo === 'ver' ? 'disabled' : '' ?>>
                <?php if (isset($errors['ubicacion'])): ?>
                    <span class="error"><?= $errors['ubicacion'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <div class="contenedor-botones">
            <?php if ($modo === 'ver'): ?>
                <a href="/empresa/configuracion?edit=1" class="boton boton-editar"><i class="fas fa-pen"></i> Modificar</a>
            <?php else: ?>
                <a href="/empresa/configuracion" class="boton boton-cancelar"><i class="fas fa-times"></i> Cancelar</a>
                <button type="submit" class="boton boton-registrar"><i class="fas fa-save"></i> Guardar</button>
            <?php endif; ?>
        </div>
    </form>
</div>
