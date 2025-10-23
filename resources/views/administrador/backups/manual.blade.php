@extends('layouts.encabezados')
@section('title', 'Gestión alumnos')

@section('content')
  <div class="crud-wrap">
    <section class="crud-card">
      <header class="crud-hero">
        <h2 class="crud-hero-title">Gestión de alumnos</h2>
        <p class="crud-hero-subtitle">Registro</p>
        <nav class="crud-tabs">
          <a href="{{ route('alumnos.create') }}" class="tab active">Registrar</a>
          <a href="{{ route('alumnos.index') }}" class="tab">Listar alumnos</a>
        </nav>
      </header>

      <div class="crud-body">
        <h1>Nuevo Alumno</h1>

        {{-- Contenedor para mostrar los errores de validación de Laravel (si los hay). --}}
        @if ($errors->any())
          <ul class="gm-errors">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        @endif

        {{-- Formulario principal, envía los datos a la ruta 'alumnos.store' para la creación. --}}
        <form class="gm-form" method="POST" action="{{ route('alumnos.store') }}">
          @csrf
          <h3>Datos de Usuario</h3>
          
          <div class="form-section">
            {{-- Campos de datos personales --}}
            <div>
              <label for="nombre">Nombre(s)</label>
              <input id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre(s)" required>
            </div>
            <div>
              <label for="apellidoP">Apellido paterno</label>
              <input id="apellidoP" name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" required>
            </div>
            <div>
              <label for="apellidoM">Apellido materno</label>
              <input id="apellidoM" name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" required>
            </div>
            <div>
              <label for="fecha_nac">Fecha de nacimiento</label>
              <input id="fecha_nac" type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
            </div>

            {{-- Campos de credenciales (Usuario y Contraseña) --}}
            <div>
              <label for="usuario">Usuario</label>
              <input id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" required>
            </div>
            <div>
              <label for="pass">Contraseña</label>
              <input id="pass" type="password" name="pass" placeholder="Contraseña" required>
            </div>
            <div>
              <label for="pass_confirmation">Confirmar contraseña</label>
              <input id="pass_confirmation" type="password" name="pass_confirmation" placeholder="Confirmar contraseña" required>
            </div>

            {{-- Campos de contacto y género --}}
            <div>
              <label for="genero">Género</label>
              <select id="genero" name="genero" required>
                <option value="">Selecciona un género</option>
                <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>M</option>
                <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>F</option>
                <option value="Otro" {{ old('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
              </select>
            </div>
            <div>
              <label for="correo">Correo electrónico</label>
              <input id="correo" type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" required>
            </div>
            <div>
              <label for="telefono">Teléfono</label>
              <input id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" required>
            </div>
            <div>
              <label for="direccion">Dirección</label>
              <input id="direccion" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" required>
            </div>

            {{-- Campo oculto para definir el rol de usuario (Rol ID: 4 = Alumno) --}}
            <input type="hidden" name="id_rol" value="4" required>
          </div>

          <h3>Datos de alumno</h3>

          <div class="form-section">
            {{-- Campos específicos del modelo Alumno --}}
            <div>
              <label for="matriculaA">Matrícula</label>
              <input id="matriculaA" name="matriculaA" value="{{ old('matriculaA') }}" placeholder="Matrícula" required>
            </div>
            
            {{-- Campo de selección de Diplomado (cargado desde el controlador) --}}
            <div>
              <label for="id_diplomado">Diplomado</label>
              <select name="id_diplomado" id="id_diplomado" required>
                  <option value="">Selecciona un diplomado</option>
                  @foreach($diplomados as $diplomado)
                      <option value="{{ $diplomado->id_diplomado }}" {{ old('id_diplomado') == $diplomado->id_diplomado ? 'selected' : '' }}>
                          {{ $diplomado->nombre }} ({{ $diplomado->grupo }})
                      </option>
                  @endforeach
              </select>
            </div>

            {{-- Campo de estatus del alumno --}}
            <div>
              <label for="estatus">Estatus</label>
              <select id="estatus" name="estatus" required>
                <option value="activo" {{ old('estatus') === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="baja" {{ old('estatus') === 'baja' ? 'selected' : '' }}>Baja</option>
                <option value="egresado" {{ old('estatus') === 'egresado' ? 'selected' : '' }}>Egresado</option>
              </select>
            </div>
          </div>

          {{-- Sección de acciones, botón de guardar y cancelar --}}
          <div class="actions">
            <a href="{{ route('alumnos.index') }}" class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </section>
  </div>
@endsection