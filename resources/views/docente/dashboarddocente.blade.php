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
            <li><a href="{{ route('docente.login') }}">Cerrar sesión</a></li>
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

        <div class="divider"></div>

        <div class="group">
          <div class="group-title">Módulos</div>
          <ul class="menu">
            <li><a href="#">Horarios</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <div class="group-title">Alumnos</div>
          <ul class="menu">
            <li><a href="#">Asistencias</a></li>
            <li><a href="#">Calificaciones</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <div class="group-title">Evaluación</div>
          <ul class="menu">
            <li><a href="#">Mi evaluación</a></li>
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