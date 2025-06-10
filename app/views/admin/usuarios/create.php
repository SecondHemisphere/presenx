<!-- Contenedor del formulario de registro -->
<div class="formulario">
    <!-- Título del formulario -->
    <h2><?= $datos['titulo'] ?></h2>

    <?php
    // Usuario vacío (formulario de creación)
    $usuario = [];

    // Errores de validación (si existen)
    $errores = $errores ?? [];

    // Ruta a la que se enviará el formulario
    $accion_formulario = "/usuarios/store";

    // Texto del botón de envío
    $submit_texto = "Registrar";

    // Inclusión del formulario reutilizable
    include __DIR__ . '/_form.php';
    ?>
</div>