import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('reporteRoot');
  if (!root) return;

  const urlRangos = root.dataset.chartRangosUrl;
  const urlExact  = root.dataset.chartExactUrl;

  let chartRangos = null;
  let chartExacta = null;

  const azulFuerte = '#112543';

  async function cargarGraficaRangos() {
    const res = await fetch(urlRangos, { credentials: 'same-origin' });
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
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      }
    });
  }

  async function cargarGraficaExacta() {
    const res = await fetch(urlExact, { credentials: 'same-origin' });
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
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      }
    });
  }

  function descargarPDF(canvasId, hiddenInputId, formId) {
    const canvas = document.getElementById(canvasId);
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById(hiddenInputId).value = dataUrl;
    document.getElementById(formId).submit();
  }

  document.getElementById('btnPDFRangos')?.addEventListener('click', () => {
    descargarPDF('chartRangos', 'chart_data_url_rangos', 'pdfFormRangos');
  });
  document.getElementById('btnPDFExacta')?.addEventListener('click', () => {
    descargarPDF('chartExacta', 'chart_data_url_exacta', 'pdfFormExacta');
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
