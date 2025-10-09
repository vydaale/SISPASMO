document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('reporteRoot');
    if (!root) return;

    const urlTotal = root.dataset.urlTotal;
    const urlCalificaciones = root.dataset.urlCalificaciones;

    const selDiplomado = document.getElementById('f_diplomado');
    // const selModulo = document.getElementById('f_modulo'); // ELIMINADO
    const btnGenerar = document.getElementById('btnGenerar');

    let chartTotal = null;
    let chartCalificaciones = null;

    function tabsInit() {
        const tabs = document.querySelectorAll('#tabs .tab');
        const sections = {
            total: document.getElementById('tab-total'),
            calificaciones: document.getElementById('tab-calificaciones')
        };
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.dataset.tab;
                sections.total.style.display = (target === 'total') ? 'block' : 'none';
                sections.calificaciones.style.display = (target === 'calificaciones') ? 'block' : 'none';
                
                // Regenerar la gráfica al cambiar de pestaña si ya hay un diplomado seleccionado
                if (selDiplomado.value) {
                    if (target === 'total') {
                        cargarGraficaTotal();
                    } else {
                        cargarGraficaCalificaciones();
                    }
                }
            });
        });
    }

    function destroyIf(chart) {
        if (chart) chart.destroy();
    }
    
    // Ya no es necesario el listener para selDiplomado, solo el botón Generar.

    async function cargarGraficaTotal() {
        const idDiplomado = selDiplomado.value;
        if (!idDiplomado) return;
    
        const params = new URLSearchParams({ id_diplomado: idDiplomado });
        const res = await fetch(`${urlTotal}?${params.toString()}`);
        if (!res.ok) return;
        const json = await res.json();
    
        const dataToShow = json.data.length > 0 ? json.data : [0];
        const labelsToShow = json.labels.length > 0 ? json.labels : ['Sin Reprobados'];
        
        const ctx = document.getElementById('chartTotal').getContext('2d');
        destroyIf(chartTotal);
        chartTotal = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsToShow,
                datasets: [{
                    label: 'Total de Alumnos Reprobados',
                    data: dataToShow,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Actualiza el input oculto para el Excel
        document.getElementById('diplomado_excel_total').value = idDiplomado;
    }

    async function cargarGraficaCalificaciones() {
        const idDiplomado = selDiplomado.value;
        if (!idDiplomado) return;

        const params = new URLSearchParams({ id_diplomado: idDiplomado });
        const res = await fetch(`${urlCalificaciones}?${params.toString()}`);
        if (!res.ok) return;
        const json = await res.json();

        const dataToShow = json.data.length > 0 ? json.data : [0, 0];

        const ctx = document.getElementById('chartCalificaciones').getContext('2d');
        destroyIf(chartCalificaciones);
        chartCalificaciones = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Cantidad de Alumnos',
                    data: dataToShow,
                    backgroundColor: ['rgba(255, 159, 64, 0.5)', 'rgba(255, 205, 86, 0.5)'],
                    borderColor: ['rgba(255, 159, 64, 1)', 'rgba(255, 205, 86, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    btnGenerar?.addEventListener('click', () => {
        if (!selDiplomado.value) {
            alert('Por favor, selecciona un Diplomado.');
            return;
        }

        const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'total';
        if (activeTab === 'total') {
            cargarGraficaTotal();
        } else {
            cargarGraficaCalificaciones();
        }
    });

    const excelFormTotal = document.getElementById('excelFormTotal');
    excelFormTotal?.addEventListener('submit', (e) => {
        const idDiplomado = selDiplomado.value;
        if (!idDiplomado) {
            e.preventDefault();
            alert('Por favor, selecciona un diplomado antes de descargar.');
            return;
        }
        document.getElementById('diplomado_excel_total').value = idDiplomado;
    });

    tabsInit();
});