@extends($layout)

@section('content')
<div class="crud-wrap">
  <div class="crud-card">
    <div class="crud-hero">
      <h2 class="crud-hero-title">Centro de notificaciones</h2>
      <p class="crud-hero-subtitle">Tus avisos del sistema</p>

      <nav class="crud-tabs" style="margin-top:10px">
        <a href="{{ route('notificaciones.index', ['estado' => 'all']) }}"
           class="tab {{ ($estado ?? 'all')==='all' ? 'active' : '' }}">Todas</a>
        <a href="{{ route('notificaciones.index', ['estado' => 'unread']) }}"
           class="tab {{ ($estado ?? '')==='unread' ? 'active' : '' }}">No leídas</a>
        <a href="{{ route('notificaciones.index', ['estado' => 'read']) }}"
           class="tab {{ ($estado ?? '')==='read' ? 'active' : '' }}">Leídas</a>
      </nav>
    </div>

    <div class="crud-body">
      @if($notifications->isEmpty())
        <div class="gm-empty">No tienes notificaciones por el momento.</div>
      @else
        <div style="display:grid;gap:16px">
          @foreach($notifications as $notification)
            @php
              $data = $notification->data ?? [];
              $tipo = $data['tipo'] ?? 'general';
              $titulo = $data['titulo'] ?? $data['observaciones'] ?? 'Notificación';
              $mensaje = $data['mensaje'] ?? null;
              $url = $data['url'] ?? null;

              // Paleta de color según tipo
              $colores = [
                'adeudo_vencido' => ['#fee2e2', '#991b1b'],     // rojo suave
                'adeudo_por_vencer' => ['#fef9c3', '#854d0e'], // amarillo suave
                'horario' => ['#dbeafe', '#1e40af'],            // azul suave
                'taller' => ['#ede9fe', '#5b21b6'],             // morado suave
                'general' => ['#f1f5f9', '#334155'],            // gris neutro
              ];
              [$bg, $text] = $colores[$tipo] ?? $colores['general'];
            @endphp

            <article style="
              border:1px solid #e5e7eb;
              border-radius:16px;
              padding:16px 20px;
              background:{{ $notification->read_at ? '#f9fafb' : '#fff' }};
              box-shadow:0 1px 3px rgba(0,0,0,.05);
              transition:all .2s ease-in-out;
            " class="hover:shadow-md">
              <header style="display:flex;justify-content:space-between;align-items:start;gap:12px">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                  <span style="
                    background:{{ $bg }};
                    color:{{ $text }};
                    border-radius:8px;
                    padding:2px 8px;
                    font-size:.8rem;
                    font-weight:600;
                  ">
                    {{ ucfirst(str_replace('_',' ', $tipo)) }}
                  </span>

                  <h3 style="margin:0;font-size:1.05rem;font-weight:800;color:#1e293b">
                    {{ $titulo }}
                  </h3>
                </div>
                <small style="opacity:.7">{{ $notification->created_at->diffForHumans() }}</small>
              </header>

              @if($mensaje)
                <p style="margin:.5rem 0 0;color:#475569">{{ $mensaje }}</p>
              @endif

              <div style="display:flex;gap:8px;align-items:center;margin-top:12px;flex-wrap:wrap">
                @if($url)
                  <a href="{{ $url }}" target="_blank"
                    style="color:{{ $text }};font-weight:600;text-decoration:none">
                    Ver detalles →
                  </a>
                @endif

                @if(!$notification->read_at)
                  <form action="{{ route('notificaciones.markOne', $notification->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-primary">Marcar como leída</button>
                  </form>
                @else
                  <span class="btn btn-primary" style="opacity:.7">Leída</span>
                @endif

                <form action="{{ route('notificaciones.destroy', $notification->id) }}" method="POST"
                      onsubmit="return confirm('¿Eliminar notificación?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger" style="background:#f43f5e">Eliminar</button>
                </form>
              </div>
            </article>
          @endforeach
        </div>

        <div class="pager" style="margin-top:20px">
          {{ $notifications->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
