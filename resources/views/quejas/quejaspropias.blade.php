<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mis quejas/sugerencias</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/dashboard.css', 'resources/css/crud.css'])
</head>
<body>

  <header class="site-header">
    <div class="header-container">
      <div class="logo">
        <img src="{{ asset('images/logoprincipal.png') }}" alt="Grupo Morelos"/>
          <span>GRUPO MORELOS</span>
      </div>
        <nav>
          <ul class="nav-links">
          <li><a href="{{ route('inicio') }}">Cerrar sesión</a></li>
          </ul>
        </nav>
    </div>
  </header>

  <div class="dash">
    <aside class="sidebar">
      <div class="profile">
        <div class="avatar" aria-hidden="true">👤</div>
        <div class="who">
          <div class="name">
            {{ auth()->user()->nombre ?? 'Usuario' }}
            {{ auth()->user()->apellidoP ?? '' }}
          </div>
          <div class="role">{{ auth()->user()->rol->nombre_rol ?? '—' }}</div>
        </div>
      </div>

      <nav class="nav">
        <div class="group">
          <div class="group-title">QUEJAS Y SUGERENCIAS</div>
          <ul class="menu">
            <li><a href="{{ route('quejas.create') }}">Nueva queja/sugerencia</a></li>
            <li><a href="{{ route('quejas.propias') }}">Mis quejas/sugerencias</a></li>
          </ul>
        </div>
      </nav>
    </aside>

    <main class="content">
      <div class="crud-wrap">
        <div class="crud-card">
          <div class="crud-hero">
            <h1 class="crud-hero-title">Mis quejas y sugerencias</h1>
          </div>

          <div class="crud-body">
            @if (session('ok'))
              <div class="gm-ok">{{ session('ok') }}</div>
            @endif

            @if ($quejas->count() === 0)
              <div class="gm-empty">Aún no has enviado ninguna.</div>
              <div class="actions" style="justify-content:flex-start;margin-top:10px">
                <a class="btn btn-primary" href="{{ route('quejas.create') }}">Enviar una</a>
              </div>
            @else
              <div class="table-responsive">
                <table class="gm-table">
                  <thead>
                    <tr>
                      <th>Tipo</th>
                      <th>Mensaje</th>
                      <th>Contacto</th>
                      <th>Estatus</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($quejas as $q)
                    <tr>
                      <td style="text-transform:capitalize">{{ $q->tipo }}</td>
                      <td>{{ Str::limit($q->mensaje, 80) }}</td>
                      <td>{{ $q->contacto ?: '—' }}</td>
                      <td>
                        @if($q->estatus === 'Atendido')
                          <span style="color:#065f46;font-weight:800">Atendido</span>
                        @else
                          <span style="color:#b45309;font-weight:800">Pendiente</span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <div class="pager">{{ $quejas->links() }}</div>
            @endif
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
```