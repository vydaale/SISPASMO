<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <title>{{ $titulo }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            margin: 0; 
            padding: 0;
            color: #333;
            font-size: 12px;
        }

        .header-pdf {
            display: flex; 
            justify-content: center;
            align-items: center; 
            padding: 15px 40px; 
            gap: 25px; 
            border-bottom: 3px solid #112543; 
            margin-bottom: 25px;
        }

        .header-pdf .logo {
            max-height: 55px;
            width: auto;
            margin: 0;
        }

        .header-pdf .title-fixed {
            font-size: 1.6em;
            font-weight: bold;
            color: #112543;
            margin: 0;
        }

        .report-title {
            text-align: center; 
            color: #112543; 
            font-size: 18px;
            margin: 25px 0 5px 0;
            font-weight: bold;
        }

        .report-info { 
            text-align: center; 
            color: #666; 
            font-size: 12px;
            margin-top: 0;
            margin-bottom: 30px;
        }
        
        .chart { 
            text-align: center;
            margin-top: 10px;
        }
        .chart img { 
            display: block; 
            margin: 30px auto; 
            max-width: 95%;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>
<body>
    
    <div class="header-pdf">
        <img class="logo" src="{{ public_path('images/logoprincipal.png') }}" alt="Logo Principal">
        <h2 class="title-fixed">Reportes académicos</h2>
    </div>

    <h1 class="report-title">{{ $titulo }}</h1>
    
    <div class="report-info">Generado: {{ $fecha }}</div>
    
    <div class="chart">
        <img src="{{ $chart_data_url }}" alt="Gráfica del reporte">
    </div>
    
</body>
</html>