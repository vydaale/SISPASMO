<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cambiar estatus</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/dashboard.css', 'resources/css/sub.css', 'resources/css/crud.css', 'resources/js/dashboard.js'])
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

        <main class="content">
            <div class="crud-wrap">
                <div class="crud-card">
                    <div class="crud-hero">
                        <h1 class="crud-hero-title">Estatus de #{{ $queja->id_queja }}</h1>
                    </div>

                    <div class="crud-body">
                        @if ($errors->any())
                        <div class="gm-errors">
                            <ul>
                                @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form class="gm-form" method="POST" action="{{ route('quejas.update', $queja) }}">
                            @csrf @method('PUT')

                            <div>
                                <div style="grid-column:1 / -1">
                                    <label><strong>Mensaje</strong></label>
                                    <div class="gm-empty">{{ $queja->mensaje }}</div>
                                </div>

                                <div>
                                    <label for="Estatus"><strong>Estatus</strong></label>
                                    <select id="estatus" name="estatus" required>
                                        <option value="Pendiente" @selected($queja->estatus === 'Pendiente')>Pendiente</option>
                                        <option value="Atendido"  @selected($queja->estatus === 'Atendido')>Atendido</option>
                                    </select>
                                </div>


                                <div>
                                    <label for="contacto"><strong>Contacto (opcional)</strong></label>
                                    <input id="contacto" name="contacto" type="text" value="{{ old('contacto', $queja->contacto) }}">
                                </div>
                            </div>

                            <div class="actions">
                                <a class="btn-ghost" href="{{ route('quejas.index') }}">Cancelar</a>
                                <button class="btn btn-primary" type="submit">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>