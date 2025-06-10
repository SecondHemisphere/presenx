<?php
// Lógica de paginación
$por_pagina = isset($_GET['por_pagina']) ? (int) $_GET['por_pagina'] : 10;
$total_registros = count($data['empleados']);
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$total_paginas = ceil($total_registros / $por_pagina);

$offset = ($pagina_actual - 1) * $por_pagina;
$registros_paginados = array_slice($data['empleados'], $offset, $por_pagina);

$inicio = $offset + 1;
$fin = min($total_registros, $pagina_actual * $por_pagina);

?>

<div class="contenedor-listados">

    <!-- Alerta de éxito o error -->
    <?php
    $mensaje_exito = $data['success_message'] ?? '';
    $mensaje_error = $data['error_message'] ?? '';
    include __DIR__ . '/../../components/alerta-flash.php';
    ?>

    <!-- Modal de confirmación para eliminar empleados -->
    <?php $mensaje_confirmacion = "¿Estás seguro de que deseas eliminar este empleado?"; ?>
    <?php include __DIR__ . '/../../components/modal-confirmacion.php'; ?>

    <!-- Título principal -->
    <h1><?= htmlspecialchars($data['title']) ?></h1>
    <hr>

    <!-- Encabezado: control de entradas y botón para nuevo empleado -->
    <?php
    $ruta_index = '/empleados';
    $ruta_crear = '/empleados/create';
    $texto_boton = 'Nuevo Empleado';
    $opciones_por_pagina = [5, 10, 25, 50];
    include __DIR__ . '/../../components/encabezado-acciones.php';
    ?>

    <!-- Tabla de empleados -->
    <div class="contenedor-tabla">
        <?php
        $columnas = [
            ['campo' => 'id', 'titulo' => 'ID'],
            ['campo' => 'foto', 'titulo' => 'Foto', 'tipo' => 'imagen'],
            ['campo' => 'nombre', 'titulo' => 'Nombre'],
            ['campo' => 'apellido', 'titulo' => 'Apellido'],
            ['campo' => 'cedula', 'titulo' => 'Cédula'],
            ['campo' => 'genero', 'titulo' => 'Género'],
            ['campo' => 'fecha_nacimiento', 'titulo' => 'Nacimiento', 'tipo' => 'fecha'],
            ['campo' => 'email', 'titulo' => 'Correo'],
            ['campo' => 'telefono', 'titulo' => 'Teléfono'],
            ['campo' => 'direccion', 'titulo' => 'Dirección'],
            ['campo' => 'fecha_ingreso', 'titulo' => 'Fecha de Ingreso'],
            ['campo' => 'nombre_cargo', 'titulo' => 'Cargo'],
            ['campo' => 'estado', 'titulo' => 'Estado', 'tipo' => 'estado']
        ];

        $filas = $registros_paginados;
        $ruta_base = '/empleados';

        include __DIR__ . '/../../components/tabla-generica.php';
        ?>
    </div>

    <!-- Controles de paginación -->
    <?php include __DIR__ . '/../../components/paginacion.php'; ?>
</div>

</body>

</html>