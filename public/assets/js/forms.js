// Deshabilita validación HTML5 en todos los formularios
document.querySelectorAll('form').forEach(form => {
    form.setAttribute('novalidate', true);
});