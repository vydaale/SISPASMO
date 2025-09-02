<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel administrador</title>

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  @vite('resources/css/crud.css')
  @vite('resources/css/dashboard.css')
  @vite(['resources/js/dashboard.js'])
  
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
          <div class="group-title">USUARIOS</div>
          <ul class="menu">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle">Alumnos</a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('alumnos.index') }}">Listar Alumnos</a></li>
                <li><a href="{{ route('alumnos.create') }}">Nuevo Alumno</a></li>
                <li><a href="{{ route('docentes.create') }}">Nuevo docente</a></li>
                <li><a href="{{ route('docentes.index') }}">Listar docente</a></li>
                <li><a href="{{ route('aspirantes.index') }}">Listar aspirantes</a></li>
                <li><a href="#">Nuevo administrativo</a></li>
                <li><a href="#">Listar administrativos</a></li>
                <li><a href="{{ route('coordinadores.create') }}">Nuevo coordinador</a></li>
                <li><a href="{{ route('coordinadores.index') }}">Listar coordinadores</a></li>
              </ul>
            </li>
          </ul>
        </div>

        <div class="divider"></div>
        <div class="group">
          <div class="group-title">Funcionalidades</div>
          <ul class="menu">
            <li><a href="#">Recibos</a></li>
            <li><a href="#">Horarios</a></li>
            <li><a href="#">Ficha médica</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <ul class="menu">
            <li><a href="#">Módulos</a></li>
            <li><a href="#">Talleres y prácticas</a></li>
            <li><a href="#">Dudas y sugerencias</a></li>
            <li><a href="#">Citas</a></li>
            <li><a href="#">Calificaciones</a></li>
            <li><a href="#">Reportes</a></li>
            <li><a href="{{ route('quejas.index') }}">Queja/sugerencia</a></li>
            <li><a href="#">Base de datos</a></li>
            <li><a href="#">Notificaciones</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="search">
          <label for="q">Buscar módulo:</label>
          <input id="q" type="text" placeholder="Escribe aquí…">
        </div>
      </nav>
    </aside>

  <div class="crud-wrap">
    <section class="crud-card">
      <header class="crud-hero">
        <h2 class="crud-hero-title">Gestión de alumnos</h2>
        <p class="crud-hero-subtitle">Listado</p>

        {{-- Pestañas (opcionales) --}}
        <nav class="crud-tabs">
          <a href="{{ route('alumnos.create') }}" class="tab">Registrar</a>
          <a href="{{ route('alumnos.index') }}" class="tab active">Listar alumnos</a>
        </nav>
      </header>

      <div class="crud-body">
        <h1>Alumnos</h1>

        @if(session('ok'))
          <div class="gm-ok">{{ session('ok') }}</div>
        @endif

        @if($alumnos->count() === 0)
          <div class="gm-empty">No hay alumnos registrados.</div>
        @else
          <div class="table-responsive">
            <table class="gm-table">
              <thead>
                <tr>
                  <th>Matrícula</th>
                  <th>Nombre</th>
                  <th>Correo</th>
                  <th>Diplomado</th>
                  <th>Grupo</th>
                  <th>Estatus</th>
                  <th class="th-actions">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @foreach($alumnos as $a)
                  <tr>
                    <td>{{ $a->matriculaA }}</td>
                    <td>
                      {{ optional($a->usuario)->nombre }}
                      {{ optional($a->usuario)->apellidoP }}
                      {{ optional($a->usuario)->apellidoM }}
                    </td>
                    <td>{{ optional($a->usuario)->correo }}</td>
                    <td>{{ $a->num_diplomado }}</td>
                    <td>{{ $a->grupo }}</td>
                    <td>{{ $a->estatus }}</td>
                    <td>
                      <div class="table-actions">
                        <a href="{{ route('alumnos.edit', $a) }}" class="btn-ghost">Editar</a>

                        <form action="{{ route('alumnos.destroy', $a) }}" method="POST" onsubmit="return confirm('¿Eliminar alumno y su usuario?')">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="crud-toolbar">
            <a href="{{ route('alumnos.create') }}" class="btn btn-primary">Nuevo alumno</a>
          </div>
          
          <div class="pager">
            {{ $alumnos->links() }}
          </div>
        @endif
      </div>
    </section>
  </div>

</body>
</html>
