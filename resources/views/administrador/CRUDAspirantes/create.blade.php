{{-- resources/views/administrador/CRUDAspirantes/create.blade.php --}}
<h1>Nuevo Aspirante</h1>

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

<form method="POST" action="{{ route('aspirantes.store') }}">
  @csrf

  <h3>Datos de Usuario</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" maxlength="100" required>
    <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" maxlength="100" required>
    <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" maxlength="100" required>
    <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>

    <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" maxlength="50" required>

    <input type="password" name="pass" placeholder="Contraseña" required>
    <input type="password" name="pass_confirmation" placeholder="Confirmar contraseña" required>

    <select name="genero" required>
      <option value="">Género</option>
      <option value="M" {{ old('genero')==='M' ? 'selected' : '' }}>M</option>
      <option value="F" {{ old('genero')==='F' ? 'selected' : '' }}>F</option>
      <option value="Otro" {{ old('genero')==='Otro' ? 'selected' : '' }}>Otro</option>
    </select>

    <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" maxlength="100" required>
    <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" maxlength="20" required>
    <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" maxlength="100" required>

    {{-- Si el rol de aspirante es fijo, puedes dejarlo hidden con su ID --}}
    <input type="number" name="id_rol" value="{{ old('id_rol') }}" placeholder="ID Rol (aspirante)" required>
    {{-- Ejemplo: <input type="hidden" name="id_rol" value="4"> --}}
  </div>

  <h3>Datos de Aspirante</h3>
  <div style="display:grid; gap:.5rem; max-width:780px;">
    <textarea name="interes" placeholder="Interés (p. ej. Diplomado X, turno, campus…)" rows="3" maxlength="50" required>{{ old('interes') }}</textarea>

    <label>Fecha/Día:
      <input type="date" name="dia" value="{{ old('dia') }}" required>
    </label>


    @php $estatusSel = old('estatus'); @endphp
    <select name="estatus" required>
      <option value="">Estatus</option>
      <option value="activo" {{ $estatusSel==='activo' ? 'selected' : '' }}>activo</option>
      <option value="rechazado" {{ $estatusSel==='rechazado' ? 'selected' : '' }}>rechazado</option>
    </select>
  </div>

  <div style="margin-top:1rem;">
    <button type="submit">Guardar</button>
    <a href="{{ route('aspirantes.index') }}">Cancelar</a>
  </div>
</form>
