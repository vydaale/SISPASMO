@extends('layouts.encabezados')
@section('title', 'Dashboard de Administrador')

@section('content')
  <h1>Bienvenido al Panel de Administrador</h1>

  <section class="stats-grid">
    <article class="stat-card">
      <h3>Alumnos</h3>
      <div id="nAlumnos" class="stat-number">{{ $alumnosTotal ?? 0 }}</div>
    </article>

    <article class="stat-card">
      <h3>Docentes</h3>
      <div id="nDocentes" class="stat-number">{{ $docentesTotal ?? 0 }}</div>
    </article>

    <article class="stat-card">
      <h3>Aspirantes</h3>
      <div id="nAspirantes" class="stat-number">{{ $aspirantesTotal ?? 0 }}</div>
    </article>
  </section>

  <section class="panel">
    <div class="panel-header">
      <h2>Alumnos activos/baja</h2>
    </div>
    <div class="panel-body">
      <canvas id="alumnosChart"
        data-activos="{{ $alumnosActivos ?? 0 }}"
        data-baja="{{ $alumnosBaja ?? 0 }}"
        data-metrics-url="{{ route('admin.dashboard.metrics') }}">
      </canvas>
    </div>
  </section>

  <section class="two-col">
    <div class="panel">
      <div class="panel-header">
        <h2>Calendario semanal</h2>
      </div>
      <div class="panel-body">
        <table class="mini-calendar">
          <thead>
            <tr>
              <th>L</th>
              <th>M</th>
              <th>M</th>
              <th>J</th>
              <th>V</th>
              <th>S</th>
              <th>D</th>
            </tr>
          </thead>
          <tbody>
            {{-- Obtener el primer día de la semana actual --}}
            @php
              $startOfWeek = \Carbon\Carbon::now()->startOfWeek();
            @endphp
            @for($r=0; $r < 5; $r++)
              <tr>
                @for($c=0; $c < 7; $c++)
                  @php
                    $currentDate = $startOfWeek->copy()->addDays($r * 7 + $c);
                    $dateKey = $currentDate->format('Y-m-d');
                    $tipoActividad = $mapaActividades[$dateKey] ?? null;
                    $class = $tipoActividad ? 'has-' . $tipoActividad : '';
                  @endphp
                  {{-- Agregar la clase dinámica a la celda --}}
                  <td class="{{ $class }}">
                    @if ($tipoActividad)
                        {{-- Mostrar la fecha si hay una actividad --}}
                        {{ $currentDate->day }}
                    @else
                        &nbsp;
                    @endif
                  </td>
                @endfor
              </tr>
            @endfor
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <div class="panel-header">
        <h2>Actividades semanales</h2>
      </div>
      <div class="panel-body">
        @forelse($actividadesSemanales as $actividad)
          <div class="activity-item">
            <span class="activity-date">
              {{ \Carbon\Carbon::parse($actividad->fecha)->locale('es')->isoFormat('dddd, D [de] MMMM') }}
            </span>
            <strong class="activity-name">
              {{ $actividad->nombre_act }}
            </strong>
          </div>
        @empty
          <p class="text-muted">No hay actividades programadas para esta semana.</p>
        @endforelse
      </div>
    </div>
</section>

  <section class="panel">
    <div class="panel-header">
      <h2>Notificaciones 🔔</h2>
    </div>
    <div class="panel-body text-muted">—</div>
  </section>
@endsection