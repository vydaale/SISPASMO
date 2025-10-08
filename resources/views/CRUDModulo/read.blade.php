@extends('layouts.encabezados')

@section('title', 'Panel de Control - Listar Módulos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de módulos</h2>
                <p class="crud-hero-subtitle">Listado</p>

                <nav class="crud-tabs">
                    <a href="{{ route('modulos.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('modulos.index') }}" class="tab active">Listar módulos</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Módulos</h1>

                @if (session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if ($modulos->count() === 0)
                    <div class="gm-empty">No hay módulos registrados.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    <th>Duración</th>
                                    <th>Estatus</th>
                                    <th>URL</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modulos as $m)
                                    <tr>
                                        <td>{{ $m->numero_modulo }}</td>
                                        <td>{{ $m->nombre_modulo }}</td>
                                        <td>{{ $m->duracion }}</td>
                                        <td>{{ $m->estatus }}</td>
                                        <td>
                                            @if(!empty($m->url))
                                                <a href="{{ $m->url }}" target="_blank" rel="noopener" class="btn btn-ghost">Ver</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('modulos.edit', $m) }}" class="btn btn-ghost">Actualizar</a>

                                                <form action="{{ route('modulos.destroy', $m) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('¿Eliminar el módulo {{ $m->nombre_modulo }}?')">
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
                        {{ $modulos->links() }}
                    </div>
                @endif

            </div>
        </section>
    </div>
@endsection