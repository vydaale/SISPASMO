@extends('layouts.encabezados')
@section('title', 'Reporte: alumnos por edad')

@section('content')
<div id="reporteRoot"
     data-chart-rangos-url="{{ route('admin.reportes.alumnosEdad.chartData') }}"
     data-chart-exact-url="{{ route('admin.reportes.alumnosEdad.chartDataExact') }}"
     data-pdf-url="{{ route('admin.reportes.alumnosEdad.pdf') }}">
  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de alumnos por edad</h1>
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="rangos" href="javascript:void(0)">Por rangos</a>
            <a class="tab"        data-tab="exacta" href="javascript:void(0)">Por edad exacta</a>
          </div>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          <section id="tab-rangos">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartRangos"></canvas>
            </div>

            <div class="actions" style="justify-content:flex-end;margin-top:14px">
            <form id="pdfFormRangos" method="POST" action="{{ route('admin.reportes.alumnosEdad.pdf') }}">
              @csrf
              <input type="hidden" name="chart_data_url" id="chart_data_url_rangos">
              <input type="hidden" name="titulo" value="Alumnos por rango de edad">
              <button type="button" class="btn btn-primary" id="btnPDFRangos">Descargar PDF</button>
            </form>
            </div>
          </section>

          <section id="tab-exacta" style="display:none">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartExacta"></canvas>
            </div>

            <div class="actions" style="justify-content:flex-end;margin-top:14px">
            <form id="pdfFormExacta" method="POST" action="{{ route('admin.reportes.alumnosEdad.pdf') }}">
              @csrf
              <input type="hidden" name="chart_data_url" id="chart_data_url_exacta">
              <input type="hidden" name="titulo" value="Alumnos por edad exacta">
              <button type="button" class="btn btn-primary" id="btnPDFExacta">Descargar PDF</button>
            </form>
            </div>
          </section>

        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  @vite('resources/js/reporteEdades.js')
@endpush
