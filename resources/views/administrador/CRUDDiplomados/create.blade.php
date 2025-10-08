@extends('layouts.encabezados')

@section('title', 'Gestión de Diplomados')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de diplomados</h2>
                <p class="crud-hero-subtitle">Registro</p>
                <nav class="crud-tabs">
                    <a href="{{ route('admin.diplomados.create') }}" class="tab active">Registrar</a>
                    <a href="{{ route('admin.diplomados.index') }}" class="tab">Listar diplomados</a>
                </nav>
            </header>
            <div class="crud-body">
                <h1>Nuevo Diplomado</h1>
                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif
                <form class="gm-form" method="POST" action="{{ route('admin.diplomados.store') }}">
                    @csrf
                    <div>
                        <input name="nombre" value="{{ old('nombre') }}" placeholder="Diplomado: #" required>
                        <input name="grupo" value="{{ old('grupo') }}" placeholder="Grupo" required>
                    </div>
                    <div>
                        <select name="tipo" required>
                            <option value="">Tipo de diplomado</option>
                            <option value="basico" {{ old('tipo') === 'Basico' ? 'selected' : '' }}>Básico</option>
                            <option value="intermedio" {{ old('tipo') === 'Intermedio' ? 'selected' : '' }}>Intermedio y avanzado</option>
                        </select>
                        <input type="number" name="capacidad" value="{{ old('capacidad') }}" placeholder="Capacidad de alumnos" required>
                    </div>
                    <div>
                        <label for="fecha_inicio">Fecha de Inicio:</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required>
                    </div>
                    <div>
                        <label for="fecha_fin">Fecha de Fin:</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}" required>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.diplomados.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection