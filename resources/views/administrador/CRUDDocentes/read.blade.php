@extends('layouts.encabezados')

@section('title', 'Panel de Control - Listar Docentes')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de docentes</h2>
                <p class="crud-hero-subtitle">Listado</p>

                <nav class="crud-tabs">
                    <a href="{{ route('docentes.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('docentes.index') }}" class="tab active">Listar docentes</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Docentes</h1>

                @if (session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if ($docentes->count() === 0)
                    <div class="gm-empty">No hay docentes registrados.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Matrícula</th>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Especialidad</th>
                                    <th>Cédula</th>
                                    <th>Salario</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($docentes as $d)
                                    <tr>
                                        <td>{{ $d->matriculaD }}</td>
                                        <td>
                                            {{ optional($d->usuario)->nombre }}
                                            {{ optional($d->usuario)->apellidoP }}
                                            {{ optional($d->usuario)->apellidoM }}
                                        </td>
                                        <td>{{ optional($d->usuario)->correo }}</td>
                                        <td>{{ $d->especialidad }}</td>
                                        <td>{{ $d->cedula }}</td>
                                        <td>{{ is_numeric($d->salario) ? number_format($d->salario, 2) : $d->salario }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('docentes.edit', $d) }}" class="btn btn-ghost">Actualizar</a>

                                                <form action="{{ route('docentes.destroy', $d) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('¿Eliminar este docente y su usuario asociado?')">
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
                        {{ $docentes->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection