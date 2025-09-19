document.addEventListener('DOMContentLoaded', function() {
    // Si la variable pagosData existe en el DOM
    const dataElement = document.getElementById('pagos-data');
    if (dataElement) {
        const pagosData = JSON.parse(dataElement.textContent);
        
        const labels = pagosData.map(p => p.fecha_pago);
        const data = pagosData.map(p => p.monto);

        const ctx = document.getElementById('pagosChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monto de Pagos',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});