@extends('layouts.encabezados')

@section('title', 'Gestión de Diplomados')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de diplomados</h2>
                <p class="crud-hero-subtitle">Actualización de datos</p>
                <nav class="crud-tabs">
                    <a href="{{ route('admin.diplomados.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('admin.diplomados.index') }}" class="tab active">Listar diplomados</a>
                </nav>
            </header>
            <div class="crud-body">
                <h1>Actualizar Diplomado</h1>
                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif
                <form class="gm-form" method="POST" action="{{ route('admin.diplomados.update', $diplomado) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-section">
                        <div>
                            <label for="nombre">Nombre del Diplomado</label>
                            <input id="nombre" name="nombre" value="{{ old('nombre', $diplomado->nombre) }}" placeholder="Nombre del diplomado" required>
                        </div>
                        <div>
                            <label for="grupo">Grupo</label>
                            <input id="grupo" name="grupo" value="{{ old('grupo', $diplomado->grupo) }}" placeholder="Grupo" required>
                        </div>
                        <div>
                            @php $tipoSel = old('tipo', $diplomado->tipo); @endphp
                            <label for="tipo">Tipo de Diplomado</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="basico" {{ $tipoSel === 'basico' ? 'selected' : '' }}>Básico</option>
                                <option value="intermedio y avanzado" {{ $tipoSel === 'intermedio y avanzado' ? 'selected' : '' }}>Intermedio y avanzado</option>
                            </select>
                        </div>
                        <div>
                            <label for="capacidad">Capacidad de Alumnos</label>
                            <input id="capacidad" type="number" name="capacidad" value="{{ old('capacidad', $diplomado->capacidad) }}" placeholder="Capacidad de alumnos" required>
                        </div>
                        <div>
                            <label for="fecha_inicio">Fecha de Inicio</label>
                            {{-- Se formatea la fecha a 'Y-m-d' para que el input la reconozca --}}
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio', \Carbon\Carbon::parse($diplomado->fecha_inicio)->format('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label for="fecha_fin">Fecha de Fin</label>
                            {{-- Se formatea la fecha a 'Y-m-d' para que el input la reconozca --}}
                            <input type="date" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', \Carbon\Carbon::parse($diplomado->fecha_fin)->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.diplomados.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection