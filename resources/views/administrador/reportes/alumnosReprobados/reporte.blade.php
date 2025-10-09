@extends('layouts.encabezados')
@section('title', 'Reporte: Alumnos Reprobados')

@section('content')
<div id="reporteRoot"
     {{-- Eliminamos data-url-modulos --}}
     data-url-total="{{ route('reportes.reprobados.total') }}"
     data-url-calificaciones="{{ route('reportes.reprobados.calificaciones') }}"
     data-url-excel="{{ route('reportes.reprobados.exportar') }}">
  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de Alumnos Reprobados</h1>
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="total" href="javascript:void(0)">Reprobados por Módulo</a>
            <a class="tab" data-tab="calificaciones" href="javascript:void(0)">Comparación Global</a>
          </div>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if(session('error')) <div class="gm-errors">{{ session('error') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          <div class="gm-filter" style="margin-bottom:12px">
            <div class="grid-2">
                <select id="f_diplomado" style="width:100%">
                    <option value="">-- Selecciona un diplomado --</option>
                    @foreach($diplomados as $diplomado)
                      <option value="{{ $diplomado->id_diplomado }}">{{ $diplomado->nombre }}</option>
                    @endforeach
                </select>
                {{-- ELIMINAMOS EL SELECT DE MÓDULO --}}
            </div>
            <div class="actions" style="margin-top:8px">
              <button id="btnGenerar" class="btn btn-primary">Generar Reporte</button>
            </div>
          </div>
          
          <section id="tab-total">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartTotal"></canvas>
            </div>
            <div class="actions" style="justify-content:flex-end;margin-top:14px">
              {{-- Pasamos id_diplomado en lugar de id_modulo --}}
              <form id="excelFormTotal" method="POST" action="{{ route('reportes.reprobados.exportar') }}">
                @csrf
                <input type="hidden" name="id_diplomado" id="diplomado_excel_total">
                <button type="submit" class="btn btn-primary" id="btnExcelTotal">Descargar Excel (Detalle por Diplomado)</button>
              </form>
            </div>
          </section>

          <section id="tab-calificaciones" style="display:none;">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartCalificaciones"></canvas>
            </div>
            {{-- ELIMINAMOS COMPLETAMENTE LA SECCIÓN DE DESCARGA AQUÍ --}}
          </section>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite('resources/js/reporteReprobados.js')
@endpush