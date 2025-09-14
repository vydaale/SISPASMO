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
                        <li><a class="active" href="{{ route('recibos.index') }}">Recibos</a></li>
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

        <!-- CONTENIDO -->
        <main class="content">
            <div class="crud-wrap">
                <section class="crud-card">
                    <header class="crud-hero">
                        <h2 class="crud-hero-title">Gestión de Recibos</h2>
                        <p class="crud-hero-subtitle">Listado</p>

                        <nav class="crud-tabs">
                            <a href="{{ route('recibos.create') }}" class="tab">Registrar</a>
                            <a href="{{ route('recibos.index') }}" class="tab active">Listar recibos</a>
                        </nav>
                    </header>

                    <div class="crud-body">
                        @if (session('ok'))
                            <div class="gm-ok">{{ session('ok') }}</div>
                        @endif

                        {{-- Filtro simple opcional (por concepto/estatus) --}}
                        <form method="GET" class="gm-filter" style="margin-bottom: 14px;">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por concepto…">
                            <select name="estatus">
                                @php $e = request('estatus'); @endphp
                                <option value="">-- Estatus --</option>
                                <option value="pendiente" {{ $e==='pendiente'?'selected':'' }}>pendiente</option>
                                <option value="validado"  {{ $e==='validado'?'selected':'' }}>validado</option>
                                <option value="rechazado" {{ $e==='rechazado'?'selected':'' }}>rechazado</option>
                            </select>
                            <button class="btn">Filtrar</button>
                            @if(request()->hasAny(['q','estatus']))
                                <a class="btn-ghost" href="{{ route('recibos.index') }}">Limpiar</a>
                            @endif
                        </form>

                        <div class="table-responsive">
                            <table class="gm-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha pago</th>
                                        <th>Concepto</th>
                                        <th>Monto</th>
                                        <th>Estatus</th>
                                        <th>Comprobante</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recibos as $r)
                                        <tr>
                                            <td>{{ $r->id_recibo }}</td>
                                            <td>{{ optional($r->fecha_pago)->format('Y-m-d') }}</td>
                                            <td>{{ $r->concepto }}</td>
                                            <td>${{ number_format($r->monto, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $r->estatus }}">
                                                    {{ ucfirst($r->estatus) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($r->comprobante_path)
                                                    <a class="btn-ghost" target="_blank" href="{{ Storage::disk('public')->url($r->comprobante_path) }}">Ver</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="actions">
                                                <a class="btn-ghost" href="{{ route('recibos.show', $r->id_recibo) }}">Ver</a>
                                                {{-- Alumno NO edita ni elimina --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">No hay recibos registrados.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination-wrap">
                            {{ $recibos->links() }}
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div> <!-- /dash -->

    <style>
        /* Badges rápidas (opcional) */
        .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; }
        .badge-pendiente { background: #fff7ed; color:#9a3412; border:1px solid #fed7aa; }
        .badge-validado  { background: #ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
        .badge-rechazado { background: #fef2f2; color:#991b1b; border:1px solid #fecaca; }
    </style>
</body>
</html>
