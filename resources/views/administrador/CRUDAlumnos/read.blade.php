{{-- resources/views/administrador/CRUDAlumnos/read.blade.php --}}
<h1>Alumnos</h1>

<p><a href="{{ route('alumnos.create') }}">Nuevo alumno</a></p>

@if(session('ok')) 
  <p style="color:green;">{{ session('ok') }}</p>
@endif

@if($alumnos->count() === 0)
  <p>No hay alumnos registrados.</p>
@else
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>Matrícula</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Diplomado</th>
        <th>Grupo</th>
        <th>Estatus</th>
        <th></th>
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
        <td>{{ $a->num_diplomado }}</td>
        <td>{{ $a->grupo }}</td>
        <td>{{ $a->estatus }}</td>
        <td>
          <a href="{{ route('alumnos.edit', $a) }}">Editar</a>
          <form action="{{ route('alumnos.destroy', $a) }}" method="POST" style="display:inline">
            @csrf @method('DELETE')
            <button type="submit" onclick="return confirm('¿Eliminar alumno y su usuario?')">Eliminar</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <div style="margin-top:12px;">
    {{ $alumnos->links() }}
  </div>
@endif
