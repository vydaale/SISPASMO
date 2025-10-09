@extends('layouts.encabezados')
@section('title', 'Reporte de Alumnos y Diplomados')

@section('content')
<div class="dash">
  <div class="crud-wrap">
    <div class="crud-card">

      <div class="crud-hero">
        <h1 class="crud-hero-title">Reporte de alumnos y diplomados</h1>
        <div class="crud-tabs" id="tabs">
          <a class="tab active" data-tab="egresados" href="javascript:void(0)">Egresados por diplomado</a>
          <a class="tab" data-tab="estatus" href="javascript:void(0)">Comparación de estatus</a>
        </div>
      </div>

      <div class="crud-body">
        @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
        @if($errors->any())
          <div class="gm-errors">
            <ul>
              @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="filter-form-inline">
            <form action="{{ route('reportes.alumnos.concluidos') }}" method="GET">
                <label for="year">Filtrar por Año de Finalización:</label>
                {{-- Envolvemos el select para limitar su ancho con la nueva clase --}}
                <div class="filter-select-wrap">
                    <select name="year" id="year" onchange="this.form.submit()" style="width:100%">
                        @foreach ($years as $y)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        @if ($egresadosAnual->isEmpty())
            <div class="alert alert-warning">No hay datos para el año {{ $year }}.</div>
        @else

        <section id="tab-egresados">
        <div class="chart-wrap" style="max-width:880px;margin:0 auto">
            <canvas
            id="egresadosAnualChart"
            data-series='@json($egresadosAnual)'>
            </canvas>
        </div>
        <div class="actions" style="justify-content:flex-end;margin-top:14px">
            <a href="{{ route('reportes.excel.egresados.anual', ['year' => $year, 'report_type' => 'egresados']) }}" class="btn btn-primary">Descargar Excel</a>
        </div>
        </section>

        <section id="tab-estatus" style="display:none">
        <div class="chart-wrap" style="max-width:880px;margin:0 auto">
            <canvas
            id="comparacionEstatusChart"
            data-series='@json($comparacionEstatus)'>
            </canvas>
        </div>
        <div class="actions" style="justify-content:flex-end;margin-top:14px">
            <a href="{{ route('reportes.excel.comparacion.estatus', ['year' => $year, 'report_type' => 'estatus']) }}" class="btn btn-primary">Descargar Excel</a>
        </div>
        </section>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  @vite('resources/js/reporteDipConcluido.js')
@endpush