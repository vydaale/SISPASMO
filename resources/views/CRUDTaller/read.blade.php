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
                <li><a href="{{ route('aspirantes.create') }}">Nuevo aspirante</a></li>
                <li><a href="{{ route('aspirantes.index') }}">Listar aspirantes</a></li>
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
            <li><a href="{{ route('modulos.index') }}">Módulos</a></li>
            <li><a href="{{ route('extracurricular.index') }}">Extra curriculares</a></li>
          </ul>
        </div>

        <div class="divider"></div>

        <div class="group">
          <ul class="menu">
            <li><a href="#">Dudas y sugerencias</a></li>
            <li><a href="#">Citas</a></li>
            <li><a href="#">Reportes</a></li>
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

    <!-- Contenido -->
    <main class="content">
      <div class="crud-wrap">
        <section class="crud-card">
          <header class="crud-hero">
            <h2 class="crud-hero-title">Gestión de extra curriculares</h2>
            <p class="crud-hero-subtitle">Listado</p>

            <nav class="crud-tabs">
              <a href="{{ route('extracurricular.create') }}" class="tab">Registrar</a>
              <a href="{{ route('extracurricular.index') }}" class="tab active">Listar actividades</a>
            </nav>
          </header>

          <div class="crud-body">
            <h1>Actividades extra curriculares</h1>

            <p><a href="{{ route('extracurricular.create') }}" class="btn btn-primary">Nueva actividad</a></p>

            @if(session('success'))
              <div class="gm-ok">{{ session('success') }}</div>
            @endif
            @if(session('ok'))
              <div class="gm-ok">{{ session('ok') }}</div>
            @endif

            @if($talleres->count() === 0)
              <div class="gm-empty">No hay actividades registradas.</div>
            @else
              <div class="table-responsive">
                <table class="gm-table">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Responsable</th>
                      <th>Fecha</th>
                      <th>Tipo</th>
                      <th>Inicio</th>
                      <th>Fin</th>
                      <th>Lugar</th>
                      <th>Modalidad</th>
                      <th>Estatus</th>
                      <th>Capacidad</th>
                      <th>Material</th>
                      <th>URL</th>
                      <th class="th-actions">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($talleres as $e)
                      <tr>
                        <td>{{ $e->nombre_act }}</td>
                        <td>{{ $e->responsable }}</td>
                        <td>{{ $e->fecha }}</td>
                        <td>{{ $e->tipo }}</td>
                        <td>{{ $e->hora_inicio }}</td>
                        <td>{{ $e->hora_fin }}</td>
                        <td>{{ $e->lugar }}</td>
                        <td>{{ $e->modalidad }}</td>
                        <td>{{ $e->estatus }}</td>
                        <td>{{ $e->capacidad }}</td>
                        <td>{{ $e->material }}</td>
                        <td>
                          @if(!empty($e->url))
                            <a href="{{ $e->url }}" target="_blank" rel="noopener">Abrir</a>
                          @else
                            —
                          @endif
                        </td>
                        <td>
                          <div class="table-actions">
                            <a href="{{ route('extracurricular.edit', $e) }}" class="btn-ghost">Actualizar</a>

                            <form action="{{ route('extracurricular.destroy', $e) }}"
                                  method="POST"
                                  onsubmit="return confirm('¿Eliminar la actividad {{ $e->nombre_act }}?')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="pager">
                {{ $talleres->links() }}
              </div>
            @endif
          </div>
        </section>
      </div>
    </main>
  </div>
</body>
</html>
