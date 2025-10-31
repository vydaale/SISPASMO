/*
 * Script de lógica para la generación de reportes de Adeudos (por mes/diplomado o por alumno).
  Gestionar la interacción de las pestañas (tabs), inicializar las gráficas Chart.js
  (tanto de barras como de dona), realizar llamadas asíncronas a la API de reportes, y
  manejar los enlaces de descarga de Excel basados en el estado actual de los filtros.
*/
document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('reporteAdeudosRoot');
  if (!root) return;

  const urlChartMes = root.dataset.urlChartMes;
  const urlChartAlumno = root.dataset.urlChartAlumno;
  const urlExportar = root.dataset.urlExportar;

  const selMes = document.getElementById('f_mes');
  const selAnio = document.getElementById('f_anio');
  const inpMatricula = document.getElementById('f_matricula');
  const btnGenerar = document.getElementById('btnGenerar');

  const btnDownloadMes = document.getElementById('btnDownloadMes');
  const btnDownloadAlumno = document.getElementById('btnDownloadAlumno');

  let chartMes = null;
  let chartAlumno = null;

  /*
   * Destruye una instancia de Chart.js si existe.
  */
  function destroyIf(chart) {
    if (chart) chart.destroy();
  }


  /*
   * Carga la gráfica de adeudos por mes y diplomado.
      Realiza la llamada AJAX a la API de reporte, destruye la gráfica anterior, y genera una nueva gráfica de barras 
      con los datos devueltos. También gestiona la visibilidad del botón de descarga Excel.
  */
  async function cargarGraficaMes() {
    const mes  = selMes.value;
    const anio = selAnio.value;
    
    const ctx = document.getElementById('chartMes').getContext('2d');
    if (!ctx) {
        console.error("No se encontró el contexto del canvas 'chartMes'.");
        return;
    }

    if (!mes || !anio) {
      destroyIf(chartMes);
      chartMes = new Chart(ctx, {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Adeudos', data: [] }] },
        options: { 
            responsive: true,
            plugins: { title: { display: true, text: 'Selecciona mes y año' } },
            scales: { y: { beginAtZero: true } }
        }
      });
      btnDownloadMes.style.display = 'none';
      return;
    }

    const res = await fetch(`${urlChartMes}?mes=${encodeURIComponent(mes)}&anio=${encodeURIComponent(anio)}`, {
      credentials: 'same-origin'
    });
    if (!res.ok) {
        btnDownloadMes.style.display = 'none';
        // Se carga la gráfica vacía para notificar el error/estado
        destroyIf(chartMes);
        chartMes = new Chart(ctx, {
            type: 'bar',
            data: { labels: [], datasets: [{ label: 'Error de Carga', data: [] }] },
            options: { 
                responsive: true,
                plugins: { title: { display: true, text: 'Error al cargar los datos del reporte por mes.' } },
                scales: { y: { beginAtZero: true } }
            }
        });
        return;
    }
    const json = await res.json();

    destroyIf(chartMes);
    chartMes = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: Array.isArray(json.labels) ? json.labels : [],
        datasets: [{
          label: 'Alumnos con adeudo',
          data: Array.isArray(json.data) ? json.data.map(n => Number(n || 0)) : [],
          backgroundColor: "rgb(17, 37, 67)",
          borderWidth: 1
        }]
      },
      options: { 
        responsive: true, 
        plugins: {
            legend: { display: false }, 
            title: {
                display: true,
                text: `Alumnos con adeudo en el mes de ${mes} / ${anio}`
            }
        },
        scales: { 
            y: { 
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Número de alumnos'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Diplomado'
                }
            }
        } 
      }
    });

    btnDownloadMes.style.display = (json.data && json.data.length > 0 && json.data.some(n => Number(n) > 0)) ? 'block' : 'none';
  }

  /*
   * Carga la gráfica de adeudos para un alumno específico (GENERAL, sin mes/año).
      Genera una nueva gráfica de BARRA con el estado de adeudo del alumno.
  */
  async function cargarGraficaAlumno() {
    const matricula = inpMatricula.value.trim();

    const ctx = document.getElementById('chartAlumno').getContext('2d');
    if (!ctx) {
        console.error("No se encontró el contexto del canvas 'chartAlumno'.");
        return;
    }

    if (!matricula) {
      destroyIf(chartAlumno);
      chartAlumno = new Chart(ctx, {
        type: 'bar', // Tipo barra
        data: { labels: ['Sin datos'], datasets: [{ data: [0] }] },
        options: { 
            responsive: true, 
            plugins: { title: { display: true, text: 'Introduce la matrícula para el reporte general' } },
            scales: { y: { beginAtZero: true } }
        }
      });
      btnDownloadAlumno.style.display = 'none';
      return;
    }
    
    console.log(`Buscando adeudos generales para Matrícula: ${matricula}`);

    let json = { labels: [], data: [] }; // Inicializar con estructura vacía
    let isError = false;
    let responseText = null;

    try {
        const apiUrl = `${urlChartAlumno}?matricula=${encodeURIComponent(matricula)}`;
        const res = await fetch(apiUrl, {
          credentials: 'same-origin'
        });
        
        // ** CLAVE: Leemos el cuerpo como texto para evitar el error de sintaxis y poder depurar **
        responseText = await res.text(); 
        
        if (!res.ok) {
            isError = true;
            console.error(`ERROR DE CONEXIÓN o SERVIDOR: Código ${res.status} al llamar a: ${apiUrl}`);
            console.error('Respuesta de error del servidor (HTML o Texto):', responseText);
        } else {
            // Intentamos parsear el texto a JSON
            try {
                json = JSON.parse(responseText);
                console.log('Datos recibidos de la API (Alumno):', json);
            } catch (parseError) {
                // Atrapa el SyntaxError: Unexpected token '<'
                isError = true;
                console.error('ERROR DE PARSEO: El servidor devolvió texto no JSON (revisar si es HTML o un mensaje de error). Texto completo:', responseText);
            }
        }

    } catch (e) {
        // Atrapa errores de red puros (ej. CORS o desconexión)
        isError = true;
        console.error('Error de red general (fetch failed):', e);
    }
    
    btnDownloadAlumno.style.display = 'none';

    if (isError || !json.data || json.data.length === 0 || json.data.every(n => Number(n) === 0)) {
        destroyIf(chartAlumno);
        chartAlumno = new Chart(ctx, {
            type: 'bar',
            data: { labels: ['Sin datos'], datasets: [{ label: 'Adeudos', data: [1] }] }, // Usar [1] para que la barra se muestre visiblemente vacía
            options: { 
                responsive: true,
                plugins: { title: { display: true, text: isError ? 'Error de conexión o datos. Revisar Consola.' : `No hay adeudos para matrícula ${matricula}.` } },
                scales: { y: { beginAtZero: true, display: false } }
            }
        });
        return;
    }


    destroyIf(chartAlumno);
    chartAlumno = new Chart(ctx, {
      type: 'bar', // Tipo barra
      data: {
        // Se utiliza la estructura labels y data del JSON de la API
        labels: Array.isArray(json.labels) && json.labels.length ? json.labels : ['Sin adeudo'],
        datasets: [{
          data: Array.isArray(json.data) && json.data.length ? json.data.map(n => Number(n || 0)) : [0],
          backgroundColor: ["rgb(17, 37, 67)", "rgb(17, 37, 67)", 
            "rgb(255, 99, 132)", "rgb(54, 162, 235)", "rgb(255, 206, 86)"],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: { 
              display: true, 
              text: `Adeudos totales para matrícula ${matricula}` 
          }
        },
        scales: { 
            y: { 
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Cantidad de adeudos'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Concepto'
                }
            }
        } 
      }
    });

    // Se ajusta la visibilidad del botón de descarga
    btnDownloadAlumno.style.display = 'block';
  }


  /* 
    *Configura la navegación y la visibilidad de las secciones del reporte (Mes vs Alumno).
  */
  function tabsInit() {
    const tabs = document.querySelectorAll('#tabs .tab');
    const sections = {
      mes: document.getElementById('tab-mes'),
      alumno: document.getElementById('tab-alumno')
    };
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const target = tab.dataset.tab;
        sections.mes.style.display    = (target === 'mes') ? 'block' : 'none';
        sections.alumno.style.display = (target === 'alumno') ? 'block' : 'none';
        
        // ** CLAVE: Cargar la gráfica al activar la pestaña **
        if (target === 'mes') {
            cargarGraficaMes();
        } else {
            // Cargar la gráfica de alumno solo al activar su pestaña
            cargarGraficaAlumno(); 
        }
      });
    });
  }


  /* Listener para el botón 'Generar Reporte'. */
  btnGenerar?.addEventListener('click', () => {
    const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'mes';
    if (activeTab === 'mes') {
      cargarGraficaMes();
    } else {
      cargarGraficaAlumno();
    }
  });


  /* Listener para el botón 'Descargar (Reporte Mes)'. */
  btnDownloadMes?.addEventListener('click', () => {
    const mes  = selMes.value;
    const anio = selAnio.value;
    if (mes && anio) {
      window.location.href = `${urlExportar}?mes=${encodeURIComponent(mes)}&anio=${encodeURIComponent(anio)}&tipo=mes`;
    } else {
      // Modificado para usar un modal/log en lugar de alert() si fuera necesario, aunque el HTML usa alert en este caso
      console.warn('Por favor, selecciona un mes y año para descargar.');
    }
  });

  /* Listener para el botón 'Descargar (Reporte Alumno)'. */
  btnDownloadAlumno?.addEventListener('click', () => {
    // Ya no se usan mes y anio para el reporte general de alumno
    const matricula = inpMatricula.value.trim();
    if (matricula) {
      window.location.href = `${urlExportar}?tipo=alumno&matricula=${encodeURIComponent(matricula)}`;
    } else {
      // Modificado para usar un modal/log en lugar de alert() si fuera necesario, aunque el HTML usa alert en este caso
      console.warn('Por favor, introduce una matrícula para descargar.');
    }
  });

  /* Inicialización de pestañas y carga de gráfica inicial. */
  tabsInit();
  
  // Se determina qué gráfica cargar al inicio (solo si la sección está activa)
  const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'mes';
  if (activeTab === 'mes') {
      cargarGraficaMes();
  } else {
      // Si la pestaña alumno está activa por defecto (aunque el HTML la oculta), se carga.
      cargarGraficaAlumno(); 
  }
});
