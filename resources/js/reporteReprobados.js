document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('reporteRoot');
    if (!root) return;

    const urlModulos = root.dataset.urlModulos;
    const urlTotal   = root.dataset.urlTotal;
    const urlCalificaciones = root.dataset.urlCalificaciones;

    const selDiplomado = document.getElementById('f_diplomado');
    const selModulo    = document.getElementById('f_modulo');
    const btnGenerar   = document.getElementById('btnGenerar');

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
            });
        });
    }

    function destroyIf(chart) {
        if (chart) chart.destroy();
    }

    // Carga los módulos al seleccionar un diplomado
    selDiplomado.addEventListener('change', async () => {
        const idDiplomado = selDiplomado.value;
        selModulo.innerHTML = '<option value="">-- Selecciona un módulo --</option>';
        if (!idDiplomado) return;

        const res = await fetch(`${urlModulos}?id_diplomado=${idDiplomado}`);
        if (!res.ok) return;
        const modulos = await res.json();

        modulos.forEach(mod => {
            const option = document.createElement('option');
            option.value = mod.id_modulo;
            option.textContent = mod.nombre;
            selModulo.appendChild(option);
        });
    });

    // Cargar la Gráfica 1
    async function cargarGraficaTotal() {
        const idDiplomado = selDiplomado.value;
        const idModulo    = selModulo.value;
        if (!idModulo) return;

        const params = new URLSearchParams({ id_diplomado: idDiplomado, id_modulo: idModulo });
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
                    label: 'Total de Alumnos Reprobados',
                    data: json.data,
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Cargar la Gráfica 2
    async function cargarGraficaCalificaciones() {
        const idModulo = selModulo.value;
        if (!idModulo) return;

        const params = new URLSearchParams({ id_modulo: idModulo });
        const res = await fetch(`${urlCalificaciones}?${params.toString()}`);
        if (!res.ok) return;
        const json = await res.json();

        const ctx = document.getElementById('chartCalificaciones').getContext('2d');
        destroyIf(chartCalificaciones);
        chartCalificaciones = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: json.labels,
                datasets: [{
                    label: 'Cantidad de Alumnos',
                    data: json.data,
                    backgroundColor: ['rgba(255, 159, 64, 0.5)', 'rgba(255, 205, 86, 0.5)'],
                    borderColor: ['rgba(255, 159, 64, 1)', 'rgba(255, 205, 86, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // Eventos
    btnGenerar?.addEventListener('click', () => {
        const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'total';
        if (activeTab === 'total') {
            cargarGraficaTotal();
        } else {
            cargarGraficaCalificaciones();
        }
    });

    // Lógica para el botón de descarga
    const excelForms = document.querySelectorAll('form[id^="excelForm"]');
    excelForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const idModulo = selModulo.value;
            if (!idModulo) {
                e.preventDefault();
                alert('Por favor, selecciona un diplomado y un módulo antes de descargar.');
            }
            document.getElementById(`modulo_excel_${form.id.includes('total') ? 'total' : 'calificaciones'}`).value = idModulo;
        });
    });

    tabsInit();
});