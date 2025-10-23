@extends('layouts.encabezados')
@section('title', 'Gestión coordinadores')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de coordinadores</h2>
                <p class="crud-hero-subtitle">Actualización</p>
                <nav class="crud-tabs">
                    <a href="{{ route('coordinadores.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('coordinadores.index') }}" class="tab active">Listar coordinadores</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Actualizar coordinador</h1>

                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif

                <form class="gm-form" method="POST" action="{{ route('coordinadores.update', $coordinador) }}">
                    @csrf
                    @method('PUT')

                    <h3>Datos de usuario</h3>
                    <div>
                        <input name="nombre" value="{{ old('nombre', $coordinador->usuario->nombre) }}" placeholder="Nombre" maxlength="100" required>
                        <input name="apellidoP" value="{{ old('apellidoP', $coordinador->usuario->apellidoP) }}" placeholder="Apellido paterno" maxlength="100" required>
                        <input name="apellidoM" value="{{ old('apellidoM', $coordinador->usuario->apellidoM) }}" placeholder="Apellido materno" maxlength="100" required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', \Carbon\Carbon::parse($coordinador->usuario->fecha_nac)->format('Y-m-d')) }}" required>                    
                    </div>

                        <div>
                            <label for="fecha_nac">Fecha de nacimiento</label>
                            <input id="fecha_nac" type="date" name="fecha_nac"
                                value="{{ old('fecha_nac', \Carbon\Carbon::parse($coordinador->usuario->fecha_nac)->format('Y-m-d')) }}"
                                required>
                        </div>

                        <div>
                            <label for="usuario">Usuario</label>
                            <input id="usuario" name="usuario"
                                value="{{ old('usuario', $coordinador->usuario->usuario) }}" placeholder="Usuario"
                                maxlength="50" required>
                        </div>

                        <input type="email" name="correo" value="{{ old('correo', $coordinador->usuario->correo) }}" placeholder="Correo" maxlength="100" required>
                        <input name="telefono" value="{{ old('telefono', $coordinador->usuario->telefono) }}" placeholder="Teléfono" maxlength="10" required>
                        <input name="direccion" value="{{ old('direccion', $coordinador->usuario->direccion) }}" placeholder="Dirección" maxlength="100" required>
                    </div>

                    <h3>Datos de Coordinador</h3>
                    <div class="form-section">
                        <div>
                            <label for="fecha_ingreso">Fecha de ingreso</label>
                            <input id="fecha_ingreso" type="date" name="fecha_ingreso"
                                value="{{ old('fecha_ingreso', $coordinador->fecha_ingreso) }}" required>
                        </div>

                        <div>
                            @php $estatusSel = old('estatus', $coordinador->estatus); @endphp
                            <label for="estatus">Estatus</label>
                            <select id="estatus" name="estatus" required>
                                <option value="">Selecciona un estatus</option>
                                <option value="activo" {{ $estatusSel === 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ $estatusSel === 'inactivo' ? 'selected' : '' }}>Inactivo
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="{{ route('coordinadores.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection
