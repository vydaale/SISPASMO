/*
 * Script de lógica para la generación de reportes de Alumnos Reprobados.
 * Coordinar las dos vistas de reporte (Total Reprobados por Módulo vs. Rangos de Calificación Reprobatoria),
 * realizar llamadas AJAX, renderizar las gráficas de barras (Chart.js), y manejar la inicialización de pestañas
 * y la validación para la descarga de reportes Excel.
 */
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('reporteRoot');
    if (!root) return;

    const urlTotal = root.dataset.urlTotal;
    const urlCalificaciones = root.dataset.urlCalificaciones;

    const selDiplomado = document.getElementById('f_diplomado');
    const btnGenerar = document.getElementById('btnGenerar');

    const excelFormTotal = document.getElementById('excelFormTotal');
    const excelFormCalificaciones = document.getElementById('excelFormCalificaciones');
    const inputDiplomadoTotal = document.getElementById('diplomado_excel_total');
    const inputDiplomadoCalificaciones = document.getElementById('diplomado_excel_calificaciones');

    let chartTotal = null;
    let chartCalificaciones = null;

    /*
     * Destruye una instancia de Chart.js si existe, liberando recursos.
    */
    function destroyIf(chart) {
        if (chart) chart.destroy();
    }
    
    /*
     * Actualiza el valor del ID de diplomado en los campos ocultos de ambos formularios de exportación.
     */
    function actualizarCamposExcel(idDiplomado) {
        inputDiplomadoTotal.value = idDiplomado;
        inputDiplomadoCalificaciones.value = idDiplomado;
    }

    /*
     * Carga la gráfica de total de alumnos reprobados por módulo en el diplomado seleccionado.
    */
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
                    label: 'Total de alumnos reprobados',
                    data: dataToShow,
                    backgroundColor: 'rgb(17, 37, 67)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Actualiza ambos campos ocultos (Aunque solo se necesita el de la pestaña activa, 
        // actualizamos ambos para consistencia)
        actualizarCamposExcel(idDiplomado);
    }


    /*
     * Carga la gráfica de alumnos reprobados por rangos de calificación (0-59 vs 60-79).
    */
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
                    label: 'Cantidad de alumnos',
                    data: dataToShow,
                    backgroundColor: ['rgb(17, 37, 67)', 'rgb(36, 86, 174)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
        
        // Actualiza ambos campos ocultos
        actualizarCamposExcel(idDiplomado);
    }
    
    /*
     * Configura la navegación y la visibilidad de las secciones del reporte (Total vs Calificaciones).
    */
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
                
                /* Regenerar la gráfica al cambiar de pestaña si ya hay un diplomado seleccionado. */
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

    btnGenerar?.addEventListener('click', () => {
        if (!selDiplomado.value) {
            alert('Por favor, selecciona un Diplomado.');
            return;
        }

        /* Determina qué gráfica cargar basándose en la pestaña activa */
        const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'total';
        if (activeTab === 'total') {
            cargarGraficaTotal();
        } else {
            cargarGraficaCalificaciones();
        }
    });

    /* * Función genérica de validación para los formularios de Excel.
     * Asegura que el diplomado esté seleccionado antes de permitir la descarga.
     */
    function setupExcelFormValidation(formElement, inputDiplomadoElement) {
        formElement?.addEventListener('submit', (e) => {
            const idDiplomado = selDiplomado.value;
            if (!idDiplomado) {
                e.preventDefault();
                alert('Por favor, selecciona un diplomado antes de descargar.');
                return;
            }
            /* Asegura que el campo oculto tenga el ID correcto (aunque ya se actualiza al cargar la gráfica). */
            inputDiplomadoElement.value = idDiplomado;
        });
    }
    
    /* Aplicar la validación a ambos formularios. */
    setupExcelFormValidation(excelFormTotal, inputDiplomadoTotal);
    setupExcelFormValidation(excelFormCalificaciones, inputDiplomadoCalificaciones);
    
    tabsInit();
});