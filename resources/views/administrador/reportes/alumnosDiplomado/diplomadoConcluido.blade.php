<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Egresados {{ $year }}</title>
    <style>
        body { font-family: sans-serif; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .chart-box { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="report-header">
        <h1>Reporte de Alumnos Egresados por Diplomado ({{ $year }})</h1>
    </div>

    <div class="chart-box">
        @if (isset($chartImage))
            <img src="{{ $chartImage }}" alt="Gráfico de Alumnos Egresados" style="width: 100%;">
        @else
            <p>No se pudo generar la gráfica.</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Diplomado</th>
                <th>Grupo</th>
                <th>Número de Egresados</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $d)
                <tr>
                    <td>{{ $d->nombre }}</td>
                    <td>{{ $d->grupo }}</td>
                    <td>{{ $d->egresados }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>