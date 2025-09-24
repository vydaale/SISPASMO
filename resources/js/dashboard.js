import Chart from 'chart.js/auto';
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('alumnosChart');
  if (!el) return;

  const ctx = el.getContext('2d');
  const activos = Number(el.dataset.activos || 0);
  const baja    = Number(el.dataset.baja || 0);

  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Alumnos'],
      datasets: [
        { label: 'Activos', data: [activos] },
        { label: 'Baja',    data: [baja] }
      ]
    },
    options: {
      responsive: true,
      scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } },
      plugins: { legend: { position: 'top' } }
    }
  });

  const metricsUrl = el.dataset.metricsUrl || `${window.location.origin}/administrador/dashboard/metrics`;

  async function refreshMetrics() {
    try {
      const res = await fetch(metricsUrl, { credentials: 'same-origin' });
      if (!res.ok) return;

      const data = await res.json();

      const nAlumnos    = document.getElementById('nAlumnos');
      const nDocentes   = document.getElementById('nDocentes');
      const nAspirantes = document.getElementById('nAspirantes');
      if (nAlumnos)    nAlumnos.textContent    = data.alumnos.total;
      if (nDocentes)   nDocentes.textContent   = data.docentes;
      if (nAspirantes) nAspirantes.textContent = data.aspirantes;

      chart.data.datasets[0].data = [data.alumnos.activos];
      chart.data.datasets[1].data = [data.alumnos.baja];
      chart.update();
    } catch {
    }
  }

  refreshMetrics();
  setInterval(refreshMetrics, 60000);
});