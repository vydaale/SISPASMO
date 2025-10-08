@extends('layouts.encabezados')

@section('title', 'Gestión de Horarios')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de Horarios</h2>
                <p class="crud-hero-subtitle">Registro</p>
                <nav class="crud-tabs">
                    <a href="{{ route('admin.horarios.create') }}" class="tab active">Registrar</a>
                    <a href="{{ route('admin.horarios.index') }}" class="tab">Listar Horarios</a>
                </nav>
            </header>
            <div class="crud-body">
                <h1>Nuevo Horario</h1>
                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif
                <form class="gm-form" method="POST" action="{{ route('admin.horarios.store') }}">
                    @csrf
                    <div>
                        <input type="date" name="fecha" value="{{ old('fecha') }}" required>
                        <select name="modalidad" required>
                            <option value="">Modalidad</option>
                            <option value="Presencial" {{ old('modalidad') === 'Presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="Virtual" {{ old('modalidad') === 'Virtual' ? 'selected' : '' }}>Virtual</option>
                            <option value="Práctica" {{ old('modalidad') === 'Práctica' ? 'selected' : '' }}>Práctica</option>
                        </select>
                    </div>
                    <div>
                        <input type="time" name="hora_inicio" value="{{ old('hora_inicio') }}" placeholder="Hora de Inicio" required>
                        <input type="time" name="hora_fin" value="{{ old('hora_fin') }}" placeholder="Hora de Fin" required>
                    </div>
                    <div>
                        <input type="text" name="aula" value="{{ old('aula') }}" placeholder="Aula/Ubicación" required>
                        <select name="id_diplomado" required>
                            <option value="">Diplomado</option>
                            @foreach ($diplomados as $diplomado)
                                <option value="{{ $diplomado->id_diplomado }}" {{ old('id_diplomado') == $diplomado->id_diplomado ? 'selected' : '' }}>{{ $diplomado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="id_modulo" required>
                            <option value="">Módulo</option>
                            @foreach ($modulos as $modulo)
                                <option value="{{ $modulo->id_modulo }}" {{ old('id_modulo') == $modulo->id_modulo ? 'selected' : '' }}>{{ $modulo->nombre_modulo }}</option>
                            @endforeach
                        </select>
                        <select name="id_docente" required>
                            <option value="">Docente</option>
                            @foreach ($docentes as $docente)
                                <option value="{{ $docente->id_docente }}" {{ old('id_docente') == $docente->id_docente ? 'selected' : '' }}>{{ $docente->usuario->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.horarios.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection