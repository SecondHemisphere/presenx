<?php

/**
 * Componente: paginacion.php
 * Descripción: Muestra controles de navegación para paginación de resultados.
 *
 * Parámetros requeridos:
 * - $pagina_actual (int): Número de la página actual.
 * - $total_paginas (int): Total de páginas disponibles.
 * - $total_registros (int): Total de registros en la lista.
 * - $inicio (int): Índice del primer registro mostrado.
 * - $fin (int): Índice del último registro mostrado.
 * - $ruta_base (string): Ruta para los enlaces de paginación.
 */

?>

<div class="paginacion-container">
    <nav>
        <ul class="paginacion-list">
            <!-- Enlace a la página anterior -->
            <li class="paginacion-item <?= $pagina_actual <= 1 ? 'disabled' : '' ?>">
                <a href="<?= $ruta_base ?>?pagina=<?= $pagina_actual - 1 ?>" class="paginacion-link">Anterior</a>
            </li>

            <!-- Enlaces numerados a cada página -->
            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                <li class="paginacion-item <?= $i == $pagina_actual ? 'active' : '' ?>">
                    <a href="<?= $ruta_base ?>?pagina=<?= $i ?>" class="paginacion-link"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <!-- Enlace a la página siguiente -->
            <li class="paginacion-item <?= $pagina_actual >= $total_paginas ? 'disabled' : '' ?>">
                <a href="<?= $ruta_base ?>?pagina=<?= $pagina_actual + 1 ?>" class="paginacion-link">Siguiente</a>
            </li>
        </ul>
    </nav>

    <!-- Información sobre el rango de resultados mostrados -->
    <div class="paginacion-info">
        Mostrando <span class="highlight"><?= $inicio ?></span> a <span class="highlight"><?= $fin ?></span> de <span class="highlight"><?= $total_registros ?></span> Entradas
    </div>
</div>