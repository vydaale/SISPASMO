<div class="table-responsive">
  <table class="gm-table">
    <thead>
      <tr>
        <th>Edad</th>
        <th>Matrícula</th>
        <th>Nombre</th>
        <th>Grupo</th>
      </tr>
    </thead>
    <tbody>
      @forelse($alumnos as $al)
        <tr>
          <td>{{ $al->edad }}</td>
          <td>{{ $al->matriculaA }}</td>
          <td>{{ $al->apellidoP }} {{ $al->apellidoM }} {{ $al->nombre }}</td>
          <td>{{ $al->grupo }}</td>
        </tr>
      @empty
        <tr><td colspan="4">Sin registros.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="pager">
  {{ $alumnos->withPath(route('admin.reportes.alumnosEdad.table'))->links() }}
</div>
