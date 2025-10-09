import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('reporteRoot');
  if (!root) return;

  const urlRangos = root.dataset.chartRangosUrl;
  const urlExact  = root.dataset.chartExactUrl;

  const selDiplomado = document.getElementById('f_diplomado');
  const btnGenerar   = document.getElementById('btnGenerar');

  let chartRangos = null;
  let chartExacta = null;

  const azulFuerte = '#112543';
  
  function getFilterParams() {
      const params = new URLSearchParams();
      const diplomadoId = selDiplomado ? selDiplomado.value : '';
      if (diplomadoId) {
          params.append('diplomado_id', diplomadoId);
      }
      return params.toString();
  }


  async function cargarGraficaRangos() {
    const params = getFilterParams();
    const fetchUrl = `${urlRangos}?${params}`;
    
    const res = await fetch(fetchUrl, { credentials: 'same-origin' });
    if (!res.ok) return;
    const json = await res.json();

    const ctx = document.getElementById('chartRangos').getContext('2d');
    if (chartRangos) chartRangos.destroy();

    chartRangos = new Chart(ctx, {
      type: 'bar',
      data: { labels: json.labels, datasets: [{ label: 'Alumnos', data: json.data, backgroundColor: azulFuerte, borderColor: azulFuerte }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            title: {
                display: true,
                text: 'Alumnos por rangos de edad'
            }
        },
        scales: { 
            y: { 
                beginAtZero: true,
                title: { display: true, text: 'Número de alumnos' } 
            },
            x: {
                title: { display: true, text: 'Rango de edad (años)' } 
            }
        }
      }
    });
  }

  async function cargarGraficaExacta() {
    const params = getFilterParams();
    const fetchUrl = `${urlExact}?${params}`;
    
    const res = await fetch(fetchUrl, { credentials: 'same-origin' });
    if (!res.ok) return;
    const json = await res.json();

    const ctx = document.getElementById('chartExacta').getContext('2d');
    if (chartExacta) chartExacta.destroy();

    chartExacta = new Chart(ctx, {
      type: 'bar',
      data: { labels: json.labels, datasets: [{ label: 'Alumnos', data: json.data, backgroundColor: azulFuerte, borderColor: azulFuerte }] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            title: {
                display: true,
                text: 'Alumnos por edad exacta'
            }
        },
        scales: { 
            y: { 
                beginAtZero: true,
                title: { display: true, text: 'Número de alumnos' }
            },
            x: {
                title: { display: true, text: 'Edad (años)' }
            }
        }
      }
    });
  }

  function descargarPDF(canvasId, hiddenInputId, formId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return; 

    const diplomadoNombre = selDiplomado.options[selDiplomado.selectedIndex].text;
    const form = document.getElementById(formId);
    
    let subTituloInput = form.querySelector('input[name="subtitulo"]');
    if (!subTituloInput) {
        subTituloInput = document.createElement('input');
        subTituloInput.type = 'hidden';
        subTituloInput.name = 'subtitulo';
        form.appendChild(subTituloInput);
    }
    subTituloInput.value = diplomadoNombre;


    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById(hiddenInputId).value = dataUrl;
    form.submit();
  }

  document.getElementById('btnPDFRangos')?.addEventListener('click', () => {
    descargarPDF('chartRangos', 'chart_data_url_rangos', 'pdfFormRangos');
  });
  document.getElementById('btnPDFExacta')?.addEventListener('click', () => {
    descargarPDF('chartExacta', 'chart_data_url_exacta', 'pdfFormExacta');
  });

  btnGenerar?.addEventListener('click', () => {
      const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'rangos';
      if (activeTab === 'rangos') {
          cargarGraficaRangos();
      } else {
          cargarGraficaExacta();
      }
  });


  document.querySelectorAll('#tabs .tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('#tabs .tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const target = tab.dataset.tab;
      const isRangos = (target === 'rangos');

      document.getElementById('tab-rangos').style.display = isRangos ? 'block' : 'none';
      document.getElementById('tab-exacta').style.display = !isRangos ? 'block' : 'none';

      if (isRangos) cargarGraficaRangos();
      else cargarGraficaExacta();
    });
  });

  cargarGraficaRangos();
});