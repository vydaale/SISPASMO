@extends('layouts.encabezados')
@section('title', 'Reporte: Aspirantes Interesados')

@section('content')
<div id="reporteRoot"
     data-url-total="{{ route('reportes.aspirantes.total') }}"
     data-url-comparacion="{{ route('reportes.aspirantes.comparacion') }}">
  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de Aspirantes</h1>
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="total" href="javascript:void(0)">Total por Tipo</a>
            <a class="tab"        data-tab="comparacion" href="javascript:void(0)">Comparación</a>
          </div>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          {{-- Filtro (solo para la primera gráfica) --}}
          <div class="gm-filter" id="filtroTotal" style="margin-bottom:12px">
            <div class="grid-1">
                <select id="f_tipo_diplomado" style="width:100%">
                    <option value="">-- Selecciona un tipo de diplomado --</option>
                    <option value="Diplomado nivel básico">Diplomado nivel básico</option>
                    <option value="Diplomado intermedio avanzado">Diplomado intermedio avanzado</option>
                </select>
            </div>
            <div class="actions" style="margin-top:8px">
              <button id="btnGenerarTotal" class="btn btn-primary">Generar Gráfica</button>
            </div>
          </div>
          
          <section id="tab-total">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartTotal"></canvas>
            </div>
          </section>

          <section id="tab-comparacion" style="display:none;">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartComparacion"></canvas>
            </div>
          </section>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  @vite('resources/js/reporteAspirantes.js')
@endpush