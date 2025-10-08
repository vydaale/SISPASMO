<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Panel de Alumno')</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/dashboard.css', 'resources/css/crud.css', 'resources/css/sub.css', 'resources/js/dashboard.js', 'resources/css/extracurriculares.css'])
    @stack('head')
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
                    <a href="{{ route('alumno.dashboard') }}">Panel</a>
                </li>
                
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <a href="{{ route('alumno.login') }}">Cerrar sesión</a>
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
                    <div class="group-title">MI INFORMACIÓN</div>
                    <ul class="menu">
                        <li><a href="{{ route('alumno.dashboard') }}">Información personal</a></li>
                        <li><a href="{{ route('mi_ficha.show') }}">Ficha medica</a></li>
                    </ul>
                </div>


                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">MÓDULOS (MATERIAS)</div>
                    <ul class="menu">
                        <li><a href="{{ route('alumno.horario') }}">Horarios</a></li>
                        <li><a href="{{ route('calif.alumno.index') }}">Calificaciones</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">EXTRACURRICULARES</div>
                    <ul class="menu">
                        <li><a href="{{ route('extracurriculares.disponibles') }}">Actividades Extracurriculares</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">TRAMITES</div>
                    <ul class="menu">
                        <li><a href="{{ route('recibos.create') }}">Recibos de pago</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">SUGERENCIAS</div>
                    <ul class="menu">
                        <li><a href="{{ route('quejas.create') }}">Nueva queja/sugerencia</a></li>
                        <li><a href="{{ route('quejas.propias') }}">Mis quejas/sugerencias</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">NOTIFICACIONES</div>
                    <ul class="menu">
                        <li><a href="{{ route('notificaciones.index') }}">Mis notificaciones</a></li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="search">
                        <label for="q">Buscar módulo:</label>
                        <input id="q" type="text" placeholder="Escribe aquí…">
                    </div>
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