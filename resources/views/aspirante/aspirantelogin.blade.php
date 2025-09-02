<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Login Aspirante</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
@vite('resources/css/login.css')
</head><body>
<div class="layout">
  <div class="left"><img class="left-img" src="{{ asset('images/loginaspirantes.png') }}" alt="Aspirantes"></div>
  <div class="right">
    <div class="card">
      <img class="logo" src="{{ asset('images/logosecundario.png') }}" alt="Grupo Morelos">
      <h1 class="title">Ingresar</h1>

      @if ($errors->any())
        <div class="error">{!! implode('<br>', $errors->all()) !!}</div>
      @endif

      <form class="form" method="POST" action="{{ route('aspirante.login.post') }}">
        @csrf
        <label class="label" for="correo">Correo electrónico</label>
        <input class="input" id="correo" name="correo" type="email" value="{{ old('correo') }}" autofocus>
        <label class="label" for="password">Contraseña</label>
        <input class="input" id="password" name="password" type="password">
        <button type="submit" class="btn btn-primary">Entrar</button>
        <a href="{{ route('aspirante.select') }}" class="btn btn-secondary">Regresar</a>
      </form>
    </div>
  </div>
</div>
</body></html>
