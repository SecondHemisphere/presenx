const pastelElement = document.getElementById("pastel-asistencias");

if (pastelElement) {
  // Lee los datos numéricos de los atributos 'data-*' del elemento.
  const datos = {
    puntual: parseInt(pastelElement.dataset.puntual || "0"),
    tarde: parseInt(pastelElement.dataset.tarde || "0"),
    ausente: parseInt(pastelElement.dataset.ausente || "0"),
  };

  const total = datos.puntual + datos.tarde + datos.ausente; // Calcula la suma total.

  if (total === 0) {
    // Establece un fondo visual si no hay datos.
    pastelElement.style.background =
      "repeating-conic-gradient(#ccc 0deg 90deg, #eee 90deg 180deg)";
    pastelElement.style.backgroundSize = "20% 20%";
    pastelElement.style.opacity = "0.7";
  } else {
    // Calcula los grados para cada segmento del pastel.
    const grados = {
      puntual: (datos.puntual / total) * 360,
      tarde: (datos.tarde / total) * 360,
      ausente: (datos.ausente / total) * 360,
    };

    const g1 = grados.puntual;
    const g2 = grados.tarde;

    // Aplica el 'conic-gradient' usando variables CSS para los colores.
    pastelElement.style.background = `
            conic-gradient(
                var(--color-estado-puntual) 0deg ${g1}deg,
                var(--color-estado-tarde) ${g1}deg ${g1 + g2}deg,
                var(--color-estado-ausente) ${g1 + g2}deg 360deg
            )
        `;
  }
}
