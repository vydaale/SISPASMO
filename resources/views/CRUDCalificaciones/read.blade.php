<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
            <img src="{{ asset('images/logoprincipal.png') }}" alt="Grupo Morelos" />
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
                    <li><a href="{{ route('recibos.index') }}">Recibos</a></li>
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
                    <li><a class="active" href="{{ route('calif.docente.index') }}">Calificaciones</a></li>
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

    <!-- CONTENIDO -->
    <main class="content">
        <div class="crud-wrap">
            <section class="crud-card">
                <header class="crud-hero">
                    <h2 class="crud-hero-title">Gestión de Calificaciones</h2>
                    <p class="crud-hero-subtitle">Mis calificaciones</p>

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
                                    <th>#</th>
                                    <th>Alumno</th>
                                    <th>Grupo</th>
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
                                        <td>{{ $c->id_calif }}</td>
                                        <td>{{ $nombre }}</td>
                                        <td>{{ $alumno->grupo ?? '—' }}</td>
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
                                            <a class="btn-ghost" href="{{ route('calif.edit', $c->id_calif) }}">Editar</a>
                                            <form action="{{ route('calif.destroy', $c->id_calif) }}" method="POST" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button class="btn-ghost" onclick="return confirm('¿Eliminar esta calificación?')">Eliminar</button>
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
    </main>
</div> <!-- /dash -->

<style>
    .truncate { max-width: 360px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; }
    .badge-score { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
</style>

</body>
</html>
