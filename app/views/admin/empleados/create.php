<!-- Contenedor del formulario de registro -->
<div class="formulario">
    <!-- Título del formulario -->
    <h2><?= $data['title'] ?></h2>

    <?php
    // Empleado vacío (formulario de creación)
    $empleado = [];

    // Errores de validación (si existen)
    $errors = $errors ?? [];

    // Ruta a la que se enviará el formulario
    $form_action = "/empleados/store";

    // Texto del botón de envío
    $submit_text = "Registrar";

    // Inclusión del formulario reutilizable
    include __DIR__ . '/_form.php';
    ?>
</div>