// Menú móvil
document.querySelector('.boton-menu-movil').addEventListener('click', function() {
    document.querySelector('.sidebar').classList.toggle('sidebar--visible');
});
        
// Cerrar menú al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!e.target.closest('.sidebar') && !e.target.closest('.boton-menu-movil')) {
        document.querySelector('.sidebar').classList.remove('sidebar--visible');
    }
});