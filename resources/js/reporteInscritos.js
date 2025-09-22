document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('reporteRoot');
    if (!root) return;

    const urlTotales = root.dataset.urlTotales;
    const urlEstatus = root.dataset.urlEstatus;

    const selDiplomado = document.getElementById('f_diplomado');
    const btnGenerar   = document.getElementById('btnGenerar');

    const btnPDFTotales = document.getElementById('btnPDFTotales');
    const btnPDFEstatus = document.getElementById('btnPDFEstatus');

    let chartTotales = null;
    let chartEstatus = null;

    function tabsInit() {
        const tabs = document.querySelectorAll('#tabs .tab');
        const sections = {
            totales: document.getElementById('tab-totales'),
            estatus: document.getElementById('tab-estatus')
        };
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.dataset.tab;
                sections.totales.style.display = (target === 'totales') ? 'block' : 'none';
                sections.estatus.style.display = (target === 'estatus') ? 'block' : 'none';
            });
        });
    }

    function destroyIf(chart) {
        if (chart) chart.destroy();
    }

    async function cargarGraficaTotales() {
        const diplomadoId = selDiplomado.value;
        const params = new URLSearchParams();
        if (diplomadoId) {
            params.append('diplomados[]', diplomadoId);
        }

        const res = await fetch(`${urlTotales}?${params.toString()}`);
        if (!res.ok) return;
        const json = await res.json();

        const ctx = document.getElementById('chartTotales').getContext('2d');
        destroyIf(chartTotales);
        chartTotales = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Total de Alumnos',
                    data: json.data,
                    backgroundColor: 'rgb(17, 37, 67)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    async function cargarGraficaEstatus() {
        const diplomadoId = selDiplomado.value;
        const params = new URLSearchParams();
        if (diplomadoId) {
            params.append('diplomados[]', diplomadoId);
        }

        const res = await fetch(`${urlEstatus}?${params.toString()}`);
        if (!res.ok) return;
        const json = await res.json();

        const ctx = document.getElementById('chartEstatus').getContext('2d');
        destroyIf(chartEstatus);
        chartEstatus = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [
                    {
                        label: 'Alumnos Activos',
                        data: json.dataActivos,
                        backgroundColor: 'rgb(17, 37, 67)',
                        borderWidth: 1
                    },
                    {
                        label: 'Alumnos Dados de Baja',
                        data: json.dataBajas,
                        backgroundColor: 'rgb(36, 86, 174)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                }
            }
        });
    }

    btnGenerar?.addEventListener('click', () => {
        const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'totales';
        if (activeTab === 'totales') {
            cargarGraficaTotales();
        } else {
            cargarGraficaEstatus();
        }
    });

    btnPDFTotales?.addEventListener('click', () => {
        if (!chartTotales) return;
        const imageData = chartTotales.toBase64Image();
        document.getElementById('chart_data_url_totales').value = imageData;
        
        const diplomadoNombre = selDiplomado.options[selDiplomado.selectedIndex].text;
        document.getElementById('subtituloTotales').value = diplomadoNombre;
        
        document.getElementById('pdfFormTotales').submit();
    });
    
    btnPDFEstatus?.addEventListener('click', () => {
        if (!chartEstatus) return;
        const imageData = chartEstatus.toBase64Image();
        document.getElementById('chart_data_url_estatus').value = imageData;
    
        const diplomadoNombre = selDiplomado.options[selDiplomado.selectedIndex].text;
        document.getElementById('subtituloEstatus').value = diplomadoNombre;
    
        document.getElementById('pdfFormEstatus').submit();
    });

    tabsInit();
    cargarGraficaTotales();
});