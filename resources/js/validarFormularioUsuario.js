document.addEventListener('DOMContentLoaded', function () {
    // Seleccionamos el formulario por su clase
    const form = document.querySelector('.gm-form');

    // Si no hay formulario en la página, detenemos el script
    if (!form) {
        return;
    }

    // NUEVO: Detectamos si es un formulario de actualización buscando el campo _method='PUT'
    const isUpdateForm = form.querySelector('input[name="_method"][value="PUT"]');

    // Escuchamos el evento 'submit' del formulario
    form.addEventListener('submit', function (event) {
        // Prevenimos el envío automático para poder validar primero
        event.preventDefault();

        // Limpiamos errores anteriores
        const errorContainer = document.querySelector('.gm-errors');
        if (errorContainer) {
            errorContainer.innerHTML = '';
        }

        let errors = [];

        // --- INICIO DE VALIDACIONES ---

        // 1. Campos que no deben estar vacíos
        const requiredFields = [
            'nombre', 'apellidoP', 'apellidoM', 'fecha_nac', 'usuario', 'pass',
            'pass_confirmation', 'genero', 'correo', 'telefono', 'direccion',
            'matriculaA', 'id_diplomado', 'estatus'
        ];

        requiredFields.forEach(fieldName => {
            // MODIFICADO: Si es un formulario de actualización, hacemos opcionales los campos de contraseña
            if (isUpdateForm && (fieldName === 'pass' || fieldName === 'pass_confirmation')) {
                return; // Saltamos la validación de "campo vacío" para las contraseñas
            }
            
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field && field.value.trim() === '') {
                // Usamos un nombre más amigable para el mensaje de error
                const friendlyName = field.placeholder || fieldName;
                errors.push(`El campo "${friendlyName}" es obligatorio.`);
            }
        });

        // NUEVA VALIDACIÓN: Si es un formulario de actualización y se escribe una nueva contraseña,
        // la confirmación se vuelve obligatoria.
        const passInput = form.querySelector('[name="pass"]');
        const passConfirmationInput = form.querySelector('[name="pass_confirmation"]');

        if (isUpdateForm && passInput && passInput.value.trim() !== '') {
            if (passConfirmationInput && passConfirmationInput.value.trim() === '') {
                errors.push('Debes confirmar la nueva contraseña.');
            }
        }

        // 2. Validación de Correo Electrónico
        const emailInput = form.querySelector('[name="correo"]');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput && emailInput.value.trim() !== '' && !emailRegex.test(emailInput.value)) {
            errors.push('El formato del correo electrónico no es válido.');
        }

        // 3. Validación del Teléfono (10 dígitos numéricos)
        const phoneInput = form.querySelector('[name="telefono"]');
        const phoneRegex = /^\d{10}$/;
        if (phoneInput && phoneInput.value.trim() !== '' && !phoneRegex.test(phoneInput.value)) {
            errors.push('El teléfono debe contener exactamente 10 dígitos numéricos.');
        }

        // 4. Validación de Fecha de Nacimiento (entre 15 y 80 años)
        const dobInput = form.querySelector('[name="fecha_nac"]');
        if (dobInput && dobInput.value) {
            const birthDate = new Date(dobInput.value);
            const today = new Date();
            
            // Ajustamos la fecha de nacimiento para evitar problemas de zona horaria
            const utcBirthDate = new Date(birthDate.getUTCFullYear(), birthDate.getUTCMonth(), birthDate.getUTCDate());

            let age = today.getFullYear() - utcBirthDate.getFullYear();
            const monthDifference = today.getMonth() - utcBirthDate.getMonth();
            const dayDifference = today.getDate() - utcBirthDate.getDate();

            // Ajuste fino de la edad
            if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
                age--;
            }

            if (age < 15) {
                errors.push('La edad mínima para registrarse es de 15 años.');
            }
            if (age > 80) {
                errors.push('La edad máxima para registrarse es de 80 años.');
            }
        }
        
        // --- FIN DE VALIDACIONES ---

        // Si hay errores, los mostramos
        if (errors.length > 0) {
            // Eliminamos duplicados por si acaso
            const uniqueErrors = [...new Set(errors)]; 
            
            if (errorContainer) {
                uniqueErrors.forEach(error => {
                    const li = document.createElement('li');
                    li.textContent = error;
                    errorContainer.appendChild(li);
                });
            } else {
                // Si no existe el contenedor, mostramos una alerta
                alert(uniqueErrors.join('\n'));
            }
            // Hacemos scroll hacia arriba para que el usuario vea los errores
            window.scrollTo(0, 0);
        } else {
            // Si no hay errores, se envía el formulario
            form.submit();
        }
    });
});