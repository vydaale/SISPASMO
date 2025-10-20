@extends('layouts.encabezados')

@section('title', 'Gestión quejas/sugerencias')

@section('content')
    <div class="crud-wrap">
        <div class="crud-card">
         <div class="crud-hero">
                <h1 class="crud-hero-title">Quejas y sugerencias</h1>
                <form method="GET" class="filter-forma"> 
                    <select name="tipo" class="filter-selectt">
                        <option value="">Tipo</option>
                        <option value="queja" @selected(request('tipo')==='queja')>Queja</option>
                        <option value="sugerencia" @selected(request('tipo')==='sugerencia')>Sugerencia</option>
                    </select>
                    <button class="submit-button" type="submit">Filtrar</button>
                </form>
            </div>

            <div class="crud-body">
                @if (session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="gm-table">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Mensaje</th>
                                <th>Contacto</th>
                                <th>Estatus</th>
                                <th class="th-actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($quejas as $q)
                                <tr>

                                    <td style="text-transform:capitalize">{{ $q->tipo }}</td>
                                    <td>{{ Str::limit($q->mensaje, 80) }}</td>
                                    <td>{{ $q->contacto ?: '—' }}</td>
                                    <td>
                                        @if($q->estatus === 'Atendido')
                                            <span style="color:#065f46;font-weight:800">Atendido</span>
                                        @else
                                            <span style="color:#b45309;font-weight:800">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="table-actions">
                                        <a class="btn btn-ghost" href="{{ route('quejas.edit', $q) }}">Actualizar</a>
                                        <form action="{{ route('quejas.destroy', $q) }}" method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger" onclick="return confirm('¿Eliminar #{{ $q->id_queja }}?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Sin resultados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pager">{{ $quejas->links() }}</div>
            </div>
        </div>
    </div>
@endsection