// Cuando se cambia la imagen del input "foto"
document.getElementById("foto").addEventListener("change", function (evento) {
  // Obtenemos el archivo seleccionado
  const archivo = evento.target.files[0];

  // Obtenemos el contenedor y la imagen de vista previa
  const contenedorVista = document.getElementById("preview-container");
  const imagenVista = document.getElementById("preview-image");

  // Si hay un archivo y es una imagen
  if (archivo && archivo.type.startsWith("image/")) {
    const lector = new FileReader();

    // Cuando la imagen esté lista
    lector.onload = function (e) {
      imagenVista.src = e.target.result;         // Mostramos la imagen
      contenedorVista.style.display = "block";   // Mostramos el contenedor
    };

    lector.readAsDataURL(archivo); // Leemos el archivo como imagen base64
  } else {
    imagenVista.src = "#";                   // Limpiamos la imagen
    contenedorVista.style.display = "none";  // Ocultamos el contenedor
  }
});
