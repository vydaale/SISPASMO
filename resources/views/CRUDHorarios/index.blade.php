@extends('layouts.encabezados')
@section('title', 'Gestión horarios')

@section('content')
<div class="crud-wrap">
    <div class="crud-card">
        <div class="crud-hero">
            <h2 class="crud-hero-title">Gestión de horarios</h2>
            <p class="crud-hero-subtitle">Listado</p>

            <nav class="crud-tabs">
                <a href="{{ route('admin.horarios.create') }}" class="tab">Registrar</a>
                <a href="{{ route('admin.horarios.index') }}" class="tab active">Listar horarios</a>
            </nav>
        </div>

        <div class="crud-body">
            <h1>Horarios</h1>
            
            {{-- Bloque de mensajes, muestra mensaje de éxito (`success`) de la sesión. --}}
            @if (session('success'))
                <div class="gm-ok">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Bloque de filtros--}}
            <div class="filter-form" style="margin-bottom: 20px;">
                {{-- Importante: El método es GET para que los filtros viajen en la URL --}}
                <form action="{{ route('admin.horarios.index') }}" method="GET" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
                    
                    {{-- 1. Filtro por Diplomado (NUEVO) --}}
                    <div class="form-group">
                        <label for="id_diplomado">Diplomado</label>
                        <select name="id_diplomado" id="id_diplomado" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 150px;">
                            <option value="">Seleccionar diplomado</option>
                            {{-- $diplomados viene del controlador --}}
                            @foreach ($diplomados as $diplomado)
                                <option value="{{ $diplomado->id_diplomado }}" 
                                    {{-- Mantener el valor seleccionado --}}
                                    {{ (isset($filtros['id_diplomado']) && $filtros['id_diplomado'] == $diplomado->id_diplomado) ? 'selected' : '' }}>
                                    {{ $diplomado->nombre }} ({{ $diplomado->grupo }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Filtro por Docente --}}
                    <div class="form-group">
                        <label for="id_docente">Docente</label>
                        <select name="id_docente" id="id_docente" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 150px;">
                            <option value="">Seleccionar docente</option>
                            {{-- $docentes viene del controlador --}}
                            @foreach ($docentes as $docente)
                                <option value="{{ $docente->id_docente }}" 
                                    {{-- Mantener el valor seleccionado --}}
                                    {{ (isset($filtros['id_docente']) && $filtros['id_docente'] == $docente->id_docente) ? 'selected' : '' }}>
                                    {{ $docente->usuario?->nombre }} {{ $docente->usuario?->apellidoP }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- 3. Filtro por Fecha --}}
                    <div class="form-group">
                        <label for="fecha">Fecha</label>
                        <input type="date" name="fecha" id="fecha" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $filtros['fecha'] ?? '' }}">
                    </div>
                    
                    {{-- 4. Filtro por Aula --}}
                    <div class="form-group">
                        <label for="aula">Aula</label>
                        <input type="text" name="aula" id="aula" placeholder="Ej: A-301" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;"
                               value="{{ $filtros['aula'] ?? '' }}">
                    </div>

                    {{-- Botones de acción --}}
                    <button type="submit" class="btn btn-primary" style="padding: 8px 15px;">Filtrar</button>
                    {{-- Botón para limpiar (ir a la URL base sin parámetros) --}}
                    <a href="{{ route('admin.horarios.index') }}" class="btn btn-primary" style="padding: 8px 15px;">Limpiar Filtros</a>
                </form>
            </div>
            {{-- 🚨 FIN DEL BLOQUE DE FILTROS 🚨 --}}


            {{-- Bloque de listado, muestra la tabla si hay datos o un mensaje de vacío. --}}
            @if ($horarios->isEmpty())
                <div class="gm-empty">
                    No hay horarios registrados que coincidan con los filtros.
                </div>
            @else
                <div class="table-responsive">
                    <table class="gm-table">
                        <thead>
                            <tr>
                                <th>Folio</h>
                                <th>Diplomado</th>
                                <th>Módulo</th>
                                <th>Docente</th>
                                <th>Fecha</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Modalidad</th>
                                <th>Aula</th>
                                <th class="th-actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Bloque de datos (bucle), itera sobre la colección paginada de horarios ($horarios). --}}
                            @foreach ($horarios as $horario)
                                <tr>
                                    <td>{{ $horario->id_horario }}</td>
                                    <td>{{ $horario->diplomado?->nombre }} ({{ $horario->diplomado?->grupo }})</td>
                                    <td>{{ $horario->modulo->nombre_modulo }}</td>
                                    <td>{{ $horario->docente?->usuario?->nombre }} {{ $horario->docente?->usuario?->apellidoP }}</td>                                    <td>{{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }}</td>
                                    <td>
                                        @switch($horario->modalidad)
                                            @case('Presencial')
                                                <span class="badge badge-validado">Presencial</span>
                                                @break
                                            @case('Virtual')
                                                <span class="badge badge-pendiente">Virtual</span>
                                                @break
                                            @case('Práctica')
                                                <span class="badge badge-rechazado">Práctica</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $horario->aula }}</td>
                                    <td>
                                        <div class="table-actions">
                                            {{-- Botón de acción, enlace al formulario de edición. --}}
                                            <a href="{{ route('admin.horarios.edit', $horario->id_horario) }}" class="btn btn-ghost">Editar</a>
                                            {{-- Formulario de eliminación, utiliza el método delete y requiere confirmación de js. --}}
                                            <form action="{{ route('admin.horarios.destroy', $horario->id_horario) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este horario?')">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Bloque de Paginación --}}
                <div style="margin-top: 20px;">
                    {{-- Usa appends para incluir los filtros en los enlaces de paginación --}}
                    {{ $horarios->appends($filtros)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection