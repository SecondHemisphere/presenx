<div class="contenido-dashboard">
    <div class="cabecera-superior">
        <h1>Bienvenido al Dashboard</h1>
        <p>Resumen general</p>
    </div>

    <div class="estadisticas-container">
        <div class="tarjeta-estadistica">
            <i class="fas fa-users"></i>
            <h3>Usuarios</h3>
            <p><?php echo ($total_usuarios) ?></p>
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

    <div class="fila-asistencias">
        <div class="grafico-container">
            <h2 class="grafico-titulo">Asistencias De Hoy</h2>
            <div class="barra-container">
                <div class="barra" style="height: <?= $porcentaje ?>%;">
                    <span class="barra-valor"><?php echo ($asistencias_hoy) ?></span>
                </div>
            </div>
            <div class="barra-etiqueta">Total</div>
        </div>

        <div class="estado-asistencias">
            <h3><i class="fa-solid fa-chart-pie"></i> Estado de Asistencias de Hoy</h3>
            
            <div class="grafico-pastel">
                <div class="pastel" id="pastel-asistencias"></div>
                <ul class="leyenda">
                    <li><span class="cuadro verde"></span> Puntuales: <?= $estado_asistencias['puntual'] ?></li>
                    <li><span class="cuadro naranja"></span> Tarde: <?= $estado_asistencias['tarde'] ?></li>
                    <li><span class="cuadro rojo"></span> Ausentes: <?= $estado_asistencias['ausente'] ?></li>
                </ul>
            </div>

            <script>
                const datos = {
                    puntual: <?= $estado_asistencias['puntual'] ?>,
                    tarde: <?= $estado_asistencias['tarde'] ?>,
                    ausente: <?= $estado_asistencias['ausente'] ?>
                };

                const total = datos.puntual + datos.tarde + datos.ausente;

                const grados = {
                    puntual: (datos.puntual / total) * 360,
                    tarde: (datos.tarde / total) * 360,
                    ausente: (datos.ausente / total) * 360
                };

                const pastel = document.getElementById('pastel-asistencias');
                const g1 = grados.puntual;
                const g2 = grados.tarde;
                const g3 = grados.ausente;

                pastel.style.background = `
                    conic-gradient(
                        #2ecc71 0deg ${g1}deg,
                        #f39c12 ${g1}deg ${g1 + g2}deg,
                        #e74c3c ${g1 + g2}deg 360deg
                    )
                `;
            </script>
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
</div>

<style>
    .fila-asistencias {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .grafico-container {
        background: #f0f4ff;
        padding: 20px;
        border-radius: 12px;
        flex: 1;
        min-width: 250px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', sans-serif;
    }

    .barra-container {
        background: #ddd;
        width: 60px;
        border-radius: 8px;
        position: relative;
        margin: 0 auto;
        display: flex;
        align-items: flex-end;
    }

    .barra {
        background-color: #3498db;
        width: 100%;
        border-radius: 8px 8px 0 0;
        text-align: center;
        color: #fff;
        font-weight: bold;
        transition: height 0.5s ease;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .barra-valor {
        padding-bottom: 5px;
        font-size: 14px;
    }

    .barra-etiqueta {
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
        font-weight: bold;
    }

    .estado-asistencias {
        background: #f0f4ff;
        padding: 20px;
        border-radius: 12px;
        flex: 2;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', sans-serif;
    }

    .estado-asistencias h3 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #333;
    }

    .grafico-pastel {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }

    .pastel {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: #ccc;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .leyenda {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .leyenda li {
        margin-bottom: 8px;
        font-size: 15px;
        display: flex;
        align-items: center;
    }

    .cuadro {
        display: inline-block;
        width: 14px;
        height: 14px;
        margin-right: 8px;
        border-radius: 3px;
    }

    .cuadro.verde { background-color: #2ecc71; }
    .cuadro.naranja { background-color: #f39c12; }
    .cuadro.rojo { background-color: #e74c3c; }

    .ultimos-ingresos {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
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

    .estadisticas-container {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }

    .tarjeta-estadistica {
        background: #fff;
        border-radius: 3rem;
        flex: 1;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .tarjeta-estadistica i {
        font-size: 2.5rem;
        color: white;
    }

    .tarjeta-estadistica h3 {
        margin: 0;
        font-size: 1.7rem;
    }

    .tarjeta-estadistica p {
        font-size: 1.7rem;
        font-weight: bold;
    }
</style>
