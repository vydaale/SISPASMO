<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  @vite('resources/css/login.css')

</head>
<body>
  <div class="layout">
    
    <div class="left">
      <img
        class="left-img"
        src="{{ asset('images/loginadmin.png') }}"
        alt="Grupo Morelos — Formación en campo">
    </div>

    <div class="right">
      <div class="card">
        <img class="logo" src="{{ asset('images/logosecundario.png') }}" alt="Grupo Morelos">

        <h2 class="title">Grupo Morelos Rescate Anfibio</h2>

        <form method="POST" action="{{ route('adminlogin.post') }}" class="form">
          @csrf

          <label class="label" for="usuario">Usuario (Coordinador/Administrador)</label>
          <input class="input" id="usuario" name="usuario" type="text" value="{{ old('usuario') }}" placeholder="Correo electrónico" required autofocus>

          <label class="label" for="password">Contraseña</label>
          <input class="input" id="password" name="password" type="password" placeholder="Contraseña" required>

          @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
          @endif

          <button type="submit" class="btn btn-primary">Ingresar</button>
          <button type="button" class="btn btn-secondary">Olvidé contraseña</button>
        </form>

