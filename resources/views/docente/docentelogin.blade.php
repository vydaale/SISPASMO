<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Docente</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  @vite('resources/css/login.css')
</head>
<body>

<div class="layout">
  <div class="left">
    <img class="left-img" src="{{ asset('images/logindocente.png') }}" alt="Acceso Docente">
  </div>

  <div class="right">
    <div class="card">
      <img class="logo" src="{{ asset('images/logosecundario.png') }}" alt="Grupo Morelos">
      <h1 class="title">Grupo Morelos Rescate Anfibio</h1>

      <form method="POST" action="{{ route('docente.login.post') }}" class="form">
      @csrf
        <label class="label" for="matricula">Matrícula</label>
        <input class="input" id="matricula" name="matricula" type="text" placeholder="Matrícula" required>

        <label class="label" for="password">Contraseña</label>
        <input class="input" id="password" name="password" type="password" placeholder="Contraseña" required>

        <button type="submit" class="btn btn-primary">Ingresar</button>
        <button type="submit" class="btn btn-secondary">Olvidé mi contraseña</a>
      </form>
    </div>
  </div>
</div>

</body>
</html>