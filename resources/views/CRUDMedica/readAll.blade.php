@extends('layouts.encabezados')

@section('title', 'Gestión Fichas Médicas')

@section('content')
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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre del alumno">
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
                      <a class="btn btn-ghost" href="{{ route('fichasmedicas.show', $f) }}">Ver</a>
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
@endsection