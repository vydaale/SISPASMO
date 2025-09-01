{{-- resources/views/administrador/CRUDAspirantes/read.blade.php --}}
<h1>Aspirantes</h1>

<p><a href="{{ route('aspirantes.create') }}">Nuevo aspirante</a></p>

@if (session('success'))
  <p style="color:green;">{{ session('success') }}</p>
@endif
@if (session('ok'))
  <p style="color:green;">{{ session('ok') }}</p>
@endif

@if ($aspirantes->count() === 0)
  <p>No hay aspirantes registrados.</p>
@else
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Interés</th>
        <th>Día</th>
        <th>Estatus</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    @foreach ($aspirantes as $a)
      <tr>
        <td>{{ $a->id_aspirante }}</td>
        <td>
          {{ optional($a->usuario)->nombre }}
          {{ optional($a->usuario)->apellidoP }}
          {{ optional($a->usuario)->apellidoM }}
        </td>
        <td>{{ optional($a->usuario)->correo }}</td>
        <td>{{ $a->interes }}</td>
        <td>
          {{-- muestra la fecha tal cual; si quieres formatear:
               \Illuminate\Support\Carbon::parse($a->dia)->format('d/m/Y') --}}
          {{ $a->dia }}
        </td>
        <td>{{ $a->estatus }}</td>
        <td>
          <a href="{{ route('aspirantes.edit', $a) }}">Actualizar</a>

          <form action="{{ route('aspirantes.destroy', $a) }}"
                method="POST"
                style="display:inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('¿Eliminar al aspirante {{ optional($a->usuario)->nombre }} {{ optional($a->usuario)->apellidoP }}?')">
              Eliminar
            </button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <div style="margin-top:12px;">
    {{ $aspirantes->links() }}
  </div>
@endif
