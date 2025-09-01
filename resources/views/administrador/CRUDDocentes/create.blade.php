{{-- resources/views/administrador/CRUDDocentes/create.blade.php --}}
<h1>Nuevo Docente</h1>

@if ($errors->any())
  <ul style="color:red;">
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

@if (session('success'))
  <p style="color:green;">{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('docentes.store') }}">
  @csrf

  <h3>Datos de Usuario</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
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
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <input name="matriculaD" value="{{ old('matriculaD') }}" placeholder="Matrícula docente" required>
    <input name="especialidad" value="{{ old('especialidad') }}" placeholder="Especialidad" required>
    <input name="cedula" value="{{ old('cedula') }}" placeholder="Cédula profesional" required>

    {{-- number con decimales para salario --}}
    <input type="number" name="salario" value="{{ old('salario') }}" placeholder="Salario" step="0.01" min="0" required>
  </div>

  <div style="margin-top:1rem;">
    <button type="submit">Guardar</button>
    <a href="{{ route('docentes.index') }}">Cancelar</a>
  </div>
</form>
