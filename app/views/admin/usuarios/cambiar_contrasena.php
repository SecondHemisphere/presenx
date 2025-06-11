<div class="contenedor-mi-cuenta">
    <form action="/usuarios/cambiar-contrasena" method="POST" novalidate>
        <label for="actual_password">Contraseña actual*</label>
        <input type="password" id="actual_password" name="actual_password" required minlength="8">
        <?php if (isset($errores['actual_password'])): ?>
            <span class="error"><?= $errores['actual_password'] ?></span>
        <?php endif; ?>

        <label for="nueva_password">Nueva contraseña*</label>
        <input type="password" id="nueva_password" name="nueva_password" required minlength="8">
        <?php if (isset($errores['nueva_password'])): ?>
            <span class="error"><?= $errores['nueva_password'] ?></span>
        <?php endif; ?>

        <label for="confirmar_password">Confirmar nueva contraseña*</label>
        <input type="password" id="confirmar_password" name="confirmar_password" required minlength="8">
        <?php if (isset($errores['confirmar_password'])): ?>
            <span class="error"><?= $errores['confirmar_password'] ?></span>
        <?php endif; ?>

        <div class="contenedor-botones">
            <button type="submit" class="boton boton-guardar"><i class="fas fa-key"></i> Cambiar Contraseña</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../include/mi_cuenta_estilos.php'; ?>
