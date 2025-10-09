@extends('layouts.encabezados')
@section('title', 'Reportes')

@section('content')
  <div class="reports-container">
    <h1>Panel de Reportes</h1>
    
    <ul class="reports-list">
      
      <li class="report-item">
        <a href="{{ route('reportes.inscritos.index') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Alumnos inscritos por diplomado</h3>
            <p class="report-description">Reporte de alumnos inscritos por diplomado.</p>
          </div>
        </a>
      </li>
      
      <li class="report-item">
        <a href="{{ route('admin.reportes.alumnosEdad.index') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Alumnos por edad</h3>
            <p class="report-description">Reporte de alumnos inscritos agrupados por edad.</p>
          </div>
        </a>
      </li>
      
      <li class="report-item">
        <a href="{{ route('reportes.alumnos.concluidos') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Diplomados concluidos</h3>
            <p class="report-description">Reporte de alumnos con diplomado concluido.</p>
          </div>
        </a>
      </li>
      
      <li class="report-item">
        <a href="{{ route('reportes.pagos') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Reporte de pagos</h3>
            <p class="report-description">Semanales y mensuales.</p>
          </div>
        </a>
      </li>
      
      <li class="report-item">
        <a href="{{ route('reportes.adeudos') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Reporte de adeudos</h3>
            <p class="report-description">Alumnos con pagos pendientes.</p>
          </div>
        </a>
      </li>
      
      <li class="report-item">
        <a href="{{ route('reportes.reprobados.index') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Alumnos reprobados</h3>
            <p class="report-description">Análisis por módulo.</p>
          </div>
        </a>
      </li>
      
      <li class="report-item">
        <a href="{{ route('reportes.aspirantes.index') }}" class="report-link">
          <div class="report-icon">✔</div>
          <div class="report-link-content">
            <h3 class="report-title">Aspirantes interesados</h3>
            <p class="report-description">Por diplomado.</p>
          </div>
        </a>
      </li>

    </ul>
  </div>
@endsection