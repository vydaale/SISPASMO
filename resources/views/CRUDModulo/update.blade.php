@extends('layouts.encabezados')

@section('title', 'Panel de Control - Actualizar Módulo')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de módulos</h2>
                <p class="crud-hero-subtitle">Actualización</p>

                <nav class="crud-tabs">
                    <a href="{{ route('modulos.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('modulos.index') }}" class="tab active">Listar módulos</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Actualizar Módulo</h1>

                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (session('ok'))
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                <form class="gm-form" method="POST" action="{{ route('modulos.update', $modulo) }}">
                    @csrf
                    @method('PUT')

                    <h3>Datos del módulo</h3>
                    <div>
                        <input type="number" name="numero_modulo"
                            value="{{ old('numero_modulo', $modulo->numero_modulo) }}"
                            placeholder="Número de módulo" required>
                        <input name="nombre_modulo" value="{{ old('nombre_modulo', $modulo->nombre_modulo) }}"
                            placeholder="Nombre del módulo" required>

                        <input name="duracion" value="{{ old('duracion', $modulo->duracion) }}"
                            placeholder="Duración (ej. 40 horas)" required>

                        @php $est = old('estatus', $modulo->estatus); @endphp
                        <select name="estatus" required>
                            <option value="activa" {{ $est === 'activa' ? 'selected' : '' }}>activa</option>
                            <option value="concluida" {{ $est === 'concluida' ? 'selected' : '' }}>concluida</option>
                        </select>

                        <input name="url" value="{{ old('url', $modulo->url) }}"
                            placeholder="URL (opcional)">
                    </div>

                    <div>
                        <textarea name="descripcion" rows="4" placeholder="Descripción" style="grid-column:1 / -1;">{{ old('descripcion', $modulo->descripcion) }}</textarea>
                    </div>

                    <div class="actions">
                        <a href="{{ route('modulos.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection