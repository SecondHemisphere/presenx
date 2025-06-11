<?php

/**
 * Componente: alerta-flash.php
 * Descripción: Muestra un mensaje emergente de éxito o error al usuario.
 *
 * Parámetros esperados:
 * - $mensaje_exito (string): Mensaje de éxito (opcional).
 * - $mensaje_error (string): Mensaje de error (opcional).
 */

?>

<?php
$mensaje_exito = $mensaje_exito ?? ($_SESSION['mensaje_exito'] ?? null);
$mensaje_error = $mensaje_error ?? ($_SESSION['mensaje_error'] ?? null);

if (isset($_SESSION['mensaje_exito'])) unset($_SESSION['mensaje_exito']);
if (isset($_SESSION['mensaje_error'])) unset($_SESSION['mensaje_error']);
?>

<?php if (!empty($mensaje_exito) || !empty($mensaje_error)): ?>
    <div id="customAlert" class="custom-alert" style="display: flex;">
        <div class="alert-content <?= $mensaje_error ? 'error' : 'success' ?>">
            <div class="alert-icon"><?= $mensaje_error ? '✕' : '✓' ?></div>
            <h3><?= $mensaje_error ? 'Error' : '¡Correcto!' ?></h3>
            <p><?= htmlspecialchars($mensaje_error ?: $mensaje_exito) ?></p>
            <button id="alertConfirmBtn" class="alert-button">Aceptar</button>
        </div>
    </div>
<?php endif; ?>