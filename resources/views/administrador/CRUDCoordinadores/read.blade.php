{{-- resources/views/administrador/CRUCCoordinadores/read.blade.php --}}
<h1>Coordinadores</h1>

<p><a href="{{ route('coordinadores.create') }}">Nuevo coordinador</a></p>

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif
@if (session('ok'))
    <p style="color:green;">{{ session('ok') }}</p>
@endif

@if ($coordinadores->count() === 0)
    <p>No hay coordinadores registrados.</p>
@else
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Fecha ingreso</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($coordinadores as $c)
                <tr>
                    <td>{{ $c->id_coordinador }}</td>
                    <td>
                        {{ optional($c->usuario)->nombre }}
                        {{ optional($c->usuario)->apellidoP }}
                        {{ optional($c->usuario)->apellidoM }}
                    </td>
                    <td>{{ optional($c->usuario)->usuario }}</td>
                    <td>{{ optional($c->usuario)->correo }}</td>
                    <td>{{ optional($c->usuario)->telefono }}</td>
                    <td>{{ $c->fecha_ingreso }}</td>
                    <td>{{ $c->estatus }}</td>
                    <td>
                        <a href="{{ route('coordinadores.edit', $c) }}">Actualizar</a>

                        <form action="{{ route('coordinadores.destroy', ['coordinador' => $c->id_coordinador]) }}"
                            method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('¿Eliminar al coordinador {{ optional($c->usuario)->nombre }} {{ optional($c->usuario)->apellidoP }}?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:12px;">
        {{ $coordinadores->links() }}
    </div>
@endif
