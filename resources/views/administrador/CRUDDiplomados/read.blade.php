@extends('layouts.encabezados')

@section('title', 'Gestión de Diplomados')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de diplomados</h2>
                <p class="crud-hero-subtitle">Listado</p>
                <nav class="crud-tabs">
                    <a href="{{ route('admin.diplomados.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('admin.diplomados.index') }}" class="tab active">Listar diplomados</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Diplomados</h1>
                @if(session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if($diplomados->isEmpty())
                    <div class="gm-empty">No hay diplomados registrados.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Grupo</th>
                                    <th>Tipo</th>
                                    <th>Capacidad</th>
                                    <th>Fecha de Inicio</th>
                                    <th>Fecha de Fin</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($diplomados as $diplomado)
                                    <tr>
                                        <td>{{ $diplomado->nombre }}</td>
                                        <td>{{ $diplomado->grupo }}</td>
                                        <td>{{ $diplomado->tipo }}</td>
                                        <td>{{ $diplomado->capacidad }}</td>
                                        <td>{{ $diplomado->fecha_inicio }}</td>
                                        <td>{{ $diplomado->fecha_fin }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('admin.diplomados.edit', $diplomado) }}" class="btn btn-ghost">Actualizar</a>
                                                <form action="{{ route('admin.diplomados.destroy', $diplomado) }}" method="POST" onsubmit="return confirm('¿Eliminar este diplomado?')">
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
                        {{ $diplomados->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection