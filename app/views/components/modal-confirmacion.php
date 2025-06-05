<?php

/**
 * Componente: modal-confirmacion.php
 * Descripción: Muestra un modal para confirmar acciones sensibles (como eliminar).
 *
 * Parámetros esperados:
 * - $mensaje_confirmacion (string): Mensaje personalizado de confirmación (opcional).
 */

?>

<div id="confirmModal" class="custom-confirm">
    <div class="confirm-content">
        <div class="confirm-icon">!</div>
        <h3>Confirmar acción</h3>
        <p><?= $mensaje_confirmacion ?? '¿Estás seguro de que deseas continuar?' ?></p>
        <div class="confirm-actions">
            <button id="confirmCancel" class="btn-cancel">Cancelar</button>
            <button id="confirmDelete" class="btn-confirm">Eliminar</button>
        </div>
    </div>
</div>