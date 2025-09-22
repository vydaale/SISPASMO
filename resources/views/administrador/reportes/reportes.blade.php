@extends('layouts.encabezados')
@section('title', 'Reportes')

@section('content')
  <h1>Reportes</h1>
  <section class="reports-grid">
    <a href="{{ route('reportes.inscritos.index') }}" class="report-card">Reporte de alumnos inscritos por diplomado</a>
    <a href="{{ route('admin.reportes.alumnosEdad.index') }}" class="report-card">Reporte de alumnos inscritos por edad</a>
    <a href="{{ route('reportes.alumnos.concluidos') }}" class="report-card">Reporte de alumnos con diplomado concluido</a>
    <a href="{{ route('reportes.pagos') }}" class="report-card">Reporte de pagos (semanales y mensuales)</a>
    <a href="{{ route('reportes.adeudos') }}" class="report-card">Reporte de adeudos</a>
    <a href="{{ route('reportes.reprobados.index') }}" class="report-card">Reporte de alumnos reprobados</a>
    <a href="{{ route('reportes.aspirantes.index') }}" class="report-card">Reporte de aspirantes interesados en diplomado</a>
  </section>
@endsection