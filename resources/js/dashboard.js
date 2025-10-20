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
                plugins: { 
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Estado de los alumnos (activos-baja)'
                    }
                },
                scales: { 
                    x: { 
                        stacked: true,
                        title: { 
                            display: true,
                            text: 'Categoría' 
                        }
                    }, 
                    y: { 
                        stacked: true, 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de alumnos'
                        }
                    } 
                }
            }
        });

        const metricsUrl = el.dataset.metricsUrl || `${window.location.origin}/administrador/dashboard/metrics`;

        async function refreshMetrics() {
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
                console.error("Error actualizando métricas del dashboard.");
            }
        }

        refreshMetrics();
        setInterval(refreshMetrics, 60000);
    }

    const searchInput = document.getElementById('q');
    if (searchInput) {
        const menuItems = document.querySelectorAll('.sidebar .menu a');
        function filterModules() {
            const filterText = searchInput.value.toLowerCase();
            menuItems.forEach(link => {
                const linkText = link.textContent.toLowerCase();
                const parentListItem = link.closest('li');
                if (parentListItem) {
                    parentListItem.style.display = linkText.includes(filterText) ? '' : 'none';
                }
            });
        }
        searchInput.addEventListener('keyup', filterModules);
    }

    const container = document.getElementById('notificaciones-container');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (container && prevBtn && nextBtn) {
        const todasNotificaciones = JSON.parse(document.getElementById('notificacionesData').textContent);

        let paginaActual = 0;
        const limite = 5;
        const totalPaginas = Math.ceil(todasNotificaciones.length / limite);

        function renderPagina() {
            container.innerHTML = '';

            const inicio = paginaActual * limite;
            const fin = inicio + limite;
            const visibles = todasNotificaciones.slice(inicio, fin);

            visibles.forEach(n => {
                const item = document.createElement('div');
                item.className = `notification-item ${n.read_at ? 'read' : 'unread'}`;
                item.innerHTML = `
                    <div class="info">
                        <span class="tipo">${n.type.split('\\').pop()}</span>
                        <span class="fecha">📅 ${new Date(n.created_at).toLocaleString('es-MX')}</span>
                    </div>
                `;
                container.appendChild(item);
            });

            prevBtn.disabled = paginaActual === 0;
            nextBtn.disabled = paginaActual >= totalPaginas - 1;
        }

        prevBtn.addEventListener('click', () => {
            if (paginaActual > 0) {
                paginaActual--;
                renderPagina();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (paginaActual < totalPaginas - 1) {
                paginaActual++;
                renderPagina();
            }
        });

        // Render inicial
        renderPagina();
    }
});