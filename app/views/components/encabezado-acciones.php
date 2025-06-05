<?php

/**
 * Componente: encabezado-acciones.php
 * Descripción: Muestra el encabezado de una sección con total de entradas visibles
 * y un botón para crear un nuevo recurso.
 *
 * Parámetros esperados:
 * - $titulo (string)
 * - $ruta_crear (string)
 * - $texto_boton (string)
 * - $opciones_por_pagina (array)
 * - $por_pagina (int)
 */
?>

<div class="encabezado">
    <h2>
        Ver |
        <form method="GET" action="<?= htmlspecialchars($ruta_index) ?>" style="display:inline;">
            <select name="por_pagina" onchange="this.form.submit()">
                <?php foreach ($opciones_por_pagina as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= $por_pagina == $opcion ? 'selected' : '' ?>>
                        <?= $opcion ?>
                    </option>
                <?php endforeach; ?>
            </select>
            | Entradas
        </form>
    </h2>
    <div class="nuevo-registro">
        <a class="btn btn-primary" href="<?= htmlspecialchars($ruta_crear) ?>">
            <i class="fas fa-plus"></i> <?= htmlspecialchars($texto_boton) ?>
        </a>
    </div>
</div>