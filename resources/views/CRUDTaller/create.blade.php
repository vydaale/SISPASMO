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


    <!-- Contenido -->
    <main class="content">
      <div class="crud-wrap">
        <section class="crud-card">
          <header class="crud-hero">
            <h2 class="crud-hero-title">Gestión de extra curriculares</h2>
            <p class="crud-hero-subtitle">Registro</p>

            <nav class="crud-tabs">
              <a href="{{ route('extracurricular.create') }}" class="tab active">Registrar</a>
              <a href="{{ route('extracurricular.index') }}" class="tab">Listar actividades</a>
            </nav>
          </header>

          <div class="crud-body">
            <h1>Nueva actividad</h1>

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

            <form class="gm-form" method="POST" action="{{ route('extracurricular.store') }}">
              @csrf

              <h3>Datos de la actividad</h3>
              <div>
                <input name="nombre_act" value="{{ old('nombre_act') }}" placeholder="Nombre de la actividad" maxlength="50" required>
                <input name="responsable" value="{{ old('responsable') }}" placeholder="Responsable" maxlength="100" required>

                <input type="date" name="fecha" value="{{ old('fecha') }}" required>

                <select name="tipo" required>
                  <option value="">Tipo</option>
                  <option value="Taller"    {{ old('tipo')==='Taller' ? 'selected' : '' }}>Taller</option>
                  <option value="Practica"  {{ old('tipo')==='Practica' ? 'selected' : '' }}>Práctica</option>
                </select>

                <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" required>
                <input type="time" name="hora_fin"    value="{{ old('hora_fin') }}" required>

                <input name="lugar" value="{{ old('lugar') }}" placeholder="Lugar" maxlength="100" required>

                <select name="modalidad" required>
                  <option value="">Modalidad</option>
                  <option value="Presencial" {{ old('modalidad')==='Presencial' ? 'selected' : '' }}>Presencial</option>
                  <option value="Virtual"    {{ old('modalidad')==='Virtual' ? 'selected' : '' }}>Virtual</option>
                </select>

                <select name="estatus" required>
                  <option value="">Estatus</option>
                  <option value="Finalizada"    {{ old('estatus')==='Finalizada' ? 'selected' : '' }}>Finalizada</option>
                  <option value="Convocatoria"  {{ old('estatus')==='Convocatoria' ? 'selected' : '' }}>Convocatoria</option>
                  <option value="En proceso"    {{ old('estatus')==='En proceso' ? 'selected' : '' }}>En proceso</option>
                </select>

                <input type="number" min="1" name="capacidad" value="{{ old('capacidad') }}" placeholder="Capacidad" required>

                <input name="material" value="{{ old('material') }}" placeholder="Material requerido" maxlength="150" required>

                <input name="url" value="{{ old('url') }}" placeholder="URL (opcional)" maxlength="200">
              </div>

              <div>
                <textarea name="descripcion" rows="3" placeholder="Descripción (máx. 200)" maxlength="200" style="grid-column:1 / -1;">{{ old('descripcion') }}</textarea>
              </div>

              <div class="actions">
                <a href="{{ route('extracurricular.index') }}" class="btn-ghost">Cancelar</a>
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
