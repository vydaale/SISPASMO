{{-- resources/views/administrador/CRUDAlumnos/create.blade.php --}}
<h1>Nuevo Alumno</h1>

@if ($errors->any())
  <ul style="color:red;">
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

<form method="POST" action="{{ route('alumnos.store') }}">
  @csrf

  <h3>Datos de Usuario</h3>
  <div>
    <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" required>
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
      <option value="M" {{ old('genero')==='M'?'selected':'' }}>M</option>
      <option value="F" {{ old('genero')==='F'?'selected':'' }}>F</option>
      <option value="Otro" {{ old('genero')==='Otro'?'selected':'' }}>Otro</option>
    </select>

    <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" required>
    <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" required>
    <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" required>
  </div>

  <div>
    <input type="hidden" name="id_rol" value="2" placeholder="ID Rol (alumno)" required>
  </div>

  <h3>Datos de Alumno</h3>
  <div>
    <input name="matriculaA" value="{{ old('matriculaA') }}" placeholder="Matrícula" required>
    <input type="number" name="num_diplomado" value="{{ old('num_diplomado') }}" placeholder="# Diplomado" required>
    <input name="grupo" value="{{ old('grupo') }}" placeholder="Grupo" required>

    <select name="estatus" required>
      <option value="activo"   {{ old('estatus')==='activo'?'selected':'' }}>activo</option>
      <option value="baja"     {{ old('estatus')==='baja'?'selected':'' }}>baja</option>
      <option value="egresado" {{ old('estatus')==='egresado'?'selected':'' }}>egresado</option>
    </select>
  </div>

  <button type="submit">Guardar</button>
  <a href="{{ route('alumnos.index') }}">Cancelar</a>
</form>
