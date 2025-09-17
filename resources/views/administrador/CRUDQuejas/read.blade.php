@extends('layouts.encabezados')

@section('title', 'Gestión quejas/sugerencias')

@section('content')
    <div class="crud-wrap">
        <div class="crud-card">
            <div class="crud-hero">
                <h1 class="crud-hero-title">Quejas y sugerencias</h1>

                <form method="GET" class="crud-tabs" style="margin-top:10px">
                    <select name="tipo" style="border-radius:999px;padding:10px 14px;border:1px solid rgba(0,0,0,.12)">
                        <option value="">Tipo</option>
                        <option value="queja" @selected(request('tipo')==='queja')>Queja</option>
                        <option value="sugerencia" @selected(request('tipo')==='sugerencia')>Sugerencia</option>
                    </select>
                    <button class="btn btn-primary" type="submit">Filtrar</button>
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
                                <th>ID</th>
                                <th>Usuario</th>
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
                                    <td>#{{ $q->id_queja }}</td>
                                    <td>
                                        @if($q->usuario)
                                            {{ $q->usuario->nombre }} {{ $q->usuario->apellidoP }} ({{ $q->usuario->usuario }})
                                        @else
                                            —
                                        @endif
                                    </td>
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
                                        <a class="btn-ghost" href="{{ route('quejas.edit', $q) }}">Estatus</a>
                                        <form action="{{ route('quejas.destroy', $q) }}" method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button class="btn-danger" onclick="return confirm('¿Eliminar #{{ $q->id_queja }}?')">Eliminar</button>
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