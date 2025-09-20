@extends('layouts.encabezados')
@section('title', 'Reporte: adeudos')

@section('content')
<div id="reporteAdeudosRoot"
     data-url-chart-mes="{{ route('reportes.adeudos.chart.mes') }}"
     data-url-chart-alumno="{{ route('reportes.adeudos.chart.alumno') }}"
     data-url-exportar="{{ route('reportes.adeudos.exportar') }}">
  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de adeudos</h1>
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="mes" href="javascript:void(0)">Por mes/año</a>
            <a class="tab"        data-tab="alumno" href="javascript:void(0)">Por matrícula</a>
          </div>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          <div class="gm-filter" style="margin-bottom:12px">
            <div class="grid-3">
              <select id="f_mes">
                <option value="">Mes</option>
                @for ($i=1;$i<=12;$i++)
                  <option value="{{ $i }}">{{ \Carbon\Carbon::createFromDate(null,$i,1)->locale('es')->monthName }}</option>
                @endfor
              </select>
              <select id="f_anio">
                <option value="">Año</option>
                @for ($y=date('Y')-5;$y<=date('Y');$y++)
                  <option value="{{ $y }}">{{ $y }}</option>
                @endfor
              </select>
              <input id="f_matricula" type="text" placeholder="Matrícula (para la pestaña de matrícula)">
            </div>
            <div class="actions" style="margin-top:8px">
              <button id="btnGenerar" class="btn btn-primary">Generar</button>
            </div>
          </div>

          <section id="tab-mes">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartMes"></canvas>
            </div>
            <div class="actions" style="margin-top:16px; text-align:right;">
                <button id="btnDownloadMes" class="btn btn-primary" style="display:none;">Descargar Excel</button>
            </div>
          </section>

          <section id="tab-alumno" style="display:none">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartAlumno"></canvas>
            </div>
            <div class="actions" style="margin-top:16px; text-align:right;">
                <button id="btnDownloadAlumno" class="btn btn-primary" style="display:none;">Descargar Excel</button>
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
  @vite('resources/js/reporteAdeudos.js')
@endpush