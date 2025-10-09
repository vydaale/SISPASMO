// dashboard.js

import Chart from 'chart.js/auto';
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('alumnosChart');
    if (el) { 
        const ctx = el.getContext('2d');
        const activos = Number(el.dataset.activos || 0);
        const baja    = Number(el.dataset.baja || 0);

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Alumnos'],
                datasets: [
                    { label: 'Activos', data: [activos] },
                    { label: 'Baja',    data: [baja] }
                ]
            },
            options: {
                responsive: true,
                // AÑADIMOS EL TÍTULO GENERAL DE LA GRÁFICA
                plugins: { 
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Estado de los Alumnos (Activos vs. Baja)' // Título descriptivo
                    }
                },
                scales: { 
                    x: { 
                        stacked: true,
                        title: { // Título para el eje X
                            display: true,
                            text: 'Categoría' // Ya que solo tienes una barra, puedes poner "Categoría" o quitarlo
                        }
                    }, 
                    y: { 
                        stacked: true, 
                        beginAtZero: true,
                        title: { // AÑADIMOS EL TÍTULO PARA EL EJE Y
                            display: true,
                            text: 'Número de Alumnos' // Indica qué representa el eje Y
                        }
                    } 
                }
            }
        });

        const metricsUrl = el.dataset.metricsUrl || `${window.location.origin}/administrador/dashboard/metrics`;

        async function refreshMetrics() {
            // ... (el resto de tu función refreshMetrics permanece igual)
            try {
                const res = await fetch(metricsUrl, { credentials: 'same-origin' });
                if (!res.ok) return;

                const data = await res.json();

                const nAlumnos    = document.getElementById('nAlumnos');
                const nDocentes   = document.getElementById('nDocentes');
                const nAspirantes = document.getElementById('nAspirantes');
                if (nAlumnos)    nAlumnos.textContent    = data.alumnos.total;
                if (nDocentes)   nDocentes.textContent   = data.docentes;
                if (nAspirantes) nAspirantes.textContent = data.aspirantes;

                chart.data.datasets[0].data = [data.alumnos.activos];
                chart.data.datasets[1].data = [data.alumnos.baja];
                chart.update();
            } catch {
            }
        }

        refreshMetrics();
        setInterval(refreshMetrics, 60000);
    }
    
    // ... (el resto del código para el buscador y el DOMContentLoaded permanece igual)
    const searchInput = document.getElementById('q');

    if (!searchInput) return;

    const menuItems = document.querySelectorAll('.sidebar .menu a');

    function filterModules() {
        const filterText = searchInput.value.toLowerCase();
        
        menuItems.forEach(function(link) {
            const linkText = link.textContent.toLowerCase();
            const parentListItem = link.closest('li');

            if (parentListItem) {
                if (linkText.includes(filterText)) {
                    parentListItem.style.display = ''; 
                } else {
                    parentListItem.style.display = 'none';
                }
            }
        });
    }

    searchInput.addEventListener('keyup', filterModules);

});