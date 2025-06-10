<div class="contenido-dashboard">
    <div class="cabecera-superior">
        <h1>Bienvenido al Dashboard</h1>
        <p>Resumen general</p>
    </div>

    <div class="estadisticas-contenedor">
        <div class="tarjeta-estadistica">
            <i class="fas fa-users"></i>
            <h3>Usuarios</h3>
            <p><?= htmlspecialchars($total_usuarios) ?></p>
        </div>
        <div class="tarjeta-estadistica">
            <i class="fas fa-sitemap"></i>
            <h3>Cargos</h3>
            <p><?= htmlspecialchars($total_cargos) ?></p>
        </div>
        <div class="tarjeta-estadistica">
            <i class="fas fa-user-tie"></i>
            <h3>Empleados</h3>
            <p><?= htmlspecialchars($total_empleados) ?></p>
        </div>
        <div class="tarjeta-estadistica">
            <i class="fas fa-user-check"></i>
            <h3>Asistencias Registradas</h3>
            <p><?= htmlspecialchars($total_asistencias) ?></p>
        </div>
    </div>

    <div class="asistencia-graficos-caja">
        <div class="grafico-container">
            <h2 class="grafico-titulo">ASISTENCIAS DE HOY</h2>
            <div class="barra-container">
                <div class="barra" style="height: <?= htmlspecialchars($porcentaje) ?>%;">
                    <span class="barra-valor"><?= htmlspecialchars($asistencias_hoy) ?></span>
                </div>
            </div>
            <div class="barra-etiqueta">TOTAL</div>
        </div>

        <div class="estado-asistencias">
            <h3><i class="fa-solid fa-chart-pie"></i> Estado de Asistencias de Hoy</h3>
            <div class="grafico-pastel">
                <div
                    class="pastel"
                    id="pastel-asistencias"
                    data-puntual="<?= htmlspecialchars($estado_asistencias['puntual']) ?>"
                    data-tarde="<?= htmlspecialchars($estado_asistencias['tarde']) ?>"
                    data-ausente="<?= htmlspecialchars($estado_asistencias['ausente']) ?>"></div>
                <ul class="leyenda">
                    <li><span class="cuadro verde"></span> Puntuales: <?= htmlspecialchars($estado_asistencias['puntual']) ?></li>
                    <li><span class="cuadro naranja"></span> Tarde: <?= htmlspecialchars($estado_asistencias['tarde']) ?></li>
                    <li><span class="cuadro rojo"></span> Ausentes: <?= htmlspecialchars($estado_asistencias['ausente']) ?></li>
                </ul>
            </div>
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
                        <td><?= htmlspecialchars($entrada->nombre . ' ' . $entrada->apellido) ?></td>
                        <td><?= htmlspecialchars(date('H:i', strtotime($entrada->entrada))) ?></td>
                        <td>
                            <?php
                            $estado = strtolower($entrada->estado);
                            $color = $estado === 'puntual' ? 'green' : ($estado === 'tarde' ? 'orange' : 'red');
                            ?>
                            <span style="color: <?= htmlspecialchars($color) ?>"><?= htmlspecialchars(ucfirst($estado)) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="assets/js/pastel-asistencias.js"></script>