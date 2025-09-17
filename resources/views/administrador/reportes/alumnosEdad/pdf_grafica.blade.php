<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <title>{{ $titulo }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h1 { font-size: 18px; margin: 0 0 8px 0; }
    .muted { color:#555; margin-bottom: 12px; }
    .chart { text-align:center; margin-top: 10px; }
    img { max-width: 100%; }
  </style>
</head>
<body>
  <h1>{{ $titulo }}</h1>
  <div class="muted">Generado: {{ $fecha }}</div>
  <div class="chart">
    <img src="{{ $chart_data_url }}" alt="Gráfica de alumnos por rango de edad">
  </div>
</body>
</html>
