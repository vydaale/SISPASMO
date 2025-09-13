{{-- resources/views/CRUDModulos/create.blade.php --}}
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
            <h2 class="crud-hero-title">Gestión de módulos</h2>
            <p class="crud-hero-subtitle">Registro</p>

            <nav class="crud-tabs">
              <a href="{{ route('modulos.create') }}" class="tab active">Registrar</a>
              <a href="{{ route('modulos.index') }}" class="tab">Listar módulos</a>
            </nav>
          </header>

          <div class="crud-body">
            <h1>Nuevo Módulo</h1>

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

            <form class="gm-form" method="POST" action="{{ route('modulos.store') }}">
              @csrf

              <h3>Datos del módulo</h3>
              <div>
                {{-- numero_modulo (int) --}}
                <input type="number" name="numero_modulo" value="{{ old('numero_modulo') }}" placeholder="Número de módulo" required>

                {{-- nombre_modulo (varchar 100) --}}
                <input name="nombre_modulo" value="{{ old('nombre_modulo') }}" placeholder="Nombre del módulo" maxlength="100" required>

                {{-- duracion (varchar 50) --}}
                <input name="duracion" value="{{ old('duracion') }}" placeholder="Duración (p. ej. 40 horas / 12 semanas)" maxlength="50" required>

                {{-- estatus (enum) --}}
                @php $estatusSel = old('estatus'); @endphp
                <select name="estatus" required>
                  <option value="">Estatus</option>
                  <option value="activa"    {{ $estatusSel === 'activa' ? 'selected' : '' }}>activa</option>
                  <option value="concluida" {{ $estatusSel === 'concluida' ? 'selected' : '' }}>concluida</option>
                </select>

                {{-- url (varchar 200) --}}
                <input type="url" name="url" value="{{ old('url') }}" placeholder="URL del módulo (opcional)" maxlength="200">
              </div>

              {{-- descripcion (text) ocupa todo el ancho --}}
              <div>
                <textarea name="descripcion" rows="4" placeholder="Descripción del módulo" required>{{ old('descripcion') }}</textarea>
              </div>

              <div class="actions">
                <a href="{{ route('modulos.index') }}" class="btn-ghost">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar</button>
              </div>
            </form>
          </div>
        </section>
      </div>
    </main>
  </div> <!-- /dash -->

</body>
</html>
