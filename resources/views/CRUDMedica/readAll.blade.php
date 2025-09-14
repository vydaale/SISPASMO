{{-- resources/views/CRUDMedica/readAll.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fichas médicas — Administración</title>

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

  <main class="content">
    <div class="crud-wrap">
      <section class="crud-card">
        <header class="crud-hero">
          <h2 class="crud-hero-title">Gestión de fichas médicas</h2>
          <p class="crud-hero-subtitle">Listado</p>

          <nav class="crud-tabs">
            <a href="{{ route('fichasmedicas.index') }}" class="tab active">Listar fichas</a>
          </nav>
        </header>

        <div class="crud-body">
          @if (session('ok'))
            <div class="gm-ok">{{ session('ok') }}</div>
          @endif

          {{-- Buscador por alumno (opcional si tienes columnas) --}}
          {{-- 
          <form method="GET" class="gm-search">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre del alumno...">
            <button>Buscar</button>
          </form>
          --}}

          <div class="table-wrap">
            <table class="gm-table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Matricula</th>
                  <th>Grupo</th>
                  <th>Institución</th>
                  <th>Crónica</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($fichas as $f)
                  <tr>
                    <td>{{ $f->id_ficha }}</td>
                    <td>{{ $f->alumno?->matriculaA ?? '—' }}</td>
                    <td>{{ $f->alumno?->grupo ?? '—' }}</td>
                    <td>{{ $f->contacto?->institucion ?? '—' }}</td>
                    <td>{{ $f->enfermedades?->enfermedad_cronica ? 'Sí' : 'No' }}</td>
                    <td class="actions">
                      <a class="btn btn-small" href="{{ route('fichasmedicas.show', $f) }}">Ver</a>
                      <form method="POST" action="{{ route('fichasmedicas.destroy', $f) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-small"
                          onclick="return confirm('¿Eliminar esta ficha? Esta acción no se puede deshacer.')">
                          Eliminar
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="empty">No hay fichas registradas.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="pagination">
            {{ $fichas->links() }}
          </div>
        </div>
      </section>
    </div>
  </main>

</body>
</html>
