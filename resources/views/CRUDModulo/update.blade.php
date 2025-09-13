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
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar
                                sesión</a>
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
                                <li><a href="{{ route('aspirantes.create') }}">Nuevo aspirante</a></li>
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
                        <li><a href="#">Recibos</a></li>
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

        <!-- Contenido -->
        <main class="content">
            <div class="crud-wrap">
                <section class="crud-card">
                    <header class="crud-hero">
                        <h2 class="crud-hero-title">Gestión de módulos</h2>
                        <p class="crud-hero-subtitle">Actualización</p>

                        <nav class="crud-tabs">
                            <a href="{{ route('modulos.create') }}" class="tab">Registrar</a>
                            <a href="{{ route('modulos.index') }}" class="tab active">Listar módulos</a>
                        </nav>
                    </header>

                    <div class="crud-body">
                        <h1>Actualizar Módulo</h1>

                        @if ($errors->any())
                            <ul class="gm-errors">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        @endif

                        @if (session('ok'))
                            <div class="gm-ok">{{ session('ok') }}</div>
                        @endif

                        <form class="gm-form" method="POST" action="{{ route('modulos.update', $modulo) }}">
                            @csrf
                            @method('PUT')

                            <h3>Datos del módulo</h3>
                            <div>
                                <input type="number" name="numero_modulo"
                                    value="{{ old('numero_modulo', $modulo->numero_modulo) }}"
                                    placeholder="Número de módulo" required>
                                <input name="nombre_modulo" value="{{ old('nombre_modulo', $modulo->nombre_modulo) }}"
                                    placeholder="Nombre del módulo" required>

                                <input name="duracion" value="{{ old('duracion', $modulo->duracion) }}"
                                    placeholder="Duración (ej. 40 horas)" required>

                                @php $est = old('estatus', $modulo->estatus); @endphp
                                <select name="estatus" required>
                                    <option value="activa" {{ $est === 'activa' ? 'selected' : '' }}>activa</option>
                                    <option value="concluida" {{ $est === 'concluida' ? 'selected' : '' }}>concluida
                                    </option>
                                </select>

                                <input name="url" value="{{ old('url', $modulo->url) }}"
                                    placeholder="URL (opcional)">
                            </div>

                            <div>
                                <textarea name="descripcion" rows="4" placeholder="Descripción" style="grid-column:1 / -1;">{{ old('descripcion', $modulo->descripcion) }}</textarea>
                            </div>

                            <div class="actions">
                                <a href="{{ route('modulos.index') }}" class="btn-ghost">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>

</html>
