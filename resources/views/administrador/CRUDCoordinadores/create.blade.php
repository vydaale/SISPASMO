{{-- resources/views/administrador/CRUCCoordinadores/create.blade.php --}}
<h1>Nuevo Coordinador</h1>

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

<form method="POST" action="{{ route('coordinadores.store') }}">
  @csrf

  <h3>Datos de Usuario</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" maxlength="100" required>
    <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" maxlength="100" required>
    <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" maxlength="100" required>
    <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>

    <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" maxlength="50" required>

    {{-- En tu controlador no validas "confirmed", así que solo pedimos pass --}}
    <input type="password" name="pass" placeholder="Contraseña" required>

    <select name="genero" required>
      <option value="">Género</option>
      <option value="M" {{ old('genero')==='M' ? 'selected' : '' }}>M</option>
      <option value="F" {{ old('genero')==='F' ? 'selected' : '' }}>F</option>
      <option value="Otro" {{ old('genero')==='Otro' ? 'selected' : '' }}>Otro</option>
    </select>

    <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" maxlength="100" required>
    <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" maxlength="20" required>
    <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" maxlength="100" required>
  </div>

  <h3>Datos de Coordinador</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <label>Fecha de ingreso:
      <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}" required>
    </label>

    @php $estatusSel = old('estatus'); @endphp
    <select name="estatus" required>
      <option value="">Estatus</option>
      <option value="activo"   {{ $estatusSel==='activo' ? 'selected' : '' }}>activo</option>
      <option value="inactivo" {{ $estatusSel==='inactivo' ? 'selected' : '' }}>inactivo</option>
    </select>
  </div>

  <div style="margin-top:1rem;">
    <button type="submit">Guardar</button>
    <a href="{{ route('coordinadores.index') }}">Cancelar</a>
  </div>
</form>
