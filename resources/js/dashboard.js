document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.dropdown');
    const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
    let timeoutId;

    // Mostrar menú al pasar el mouse
    dropdown.addEventListener('mouseenter', function() {
        clearTimeout(timeoutId);
        dropdown.classList.add('active');
    });

    // Ocultar menú al salir el mouse
    dropdown.addEventListener('mouseleave', function() {
        timeoutId = setTimeout(() => {
            dropdown.classList.remove('active');
        }, 200);
    });

    // Prevenir navegación del enlace principal
    dropdownToggle.addEventListener('click', function(e) {
        e.preventDefault();
        dropdown.classList.toggle('active');
    });

    // Cerrar menú al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('active');
        }
    });
});