document.addEventListener('DOMContentLoaded', () => {
    const moduloSelect = document.getElementById('id_modulo');
    const alumnoSelect = document.getElementById('id_alumno');

    if (!moduloSelect || !alumnoSelect) {
        return; // Salir si no se encuentran los elementos
    }

    const cargarAlumnos = async (moduloId) => {
        // Limpiar y deshabilitar el select de alumnos mientras se cargan los datos
        alumnoSelect.innerHTML = '<option value="">Cargando...</option>';
        alumnoSelect.disabled = true;

        if (!moduloId) {
            alumnoSelect.innerHTML = '<option value="">Selecciona un módulo primero</option>';
            return;
        }

        // Obtener la URL base desde el atributo data-url y reemplazar el placeholder
        const baseUrl = moduloSelect.dataset.url.replace('/0', '');
        const url = `${baseUrl}/${moduloId}`;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor.');
            }
            const alumnos = await response.json();

            // Limpiar de nuevo antes de llenar
            alumnoSelect.innerHTML = '';

            if (alumnos.length > 0) {
                alumnoSelect.innerHTML = '<option value="">Selecciona un alumno</option>';
                alumnos.forEach(alumno => {
                    const option = document.createElement('option');
                    option.value = alumno.id;
                    option.textContent = alumno.nombre;
                    alumnoSelect.appendChild(option);
                });
                alumnoSelect.disabled = false;
            } else {
                alumnoSelect.innerHTML = '<option value="">No hay alumnos en este módulo</option>';
            }

        } catch (error) {
            console.error('Error al cargar los alumnos:', error);
            alumnoSelect.innerHTML = '<option value="">Error al cargar alumnos</option>';
        }
    };

    // Evento que se dispara cuando el docente cambia el módulo
    moduloSelect.addEventListener('change', () => {
        const moduloId = moduloSelect.value;
        cargarAlumnos(moduloId);
    });
});