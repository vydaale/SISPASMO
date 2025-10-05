@extends('layouts.encabezadosAl')

@section('title', 'Actividades Extracurriculares')

@section('styles')
    <link rel="stylesheet" href="{{ asset('../../css/x.css') }}">
@endsection

@section('content')
    <div class="container mt-4">

        {{-- Mensajes de alerta --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- ============================================= --}}
        {{-- ========= SECCIÓN MIS INSCRIPCIONES ========= --}}
        {{-- ============================================= --}}
        <h2 class="mb-3">Mis Inscripciones</h2>
        <hr>
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-5 card-deck">
            @forelse ($misInscripciones as $actividad)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">{{ $actividad->nombre_act }} ({{ $actividad->tipo }})</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $actividad->descripcion }}</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Fecha:</strong> {{ $actividad->fecha->format('d/m/Y') }}
                                </li>
                                <li class="list-group-item"><strong>Lugar:</strong> {{ $actividad->lugar }}</li>
                                <li class="list-group-item"><strong>Modalidad:</strong> {{ $actividad->modalidad }}</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <form action="{{ route('extracurriculares.cancelar', $actividad->id_extracurricular) }}"
                                method="POST" class="d-grid">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Estás seguro de que quieres cancelar tu inscripción?')">
                                    Cancelar Inscripción
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>Aún no te has inscrito a ninguna actividad.</p>
                </div>
            @endforelse
        </div>

        {{-- ======================================================= --}}
        {{-- ========= SECCIÓN ACTIVIDADES DISPONIBLES ========= --}}
        {{-- ======================================================= --}}
        <h2 class="mb-3">Actividades Extracurriculares Disponibles</h2>
        <hr>
        <div class="row row-cols-1 row-cols-md-3 g-4 card-deck">
            @forelse ($extracurricularesDisponibles as $actividad)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $actividad->nombre_act }} ({{ $actividad->tipo }})</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">{{ $actividad->descripcion }}</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Fecha:</strong>
                                    {{ $actividad->fecha->format('d/m/Y') }}</li>
                                <li class="list-group-item"><strong>Lugar:</strong> {{ $actividad->lugar }}</li>
                                <li class="list-group-item">
                                    <strong>Cupos disponibles:</strong>
                                    {{ $actividad->capacidad - $actividad->alumnos()->count() }} /
                                    {{ $actividad->capacidad }}
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <form action="{{ route('extracurriculares.inscribir', $actividad->id_extracurricular) }}"
                                method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-primary">Inscribirme</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No hay nuevas actividades disponibles en este momento.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
