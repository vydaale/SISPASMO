@extends('layouts.encabezadosDoc')

@section('title', 'Gestión Calificaciones')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de Calificaciones</h2>
                <p class="crud-hero-subtitle">Calificaciones</p>

                <nav class="crud-tabs">
                    <a href="{{ route('calif.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('calif.docente.index') }}" class="tab active">Listado</a>
                </nav>
            </header>

            <div class="crud-body">
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                {{-- Filtros --}}
                <form method="GET" class="gm-filter" style="margin-bottom: 14px;">
                    <div class="grid-3">
                        <div>
                            <label for="f_id_alumno">Alumno</label>
                            <select id="f_id_alumno" name="id_alumno">
                                <option value="">-- Todos --</option>
                                @foreach($misAlumnos as $a)
                                    @php
                                        $nombre = optional($a->usuario)->nombre.' '.optional($a->usuario)->apellidoP.' '.optional($a->usuario)->apellidoM;
                                        $nombre = trim($nombre) ?: ('Alumno #'.$a->id_alumno);
                                    @endphp
                                    <option value="{{ $a->id_alumno }}" {{ request('id_alumno')==$a->id_alumno?'selected':'' }}>
                                        {{ $nombre }} — Grupo: {{ $a->grupo }} — Dipl.: {{ $a->num_diplomado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="f_id_modulo">Módulo</label>
                            <select id="f_id_modulo" name="id_modulo">
                                <option value="">-- Todos --</option>
                                @foreach($modulos as $m)
                                    <option value="{{ $m->id_modulo }}" {{ request('id_modulo')==$m->id_modulo?'selected':'' }}>
                                        Mód. {{ $m->numero_modulo }} — {{ $m->nombre_modulo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="f_tipo">Tipo</label>
                            <input id="f_tipo" type="text" name="tipo" value="{{ request('tipo') }}" placeholder="Parcial 1, Final, Taller…">
                        </div>
                    </div>

                    <div style="margin-top:10px">
                        <button class="btn">Filtrar</button>
                        @if(request()->hasAny(['id_alumno','id_modulo','tipo']))
                            <a class="btn-ghost" href="{{ route('calif.docente.index') }}">Limpiar</a>
                        @endif
                    </div>
                </form>

                {{-- Tabla --}}
                <div class="table-responsive">
                    <table class="gm-table">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Diplomado</th>
                                <th>Módulo</th>
                                <th>Tipo</th>
                                <th>Calificación</th>
                                <th>Observación</th>
                                <th style="width:160px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($califs as $c)
                                @php
                                    $alumno = $c->alumno;
                                    $usr = optional($alumno)->usuario;
                                    $nombre = trim(($usr->nombre ?? '').' '.($usr->apellidoP ?? '').' '.($usr->apellidoM ?? ''));
                                    $nombre = $nombre ?: ('Alumno #'.($alumno->id_alumno ?? '—'));
                                    $mod = $c->modulo;
                                @endphp
                                <tr>
                                    <td>{{ $nombre }}</td>
                                    <td>{{ $alumno->diplomado->nombre ?? '—' }}</td>
                                    <td>
                                        @if($mod)
                                            Mód. {{ $mod->numero_modulo }} — {{ $mod->nombre_modulo }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $c->tipo }}</td>
                                    <td>
                                        <span class="badge badge-score">{{ number_format($c->calificacion, 2) }}</span>
                                    </td>
                                    <td class="truncate" title="{{ $c->observacion }}">{{ \Illuminate\Support\Str::limit($c->observacion, 60) }}</td>
                                    <td class="actions">
                                        <a class="btn btn-ghost" href="{{ route('calif.edit', $c->id_calif) }}">Editar</a>
                                        <br></br>
                                        <form action="{{ route('calif.destroy', $c->id_calif) }}" method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('¿Eliminar esta calificación?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No hay calificaciones registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrap">
                    {{ $califs->links() }}
                </div>
            </div>
        </section>
    </div>

    <style>
        .truncate { max-width: 360px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; }
        .badge-score { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
    </style>
@endsection