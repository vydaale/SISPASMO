@extends('layouts.encabezados')

@section('title', 'Reporte de Pagos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Reporte de Pagos</h2>
                <p class="crud-hero-subtitle">Visualización y exportación</p>
            </header>

            <div class="crud-body">
                {{-- Filtro de fecha y tipo de reporte --}}
                <form method="GET" class="gm-filter" action="{{ route('reportes.pagos') }}" style="margin-bottom: 20px;">
                    <div class="grid-2">
                        <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" placeholder="Fecha de inicio" required>
                        <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" placeholder="Fecha de fin" required>
                    </div>
                    <div class="grid-2" style="margin-top: 8px;">
                        <select name="periodo" required>
                            <option value="semanal" {{ request('periodo') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                            <option value="mensual" {{ request('periodo') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                        </select>
                        <button class="btn">Generar Reporte</button>
                    </div>
                </form>

                @if(request()->hasAny(['fecha_inicio', 'fecha_fin']))
                    <div class="report-section">
                        <h3>Pagos del periodo: {{ request('fecha_inicio') }} al {{ request('fecha_fin') }}</h3>
                        
                        {{-- Contenedor de la gráfica --}}
                        <div style="margin-top: 20px; text-align: center;">
                            @if($pagos->isEmpty())
                                <div class="gm-info">No se encontraron pagos validados en el período seleccionado.</div>
                            @else
                                <canvas id="pagosChart" style="max-height: 400px;"></canvas>
                                
                                {{-- Elemento oculto para pasar datos de PHP a JavaScript --}}
                                <div id="pagos-data" style="display:none;">{!! $pagos->toJson() !!}</div>

                                {{-- Botón de descarga para el Excel --}}
                                <form action="{{ route('reportes.exportar') }}" method="GET" style="margin-top: 15px;">
                                    <input type="hidden" name="fecha_inicio" value="{{ request('fecha_inicio') }}">
                                    <input type="hidden" name="fecha_fin" value="{{ request('fecha_fin') }}">
                                    <button type="submit" class="btn btn-secondary">Descargar Excel</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="gm-info">Selecciona un rango de fechas y un tipo de periodo para generar el reporte.</div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite('resources/js/reportePagosSemMen.js')
@endpush