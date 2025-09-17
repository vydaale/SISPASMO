@extends('layouts.encabezadosAl')

@section('title', 'Recibos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de Recibos</h2>
                <p class="crud-hero-subtitle">Listado</p>

                <nav class="crud-tabs">
                    <a href="{{ route('recibos.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('recibos.index') }}" class="tab active">Listar recibos</a>
                </nav>
            </header>

            <div class="crud-body">
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                {{-- Filtro simple opcional (por concepto/estatus) --}}
                <form method="GET" class="gm-filter" style="margin-bottom: 14px;">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por concepto…">
                    <select name="estatus">
                        @php $e = request('estatus'); @endphp
                        <option value="">-- Estatus --</option>
                        <option value="pendiente" {{ $e==='pendiente'?'selected':'' }}>pendiente</option>
                        <option value="validado"  {{ $e==='validado'?'selected':'' }}>validado</option>
                        <option value="rechazado" {{ $e==='rechazado'?'selected':'' }}>rechazado</option>
                    </select>
                    <button class="btn">Filtrar</button>
                    @if(request()->hasAny(['q','estatus']))
                        <a class="btn-ghost" href="{{ route('recibos.index') }}">Limpiar</a>
                    @endif
                </form>

                <div class="table-responsive">
                    <table class="gm-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha pago</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th>Estatus</th>
                                <th>Comprobante</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recibos as $r)
                                <tr>
                                    <td>{{ $r->id_recibo }}</td>
                                    <td>{{ optional($r->fecha_pago)->format('Y-m-d') }}</td>
                                    <td>{{ $r->concepto }}</td>
                                    <td>${{ number_format($r->monto, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $r->estatus }}">
                                            {{ ucfirst($r->estatus) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($r->comprobante_path)
                                            <a class="btn-ghost" target="_blank" href="{{ Storage::disk('public')->url($r->comprobante_path) }}">Ver</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="actions">
                                        <a class="btn-ghost" href="{{ route('recibos.show', $r->id_recibo) }}">Ver</a>
                                        {{-- Alumno NO edita ni elimina --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">No hay recibos registrados.</td>
                                </tr>
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