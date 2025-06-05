<!DOCTYPE html>
<html lang="es">
<!-- Head -->
<?php require_once __DIR__ . '/head.php'; ?>

<body>
    <!-- Navegación -->
    <?php
    if (!($esLogin ?? false)) {
        require_once __DIR__ . '/navbar_sistema.php';
    } else {
        require_once __DIR__ . '/navbar_publico.php';
    }
    ?>

    <?php if (!($esLogin ?? false)): ?>
        <!-- Layout Principal -->
        <div class="layout-principal">
            <!-- Barra Lateral -->
            <?php if (empty($hideSidebar)): ?>
                <aside class="sidebar">
                    <?php require_once __DIR__ . '/sidebar.php'; ?>
                </aside>
            <?php endif; ?>
            <!-- Contenido Principal -->
            <main class="contenedor-principal">
    <?php else: ?>
        <!-- Layout simplificado para login -->
        <main class="contenedor-principal-login">
    <?php endif; ?>

    <!-- Vista -->
    <?php
    if (isset($view)) {
        if (isset($data)) extract($data);
        require_once $view;
    } else {
        echo "<p>Error: vista no especificada.</p>";
    }
    ?>

    <?php if (!($esLogin ?? false)): ?>
            </main>
        </div>
    <?php else: ?>
        </main>
    <?php endif; ?>

    <!-- Footer -->
    <?php
    if (!($esLogin ?? false)) {
        require_once __DIR__ . '/footer_sistema.php';
    } else {
        require_once __DIR__ . '/footer_publico.php';
    }
    ?>

    <!-- Scripts -->
    <script src="/assets/js/alerta.js"></script>
</body>
</html>
