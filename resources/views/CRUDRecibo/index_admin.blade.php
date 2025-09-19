@extends('layouts.encabezados')

@section('title', 'Gestión Recibos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de Recibos</h2>
                <p class="crud-hero-subtitle">Listado (administración)</p>

                <nav class="crud-tabs">
                    <a href="{{ route('recibos.admin.index') }}" class="tab active">Listar recibos</a>
                </nav>
            </header>

            <div class="crud-body">
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                {{-- Filtros --}}
                <form method="GET" class="gm-filter" style="margin-bottom: 14px;">
                    <div class="grid-3">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por concepto o alumno…">
                        @php $e = request('estatus'); @endphp
                        <select name="estatus">
                            <option value="">-- Estatus --</option>
                            <option value="pendiente" {{ $e==='pendiente'?'selected':'' }}>pendiente</option>
                            <option value="validado"  {{ $e==='validado'?'selected':'' }}>validado</option>
                            <option value="rechazado" {{ $e==='rechazado'?'selected':'' }}>rechazado</option>
                        </select>
                        <div class="grid-2" style="gap:8px">
                            <input type="date" name="f1" value="{{ request('f1') }}" placeholder="Desde">
                            <input type="date" name="f2" value="{{ request('f2') }}" placeholder="Hasta">
                        </div>
                    </div>
                    <div style="margin-top:8px">
                        <button class="btn">Filtrar</button>
                        @if(request()->hasAny(['q','estatus','f1','f2']))
                            <a class="btn-ghost" href="{{ route('recibos.admin.index') }}">Limpiar</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="gm-table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Matricula</th>
                            <th>Fecha pago</th>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Estatus</th>
                            <th>Validado por</th>
                            <th>Comprobante</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recibos as $r)
                            <tr>
                                <td>{{ $r->id_recibo }}</td>
                                <td>{{ $r->alumno->matricula ?? '—' }}</td>
                                <td>{{ optional($r->fecha_pago)->format('Y-m-d') }}</td>
                                <td>{{ $r->concepto }}</td>
                                <td>${{ number_format($r->monto, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $r->estatus }}">{{ ucfirst($r->estatus) }}</span>
                                </td>
                                <td>
                                    @if($r->validado_por)
                                        {{ $r->validador->nombre ?? '—' }}
                                        <small class="muted" style="display:block">
                                            {{ optional($r->fecha_validacion)->format('Y-m-d H:i') }}
                                        </small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($r->comprobante_path)
                                        <a class="btn-ghost" target="_blank" href="{{ Storage::disk('public')->url($r->comprobante_path) }}">Ver</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="actions">
                                    <form action="{{ route('recibos.destroy', $r->id_recibo) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn-ghost" onclick="return confirm('¿Eliminar recibo #{{ $r->id_recibo }}?')">Eliminar</button>
                                    </form>

                                    <button class="btn-ghost" data-open="#v{{ $r->id_recibo }}">Validar</button>

                                    <div id="v{{ $r->id_recibo }}" class="gm-modal" style="display:none">
                                        <div class="gm-modal-card">
                                            <h3>Validar recibo #{{ $r->id_recibo }}</h3>
                                            <form method="POST" action="{{ route('recibos.validar', $r->id_recibo) }}">
                                                @csrf
                                                <div class="grid-2">
                                                    <div>
                                                        <label>Estatus</label>
                                                        <select name="estatus" required>
                                                            <option value="validado"  {{ $r->estatus==='validado'?'selected':'' }}>Validado</option>
                                                            <option value="rechazado" {{ $r->estatus==='rechazado'?'selected':'' }}>Rechazado</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label>Comentarios</label>
                                                        <input name="comentarios" value="{{ old('comentarios', $r->comentarios) }}" placeholder="Notas…">
                                                    </div>
                                                </div>
                                                <div class="actions" style="margin-top:12px">
                                                    <button type="button" class="btn-ghost" data-close="#v{{ $r->id_recibo }}">Cancelar</button>
                                                    <button class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9">No hay recibos.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrap">
                    {{ $recibos->links() }}
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
  @vite('resources/js/recibo.js')
@endpush
