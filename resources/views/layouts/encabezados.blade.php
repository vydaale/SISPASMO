<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Panel de Administrador')</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/dashboard.css', 'resources/css/crud.css', 'resources/js/dashboard.js'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @stack('styles')
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
                    <a href="{{ route('admin.dashboard') }}">Panel</a>
                </li>
                
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
                        <li><a href="{{route('admin.index')}}">Administradores</a></li>
                        <li><a href="{{route('coordinadores.index')}}">Coordinadores</a></li>
                        <li><a href="{{ route('docentes.index') }}">Docente</a></li>
                        <li><a href="{{ route('alumnos.index') }}">Alumnos</a></li>
                        <li><a href="{{route('aspirantes.index')}}">Aspirantes</a></li>
                        </li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">ACADÉMICO</div>
                    <ul class="menu">
                        <li><a href="{{route('admin.diplomados.index')}}">Diplomados</a></li>
                        <li><a href="{{route('modulos.index')}}">Módulos (materias)</a></li>
                        <li><a href="{{route('admin.horarios.index') }}">Horarios</a></li>
                        <li><a href="{{route('extracurricular.index')}}">Talleres y prácticas</a></li>
                        <li><a href="{{route('admin.reportes')}}">Reportes</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">ADMINISTRACIÓN</div>
                    <ul class="menu">
                        <li><a href="{{route('recibos.admin.index')}}">Recibos</a></li>
                        <li><a href="{{route('fichasmedicas.index')}}">Ficha médica</a></li>
                        <li><a href="{{route('citas.index')}}">Citas</a></li>
                        <li><a href="{{ route('quejas.index') }}">Dudas/sugerencias</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">SOPORTE</div>
                    <ul class="menu">
                        <li><a href="{{ route('notificaciones.index') }}">Mis notificaciones</a></li>
                        <li><a href="{{ route('admin.backups.manual.index') }}">Respaldo de BD</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="search">
                    <label for="q">Buscar módulo:</label>
                    <input id="q" type="text" placeholder="Escribe aquí…">
                </div>
            </nav>
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.userway.org/widget.js" data-account="kvnkkEfZx0"></script>

    @stack('scripts')
</body>
</html>