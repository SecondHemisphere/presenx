<?php include __DIR__ . '/../../components/alerta-flash.php'; ?>

<div class="contenedor-mi-cuenta">
    <div class="encabezado-configuracion">
        <h2>Cambiar Contraseña</h2>
        <p class="subtitulo">Actualiza tu contraseña de acceso</p>
        <hr>
    </div>

    <form action="/usuarios/actualizar-contrasena" method="POST" novalidate>
        <label for="contrasena_actual">Contraseña actual*</label>
        <input type="password" id="contrasena_actual" name="contrasena_actual" required minlength="8">
        <?php if (!empty($errores['contrasena_actual'])): ?>
            <span class="error"><?= htmlspecialchars($errores['contrasena_actual']) ?></span>
        <?php endif; ?>

        <label for="nueva_contrasena">Nueva contraseña*</label>
        <input type="password" id="nueva_contrasena" name="nueva_contrasena" required minlength="8">
        <?php if (!empty($errores['nueva_contrasena'])): ?>
            <span class="error"><?= htmlspecialchars($errores['nueva_contrasena']) ?></span>
        <?php endif; ?>

        <label for="confirmar_contrasena">Confirmar nueva contraseña*</label>
        <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required minlength="8">
        <?php if (!empty($errores['confirmar_contrasena'])): ?>
            <span class="error"><?= htmlspecialchars($errores['confirmar_contrasena']) ?></span>
        <?php endif; ?>

        <div class="contenedor-botones">
            <a href="/usuarios/mi-cuenta" class="link-cambiar-contrasena">
                Regresar
            </a>
            <button type="submit" class="boton boton-guardar">
                <i class="fas fa-key"></i> Cambiar Contraseña
            </button>
        </div>
    </form>
</div>