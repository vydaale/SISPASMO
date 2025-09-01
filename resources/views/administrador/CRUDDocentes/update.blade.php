{{-- resources/views/administrador/CRUDDocentes/update.blade.php --}}
<h1>Actualizar Docente</h1>

@if ($errors->any())
  <ul style="color:red;">
    @foreach ($errors->all() as $e)
      <li>{{ $e }}</li>
    @endforeach
  </ul>
@endif

@if (session('ok'))
  <p style="color:green;">{{ session('ok') }}</p>
@endif

<form method="POST" action="{{ route('docentes.update', $docente) }}">
  @csrf
  @method('PUT')

  <h3>Datos de Usuario</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <input name="nombre" value="{{ old('nombre', $docente->usuario->nombre) }}" placeholder="Nombre" required>
    <input name="apellidoP" value="{{ old('apellidoP', $docente->usuario->apellidoP) }}" placeholder="Apellido paterno" required>
    <input name="apellidoM" value="{{ old('apellidoM', $docente->usuario->apellidoM) }}" placeholder="Apellido materno" required>

    {{-- Si tu campo viene como 'YYYY-MM-DD' basta con esto. --}}
    <input type="date" name="fecha_nac" value="{{ old('fecha_nac', $docente->usuario->fecha_nac) }}" required>

    <input name="usuario" value="{{ old('usuario', $docente->usuario->usuario) }}" placeholder="Usuario" required>

    {{-- Deja vacío para NO cambiar la contraseña --}}
    <input type="password" name="pass" placeholder="Nueva contraseña (opcional)">
    <input type="password" name="pass_confirmation" placeholder="Confirmar nueva contraseña (si la cambias)">
  </div>

  <div style="display:grid; gap:.5rem; max-width:780px; margin-top:.5rem;">
    @php $generoSel = old('genero', $docente->usuario->genero); @endphp
    <select name="genero" required>
      <option value="">Género</option>
      <option value="M" {{ $generoSel==='M' ? 'selected' : '' }}>M</option>
      <option value="F" {{ $generoSel==='F' ? 'selected' : '' }}>F</option>
      <option value="Otro" {{ $generoSel==='Otro' ? 'selected' : '' }}>Otro</option>
    </select>

    <input type="email" name="correo" value="{{ old('correo', $docente->usuario->correo) }}" placeholder="Correo" required>
    <input name="telefono" value="{{ old('telefono', $docente->usuario->telefono) }}" placeholder="Teléfono" required>
    <input name="direccion" value="{{ old('direccion', $docente->usuario->direccion) }}" placeholder="Dirección" required>

    <input type="number" name="id_rol" value="{{ old('id_rol', $docente->usuario->id_rol) }}" placeholder="ID Rol (docente)" required>
    {{-- Si el rol de docente es fijo, podrías usar:
         <input type="hidden" name="id_rol" value="3"> --}}
  </div>

  <h3>Datos de Docente</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <input name="matriculaD" value="{{ old('matriculaD', $docente->matriculaD) }}" placeholder="Matrícula docente" required>
    <input name="especialidad" value="{{ old('especialidad', $docente->especialidad) }}" placeholder="Especialidad" required>
    <input name="cedula" value="{{ old('cedula', $docente->cedula) }}" placeholder="Cédula profesional" required>

    {{-- coincide con tu validación decimal:0,2 --}}
    <input type="number" step="0.01" min="0" name="salario" value="{{ old('salario', $docente->salario) }}" placeholder="Salario" required>
  </div>

  <div style="margin-top:1rem;">
    <button type="submit">Actualizar</button>
    <a href="{{ route('docentes.index') }}">Cancelar</a>
  </div>
</form>
