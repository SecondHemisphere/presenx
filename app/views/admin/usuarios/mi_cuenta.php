<?php include __DIR__ . '/../../components/alerta-flash.php'; ?>

<div class="contenedor-mi-cuenta">
    <div class="encabezado-configuracion">
        <h2>Mi Cuenta</h2>
        <p class="subtitulo">Administra la información de tu perfil y cambia tu contraseña</p>
        <hr>
    </div>

    <form action="/usuarios/actualizar-cuenta" method="POST" novalidate>
        <label for="nombre">Nombre completo*</label>
        <input type="text" id="nombre" name="nombre" required maxlength="150" value="<?= htmlspecialchars($usuario->nombre ?? '') ?>">
        <?php if (isset($errores['nombre'])): ?>
            <span class="error"><?= $errores['nombre'] ?></span>
        <?php endif; ?>

        <label for="email">Correo electrónico*</label>
        <input type="email" id="email" name="email" required maxlength="100" value="<?= htmlspecialchars($usuario->email ?? '') ?>">
        <?php if (isset($errores['email'])): ?>
            <span class="error"><?= $errores['email'] ?></span>
        <?php endif; ?>

        <div class="contenedor-botones">
            <button type="submit" class="boton boton-guardar"><i class="fas fa-save"></i> Guardar Cambios</button>
        </div>
    </form>
</div>