@extends('layouts.encabezados')

@section('title', 'Gestión queja/sugerencia')

@section('content')
    <div class="crud-wrap">
        <div class="crud-card">
            <div class="crud-hero">
                <h1 class="crud-hero-title">Estatus de #{{ $queja->id_queja }}</h1>
            </div>

            <div class="crud-body">
                @if ($errors->any())
                    <div class="gm-errors">
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="gm-form" method="POST" action="{{ route('quejas.update', $queja) }}">
                    @csrf @method('PUT')

                    <div>
                        <div style="grid-column:1 / -1">
                            <label><strong>Mensaje</strong></label>
                            <div class="gm-empty">{{ $queja->mensaje }}</div>
                        </div>

                        <div>
                            <label for="Estatus"><strong>Estatus</strong></label>
                            <select id="estatus" name="estatus" required>
                                <option value="Pendiente" @selected($queja->estatus === 'Pendiente')>Pendiente</option>
                                <option value="Atendido"  @selected($queja->estatus === 'Atendido')>Atendido</option>
                            </select>
                        </div>


                        <div>
                            <label for="contacto"><strong>Contacto (opcional)</strong></label>
                            <input id="contacto" name="contacto" type="text" value="{{ old('contacto', $queja->contacto) }}">
                        </div>
                    </div>

                    <div class="actions">
                        <a class="btn btn-danger" href="{{ route('quejas.index') }}">Cancelar</a>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection