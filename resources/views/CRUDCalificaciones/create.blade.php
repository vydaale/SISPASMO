@extends('layouts.encabezadosDoc')

@section('title', 'Gestión Calificaciones')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de calificaciones</h2>
                <p class="crud-hero-subtitle">Captura</p>

                <nav class="crud-tabs">
                    <a href="{{ route('calif.create') }}" class="tab active">Registrar</a>
                    <a href="{{ route('calif.docente.index') }}" class="tab">Mis calificaciones</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Nueva calificación</h1>

                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                <form class="gm-form" method="POST" action="{{ route('calif.store') }}">
                    @csrf

                    <h3>Datos</h3>

                    <div class="grid-2">
                        <div>
                            <label for="id_alumno">Alumno</label>
                            <select id="id_alumno" name="id_alumno" required>
                                <option value="">Selecciona un alumno</option>
                                @foreach($alumnos as $a)
                                    @php
                                        $nombre = optional($a->usuario)->nombre.' '.optional($a->usuario)->apellidoP.' '.optional($a->usuario)->apellidoM;
                                        $nombre = trim($nombre) ?: ('Alumno #'.$a->id_alumno);
                                    @endphp
                                    <option value="{{ $a->id_alumno }}" {{ old('id_alumno')==$a->id_alumno?'selected':'' }}>
                                        {{ $nombre }} — Grupo: {{ $a->diplomado->grupo }} — Diplomado: {{ $a->diplomado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_alumno') <small class="gm-error">{{ $message }}</small> @enderror
                        </div>

                        <div>
                            <label for="id_modulo">Módulo</label>
                            <select id="id_modulo" name="id_modulo" required>
                                <option value="">Selecciona un módulo</option>
                                @foreach($modulos as $m)
                                    <option value="{{ $m->id_modulo }}" {{ old('id_modulo')==$m->id_modulo?'selected':'' }}>
                                        Mód. {{ $m->numero_modulo }} — {{ $m->nombre_modulo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_modulo') <small class="gm-error">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="grid-2">
                        <div>
                            <label for="tipo">Tipo</label>
                            <input list="tipos" id="tipo" name="tipo" value="{{ old('tipo') }}" placeholder="Parcial 1, Parcial 2, Final, Práctica…" required>
                            <datalist id="tipos">
                                <option value="Parcial 1"></option>
                                <option value="Parcial 2"></option>
                                <option value="Final"></option>
                                <option value="Práctica"></option>
                                <option value="Taller"></option>
                            </datalist>
                            @error('tipo') <small class="gm-error">{{ $message }}</small> @enderror
                        </div>

                        <div>
                            <label for="calificacion">Calificación</label>
                            <input type="number" step="0.01" min="0" max="100" id="calificacion" name="calificacion" value="{{ old('calificacion') }}" placeholder="0 - 100" required>
                            @error('calificacion') <small class="gm-error">{{ $message }}</small> @enderror
                            <small class="gm-help">Rango: 0 a 100. Usa punto decimal (ej. 89.50).</small>
                        </div>
                    </div>

                    <div>
                        <label for="observacion">Observación (opcional)</label>
                        <textarea id="observacion" name="observacion" rows="3" placeholder="Comentarios, evidencias, correcciones…">{{ old('observacion') }}</textarea>
                        @error('observacion') <small class="gm-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="actions">
                        <a href="{{ route('calif.docente.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
  @vite('resources/js/calificacion.js')
@endpush