{{-- resources/views/administrador/CRUCCoordinadores/update.blade.php --}}
<h1>Actualizar Coordinador</h1>

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

<form method="POST" action="{{ route('coordinadores.update', $coordinador) }}">
  @csrf
  @method('PUT')

  <h3>Datos de Usuario</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <input name="nombre" value="{{ old('nombre', $coordinador->usuario->nombre) }}" placeholder="Nombre" maxlength="100" required>
    <input name="apellidoP" value="{{ old('apellidoP', $coordinador->usuario->apellidoP) }}" placeholder="Apellido paterno" maxlength="100" required>
    <input name="apellidoM" value="{{ old('apellidoM', $coordinador->usuario->apellidoM) }}" placeholder="Apellido materno" maxlength="100" required>

    <input type="date" name="fecha_nac" value="{{ old('fecha_nac', $coordinador->usuario->fecha_nac) }}" required>

    <input name="usuario" value="{{ old('usuario', $coordinador->usuario->usuario) }}" placeholder="Usuario" maxlength="50" required>

    @php $generoSel = old('genero', $coordinador->usuario->genero); @endphp
    <select name="genero" required>
      <option value="">Género</option>
      <option value="M" {{ $generoSel==='M' ? 'selected' : '' }}>M</option>
      <option value="F" {{ $generoSel==='F' ? 'selected' : '' }}>F</option>
      <option value="Otro" {{ $generoSel==='Otro' ? 'selected' : '' }}>Otro</option>
    </select>

    <input type="email" name="correo" value="{{ old('correo', $coordinador->usuario->correo) }}" placeholder="Correo" maxlength="100" required>
    <input name="telefono" value="{{ old('telefono', $coordinador->usuario->telefono) }}" placeholder="Teléfono" maxlength="20" required>
    <input name="direccion" value="{{ old('direccion', $coordinador->usuario->direccion) }}" placeholder="Dirección" maxlength="100" required>
  </div>

  <h3>Datos de Coordinador</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <label>Fecha de ingreso:
      <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', $coordinador->fecha_ingreso) }}" required>
    </label>

    @php $estatusSel = old('estatus', $coordinador->estatus); @endphp
    <select name="estatus" required>
      <option value="activo"   {{ $estatusSel==='activo' ? 'selected' : '' }}>activo</option>
      <option value="inactivo" {{ $estatusSel==='inactivo' ? 'selected' : '' }}>inactivo</option>
    </select>
  </div>

  <div style="margin-top:1rem;">
    <button type="submit">Actualizar</button>
    <a href="{{ route('coordinadores.index') }}">Cancelar</a>
  </div>
</form>
