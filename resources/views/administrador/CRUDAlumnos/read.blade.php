@extends('layouts.encabezados')

@section('title', 'Gestión Alumnos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de alumnos</h2>
                <p class="crud-hero-subtitle">Listado</p>

                <nav class="crud-tabs">
                    <a href="{{ route('alumnos.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('alumnos.index') }}" class="tab active">Listar alumnos</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Alumnos</h1>

                @if(session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if($alumnos->count() === 0)
                    <div class="gm-empty">No hay alumnos registrados.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Diplomado</th> {{-- Este encabezado de columna no necesita cambio --}}
                                    <th>Grupo</th> {{-- Este encabezado de columna no necesita cambio --}}
                                    <th>Estatus</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alumnos as $a)
                                    <tr>
                                        <td>{{ $a->matriculaA }}</td>
                                        <td>
                                            {{ optional($a->usuario)->nombre }}
                                            {{ optional($a->usuario)->apellidoP }}
                                            {{ optional($a->usuario)->apellidoM }}
                                        </td>
                                        <td>{{ optional($a->usuario)->correo }}</td>
                                        {{-- ---------------------------------------------------------------- --}}
                                        {{-- CAMBIO AQUÍ: Usamos la relación `diplomado` para acceder a los datos --}}
                                        {{-- ---------------------------------------------------------------- --}}
                                        <td>{{ optional($a->diplomado)->nombre }}</td>
                                        <td>{{ optional($a->diplomado)->grupo }}</td>
                                        {{-- ---------------------------------------------------------------- --}}
                                        {{-- FIN DEL CAMBIO --}}
                                        {{-- ---------------------------------------------------------------- --}}
                                        <td>{{ $a->estatus }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('alumnos.edit', $a) }}" class="btn-ghost">Editar</a>

                                                <form action="{{ route('alumnos.destroy', $a) }}" method="POST" onsubmit="return confirm('¿Eliminar alumno y su usuario?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="crud-toolbar">
                        <a href="{{ route('alumnos.create') }}" class="btn btn-primary">Nuevo alumno</a>
                    </div>

                    <div class="pager">
                        {{ $alumnos->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection