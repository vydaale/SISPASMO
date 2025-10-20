@extends('layouts.encabezadosAl')

@section('title', 'Mi Información Personal')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Mi información personal</h2>
                <p class="crud-hero-subtitle">Datos del alumno</p>
            </header>

            <div class="crud-body">
                <div class="info-ficha">
                    <div class="info-item">
                        <span class="info-label">Nombre(s):</span>
                        <span class="info-value">{{ auth()->user()->nombre ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Apellido Paterno:</span>
                        <span class="info-value">{{ auth()->user()->apellidoP ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Apellido Materno:</span>
                        <span class="info-value">{{ auth()->user()->apellidoM ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Edad:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse(auth()->user()->fecha_nac)->age ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Diplomado:</span>
                        <span class="info-value">{{ auth()->user()->alumno->diplomado->nombre ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Estatus:</span>
                        <span class="info-value">{{ auth()->user()->alumno->estatus ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection