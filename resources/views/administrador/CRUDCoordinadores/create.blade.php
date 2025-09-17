@extends('layouts.encabezados')

@section('title', 'Gestión Coordinadores')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de coordinadores</h2>
                <p class="crud-hero-subtitle">Registro</p>

                <nav class="crud-tabs">
                    <a href="{{ route('coordinadores.create') }}" class="tab active">Registrar</a>
                    <a href="{{ route('coordinadores.index') }}" class="tab">Listar coordinadores</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Nuevo Coordinador</h1>

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

                <form class="gm-form" method="POST" action="{{ route('coordinadores.store') }}">
                    @csrf

                    <h3>Datos de Usuario</h3>
                    <div>
                        <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" maxlength="100" required>
                        <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" maxlength="100" required>
                        <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" maxlength="100" required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>

                        <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" maxlength="50" required>
                        <input type="password" name="pass" placeholder="Contraseña" required>

                        <select name="genero" required>
                            <option value="">Género</option>
                            <option value="M" {{ old('genero')==='M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ old('genero')==='F' ? 'selected' : '' }}>F</option>
                            <option value="Otro" {{ old('genero')==='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" maxlength="100" required>
                        <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" maxlength="20" required>
                        <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" maxlength="100" required>
                    </div>

                    <h3>Datos de Coordinador</h3>
                    <div>
                        <label>Fecha de ingreso:
                            <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}" required>
                        </label>

                        @php $estatusSel = old('estatus'); @endphp
                        <select name="estatus" required>
                            <option value="">Estatus</option>
                            <option value="activo"   {{ $estatusSel==='activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ $estatusSel==='inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="actions">
                        <a href="{{ route('coordinadores.index') }}" class="btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection