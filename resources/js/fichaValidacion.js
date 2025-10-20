document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.gm-form');
    if (!form) return;

    // --- LÓGICA DE VALIDACIÓN ANTES DE ENVIAR ---
    form.addEventListener('submit', function(event) {
        event.preventDefault(); // Detener el envío
        let isValid = true; // Asumimos que el formulario es válido al inicio

        // Limpiar errores anteriores de JS
        document.querySelectorAll('.gm-error-message-js').forEach(el => el.remove());

        // --- VALIDACIONES ---

        // 1. VALIDACIÓN: CONTACTO DE EMERGENCIA OBLIGATORIO
        const contactFields = [
            { name: 'contacto[nombre]', label: 'Nombre del contacto' },
            { name: 'contacto[apellidos]', label: 'Apellidos del contacto' },
            { name: 'contacto[domicilio]', label: 'Domicilio del contacto' },
            { name: 'contacto[telefono]', label: 'Teléfono del contacto' },
            { name: 'contacto[parentesco]', label: 'Parentesco del contacto' },
            { name: 'contacto[institucion]', label: 'Institución del contacto' },
        ];

        contactFields.forEach(item => {
            const field = form.querySelector(`[name="${item.name}"]`);
            if (!field.value.trim()) {
                isValid = false;
                showError(field, `El campo "${item.label}" es obligatorio.`);
            }
        });

        // 2. VALIDACIÓN: TELÉFONOS NUMÉRICOS
        const phoneFields = [
            { name: 'enfermedades[telefono_medico]', label: 'Teléfono del médico' },
            { name: 'contacto[telefono]', label: 'Teléfono del contacto' },
        ];
        
        const numericRegex = /^[0-9]+$/;

        phoneFields.forEach(item => {
            const field = form.querySelector(`[name="${item.name}"]`);
            if (field.value.trim() && !numericRegex.test(field.value.trim())) {
                isValid = false;
                showError(field, `El campo "${item.label}" solo debe contener números.`);
            }
        });

        // --- MANEJO DE ERRORES ---
        if (isValid) {
            form.submit(); // Si todo es válido, enviar el formulario
        } else {
            // Si hay errores, hacer scroll hacia el primer campo con error
            const firstErrorField = form.querySelector('.has-error');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    /**
     * Función para mostrar un mensaje de error debajo de un campo.
     * @param {HTMLElement} field - El campo del formulario que tiene el error.
     * @param {string} message - El mensaje de error a mostrar.
     */
    function showError(field, message) {
        // Añadir clase de error al contenedor del campo para resaltarlo (opcional)
        const fieldContainer = field.closest('div');
        if (fieldContainer) {
            fieldContainer.classList.add('has-error');
        }

        // Crear y mostrar el mensaje de error
        const errorElement = document.createElement('span');
        errorElement.className = 'gm-error-message gm-error-message-js'; // Añadimos una clase extra para JS
        errorElement.textContent = message;
        field.insertAdjacentElement('afterend', errorElement);
    }
});