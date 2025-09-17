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
                data-baja="{{ $alumnosBaja ?? 0 }}">
            </canvas>
        </div>
    </section>

    <section class="two-col">
        <div class="panel">
            <div class="panel-header"><h2>Calendario semanal</h2></div>
            <div class="panel-body">
                <table class="mini-calendar">
                    <thead><tr><th>S</th><th>M</th><th>T</th><th>W</th><th>T</th><th>F</th><th>S</th></tr></thead>
                    <tbody>
                        @for($r=0;$r<5;$r++)
                        <tr>@for($c=0;$c<7;$c++)<td>&nbsp;</td>@endfor</tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header"><h2>Actividades semanales</h2></div>
            <div class="panel-body">
                <input class="ghost-input" placeholder="" />
                <input class="ghost-input" placeholder="" />
                <input class="ghost-input" placeholder="" />
                <input class="ghost-input" placeholder="" />
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Notificaciones 🔔</h2></div>
        <div class="panel-body text-muted">—</div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection