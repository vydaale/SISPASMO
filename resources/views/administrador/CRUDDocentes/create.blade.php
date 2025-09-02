<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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
        <img src="{{ asset('images/logoprincipal.png') }}" alt="Grupo Morelos"/>
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

    <!-- Contenido -->
    <main class="content">
      <div class="crud-wrap">
        <section class="crud-card">
          <header class="crud-hero">
            <h2 class="crud-hero-title">Gestión de docentes</h2>
            <p class="crud-hero-subtitle">Registro</p>

            <nav class="crud-tabs">
              <a href="{{ route('docentes.create') }}" class="tab active">Registrar</a>
              <a href="{{ route('docentes.index') }}" class="tab">Listar docentes</a>
            </nav>
          </header>

          <div class="crud-body">
            <h1>Nuevo Docente</h1>

            @if ($errors->any())
              <ul class="gm-errors">
                @foreach ($errors->all() as $e)
                  <li>{{ $e }}</li>
                @endforeach
              </ul>
            @endif

            @if (session('success'))
              <div class="gm-ok">{{ session('success') }}</div>
            @endif

            <form class="gm-form" method="POST" action="{{ route('docentes.store') }}">
              @csrf

              <h3>Datos de Usuario</h3>
              <div>
                <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" required>
                <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" required>
                <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" required>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>

                <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" required>

                <input type="password" name="pass" placeholder="Contraseña" required>
                <input type="password" name="pass_confirmation" placeholder="Confirmar contraseña" required>

                <select name="genero" required>
                  <option value="">Género</option>
                  <option value="M" {{ old('genero')==='M' ? 'selected' : '' }}>M</option>
                  <option value="F" {{ old('genero')==='F' ? 'selected' : '' }}>F</option>
                  <option value="Otro" {{ old('genero')==='Otro' ? 'selected' : '' }}>Otro</option>
                </select>

                <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" required>
                <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" required>
                <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" required>

                <input type="number" name="id_rol" value="{{ old('id_rol') }}" placeholder="ID Rol (docente)" required>
              </div>

              <h3>Datos de Docente</h3>
              <div>
                <input name="matriculaD" value="{{ old('matriculaD') }}" placeholder="Matrícula docente" required>
                <input name="especialidad" value="{{ old('especialidad') }}" placeholder="Especialidad" required>
                <input name="cedula" value="{{ old('cedula') }}" placeholder="Cédula profesional" required>
                <input type="number" name="salario" value="{{ old('salario') }}" placeholder="Salario" step="0.01" min="0" required>
              </div>

              <div class="actions">
                <a href="{{ route('docentes.index') }}" class="btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </main>
  </div>

</body>
</html>