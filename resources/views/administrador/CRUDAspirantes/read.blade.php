@extends('layouts.encabezados')

@section('title', 'Gestión Aspirantes')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de aspirantes</h2>
                <p class="crud-hero-subtitle">Listado</p>

                <nav class="crud-tabs">
                    <a href="{{ route('aspirantes.index') }}" class="tab active">Listar aspirantes</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Aspirantes</h1>
                @if (session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif
                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                @if ($aspirantes->count() === 0)
                    <div class="gm-empty">No hay aspirantes registrados.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Correo</th>
                                    <th>Interés</th>
                                    <th>Día</th>
                                    <th>Estatus</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aspirantes as $a)
                                    <tr>
                                        <td>
                                            {{ optional($a->usuario)->nombre }}
                                            {{ optional($a->usuario)->apellidoP }}
                                            {{ optional($a->usuario)->apellidoM }}
                                        </td>
                                        <td>{{ optional($a->usuario)->correo }}</td>
                                        <td>{{ $a->interes }}</td>
                                        <td>{{ $a->dia }}</td>
                                        <td>{{ $a->estatus }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="{{ route('aspirantes.edit', $a) }}" class="btn btn-ghost">Actualizar</a>

                                                <form action="{{ route('aspirantes.destroy', $a) }}" method="POST" onsubmit="return confirm('¿Eliminar al aspirante {{ optional($a->usuario)->nombre }} {{ optional($a->usuario)->apellidoP }}?')">
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
                        {{ $aspirantes->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection