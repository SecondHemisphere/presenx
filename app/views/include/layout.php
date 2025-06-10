<!DOCTYPE html>
<html lang="es">
<!-- Head -->
<?php require_once __DIR__ . '/head.php'; ?>

<body>
    <!-- Navegación -->
    <?php
    // Si no es página de login, carga la barra de navegación del sistema
    // Si es página de login, carga barra de navegación pública
    if (!($esLogin ?? false)) {
        require_once __DIR__ . '/navbar_sistema.php';
    } else {
        require_once __DIR__ . '/navbar_publico.php';
    }
    ?>

    <?php if (!($esLogin ?? false)): ?>
        <!-- Layout principal para páginas internas -->
        <div class="layout-principal">
            <!-- Barra lateral -->
            <?php if (empty($ocultarSidebar)): ?>
                <aside class="sidebar">
                    <?php require_once __DIR__ . '/sidebar.php'; ?>
                </aside>
            <?php endif; ?>

            <!-- Contenido principal -->
            <main class="contenedor-principal">
            <?php else: ?>
                <!-- Layout simplificado para página de login -->
                <main class="contenedor-principal-login">
                <?php endif; ?>

                <!-- Inclusión de la vista dinámica -->
                <?php
                if (isset($vista)) {
                    // Extraer datos para que estén disponibles como variables en la vista
                    if (isset($datos)) extract($datos);
                    require_once __DIR__ . '/../' . $vista;
                } else {
                    echo "<p>Error: no se especificó la vista.</p>";
                }
                ?>

                <?php if (!($esLogin ?? false)): ?>
                </main>
        </div>
    <?php else: ?>
        </main>
    <?php endif; ?>

    <!-- Pie de página -->
    <?php
    // Carga pie de página según tipo de página (login o sistema)
    if (!($esLogin ?? false)) {
        require_once __DIR__ . '/footer_sistema.php';
    } else {
        require_once __DIR__ . '/footer_publico.php';
    }
    ?>

    <!-- Scripts JS -->
    <script src="/assets/js/alerta.js"></script>
</body>

</html>