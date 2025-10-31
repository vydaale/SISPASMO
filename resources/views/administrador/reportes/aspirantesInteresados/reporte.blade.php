@extends('layouts.encabezados')
@section('title', 'Reporte aspirantes interesados')

@section('content')
<div id="reporteRoot"
     data-url-total="{{ route('reportes.aspirantes.total') }}"
     data-url-comparacion="{{ route('reportes.aspirantes.comparacion') }}">
  <div class="dash">
    <div class="crud-wrap">
      <div class="crud-card">

        <div class="crud-hero">
          <h1 class="crud-hero-title">Reporte de aspirantes</h1>

          {{-- Navegación de pestañas, controla la vista de reporte (total vs comparación). --}}
          <div class="crud-tabs" id="tabs">
            <a class="tab active" data-tab="total" href="javascript:void(0)">Total por tipo</a>
            <a class="tab" data-tab="comparacion" href="javascript:void(0)">Comparación</a>
          </div>
        </div>

        <div class="crud-body">
        
        {{-- Bloque de FILTROS de la pestaña "total por tipo" (VOLVEMOS al ID original: filtroTotal) --}}
        <div class="filter-forms" id="filtroTotal" 
               style="display: flex; align-items: center; gap: 15px; margin-top: 15px; margin-bottom: 20px;">
            
            <div style="flex-grow: 1; max-width: 300px;">
              {{-- Selector para filtrar el reporte por tipo de diplomado (básico o intermedio/avanzado). --}}
                <select id="f_tipo_diplomado" style="width:100%; padding: 8px; border-radius: 4px;">
                    <option value="todos">Todos los tipos de diplomado</option>
                    <option value="basico">Diplomado nivel básico</option>
                    <option value="intermedio y avanzado">Diplomado intermedio y avanzado</option>
                </select>
            </div>
            
            {{-- Botón principal para generar la gráfica de la pestaña "total". --}}
            <button id="btnGenerarTotal" class="submit-button">Generar</button>
        </div>

          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif
                    
          {{-- Sección de gráfica 1, total por tipo (activa por defecto). --}}
          <section id="tab-total">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartTotal"></canvas>
            </div>
            
            {{-- Controles y Botón de DESCARGA (Modo Total) --}}
            {{-- Se usa un formulario GET para enviar el filtro de selección al controlador de exportación --}}
            <form method="GET" action="{{ route('reportes.aspirantes.exportar') }}"
                   id="controlesTotal" style="text-align: right; margin-top: 15px;">
                
                <input type="hidden" name="modo" value="total"> 
                {{-- Campo oculto: Se sincronizará con JavaScript con el valor del select --}}
                <input type="hidden" id="export_tipo_diplomado" name="tipo" value="todos">
                
                <button type="submit" class="btn btn-primary">
                    Descargar reporte (XML)
                </button>
            </form>
          </section>

          {{-- Sección de gráfica 2, comparación entre tipos (oculta por defecto). --}}
          <section id="tab-comparacion" style="display:none;">
            <div class="chart-wrap" style="max-width:880px;margin:0 auto">
              <canvas id="chartComparacion"></canvas>
            </div>
            
            {{-- Botón de DESCARGA (Modo Comparación) --}}
            <div style="text-align: right; margin-top: 15px;">
                <a href="{{ route('reportes.aspirantes.exportar', ['modo' => 'comparacion', 'tipo' => 'todos']) }}" 
                    class="btn btn-primary">
                    Descargar reporte (XML)
                </a>
            </div>
            
            {{-- ⚠️ Agregamos un DIV de control vacío para que el script pueda ocultar/mostrar los controles de total --}}
            {{-- Si el script busca 'filtroTotal' para ocultarlo, debe haber algo en la vista de comparación que reemplace el espacio --}}
            <div id="controlesTotal" style="display: none;"></div>
          </section>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  {{-- Script para sincronizar el filtro de la gráfica con el filtro de exportación. --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectFiltro = document.getElementById('f_tipo_diplomado');
        const inputExport = document.getElementById('export_tipo_diplomado');

        // Función para sincronizar el valor del select con el input oculto del formulario de descarga
        function sincronizarFiltro() {
            // Usa el valor del select (que es 'basico', 'intermedio y avanzado', o 'todos')
            inputExport.value = selectFiltro.value || 'todos'; 
        }

        // Sincronizar al cargar y al cambiar el select
        sincronizarFiltro();
        selectFiltro.addEventListener('change', sincronizarFiltro);
        
        // Sincronizar también antes de generar la gráfica (por si el JS de la gráfica lo necesita)
        document.getElementById('btnGenerarTotal').addEventListener('click', sincronizarFiltro);
    });
  </script>
  
  {{-- Se incluye el script de lógica para el manejo de filtros, gráficas y la interacción de pestañas. --}}
  @vite('resources/js/reporteAspirantes.js')
@endpush