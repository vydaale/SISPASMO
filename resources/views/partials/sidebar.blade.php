<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel administrador</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/dashboard.css')
    @vite(['resources/css/sub.css', 'resources/js/dashboard.js'])
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
                                <li><a href="{{route('aspirantes.index')}}">Listar aspirantes</a></li>
                                <li><a href="">Nuevo administrativo</a></li>
                                <li><a href="">Listar administrativos</a></li>
                                <li><a href="{{route('coordinadores.create')}}">Nuevo coordinador</a></li>
                                <li><a href="{{route('coordinadores.index')}}">Listar coordinadores</a></li>
                                <li><a href="{{route('admin.create')}}">Nuevo administrador</a></li>
                                <li><a href="{{ route('admin.index') }}">Listar administradores</a></li>

                            </ul>
                        </li>
                    </ul>

                    
                </div>
                <div class="divider"></div>

                <div class="group">
                    <div class="group-title">Funcionalidades</div>
                    <ul class="menu">
                        <li><a href="{{route('recibos.admin.index')}}">Recibos</a></li>
                        <li><a href="#">Horarios</a></li>
                        <li><a href="{{route('fichasmedicas.index')}}">Fichas médica</a></li>
                    </ul>
                </div>

                <div class="divider"></div>

                <div class="group">
                    <ul class="menu">
                        <li><a href="{{route('modulos.index')}}">Módulos</a></li>
                        <li><a href="{{route('extracurricular.index')}}">Talleres y prácticas</a></li>
                        <li><a href="{{ route('quejas.index') }}">Queja/sugerencia</a></li>
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