@extends('layouts.encabezadosAl')

@section('title', 'Calificaciones')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Mis calificaciones</h2>
                <p class="crud-hero-subtitle">Consulta</p>

                <nav class="crud-tabs">
                    <a href="{{ route('calif.alumno.index') }}" class="tab active">Listado</a>
                </nav>
            </header>

            <div class="crud-body">
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                {{-- Filtros --}}
                <form method="GET" class="gm-filter" style="margin-bottom:14px;">
                    <div class="grid-3">
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
                        <div>
                            <label>&nbsp;</label>
                            <div>
                                <button class="btn">Filtrar</button>
                                @if(request()->hasAny(['id_modulo','tipo']))
                                    <a class="btn-ghost" href="{{ route('calif.alumno.index') }}">Limpiar</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Tabla --}}
                <div class="table-responsive">
                    <table class="gm-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Módulo</th>
                                <th>Tipo</th>
                                <th>Calificación</th>
                                <th>Observación</th>
                                <th>Docente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($califs as $c)
                                @php
                                    $mod = $c->modulo;
                                    $docUsr = optional($c->docente)->usuario;
                                    $docenteNombre = trim(($docUsr->nombre ?? '').' '.($docUsr->apellidoP ?? '').' '.($docUsr->apellidoM ?? '')) ?: '—';
                                @endphp
                                <tr>
                                    <td>{{ $c->id_calif }}</td>
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
                                    <td class="truncate" title="{{ $c->observacion }}">{{ \Illuminate\Support\Str::limit($c->observacion, 80) }}</td>
                                    <td>{{ $docenteNombre }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">No hay calificaciones registradas.</td>
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
        .truncate { max-width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; }
        .badge-score { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
    </style>
@endsection