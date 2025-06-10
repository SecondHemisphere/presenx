<!-- Contenedor del formulario de edición -->
<div class="formulario">
    <!-- Título del formulario -->
    <h2><?= $datos['titulo'] ?></h2>

    <?php
    // Datos del usuario a editar
    $usuario = $datos['usuario'];

    // Errores de validación (si existen)
    $errores = $errores ?? [];

    // Ruta a la que se enviará el formulario
    $accion_formulario = "/usuarios/update/{$usuario->id}";

    // Texto del botón de envío
    $submit_texto = "Actualizar";

    // Inclusión del formulario reutilizable
    include __DIR__ . '/_form.php';
    ?>
</div>