<div class="contenido-dashboard">
    <div class="cabecera-superior">
        <h1>Bienvenido al Dashboard</h1>
        <p>Resumen general</p>
    </div>

    <div class="estadisticas-container">
        <div class="tarjeta-estadistica">
            <i class="fas fa-users"></i>
            <h3>Usuarios</h3>
            <p>?</p>
        </div>
        <div class="tarjeta-estadistica">
            <i class="fas fa-sitemap"></i>
            <h3>Cargos</h3>
            <p><?php echo ($total_cargos) ?></p>
        </div>
        <div class="tarjeta-estadistica">
            <i class="fas fa-user-tie"></i>
            <h3>Empleados</h3>
            <p><?php echo ($total_empleados) ?></p>
        </div>
        <div class="tarjeta-estadistica">
            <i class="fas fa-user-check"></i>
            <h3>Asistencias Registradas</h3>
            <p><?php echo ($total_asistencias) ?></p>
        </div>
    </div>

    <?php
    $porcentaje = 0;
    if ($total_empleados > 0) {
        $porcentaje = ($asistencias_hoy / $total_empleados) * 100;
        $porcentaje = min($porcentaje, 100);
    }
    ?>

    <div class="grafico-container">
        <h2 class="grafico-titulo">Asistencias De Hoy</h2>
        <div class="barra-container">
            <div class="barra" style="height: <?= $porcentaje ?>s;">
                <span class="barra-valor"><?php echo ($asistencias_hoy) ?></span>
            </div>
        </div>
        <div class="barra-etiqueta">Total</div>
    </div>

    <div class="estado-asistencias">
        <h3><i class="fa-solid fa-chart-pie"></i> Estado de Asistencias de Hoy</h3>
        <ul class="estado-lista">
            <li>
                <i class="fa-solid fa-circle-check icon verde"></i>
                <span class="etiqueta">Puntuales:</span>
                <span class="valor"><?php echo $estado_asistencias['puntual']; ?></span>
            </li>
            <li>
                <i class="fa-solid fa-clock icon naranja"></i>
                <span class="etiqueta">Tarde:</span>
                <span class="valor"><?php echo $estado_asistencias['tarde']; ?></span>
            </li>
            <li>
                <i class="fa-solid fa-user-xmark icon rojo"></i>
                <span class="etiqueta">Ausentes:</span>
                <span class="valor"><?php echo $estado_asistencias['ausente']; ?></span>
            </li>
        </ul>
    </div>
</div>

<div class="contenedor-tabla ultimos-ingresos">
    <h3>Últimos ingresos del día</h3>
    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Entrada</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ultimasEntradas as $entrada): ?>
                <tr>
                    <td><?php echo $entrada->nombre . ' ' . $entrada->apellido; ?></td>
                    <td><?php echo date('H:i', strtotime($entrada->entrada)); ?></td>
                    <td>
                        <?php
                        $estado = strtolower($entrada->estado);
                        $color = $estado === 'puntual' ? 'green' : ($estado === 'tarde' ? 'orange' : 'red');
                        ?>
                        <span style="color: <?php echo $color; ?>"><?php echo ucfirst($estado); ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .estado-asistencias {
        background: #f0f4ff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', sans-serif;
    }

    .estado-asistencias h3 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #333;
    }

    .estado-lista {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .estado-lista li {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .estado-lista .icon {
        font-size: 18px;
        margin-right: 10px;
    }

    .icon.verde {
        color: #2ecc71;
    }

    .icon.naranja {
        color: #f39c12;
    }

    .icon.rojo {
        color: #e74c3c;
    }

    .etiqueta {
        min-width: 80px;
        font-weight: 600;
    }

    .valor {
        margin-left: 5px;
    }

    .estado-asistencias,
    .ultimos-ingresos {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .estado-asistencias ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .estado-asistencias li {
        font-size: 16px;
        margin: 5px 0;
    }

    .ultimos-ingresos table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .ultimos-ingresos th,
    .ultimos-ingresos td {
        padding: 8px;
        border-bottom: 1px solid #ccc;
    }
</style>