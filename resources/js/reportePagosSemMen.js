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
          backgroundColor: 'rgba(54, 162, 235, 0.5)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: { scales: { y: { beginAtZero: true } } }
    });
  });
  