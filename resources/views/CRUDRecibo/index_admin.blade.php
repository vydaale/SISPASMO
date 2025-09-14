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
                    {{ auth()->user()->nombre ?? 'Usuario' }} {{ auth()->user()->apellidoP ?? '' }}
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
                    <li><a class="active" href="{{ route('recibos.admin.index') }}">Recibos</a></li>
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
                    <p class="crud-hero-subtitle">Listado (administración)</p>

                    <nav class="crud-tabs">
                        <a href="{{ route('recibos.create') }}" class="tab">Registrar</a>
                        <a href="{{ route('recibos.admin.index') }}" class="tab active">Listar recibos</a>
                    </nav>
                </header>

                <div class="crud-body">
                    @if (session('ok'))
                        <div class="gm-ok">{{ session('ok') }}</div>
                    @endif

                    {{-- Filtros --}}
                    <form method="GET" class="gm-filter" style="margin-bottom: 14px;">
                        <div class="grid-3">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por concepto o alumno…">
                            @php $e = request('estatus'); @endphp
                            <select name="estatus">
                                <option value="">-- Estatus --</option>
                                <option value="pendiente" {{ $e==='pendiente'?'selected':'' }}>pendiente</option>
                                <option value="validado"  {{ $e==='validado'?'selected':'' }}>validado</option>
                                <option value="rechazado" {{ $e==='rechazado'?'selected':'' }}>rechazado</option>
                            </select>
                            <div class="grid-2" style="gap:8px">
                                <input type="date" name="f1" value="{{ request('f1') }}" placeholder="Desde">
                                <input type="date" name="f2" value="{{ request('f2') }}" placeholder="Hasta">
                            </div>
                        </div>
                        <div style="margin-top:8px">
                            <button class="btn">Filtrar</button>
                            @if(request()->hasAny(['q','estatus','f1','f2']))
                                <a class="btn-ghost" href="{{ route('recibos.admin.index') }}">Limpiar</a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Matricula</th>
                                <th>Fecha pago</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Estatus</th>
                                <th>Validado por</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recibos as $r)
                                <tr>
                                    <td>{{ $r->id_recibo }}</td>
                                    <td>{{ $r->alumno->matricula ?? '—' }}</td>
                                    <td>{{ optional($r->fecha_pago)->format('Y-m-d') }}</td>
                                    <td>{{ $r->concepto }}</td>
                                    <td>${{ number_format($r->monto, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $r->estatus }}">{{ ucfirst($r->estatus) }}</span>
                                    </td>
                                    <td>
                                        @if($r->validado_por)
                                            {{ $r->validador->nombre ?? '—' }}
                                            <small class="muted" style="display:block">
                                                {{ optional($r->fecha_validacion)->format('Y-m-d H:i') }}
                                            </small>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($r->comprobante_path)
                                            <a class="btn-ghost" target="_blank" href="{{ Storage::disk('public')->url($r->comprobante_path) }}">Ver</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="actions">

                                        <form action="{{ route('recibos.destroy', $r->id_recibo) }}" method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost" onclick="return confirm('¿Eliminar recibo #{{ $r->id_recibo }}?')">Eliminar</button>
                                        </form>

                                        {{-- Validar/Rechazar (inline) --}}
                                        <button class="btn-ghost" data-open="#v{{ $r->id_recibo }}">Validar</button>

                                        {{-- Dialogo simple --}}
                                        <div id="v{{ $r->id_recibo }}" class="gm-modal" style="display:none">
                                            <div class="gm-modal-card">
                                                <h3>Validar recibo #{{ $r->id_recibo }}</h3>
                                                <form method="POST" action="{{ route('recibos.validar', $r->id_recibo) }}">
                                                    @csrf
                                                    <div class="grid-2">
                                                        <div>
                                                            <label>Estatus</label>
                                                            <select name="estatus" required>
                                                                <option value="validado"  {{ $r->estatus==='validado'?'selected':'' }}>validado</option>
                                                                <option value="rechazado" {{ $r->estatus==='rechazado'?'selected':'' }}>rechazado</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label>Comentarios</label>
                                                            <input name="comentarios" value="{{ old('comentarios', $r->comentarios) }}" placeholder="Notas…">
                                                        </div>
                                                    </div>
                                                    <div class="actions" style="margin-top:12px">
                                                        <button type="button" class="btn-ghost" data-close="#v{{ $r->id_recibo }}">Cancelar</button>
                                                        <button class="btn btn-primary">Guardar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9">No hay recibos.</td></tr>
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
    .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; }
    .badge-pendiente { background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
    .badge-validado  { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
    .badge-rechazado { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }

    /* Modal simple */
    .gm-modal { position:fixed; inset:0; background:rgba(0,0,0,.35); display:flex; align-items:center; justify-content:center; padding:16px; z-index:50; }
    .gm-modal-card { background:#fff; border-radius:16px; padding:18px; width:min(560px, 100%); box-shadow: 0 10px 30px rgba(0,0,0,.12); }
</style>

<script>
    // abrir/cerrar modal de validación
    document.querySelectorAll('[data-open]').forEach(btn => {
        btn.addEventListener('click', () => {
            const sel = btn.getAttribute('data-open');
            const el = document.querySelector(sel);
            if (el) el.style.display = 'flex';
        });
    });
    document.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const sel = btn.getAttribute('data-close');
            const el = document.querySelector(sel);
            if (el) el.style.display = 'none';
        });
    });
</script>

</body>
</html>
