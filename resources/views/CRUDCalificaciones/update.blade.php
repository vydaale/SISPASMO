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
                    <p class="crud-hero-subtitle">Editar</p>

                    <nav class="crud-tabs">
                        <a href="{{ route('calif.create') }}" class="tab">Registrar</a>
                        <a href="{{ route('calif.docente.index') }}" class="tab">Listado</a>
                        <a href="#" class="tab active" onclick="return false;">Editar</a>
                    </nav>
                </header>

                <div class="crud-body">
                    <h1>Editar calificación #{{ $calif->id_calif }}</h1>

                    @if (session('ok'))
                        <div class="gm-ok">{{ session('ok') }}</div>
                    @endif

                    @if ($errors->any())
                        <ul class="gm-errors">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <form class="gm-form" method="POST" action="{{ route('calif.update', $calif->id_calif) }}">
                        @csrf
                        @method('PUT')

                        {{-- =========================
                             Datos de la calificación
                        ========================== --}}
                        <h3>Datos</h3>

                        <div class="grid-2">
                            <div>
                                <label for="id_alumno">Alumno</label>
                                <select id="id_alumno" name="id_alumno" required>
                                    <option value="">-- Selecciona un alumno --</option>
                                    @foreach($alumnos as $a)
                                        @php
                                            $nombre = optional($a->usuario)->nombre.' '.optional($a->usuario)->apellidoP.' '.optional($a->usuario)->apellidoM;
                                            $nombre = trim($nombre) ?: ('Alumno #'.$a->id_alumno);
                                            $sel = old('id_alumno', $calif->id_alumno) == $a->id_alumno ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $a->id_alumno }}" {{ $sel }}>
                                            {{ $nombre }} — Grupo: {{ $a->grupo }} — Diplomado: {{ $a->num_diplomado }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_alumno') <small class="gm-error">{{ $message }}</small> @enderror
                            </div>

                            <div>
                                <label for="id_modulo">Módulo</label>
                                <select id="id_modulo" name="id_modulo" required>
                                    <option value="">-- Selecciona un módulo --</option>
                                    @foreach($modulos as $m)
                                        @php
                                            $sel = old('id_modulo', $calif->id_modulo) == $m->id_modulo ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $m->id_modulo }}" {{ $sel }}>
                                            Mód. {{ $m->numero_modulo }} — {{ $m->nombre_modulo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_modulo') <small class="gm-error">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="grid-2">
                            <div>
                                <label for="tipo">Tipo</label>
                                <input list="tipos" id="tipo" name="tipo"
                                       value="{{ old('tipo', $calif->tipo) }}"
                                       placeholder="Parcial 1, Parcial 2, Final, Práctica…" required>
                                <datalist id="tipos">
                                    <option value="Parcial 1"></option>
                                    <option value="Parcial 2"></option>
                                    <option value="Final"></option>
                                    <option value="Práctica"></option>
                                    <option value="Taller"></option>
                                </datalist>
                                @error('tipo') <small class="gm-error">{{ $message }}</small> @enderror
                            </div>

                            <div>
                                <label for="calificacion">Calificación</label>
                                <input type="number" step="0.01" min="0" max="100" id="calificacion" name="calificacion"
                                       value="{{ old('calificacion', number_format($calif->calificacion, 2, '.', '')) }}"
                                       placeholder="0 - 100" required>
                                @error('calificacion') <small class="gm-error">{{ $message }}</small> @enderror
                                <small class="gm-help">Rango: 0 a 100. Usa punto decimal (ej. 89.50).</small>
                            </div>
                        </div>

                        <div>
                            <label for="observacion">Observación (opcional)</label>
                            <textarea id="observacion" name="observacion" rows="3"
                                      placeholder="Comentarios, evidencias, correcciones…">{{ old('observacion', $calif->observacion) }}</textarea>
                            @error('observacion') <small class="gm-error">{{ $message }}</small> @enderror
                        </div>

                        <div class="actions">
                            <a href="{{ route('calif.docente.index') }}" class="btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                            </form>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>
</div> <!-- /dash -->

<script>
    // Validación suave en cliente para 0-100
    const inputCalif = document.getElementById('calificacion');
    inputCalif?.addEventListener('input', () => {
        const v = parseFloat(inputCalif.value);
        if (isNaN(v)) return;
        if (v < 0) inputCalif.value = 0;
        if (v > 100) inputCalif.value = 100;
    });
</script>

<style>
    .badge { display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; }
    .badge-score { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
</style>

</body>
</html>
