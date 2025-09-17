<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="crud.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Restablecer contraseña</h2>
                <p class="crud-hero-subtitle">Ingresa tu nueva contraseña</p>
            </header>
            @vite('resources/css/crud.css')

            <div class="crud-body">
                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (session('status'))
                    <div class="gm-ok">{{ session('status') }}</div>
                @endif
                
                <form method="POST" action="{{ route('password.update') }}" class="gm-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <input name="correo" type="email" value="{{ old('correo', $correo) }}" placeholder="Correo" required autofocus>
                        <input name="password" type="password" placeholder="Nueva contraseña" required>
                        <input name="password_confirmation" type="password" placeholder="Confirmar contraseña" required>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Restablecer</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</body>
</html>