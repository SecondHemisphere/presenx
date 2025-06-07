<?php
// Lógica de paginación
$por_pagina = isset($_GET['por_pagina']) ? (int) $_GET['por_pagina'] : 10;
$total_registros = count($data['asistencias']);
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_paginas = ceil($total_registros / $por_pagina);

$offset = ($pagina_actual - 1) * $por_pagina;
$registros_paginados = array_slice($data['asistencias'], $offset, $por_pagina);

foreach ($registros_paginados as $asistencia) {
    if (!empty($asistencia->entrada)) {
        $asistencia->entrada = date('H:i', strtotime($asistencia->entrada));
    }
    if (!empty($asistencia->salida)) {
        $asistencia->salida = date('H:i', strtotime($asistencia->salida));
    }
}

$inicio = $offset + 1;
$fin = min($total_registros, $pagina_actual * $por_pagina);
?>

<div class="contenedor-listados">

    <!-- Alerta de éxito o error -->
    <?php
    $mensaje_exito = $data['success_message'] ?? '';
    $mensaje_error = $data['error_message'] ?? '';
    include __DIR__ . '/../components/alerta-flash.php';
    ?>

    <!-- Modal de confirmación para eliminar asistencias -->
    <?php $mensaje_confirmacion = "¿Estás seguro de que deseas eliminar esta asistencia?"; ?>
    <?php include __DIR__ . '/../components/modal-confirmacion.php'; ?>

    <!-- Título principal -->
    <h1><?= htmlspecialchars($data['title']) ?></h1>
    <hr>

    <!-- Encabezado: control de entradas y botón para nueva asistencia-->
    <?php
    $ruta_index = '/asistencias';
    $ruta_crear = '/asistencias/create';
    $texto_boton = 'Nueva Asistencia';
    $opciones_por_pagina = [5, 10, 25, 50];
    include __DIR__ . '/../components/encabezado-acciones.php';
    ?>

    <!-- Tabla de asistencias -->
    <div class="contenedor-tabla">
        <?php
        $columnas = [
            ['campo' => 'id', 'titulo' => 'ID'],
            ['campo' => 'nombre_empleado', 'titulo' => 'Nombre'],
            ['campo' => 'apellido_empleado', 'titulo' => 'Apellido'],
            ['campo' => 'entrada', 'titulo' => 'Hora de Entrada'],
            ['campo' => 'salida', 'titulo' => 'Hora de Salida'],
            ['campo' => 'fecha', 'titulo' => 'Fecha'],
            ['campo' => 'estado', 'titulo' => 'Estado'],
        ];

        $filas = $registros_paginados;
        $ruta_base = '/asistencias';

        include __DIR__ . '/../components/tabla-generica.php';
        ?>
    </div>

    <!-- Controles de paginación -->
    <?php include __DIR__ . '/../components/paginacion.php'; ?>
</div>

</body>
</html>
