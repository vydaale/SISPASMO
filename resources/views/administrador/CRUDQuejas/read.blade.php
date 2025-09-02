<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestionar quejas/sugerencias</title>

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
                        <li><a href="{{ route('quejas.index') }}">Dudas y sugerencias</a></li>
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
                        <h1 class="crud-hero-title">Quejas y sugerencias</h1>

                        <form method="GET" class="crud-tabs" style="margin-top:10px">
                            <select name="tipo" style="border-radius:999px;padding:10px 14px;border:1px solid rgba(0,0,0,.12)">
                                <option value="">Tipo</option>
                                <option value="queja" @selected(request('tipo')==='queja')>Queja</option>
                                <option value="sugerencia" @selected(request('tipo')==='sugerencia')>Sugerencia</option>
                            </select>
                            <button class="btn btn-primary" type="submit">Filtrar</button>
                        </form>
                    </div>

                    <div class="crud-body">
                        @if (session('success'))
                        <div class="gm-ok">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="gm-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Tipo</th>
                                        <th>Mensaje</th>
                                        <th>Contacto</th>
                                        <th>Estatus</th>
                                        <th class="th-actions">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($quejas as $q)
                                    <tr>
                                        <td>#{{ $q->id_queja }}</td>
                                        <td>
                                            @if($q->usuario)
                                            {{ $q->usuario->nombre }} {{ $q->usuario->apellidoP }} ({{ $q->usuario->usuario }})
                                            @else
                                            —
                                            @endif
                                        </td>
                                        <td style="text-transform:capitalize">{{ $q->tipo }}</td>
                                        <td>{{ Str::limit($q->mensaje, 80) }}</td>
                                        <td>{{ $q->contacto ?: '—' }}</td>
                                        <td>
                                            @if($q->estatus === 'Atendido')
                                            <span style="color:#065f46;font-weight:800">Atendido</span>
                                            @else
                                            <span style="color:#b45309;font-weight:800">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="table-actions">
                                            <a class="btn-ghost" href="{{ route('quejas.edit', $q) }}">Estatus</a>
                                            <form action="{{ route('quejas.destroy', $q) }}" method="POST" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button class="btn-danger" onclick="return confirm('¿Eliminar #{{ $q->id_queja }}?')">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">Sin resultados</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="pager">{{ $quejas->links() }}</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>