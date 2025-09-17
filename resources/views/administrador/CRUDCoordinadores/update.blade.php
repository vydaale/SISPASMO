@extends('layouts.encabezados')

@section('title', 'Gestión Coordinadores')

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
                <h1>Actualizar Coordinador</h1>

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

                    <h3>Datos de Usuario</h3>
                    <div>
                        <input name="nombre" value="{{ old('nombre', $coordinador->usuario->nombre) }}" placeholder="Nombre" maxlength="100" required>
                        <input name="apellidoP" value="{{ old('apellidoP', $coordinador->usuario->apellidoP) }}" placeholder="Apellido paterno" maxlength="100" required>
                        <input name="apellidoM" value="{{ old('apellidoM', $coordinador->usuario->apellidoM) }}" placeholder="Apellido materno" maxlength="100" required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', $coordinador->usuario->fecha_nac) }}" required>
                    </div>

                    <div>
                        <input name="usuario" value="{{ old('usuario', $coordinador->usuario->usuario) }}" placeholder="Usuario" maxlength="50" required>

                        @php $generoSel = old('genero', $coordinador->usuario->genero); @endphp
                        <select name="genero" required>
                            <option value="">Género</option>
                            <option value="M" {{ $generoSel==='M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ $generoSel==='F' ? 'selected' : '' }}>F</option>
                            <option value="Otro" {{ $generoSel==='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                        <input type="email" name="correo" value="{{ old('correo', $coordinador->usuario->correo) }}" placeholder="Correo" maxlength="100" required>
                        <input name="telefono" value="{{ old('telefono', $coordinador->usuario->telefono) }}" placeholder="Teléfono" maxlength="20" required>
                        <input name="direccion" value="{{ old('direccion', $coordinador->usuario->direccion) }}" placeholder="Dirección" maxlength="100" required>
                    </div>

                    <h3>Datos de Coordinador</h3>
                    <div>
                        <label>Fecha de ingreso:
                            <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', $coordinador->fecha_ingreso) }}" required>
                        </label>

                        @php $estatusSel = old('estatus', $coordinador->estatus); @endphp
                        <select name="estatus" required>
                            <option value="activo"   {{ $estatusSel==='activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ $estatusSel==='inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="actions">
                        <a href="{{ route('coordinadores.index') }}" class="btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection