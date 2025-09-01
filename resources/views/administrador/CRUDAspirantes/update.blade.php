{{-- resources/views/administrador/CRUDAspirantes/update.blade.php --}}
<h1>Actualizar Aspirante</h1>

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

<form method="POST" action="{{ route('aspirantes.update', $aspirante) }}">
    @csrf
    @method('PUT')

    <h3>Datos de Usuario</h3>
    <div style="display:grid; gap:.5rem; max-width:780px;">
        <input name="nombre" value="{{ old('nombre', $aspirante->usuario->nombre) }}" placeholder="Nombre" maxlength="100"
            required>
        <input name="apellidoP" value="{{ old('apellidoP', $aspirante->usuario->apellidoP) }}"
            placeholder="Apellido paterno" maxlength="100" required>
        <input name="apellidoM" value="{{ old('apellidoM', $aspirante->usuario->apellidoM) }}"
            placeholder="Apellido materno" maxlength="100" required>

        {{-- Asegúrate que venga como YYYY-MM-DD --}}
        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', $aspirante->usuario->fecha_nac) }}" required>

        <input name="usuario" value="{{ old('usuario', $aspirante->usuario->usuario) }}" placeholder="Usuario"
            maxlength="50" required>

        {{-- Deja en blanco para NO cambiar la contraseña --}}
        <input type="password" name="pass" placeholder="Nueva contraseña (opcional)">
        <input type="password" name="pass_confirmation" placeholder="Confirmar nueva contraseña (si la cambias)">
    </div>

    <div style="display:grid; gap:.5rem; max-width:780px; margin-top:.5rem;">
        @php $generoSel = old('genero', $aspirante->usuario->genero); @endphp
        <select name="genero" required>
            <option value="">Género</option>
            <option value="M" {{ $generoSel === 'M' ? 'selected' : '' }}>M</option>
            <option value="F" {{ $generoSel === 'F' ? 'selected' : '' }}>F</option>
            <option value="Otro" {{ $generoSel === 'Otro' ? 'selected' : '' }}>Otro</option>
        </select>

        <input type="email" name="correo" value="{{ old('correo', $aspirante->usuario->correo) }}"
            placeholder="Correo" maxlength="100" required>
        <input name="telefono" value="{{ old('telefono', $aspirante->usuario->telefono) }}" placeholder="Teléfono"
            maxlength="20" required>
        <input name="direccion" value="{{ old('direccion', $aspirante->usuario->direccion) }}" placeholder="Dirección"
            maxlength="100" required>

        <input type="number" name="id_rol" value="{{ old('id_rol', $aspirante->usuario->id_rol) }}"
            placeholder="ID Rol (aspirante)" required>
    </div>

    <h3>Datos de Aspirante</h3>
    <div style="display:grid; gap:.5rem; max-width:780px;">
        <textarea name="interes" placeholder="Interés" rows="3" maxlength="50" required>{{ old('interes', $aspirante->interes) }}</textarea>

        <label>Fecha/Día:
            <input type="date" name="dia" value="{{ old('dia', $aspirante->dia) }}" required>
        </label>

        @php $estatusSel = old('estatus', $aspirante->estatus); @endphp
        <select name="estatus" required>
            <option value="activo" {{ $estatusSel === 'activo' ? 'selected' : '' }}>activo</option>
            <option value="rechazado" {{ $estatusSel === 'rechazado' ? 'selected' : '' }}>rechazado</option>
        </select>
    </div>

    <div style="margin-top:1rem;">
        <button type="submit">Actualizar</button>
        <a href="{{ route('aspirantes.index') }}">Cancelar</a>
    </div>
</form>
