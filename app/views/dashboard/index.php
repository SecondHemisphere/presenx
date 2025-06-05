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
            <p><?php echo($total_cargos) ?></p>
        </div>
    </div>
    
    <div class="grafico-container">
        <h2 class="grafico-titulo">Libros Disponibles</h2>
        <div class="barra-container">
            <div class="barra" style="height: 70%;">
                <span class="barra-valor">70</span>
            </div>
        </div>
        <div class="barra-etiqueta">Total</div>
    </div>
</div>