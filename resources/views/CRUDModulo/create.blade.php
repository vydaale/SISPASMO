@extends('layouts.encabezados')

@section('title', 'Gestión Módulos')

@section('content')
  <div class="crud-wrap">
    <section class="crud-card">
      <header class="crud-hero">
        <h2 class="crud-hero-title">Gestión de módulos</h2>
        <p class="crud-hero-subtitle">Registro</p>

        <nav class="crud-tabs">
          <a href="{{ route('modulos.create') }}" class="tab active">Registrar</a>
          <a href="{{ route('modulos.index') }}" class="tab">Listar módulos</a>
        </nav>
      </header>

      <div class="crud-body">
        <h1>Nuevo Módulo</h1>

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

        <form class="gm-form" method="POST" action="{{ route('modulos.store') }}">
          @csrf

          <h3>Datos del módulo</h3>
          <div>
            <input type="number" name="numero_modulo" value="{{ old('numero_modulo') }}" placeholder="Número de módulo" required>
            <input name="nombre_modulo" value="{{ old('nombre_modulo') }}" placeholder="Nombre del módulo" maxlength="100" required>
            <input name="duracion" value="{{ old('duracion') }}" placeholder="Duración (p. ej. 40 horas / 12 semanas)" maxlength="50" required>
            
            @php $estatusSel = old('estatus'); @endphp
            <select name="estatus" required>
              <option value="">Estatus</option>
              <option value="activa"    {{ $estatusSel === 'activa' ? 'selected' : '' }}>Activa</option>
              <option value="concluida" {{ $estatusSel === 'concluida' ? 'selected' : '' }}>Concluida</option>
            </select>
            
            <input type="url" name="url" value="{{ old('url') }}" placeholder="URL del módulo (opcional)" maxlength="200">
          </div>

          <div>
            <textarea name="descripcion" rows="4" placeholder="Descripción del módulo" required>{{ old('descripcion') }}</textarea>
          </div>

          <div class="actions">
            <a href="{{ route('modulos.index') }}" class="btn btn-danger">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </section>
  </div>
@endsection