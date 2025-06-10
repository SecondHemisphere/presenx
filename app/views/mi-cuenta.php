<div class="contenedor-mi-cuenta">
    <div class="encabezado-configuracion">
        <h2>Mi Cuenta</h2>
        <p class="subtitulo">Administra la información de tu perfil y cambia tu contraseña</p>
        <hr>
    </div>

    <form action="/mi-cuenta/actualizar" method="POST" novalidate>
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

    <hr>

    <form action="/mi-cuenta/cambiar-contrasena" method="POST" novalidate>
        <label for="actual_password">Contraseña actual*</label>
        <input type="password" id="actual_password" name="actual_password" required minlength="8">

        <label for="nueva_password">Nueva contraseña*</label>
        <input type="password" id="nueva_password" name="nueva_password" required minlength="8">

        <label for="confirmar_password">Confirmar nueva contraseña*</label>
        <input type="password" id="confirmar_password" name="confirmar_password" required minlength="8">

        <div class="contenedor-botones">
            <button type="submit" class="boton boton-guardar"><i class="fas fa-key"></i> Cambiar Contraseña</button>
        </div>
    </form>
</div>

<style>
    .contenedor-mi-cuenta {
        background-color: #ffffff;
        padding: 1rem;
        border-radius: 14px;
    }

    .contenedor-mi-cuenta .encabezado-configuracion h2 {
        font-size: 2rem;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .contenedor-mi-cuenta .encabezado-configuracion .subtitulo {
        font-size: 1.6rem;
        color: #7f8c8d;
    }

    .contenedor-mi-cuenta hr {
        border: none;
        border-top: 0.2rem solid #ddd;
        margin-bottom: 2rem;
        margin-top: 0.3rem;
    }

    .contenedor-mi-cuenta form label {
        font-weight: 600;
        font-size: 1.6rem;
    }

    .contenedor-mi-cuenta form input {
        padding: 0.6rem;
        border: 1px solid #bbb;
        border-radius: 8px;
        width: 100%;
        margin-top: 0.3rem;
        margin-bottom: 0.3rem;
    }

    .contenedor-mi-cuenta form input:focus {
        border-color: #2980b9;
        outline: none;
    }

    .contenedor-mi-cuenta .error {
        color: #d9534f;
        font-size: 1.4rem;
    }

    .contenedor-mi-cuenta .contenedor-botones {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 0.5rem;
        margin-bottom: 1rem;
    }

    .contenedor-mi-cuenta .boton {
        padding: 0.7rem 1.4rem;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 1rem;
        color: white;
    }

    .contenedor-mi-cuenta .boton-guardar {
        background-color: var(--color-secundario, #2980b9);
    }

    .contenedor-mi-cuenta .boton-guardar:hover {
        background-color: #1f618d;
    }
    
</style>