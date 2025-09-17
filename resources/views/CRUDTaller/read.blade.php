@extends('layouts.encabezados')

@section('title', 'Gestión Talleres/Prácticas')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de extra curriculares</h2>
                <p class="crud-hero-subtitle">Listado</p>

                <nav class="crud-tabs">
                    <a href="{{ route('extracurricular.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('extracurricular.index') }}" class="tab active">Listar actividades</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Actividades extra curriculares</h1>

                <p><a href="{{ route('extracurricular.create') }}" class="btn btn-primary">Nueva actividad</a></p>

                @if(session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif
                @if(session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if($talleres->count() === 0)
                    <div class="gm-empty">No hay actividades registradas.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Responsable</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Lugar</th>
                                    <th>Modalidad</th>
                                    <th>Estatus</th>
                                    <th>Capacidad</th>
                                    <th>Material</th>
                                    <th>URL</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($talleres as $e)
                                    <tr>
                                        <td>{{ $e->nombre_act }}</td>
                                        <td>{{ $e->responsable }}</td>
                                        <td>{{ $e->fecha }}</td>
                                        <td>{{ $e->tipo }}</td>
                                        <td>{{ $e->hora_inicio }}</td>
                                        <td>{{ $e->hora_fin }}</td>
                                        <td>{{ $e->lugar }}</td>
                                        <td>{{ $e->modalidad }}</td>
                                        <td>{{ $e->estatus }}</td>
                                        <td>{{ $e->capacidad }}</td>
                                        <td>{{ $e->material }}</td>
                                        <td>
                                            @if(!empty($e->url))
                                                <a href="{{ $e->url }}" target="_blank" rel="noopener">Abrir</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('extracurricular.edit', $e) }}" class="btn-ghost">Actualizar</a>

                                                <form action="{{ route('extracurricular.destroy', $e) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('¿Eliminar la actividad {{ $e->nombre_act }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pager">
                        {{ $talleres->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection