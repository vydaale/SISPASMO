import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('reporteAdeudosRoot');
  if (!root) return;

  const urlChartMes     = root.dataset.urlChartMes;
  const urlChartAlumno  = root.dataset.urlChartAlumno;
  const urlExportar     = root.dataset.urlExportar;

  const selMes        = document.getElementById('f_mes');
  const selAnio       = document.getElementById('f_anio');
  const inpMatricula  = document.getElementById('f_matricula');
  const btnGenerar    = document.getElementById('btnGenerar');

  const btnDownloadMes    = document.getElementById('btnDownloadMes');
  const btnDownloadAlumno = document.getElementById('btnDownloadAlumno');

  let chartMes     = null;
  let chartAlumno  = null;

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
      });
    });
  }

  function destroyIf(chart) {
    if (chart) chart.destroy();
  }

  async function cargarGraficaMes() {
    const mes  = selMes.value;
    const anio = selAnio.value;
    if (!mes || !anio) {
      const ctx = document.getElementById('chartMes').getContext('2d');
      destroyIf(chartMes);
      chartMes = new Chart(ctx, {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Adeudos', data: [] }] },
        options: { scales: { y: { beginAtZero: true } } }
      });
      btnDownloadMes.style.display = 'none';
      return;
    }

    const res = await fetch(`${urlChartMes}?mes=${encodeURIComponent(mes)}&anio=${encodeURIComponent(anio)}`, {
      credentials: 'same-origin'
    });
    if (!res.ok) {
        btnDownloadMes.style.display = 'none';
        return;
    }
    const json = await res.json();

    const ctx = document.getElementById('chartMes').getContext('2d');
    destroyIf(chartMes);
    chartMes = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: Array.isArray(json.labels) ? json.labels : [],
        datasets: [{
          label: 'Alumnos con adeudo',
          data: Array.isArray(json.data) ? json.data.map(n => Number(n || 0)) : [],
          backgroundColor: 'rgb(17, 37, 67)',
          borderWidth: 1
        }]
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    btnDownloadMes.style.display = (json.data.length > 0) ? 'block' : 'none';
  }

  async function cargarGraficaAlumno() {
    const mes       = selMes.value;
    const anio      = selAnio.value;
    const matricula = inpMatricula.value.trim();

    const ctx = document.getElementById('chartAlumno').getContext('2d');
    if (!mes || !anio || !matricula) {
      // Estado vacío si faltan filtros
      destroyIf(chartAlumno);
      chartAlumno = new Chart(ctx, {
        type: 'doughnut',
        data: { labels: ['Sin datos'], datasets: [{ data: [0] }] },
        options: { responsive: true }
      });
      btnDownloadAlumno.style.display = 'none';
      return;
    }

    const res = await fetch(`${urlChartAlumno}?mes=${encodeURIComponent(mes)}&anio=${encodeURIComponent(anio)}&matricula=${encodeURIComponent(matricula)}`, {
      credentials: 'same-origin'
    });
    if (!res.ok) {
        btnDownloadAlumno.style.display = 'none';
        return;
    }
    const json = await res.json();

    destroyIf(chartAlumno);
    chartAlumno = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: Array.isArray(json.labels) && json.labels.length ? json.labels : ['Sin adeudo'],
        datasets: [{
          data: Array.isArray(json.data) && json.data.length ? json.data.map(n => Number(n || 0)) : [0],
          backgroundColor: ['rgb(17, 37, 67)','rgba(54, 162, 235, 0.5)'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' },
          title: { display: true, text: 'Adeudo por matrícula' }
        }
      }
    });

    btnDownloadAlumno.style.display = (json.data.length > 0) ? 'block' : 'none';
  }

  btnGenerar?.addEventListener('click', () => {
    const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'mes';
    if (activeTab === 'mes') {
      cargarGraficaMes();
    } else {
      cargarGraficaAlumno();
    }
  });

  btnDownloadMes?.addEventListener('click', () => {
    const mes  = selMes.value;
    const anio = selAnio.value;
    if (mes && anio) {
      window.location.href = `${urlExportar}?mes=${encodeURIComponent(mes)}&anio=${encodeURIComponent(anio)}&tipo=mes`;
    } else {
        alert('Por favor, selecciona un mes y año para descargar.');
    }
  });

  btnDownloadAlumno?.addEventListener('click', () => {
    const mes       = selMes.value;
    const anio      = selAnio.value;
    const matricula = inpMatricula.value.trim();
    if (mes && anio && matricula) {
      window.location.href = `${urlExportar}?mes=${encodeURIComponent(mes)}&anio=${encodeURIComponent(anio)}&tipo=alumno&matricula=${encodeURIComponent(matricula)}`;
    } else {
      alert('Por favor, selecciona un mes, año y matrícula para descargar.');
    }
  });

  tabsInit();
  const activeTab = document.querySelector('#tabs .tab.active')?.dataset.tab || 'mes';
  if (activeTab === 'mes') {
      cargarGraficaMes();
  } else {
      cargarGraficaAlumno();
  }
});