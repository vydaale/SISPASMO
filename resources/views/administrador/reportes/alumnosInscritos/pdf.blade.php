<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: sans-serif; }
        h1 { text-align: center; color: #333; }
        h3 { text-align: center; color: #666; }
        img { display: block; margin: 20px auto; max-width: 100%; }
    </style>
</head>
<body>
    <h1>{{ $titulo }}</h1>
    <h3>{{ $subtitulo }}</h3>
    <img src="{{ $imageData }}" alt="Gráfica del reporte">
</body>
</html>