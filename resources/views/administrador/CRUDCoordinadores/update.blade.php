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
                    <div class="form-section">
                        <div>
                            <label for="nombre">Nombre(s)</label>
                            <input id="nombre" name="nombre" value="{{ old('nombre', $coordinador->usuario->nombre) }}"
                                placeholder="Nombre" maxlength="100" required>
                        </div>

                        <div>
                            <label for="apellidoP">Apellido Paterno</label>
                            <input id="apellidoP" name="apellidoP"
                                value="{{ old('apellidoP', $coordinador->usuario->apellidoP) }}"
                                placeholder="Apellido paterno" maxlength="100" required>
                        </div>

                        <div>
                            <label for="apellidoM">Apellido Materno</label>
                            <input id="apellidoM" name="apellidoM"
                                value="{{ old('apellidoM', $coordinador->usuario->apellidoM) }}"
                                placeholder="Apellido materno" maxlength="100" required>
                        </div>

                        <div>
                            <label for="fecha_nac">Fecha de Nacimiento</label>
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

                        <div>
                            @php $generoSel = old('genero', $coordinador->usuario->genero); @endphp
                            <label for="genero">Género</label>
                            <select id="genero" name="genero" required>
                                <option value="">Selecciona un género</option>
                                <option value="M" {{ $generoSel === 'M' ? 'selected' : '' }}>M</option>
                                <option value="F" {{ $generoSel === 'F' ? 'selected' : '' }}>F</option>
                                <option value="Otro" {{ $generoSel === 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        <div>
                            <label for="correo">Correo Electrónico</label>
                            <input id="correo" type="email" name="correo"
                                value="{{ old('correo', $coordinador->usuario->correo) }}" placeholder="Correo"
                                maxlength="100" required>
                        </div>

                        <div>
                            <label for="telefono">Teléfono</label>
                            <input id="telefono" name="telefono"
                                value="{{ old('telefono', $coordinador->usuario->telefono) }}" placeholder="Teléfono"
                                maxlength="20" required>
                        </div>

                        <div>
                            <label for="direccion">Dirección</label>
                            <input id="direccion" name="direccion"
                                value="{{ old('direccion', $coordinador->usuario->direccion) }}" placeholder="Dirección"
                                maxlength="100" required>
                        </div>
                    </div>

                    <h3>Datos de Coordinador</h3>
                    <div class="form-section">
                        <div>
                            <label for="fecha_ingreso">Fecha de Ingreso</label>
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
