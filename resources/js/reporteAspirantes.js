// resources/js/reporteAspirantes.js

document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('reporteRoot');
    if (!root) return;

    const urlTotal       = root.dataset.urlTotal;
    const urlComparacion = root.dataset.urlComparacion;

    const selTipoDiplomado = document.getElementById('f_tipo_diplomado');
    const btnGenerarTotal  = document.getElementById('btnGenerarTotal');
    const filtroTotalDiv   = document.getElementById('filtroTotal');

    let chartTotal = null;
    let chartComparacion = null;

    function tabsInit() {
        const tabs = document.querySelectorAll('#tabs .tab');
        const sections = {
            total: document.getElementById('tab-total'),
            comparacion: document.getElementById('tab-comparacion')
        };
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.dataset.tab;
                sections.total.style.display = (target === 'total') ? 'block' : 'none';
                sections.comparacion.style.display = (target === 'comparacion') ? 'block' : 'none';

                // Mostrar/ocultar el filtro
                filtroTotalDiv.style.display = (target === 'total') ? 'block' : 'none';

                // Cargar la gráfica al cambiar de tab
                if (target === 'total') {
                    if (selTipoDiplomado.value) {
                        cargarGraficaTotal();
                    }
                } else if (target === 'comparacion') {
                    cargarGraficaComparacion();
                }
            });
        });
    }

    function destroyIf(chart) {
        if (chart) chart.destroy();
    }

    async function cargarGraficaTotal() {
        const tipoDiplomado = selTipoDiplomado.value;
        if (!tipoDiplomado) return;

        const params = new URLSearchParams({ tipo: tipoDiplomado });
        const res = await fetch(`${urlTotal}?${params.toString()}`);
        if (!res.ok) return;
        const json = await res.json();

        const ctx = document.getElementById('chartTotal').getContext('2d');
        destroyIf(chartTotal);
        chartTotal = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Total de Aspirantes Interesados',
                    data: json.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    async function cargarGraficaComparacion() {
        const res = await fetch(urlComparacion);
        if (!res.ok) return;
        const json = await res.json();

        const ctx = document.getElementById('chartComparacion').getContext('2d');
        destroyIf(chartComparacion);
        chartComparacion = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Total de Aspirantes',
                    data: json.data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    btnGenerarTotal?.addEventListener('click', cargarGraficaTotal);

    tabsInit();
    cargarGraficaComparacion();
});