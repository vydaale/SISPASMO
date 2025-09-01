{{-- resources/views/administrador/CRUDDocentes/read.blade.php --}}
<h1>Docentes</h1>

<p><a href="{{ route('docentes.create') }}">Nuevo docente</a></p>

@if (session('success'))
  <p style="color:green;">{{ session('success') }}</p>
@endif
@if (session('ok'))
  <p style="color:green;">{{ session('ok') }}</p>
@endif

@if ($docentes->count() === 0)
  <p>No hay docentes registrados.</p>
@else
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Matrícula</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Especialidad</th>
        <th>Cédula</th>
        <th>Salario</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    @foreach ($docentes as $d)
      <tr>
        <td>{{ $d->id_docente }}</td>
        <td>{{ $d->matriculaD }}</td>
        <td>
          {{ optional($d->usuario)->nombre }}
          {{ optional($d->usuario)->apellidoP }}
          {{ optional($d->usuario)->apellidoM }}
        </td>
        <td>{{ optional($d->usuario)->correo }}</td>
        <td>{{ $d->especialidad }}</td>
        <td>{{ $d->cedula }}</td>
        <td>
          {{-- muestra con dos decimales si viene numérico --}}
          @php
            $sal = $d->salario;
            echo is_numeric($sal) ? number_format($sal, 2) : $sal;
          @endphp
        </td>
        <td>
          <a href="{{ route('docentes.edit', $d) }}">Actualizar</a>

          <form action="{{ route('docentes.destroy', $d) }}"
                method="POST"
                style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('¿Eliminar este docente y su usuario asociado?')">
              Eliminar
            </button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <div style="margin-top:12px;">
    {{ $docentes->links() }}
  </div>
@endif
