@extends('layouts.encabezados')

@section('title', 'Panel de Control - Actualizar Actividad')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de extra curriculares</h2>
                <p class="crud-hero-subtitle">Actualización</p>

                <nav class="crud-tabs">
                    <a href="{{ route('extracurricular.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('extracurricular.index') }}" class="tab active">Listar actividades</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Actualizar actividad</h1>

                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                <form class="gm-form" method="POST" action="{{ route('extracurricular.update', $taller) }}">
                    @csrf
                    @method('PUT')

                    <h3>Datos generales</h3>
                    <div>
                        <input name="nombre_act" value="{{ old('nombre_act', $taller->nombre_act) }}"
                            placeholder="Nombre de la actividad" maxlength="50" required>
                        <input name="responsable" value="{{ old('responsable', $taller->responsable) }}"
                            placeholder="Responsable" maxlength="100" required>
                        <input type="date" name="fecha" value="{{ old('fecha', $taller->fecha) }}" required>

                        @php $tipoSel = old('tipo', $taller->tipo); @endphp
                        <select name="tipo" required>
                            <option value="">Tipo</option>
                            <option value="Taller" {{ $tipoSel === 'Taller' ? 'selected' : '' }}>Taller</option>
                            <option value="Practica" {{ $tipoSel === 'Practica' ? 'selected' : '' }}>Práctica
                            </option>
                        </select>

                        <input type="time" name="hora_inicio" step="60"
                            value="{{ old('hora_inicio', substr($taller->hora_inicio, 0, 5)) }}" required>

                        <input type="time" name="hora_fin" step="60"
                            value="{{ old('hora_fin', substr($taller->hora_fin, 0, 5)) }}" required>

                        <input name="lugar" value="{{ old('lugar', $taller->lugar) }}" placeholder="Lugar"
                            maxlength="100" required>

                        @php $modalidadSel = old('modalidad', $taller->modalidad); @endphp
                        <select name="modalidad" required>
                            <option value="">Modalidad</option>
                            <option value="Presencial" {{ $modalidadSel === 'Presencial' ? 'selected' : '' }}>
                                Presencial</option>
                            <option value="Virtual" {{ $modalidadSel === 'Virtual' ? 'selected' : '' }}>Virtual
                            </option>
                        </select>

                        @php $estatusSel = old('estatus', $taller->estatus); @endphp
                        <select name="estatus" required>
                            <option value="">Estatus</option>
                            <option value="Finalizada" {{ $estatusSel === 'Finalizada' ? 'selected' : '' }}>
                                Finalizada</option>
                            <option value="Convocatoria" {{ $estatusSel === 'Convocatoria' ? 'selected' : '' }}>
                                Convocatoria</option>
                            <option value="En proceso" {{ $estatusSel === 'En proceso' ? 'selected' : '' }}>En
                                proceso</option>
                        </select>

                        <input type="number" name="capacidad" value="{{ old('capacidad', $taller->capacidad) }}"
                            placeholder="Capacidad" min="0" required>
                        <input name="material" value="{{ old('material', $taller->material) }}"
                            placeholder="Material requerido" maxlength="150" required>
                        <input name="url" value="{{ old('url', $taller->url) }}" placeholder="URL (opcional)"
                            maxlength="200">
                        <input name="descripcion" value="{{ old('descripcion', $taller->descripcion) }}"
                            placeholder="Descripción (máx. 200)" maxlength="200" required>
                    </div>

                    <div class="actions">
                        <a href="{{ route('extracurricular.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection