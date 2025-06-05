<!-- Contenedor del formulario de edición -->
<div class="formulario">
    <!-- Título del formulario -->
    <h2><?= $data['title'] ?></h2>

    <?php
    // Datos del cargo a editar
    $cargo = $data['cargo'];

    // Errores de validación (si existen)
    $errors = $errors ?? [];

    // Ruta a la que se enviará el formulario
    $form_action = "/cargos/update/{$cargo->id}";

    // Texto del botón de envío
    $submit_text = "Actualizar";

    // Inclusión del formulario reutilizable
    include __DIR__ . '/_form.php';
    ?>
</div>