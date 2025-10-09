import Chart from 'chart.js/auto';

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
        const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'total';

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.dataset.tab;
                sections.total.style.display = (target === 'total') ? 'block' : 'none';
                sections.comparacion.style.display = (target === 'comparacion') ? 'block' : 'none';

                filtroTotalDiv.style.display = (target === 'total') ? 'flex' : 'none'; 
                
                if (target === 'comparacion') {
                    cargarGraficaComparacion();
                }
            });
        });
        
        if (activeTab === 'comparacion') {
            cargarGraficaComparacion();
        } else {
            filtroTotalDiv.style.display = 'flex'; 
        }
    }

    function destroyIf(chart) {
        if (chart) chart.destroy();
    }

    async function cargarGraficaTotal() {
        const tipoDiplomado = selTipoDiplomado.value;
        if (!tipoDiplomado) {
            alert('Por favor, selecciona un tipo de diplomado.');
            return;
        }

        const params = new URLSearchParams({ tipo: tipoDiplomado });
        const res = await fetch(`${urlTotal}?${params.toString()}`); 
        if (!res.ok) {
            console.error('Error al cargar datos del total:', res.status, res.statusText);
            return;
        }
        const json = await res.json();
        
        const dataToShow = json.data.length > 0 ? json.data : [0];

        const ctx = document.getElementById('chartTotal').getContext('2d');
        destroyIf(chartTotal);
        
        const labelTexto = selTipoDiplomado.options[selTipoDiplomado.selectedIndex].text;

        chartTotal = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [labelTexto], 
                datasets: [{
                    label: 'Total de aspirantes interesados',
                    data: dataToShow,
                    backgroundColor: 'rgb(17, 37, 67)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: `Total de aspirantes: ${labelTexto}` 
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de aspirantes'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tipo de diplomado seleccionado'
                        }
                    }
                }
            }
        });
    }

    async function cargarGraficaComparacion() {
        const res = await fetch(`${urlComparacion}`);
        if (!res.ok) {
            console.error('Error al cargar datos de comparación:', res.status, res.statusText);
            return;
        }
        const json = await res.json();

        const dataToShow = json.data.length > 0 ? json.data : [0, 0];

        const ctx = document.getElementById('chartComparacion').getContext('2d');
        destroyIf(chartComparacion);
        chartComparacion = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Total de aspirantes',
                    data: dataToShow,
                    backgroundColor: [
                        'rgb(17, 37, 67)',
                        'rgba(54, 162, 235, 0.5)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }, 
                    title: {
                        display: true,
                        text: 'Comparación de aspirantes por tipo de diplomado'
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de aspirantes'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tipo de diplomado'
                        }
                    }
                }
            }
        });
    }

    btnGenerarTotal?.addEventListener('click', cargarGraficaTotal);

    tabsInit();
});