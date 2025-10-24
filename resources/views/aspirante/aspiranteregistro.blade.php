<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro Aspirante</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  @vite('resources/css/registro.css')
  </head><body>
  <div class="layout">
    <div class="left"><img class="left-img" src="{{ asset('images/aspirante.png') }}" alt="Aspirantes"></div>
    <div class="right">
      <div class="card">
        <img class="logo" src="{{ asset('images/logosecundario.png') }}" alt="Grupo Morelos">
        <h1 class="title">Grupo Morelos Rescate Anfibio</h1>

        {{-- Bloque de errores, muestra los errores de validación de laravel (si los hay), unidos por salto de línea. --}}
        @if ($errors->any())
          <div class="error">{!! implode('<br>', $errors->all()) !!}</div>
        @endif

        {{-- Formulario principal, envía todos los datos del aspirante y crea un nuevo usuario. --}}
        <form class="form" method="POST" action="{{ route('aspirante.register') }}">
          @csrf

          {{-- Bloque de datos personales, incluye nombre, apellidos, fecha de nacimiento y género. --}}
          <label class="label">Nombre</label>
          <input class="input" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre(s)" required>
          @error('nombre')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Apellido paterno</label>
          <input class="input" name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" required>
          @error('apellidoP')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Apellido materno</label>
          <input class="input" name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" required>
          @error('apellidoM')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Fecha de nacimiento</label>
          <input class="input" name="fecha_nac" type="date" value="{{ old('fecha_nac') }}" required>
          @error('fecha_nac')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Sexo</label>
          <select class="input" name="genero">
            <option value="">Selecciona</option>
            <option value="M" {{ old('genero')=='M'?'selected':'' }}>Masculino</option>
            <option value="F" {{ old('genero')=='F'?'selected':'' }}>Femenino</option>
            <option value="Otro" {{ old('genero')=='Otro'?'selected':'' }}>Otro</option>
          </select>

          <label class="label">Teléfono</label>
          <input class="input" name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" maxlength="10" required>
          @error('telefono')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Correo electrónico</label>
          <input class="input" name="correo" type="email" value="{{ old('correo') }}" placeholder="Correo electrónico" required>
          @error('correo')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Dirección</label>
          <input class="input" name="direccion" value="{{ old('direccion') }}" placeholder="Domicilio" required>
          @error('direccion')
            <div class="error">{{ $message }}</div>
          @enderror

          <label class="label">Diplomado de interés</label>
            @if(($diplomados ?? collect())->isEmpty())
              <select class="input" disabled>
                <option>No hay diplomados registrados</option>
              </select>
            @else
              <select class="input" name="id_diplomado" required>
                <option value="">Selecciona</option>
                @foreach($diplomados as $d)
                  <option value="{{ $d->id_diplomado }}" {{ old('id_diplomado') == $d->id_diplomado ? 'selected' : '' }}>
                    {{ $d->nombre }}
                  </option>
                @endforeach
              </select>
              @error('id_diplomado')
                <small class="error">{{ $message }}</small>
              @enderror
            @endif

          <label class="label">Fecha preferente</label>
          <input class="input" name="dia" type="date" value="{{ old('dia') }}">

          <label class="label">Contraseña</label>
          <input class="input" name="password" type="password" required>
          <label class="label">Confirmar contraseña</label>
          <input class="input" name="password_confirmation" type="password" required>
          @error('password')
            <div class="error">{{ $message }}</div>
          @enderror

          <label style="display:flex;align-items:center;gap:.6rem;margin-top:.5rem">
            <input type="checkbox" name="acepto" value="1" style="transform:scale(1.1)">
            Aceptar aviso de privacidad y condiciones
          </label>
          
          {{-- Botones de acción, Registrarse y Regresar. --}}
          <button type="submit" class="btn btn-primary">Registrarse</button>
          <a href="{{ route('aspirante.select') }}" class="btn btn-secondary">Regresar</a>
        </form>
      </div>
    </div>
  </div>
  </body>
</html>
