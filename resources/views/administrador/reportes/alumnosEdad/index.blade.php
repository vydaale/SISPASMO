<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Reporte: alumnos por edad</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/crud.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  </head>
  <body>

  <header class="site-header">
    <div class="header-container">
      <div class="logo">
        <img src="{{ asset('images/logoprincipal.png') }}" alt="Grupo Morelos"/>
        <span>GRUPO MORELOS</span>
      </div>
      <nav>
        <ul class="nav-links">
          <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
          <li>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</a>
            </form>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de alumnos por edad</h1>
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="rangos" href="javascript:void(0)">Por rangos</a>
            <a class="tab" data-tab="tabla" href="javascript:void(0)">Por edad exacta</a>
          </div>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          {{-- TAB: GRÁFICA POR RANGOS --}}
          <section id="tab-rangos">
            <div style="max-width:880px;margin:0 auto">
              <canvas id="chartRangos"></canvas>
            </div>

            <div class="actions" style="justify-content:flex-end;margin-top:14px">
              <form id="pdfForm" method="POST" action="{{ route('admin.reportes.alumnosEdad.pdf') }}">
                @csrf
                <input type="hidden" name="chart_data_url" id="chart_data_url">
                <input type="hidden" name="titulo" value="Alumnos por rango de edad">
                <button type="button" class="btn btn-primary" onclick="descargarPDF()">Descargar PDF</button>
              </form>
            </div>
          </section>

          <section id="tab-tabla" style="display:none">
            <div id="tablaContainer">
            </div>
            <div class="actions" style="justify-content:flex-end;margin-top:14px">
              <a class="btn btn-primary" href="{{ route('admin.reportes.alumnosEdad.excel') }}">Descargar Excel</a>
            </div>
          </section>

        </div> {{-- .crud-body --}}
      </div> {{-- .crud-card --}}
    </div> {{-- .crud-wrap --}}
  </div> {{-- .dash --}}

  <script>
  let chart;

  async function cargarGrafica() {
    const res = await fetch('{{ route('admin.reportes.alumnosEdad.chartData') }}', {credentials:'same-origin'});
    const json = await res.json();

    const ctx = document.getElementById('chartRangos').getContext('2d');
    if (chart) chart.destroy();
    chart = new Chart(ctx, {
      type: 'bar',
      data: { labels: json.labels, datasets: [{ label: 'Alumnos', data: json.data }] },
      options: {
        responsive: true,
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
      }
    });
  }

  async function cargarTabla(url = '{{ route('admin.reportes.alumnosEdad.table') }}') {
    const res = await fetch(url, {credentials:'same-origin'});
    const html = await res.text();
    document.getElementById('tablaContainer').innerHTML = html;

    document.querySelectorAll('#tablaContainer .pager a').forEach(a => {
      a.addEventListener('click', e => {
        e.preventDefault();
        cargarTabla(a.getAttribute('href'));
      });
    });
  }

  function descargarPDF() {
    const canvas = document.getElementById('chartRangos');
    const dataUrl = canvas.toDataURL('image/png');
    document.getElementById('chart_data_url').value = dataUrl;
    document.getElementById('pdfForm').submit();
  }

  // Tabs
  document.querySelectorAll('#tabs .tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('#tabs .tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const target = tab.dataset.tab;
      document.getElementById('tab-rangos').style.display = (target === 'rangos') ? 'block' : 'none';
      document.getElementById('tab-tabla').style.display  = (target === 'tabla')  ? 'block' : 'none';

      if (target === 'rangos') cargarGrafica();
      if (target === 'tabla')  cargarTabla();
    });
  });

  cargarGrafica();
  </script>
</body>
</html>
