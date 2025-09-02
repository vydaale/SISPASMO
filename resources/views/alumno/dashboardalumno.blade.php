<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  @vite('resources/css/dashboard.css')
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
            <li><a href="{{ route('alumno.login') }}">Cerrar sesión</a></li>
          </ul>
        </nav>
    </div>
  </header>

  <div class="dash">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar" aria-hidden="true">👤</div>
        <div class="who">
          <div class="name">
            {{ auth()->user()->nombre ?? 'Usuario' }}
            {{ auth()->user()->apellidoP ?? '' }}
          </div>
          <div class="role">{{ auth()->user()->rol->nombre_rol ?? '—' }}</div>
        </div>
      </div>

      <nav class="nav">
        <div class="group">
          <div class="group-title">INFORMACIÓN PERSONAL</div>
          <ul class="menu">
            <li><a href="#">Mi información</a></li>
          </ul>
        </div>

        <div class="group">
          <div class="group-title">MÓDULOS</div>
          <ul class="menu">
            <li><a href="#">Calificaciones</a></li>
            <li><a href="#">Horarios</a></li>
            <li><a href="#">Historial</a></li>
            <li><a href="{{ route('quejas.create') }}">Nueva queja/sugerencia</a></li>
            <li><a href="{{ route('quejas.propias') }}">Mis quejas/sugerencias</a></li>  
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <div class="group-title">EXTRACURRICULARES</div>
          <ul class="menu">
            <li><a href="#">Talleres</a></li>
            <li><a href="#">Prácticas</a></li>
            <li><a href="#">Cursos</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <div class="group-title">EVI</div>
          <ul class="menu">
            <li><a href="#">Evaluación</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <div class="group-title">TRAMITES</div>
          <ul class="menu">
            <li><a href="#">Constancias</a></li>
          </ul>
        </div>

        
        <div class="search">
          <label for="q">Buscar módulo:</label>
          <input id="q" type="text" placeholder="Escribe aquí…">
        </div>
      </nav>
    </aside>

    <main class="content">
    </main>
  </div>

</body>
</html>