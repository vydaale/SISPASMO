const inputCalif = document.getElementById('calificacion');
        inputCalif?.addEventListener('input', () => {
            const v = parseFloat(inputCalif.value);
            if (isNaN(v)) return;
            if (v < 0) inputCalif.value = 0;
            if (v > 100) inputCalif.value = 100;
        });