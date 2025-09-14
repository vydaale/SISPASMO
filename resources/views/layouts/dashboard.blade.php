<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title','Panel administrador')</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  @vite('resources/css/crud.css')
  @vite('resources/css/dashboard.css')
  @vite(['resources/js/dashboard.js'])
</head>
<body>
  <div class="dash">
    @include('partials.sidebar') {{-- <==== AQUÍ TRAES TU BARRA LATERAL --}}
    <main class="content">
      @yield('content') {{-- cada página inyecta su contenido aquí --}}
    </main>
  </div>
</body>
</html>
