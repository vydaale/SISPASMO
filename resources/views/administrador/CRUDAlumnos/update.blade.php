{{-- resources/views/administrador/CRUDAlumnos/edit.blade.php --}}
<h1>Editar Alumno</h1>

@if ($errors->any())
  <ul style="color:red;">
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

@if(session('ok')) 
  <p style="color:green;">{{ session('ok') }}</p>
@endif

<form method="POST" action="{{ route('alumnos.update', $alumno) }}">
  @csrf
  @method('PUT')

  <h3>Datos de Usuario</h3>
  <div>
    <input name="nombre" value="{{ old('nombre', $alumno->usuario->nombre) }}" placeholder="Nombre" required>
    <input name="apellidoP" value="{{ old('apellidoP', $alumno->usuario->apellidoP) }}" placeholder="Apellido paterno" required>
    <input name="apellidoM" value="{{ old('apellidoM', $alumno->usuario->apellidoM) }}" placeholder="Apellido materno" required>
    <input type="date" name="fecha_nac" value="{{ old('fecha_nac', $alumno->usuario->fecha_nac) }}" required>
  </div>

  <div>
    <input name="usuario" value="{{ old('usuario', $alumno->usuario->usuario) }}" placeholder="Usuario" required>

    {{-- Deja vacío para NO cambiar la contraseña --}}
    <input type="password" name="pass" placeholder="Nueva contraseña (opcional)">
    <input type="password" name="pass_confirmation" placeholder="Confirmar nueva contraseña (si la cambias)">
  </div>

  <div>
    @php $generoSel = old('genero', $alumno->usuario->genero); @endphp
    <select name="genero" required>
      <option value="">Género</option>
      <option value="M" {{ $generoSel==='M' ? 'selected' : '' }}>M</option>
      <option value="F" {{ $generoSel==='F' ? 'selected' : '' }}>F</option>
      <option value="Otro" {{ $generoSel==='Otro' ? 'selected' : '' }}>Otro</option>
    </select>

    <input type="email" name="correo" value="{{ old('correo', $alumno->usuario->correo) }}" placeholder="Correo" required>
    <input name="telefono" value="{{ old('telefono', $alumno->usuario->telefono) }}" placeholder="Teléfono" required>
    <input name="direccion" value="{{ old('direccion', $alumno->usuario->direccion) }}" placeholder="Dirección" required>

    <input type="number" name="id_rol" value="{{ old('id_rol', $alumno->usuario->id_rol) }}" placeholder="ID Rol" required>
  </div>

  <h3>Datos de Alumno</h3>
  <div>
    <input name="matriculaA" value="{{ old('matriculaA', $alumno->matriculaA) }}" placeholder="Matrícula" required>
    <input type="number" name="num_diplomado" value="{{ old('num_diplomado', $alumno->num_diplomado) }}" placeholder="# Diplomado" required>
    <input name="grupo" value="{{ old('grupo', $alumno->grupo) }}" placeholder="Grupo" required>

    @php $estatusSel = old('estatus', $alumno->estatus); @endphp
    <select name="estatus" required>
      <option value="activo"   {{ $estatusSel==='activo' ? 'selected' : '' }}>activo</option>
      <option value="baja"     {{ $estatusSel==='baja' ? 'selected' : '' }}>baja</option>
      <option value="egresado" {{ $estatusSel==='egresado' ? 'selected' : '' }}>egresado</option>
    </select>
  </div>

  <button type="submit">Actualizar</button>
  <a href="{{ route('alumnos.index') }}">Cancelar</a>
</form>
