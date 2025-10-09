document.addEventListener('DOMContentLoaded', function() {
  const dataElement = document.getElementById('pagos-data');
  const canvas = document.getElementById('pagosChart');
  if (!dataElement || !canvas || typeof Chart === 'undefined') return;

  let pagosData = JSON.parse(dataElement.textContent || '[]');

  if (!Array.isArray(pagosData) && pagosData && typeof pagosData === 'object') {
    pagosData = Object.values(pagosData);
  }
  if (!Array.isArray(pagosData) || pagosData.length === 0) return;

  const labels = pagosData.map(p => p.label ?? p.fecha_pago);
  const data   = pagosData.map(p => Number(p.monto || 0));

  const ctx = canvas.getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Monto de Pagos',
        data,
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
              text: 'Total de pagos calidados por período'
          }
      },
      scales: {
          y: { 
              beginAtZero: true,
              title: {
                  display: true,
                  text: 'Monto total recibido (MXN)'
              }
          },
          x: {
              title: {
                  display: true,
                  text: 'Período (semana o mes)'
              }
          }
      } 
    }
  });
});