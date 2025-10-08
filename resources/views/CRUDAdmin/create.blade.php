@extends('layouts.encabezados')

@section('title', 'Gestión Administradores')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de administradores</h2>
                <p class="crud-hero-subtitle">Registro</p>

                <nav class="crud-tabs">
                    <a href="{{ route('admin.create') }}" class="tab active">Registrar</a>
                    <a href="{{ route('admin.index') }}" class="tab">Listar administradores</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Nuevo Administrador</h1>

                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                <form class="gm-form" method="POST" action="{{ route('admin.store') }}">
                    @csrf

                    <h3>Datos de Usuario</h3>
                    <div>
                        <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" required>
                        <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno"
                            required>
                        <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno"
                            required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                    </div>

                    <div>
                        <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" required>
                        <input type="password" name="pass" placeholder="Contraseña" required>
                        <input type="password" name="pass_confirmation" placeholder="Confirmar contraseña"
                            required>
                    </div>

                    <div>
                        <select name="genero" required>
                            <option value="">Género</option>
                            <option value="M" {{ old('genero') === 'M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ old('genero') === 'F' ? 'selected' : '' }}>F</option>
                            <option value="Otro" {{ old('genero') === 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                        <input type="email" name="correo" value="{{ old('correo') }}"
                            placeholder="Correo" required>
                        <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono"
                            required>
                        <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección"
                            required>
                    </div>

                    <div>
                        <input type="hidden" name="id_rol" value="{{ old('id_rol', 1) }}">
                    </div>


                    <h3>Datos de Administrador</h3>
                    <div>
                        <input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso') }}"
                            placeholder="Fecha de ingreso" required>

                        @php $rolSel = old('rol'); @endphp
                        <select name="rol" required>
                            <option value="">Rol</option>
                            <option value="administrador" {{ $rolSel === 'administrador' ? 'selected' : '' }}>
                                Administrador</option>
                            <option value="superadmin" {{ $rolSel === 'superadmin' ? 'selected' : '' }}>
                                Superadmin</option>
                        </select>

                        @php $estatusSel = old('estatus'); @endphp
                        <select name="estatus" required>
                            <option value="">Estatus</option>
                            <option value="activo" {{ $estatusSel === 'activo' ? 'selected' : '' }}>Activo
                            </option>
                            <option value="inactivo" {{ $estatusSel === 'inactivo' ? 'selected' : '' }}>Inactivo
                            </option>
                        </select>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href="{{ route('admin.index') }}" class="btn btn-danger">Cancelar</a>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection