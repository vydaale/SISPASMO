@extends('layouts.encabezados')

@section('title', 'Panel de Control - Citas')

@section('content')
    <div class="crud-wrap">
      <div class="crud-card">
        <div class="crud-hero">
          <h1 class="crud-hero-title">Citas</h1>
          <p class="crud-hero-subtitle">Listado</p>
          <form method="GET" class="filter-forma">
              <select name="estatus" onchange="this.form.submit()" class="filter-selectt">
                  <option value="">Todas</option>
                  @foreach(['Pendiente','Aprobada','Concluida','Cancelada'] as $st)
                      <option value="{{ $st }}" @selected(request('estatus')===$st)>{{ $st }}</option>
                  @endforeach
              </select>
          </form>
        </div>

        <div class="crud-body">
          @if(session('ok')) <div class="gm-ok">{{ session('ok') }}</div> @endif
          @if($errors->any())
            <div class="gm-errors"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
          @endif

          <div class="table-responsive">
            <table class="gm-table">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Hora</th>
                  <th>Estatus</th>
                  <th>Aspirante</th>
                  <th>Coordinador</th>
                  <th>Lugar</th>
                  <th class="th-actions">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($citas as $c)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($c->fecha_cita)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($c->hora_cita)->format('H:i') }}</td>
                    <td>{{ $c->estatus }}</td>
                    <td>
                      @if($c->aspirante && $c->aspirante->usuario)
                        {{ $c->aspirante->usuario->nombre }} {{ $c->aspirante->usuario->apellidoP }}
                      @else — @endif
                    </td>
                    <td>
                      @if($c->coordinador && $c->coordinador->usuario)
                        {{ $c->coordinador->usuario->nombre }} {{ $c->coordinador->usuario->apellidoP }}
                      @else — @endif
                    </td>
                    <td>{{ $c->lugar }}</td>
                    <td>
                      <div class="table-actions">
                        @foreach(['Pendiente','Aprobada','Concluida','Cancelada'] as $st)
                          @if($st !== $c->estatus)
                            <form method="POST" action="{{ route('admin.citas.updateStatus',$c) }}">
                              @csrf @method('PATCH')
                              <input type="hidden" name="estatus" value="{{ $st }}">
                              <button class="btn btn-ghost" title="Cambiar a {{ $st }}">{{ $st }}</button>
                            </form>
                          @endif
                        @endforeach
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7">Sin citas.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="pager">{{ $citas->links() }}</div>
        </div>
      </div>
    </div>
@endsection