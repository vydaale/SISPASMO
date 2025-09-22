@extends('layouts.encabezados')
@section('title', 'Reporte: Alumnos Inscritos')

@section('content')
<div id="reporteRoot"
     data-url-totales="{{ route('reportes.inscritos.totales') }}"
     data-url-estatus="{{ route('reportes.inscritos.estatus') }}"
     data-pdf-url="{{ route('reportes.inscritos.pdf') }}">
  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de Alumnos Inscritos</h1>
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="totales" href="javascript:void(0)">Total por Diplomado</a>
            <a class="tab"        data-tab="estatus" href="javascript:void(0)">Estatus de Alumnos</a>
          </div>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          {{-- Filtro de diplomados --}}
          <div class="gm-filter" style="margin-bottom:12px">
            <div class="grid-1">
                <select id="f_diplomado" style="width:100%">
                  <option value="">-- Selecciona un diplomado --</option>
                  @foreach($diplomados as $diplomado)
                    <option value="{{ $diplomado->id_diplomado }}">{{ $diplomado->nombre }}</option>
                  @endforeach
                </select>
            </div>
            <div class="actions" style="margin-top:8px">
              <button id="btnGenerar" class="btn btn-primary">Generar Reporte</button>
            </div>
          </div>
          
          <section id="tab-totales">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartTotales"></canvas>
            </div>
            <div class="actions" style="justify-content:flex-end;margin-top:14px">
              <form id="pdfFormTotales" method="POST" action="{{ route('reportes.inscritos.pdf') }}">
                @csrf
                <input type="hidden" name="chart_data_url" id="chart_data_url_totales">
                <input type="hidden" name="titulo" value="Total de Alumnos por Diplomado">
                <input type="hidden" name="subtitulo" id="subtituloTotales">
                <button type="button" class="btn btn-primary" id="btnPDFTotales">Descargar PDF</button>
              </form>
            </div>
          </section>

          <section id="tab-estatus" style="display:none;">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartEstatus"></canvas>
            </div>
            <div class="actions" style="justify-content:flex-end;margin-top:14px">
              <form id="pdfFormEstatus" method="POST" action="{{ route('reportes.inscritos.pdf') }}">
                @csrf
                <input type="hidden" name="chart_data_url" id="chart_data_url_estatus">
                <input type="hidden" name="titulo" value="Estatus de Alumnos por Diplomado">
                <input type="hidden" name="subtitulo" id="subtituloEstatus">
                <button type="button" class="btn btn-primary" id="btnPDFEstatus">Descargar PDF</button>
              </form>
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
  @vite('resources/js/reporteInscritos.js')
@endpush