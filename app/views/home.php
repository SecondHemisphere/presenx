<div class="contenedor-principal">
    <h1>BIENVENIDOS, REGISTRA TU ASISTENCIA</h1>

    <div class="fecha-hora">
        <span id="currentDateTime"></span>
    </div>

    <div class="tarjeta-formulario formulario">
        <p><a href="/login">Ingresar al sistema</a></p>

        <h3>Ingrese su Cédula</h3>

        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="mensaje-exito"><?= $_SESSION['success_message'] ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>


        <form action="/storeAsistencia" method="POST" novalidate>
            <div class="grupo-entrada <?= isset($_SESSION['error_message']) ? 'error-input' : '' ?>">
                <label for="cedulaEmpleado">Cédula del empleado</label>
                <input
                    type="text"
                    name="cedula"
                    id="cedulaEmpleado"
                    placeholder="Cédula del empleado"
                    pattern="\d{10}"
                    title="Ingrese una cédula válida de 10 dígitos"
                    required
                    maxlength="10"
                    minlength="10"
                    value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>">
                <?php if (!empty($_SESSION['error_message'])): ?>
                    <span class="error"><?= $_SESSION['error_message'] ?></span>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
            </div>

            <div class="grupo-botones">
                <button type="submit" name="tipo" value="entrada" class="boton-entrada">ENTRADA</button>
                <button type="submit" name="tipo" value="salida" class="boton-salida">SALIDA</button>
            </div>
        </form>
    </div>

</div>

<script>
    function updateDateTime() {
        const now = new Date();

        const dateOptions = {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        };
        const timeOptions = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        const formattedDate = now.toLocaleDateString('es-EC', dateOptions);
        const formattedTime = now.toLocaleTimeString('es-EC', timeOptions);

        document.getElementById('currentDateTime').textContent = `${formattedDate}, ${formattedTime}`;
    }

    updateDateTime();
    setInterval(updateDateTime, 1000);
</script>