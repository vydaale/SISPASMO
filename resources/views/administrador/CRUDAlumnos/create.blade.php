@extends('layouts.encabezados')

@section('title', 'Gestión Alumnos')

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
        @if ($errors->any())
          <ul class="gm-errors">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        @endif
        <form class="gm-form" method="POST" action="{{ route('alumnos.store') }}">
          @csrf
          <h3>Datos de Usuario</h3>
          <div>
            <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre(s)" required>
            <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" required>
            <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" required>
            <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
          </div>
          <div>
            <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" required>
            <input type="password" name="pass" placeholder="Contraseña" required>
            <input type="password" name="pass_confirmation" placeholder="Confirmar contraseña" required>
          </div>
          <div>
            <select name="genero" required>
              <option value="">Género</option>
              <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>M</option>
              <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>F</option>
              <option value="Otro" {{ old('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
            </select>
            <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" required>
            <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" maxlength="10" required>
            <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" required>
          </div>
          <div>
            <input type="hidden" name="id_rol" value="4" required>
          </div>
          <h3>Datos de Alumno</h3>
          <div>
            <input name="matriculaA" value="{{ old('matriculaA') }}" placeholder="Matrícula" required>
            
            {{-- <input type="number" name="num_diplomado" value="{{ old('num_diplomado') }}" placeholder="# Diplomado" required> --}}
            {{-- <input name="grupo" value="{{ old('grupo') }}" placeholder="Grupo" required> --}}

            <select name="id_diplomado" id="id_diplomado" required>
                <option value="">Selecciona un diplomado</option>
                @foreach($diplomados as $diplomado)
                    <option value="{{ $diplomado->id_diplomado }}">
                        {{ $diplomado->nombre }} ({{ $diplomado->grupo }})
                    </option>
                @endforeach
            </select>

            <select name="estatus" required>
              <option value="activo" {{ old('estatus') === 'activo' ? 'selected' : '' }}>Activo</option>
              <option value="baja" {{ old('estatus') === 'baja' ? 'selected' : '' }}>Baja</option>
              <option value="egresado" {{ old('estatus') === 'egresado' ? 'selected' : '' }}>Egresado</option>
            </select>
          </div>

          <div class="actions">
            <a href="{{ route('alumnos.index') }}" class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </section>
  </div>
@endsection