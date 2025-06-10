<?php
// Variables paginación
$por_pagina = isset($_GET['por_pagina']) ? (int) $_GET['por_pagina'] : 10;
$total_registros = count($datos['usuarios']);
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_paginas = (int) ceil($total_registros / $por_pagina);

$offset = ($pagina_actual - 1) * $por_pagina;
$registros_paginados = array_slice($datos['usuarios'], $offset, $por_pagina);

$inicio = $total_registros > 0 ? $offset + 1 : 0;
$fin = min($total_registros, $pagina_actual * $por_pagina);
?>

<div class="contenedor-listados">

    <!-- Alerta de éxito o error -->
    <?php
    // Usar las variables correctas de mensajes (mensaje_exito y mensaje_error según controlador)
    $mensaje_exito = $datos['mensaje_exito'] ?? '';
    $mensaje_error = $datos['mensaje_error'] ?? '';
    include __DIR__ . '/../../components/alerta-flash.php';
    ?>

    <!-- Modal confirmación eliminación -->
    <?php $mensaje_confirmacion = "¿Estás seguro de que deseas eliminar este usuario?"; ?>
    <?php include __DIR__ . '/../../components/modal-confirmacion.php'; ?>

    <!-- Título principal -->
    <h1><?= htmlspecialchars($datos['titulo']) ?></h1>
    <hr>

    <!-- Encabezado con controles -->
    <?php
    $ruta_index = '/usuarios';
    $ruta_crear = '/usuarios/create';
    $texto_boton = 'Nuevo Usuario';
    $opciones_por_pagina = [5, 10, 25, 50];
    include __DIR__ . '/../../components/encabezado-acciones.php';
    ?>

    <!-- Tabla usuarios -->
    <div class="contenedor-tabla">
        <?php
        $columnas = [
            ['campo' => 'id', 'titulo' => 'ID'],
            ['campo' => 'nombre', 'titulo' => 'Nombre'],
            ['campo' => 'email', 'titulo' => 'Email'],
            ['campo' => 'rol', 'titulo' => 'Rol'],
            ['campo' => 'estado', 'titulo' => 'Estado'],
        ];

        $filas = $registros_paginados;
        $ruta_base = '/usuarios';

        include __DIR__ . '/../../components/tabla-generica.php';
        ?>
    </div>

    <!-- Controles de paginación -->
    <?php include __DIR__ . '/../../components/paginacion.php'; ?>
</div>
