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
          <div class="form-section">

            {{--
              NOTA: El campo "numero_modulo" se ha quitado del formulario.
              Para que sea autoincrementable, debes calcularlo en tu ModuloController
              en el método `store`, justo antes de guardar el nuevo módulo.
              Por ejemplo:
              $ultimoModulo = Modulo::where('id_diplomado', $request->id_diplomado)->max('numero_modulo');
              $request['numero_modulo'] = $ultimoModulo + 1;
            --}}

            <div>
              <label for="nombre_modulo">Nombre del Módulo</label>
              <input id="nombre_modulo" name="nombre_modulo" value="{{ old('nombre_modulo') }}" placeholder="Nombre del módulo" maxlength="100" required>
            </div>
            <div>
              <label for="duracion">Duración</label>
              <input id="duracion" name="duracion" value="{{ old('duracion') }}" placeholder="Ej: 40 horas / 12 semanas" maxlength="50" required>
            </div>
            <div>
              @php $estatusSel = old('estatus'); @endphp
              <label for="estatus">Estatus</label>
              <select id="estatus" name="estatus" required>
                <option value="">Selecciona un estatus</option>
                <option value="activa"    {{ $estatusSel === 'activa' ? 'selected' : '' }}>Activa</option>
                <option value="concluida" {{ $estatusSel === 'concluida' ? 'selected' : '' }}>Concluida</option>
              </select>
            </div>
            <div>
              <label for="url">URL del Módulo (opcional)</label>
              <input id="url" type="url" name="url" value="{{ old('url') }}" placeholder="URL del módulo (opcional)" maxlength="200">
            </div>
            <div>
              <label for="descripcion">Descripción del Módulo</label>
              <textarea id="descripcion" name="descripcion" rows="4" placeholder="Descripción del módulo" required>{{ old('descripcion') }}</textarea>
            </div>
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