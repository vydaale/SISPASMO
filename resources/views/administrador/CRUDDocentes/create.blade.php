@extends('layouts.encabezados')

@section('title', 'Gestión Docentes')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de docentes</h2>
                <p class="crud-hero-subtitle">Registro</p>

                <nav class="crud-tabs">
                    <a href="{{ route('docentes.create') }}" class="tab active">Registrar</a>
                    <a href="{{ route('docentes.index') }}" class="tab">Listar docentes</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Nuevo Docente</h1>

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

                <form class="gm-form" method="POST" action="{{ route('docentes.store') }}">
                    @csrf

                    <h3>Datos de Usuario</h3>
                    <div class="form-section">
                        <div>
                            <label for="nombre">Nombre(s)</label>
                            <input id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre(s)" required>
                        </div>

                        <div>
                            <label for="apellidoP">Apellido Paterno</label>
                            <input id="apellidoP" name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" required>
                        </div>

                        <div>
                            <label for="apellidoM">Apellido Materno</label>
                            <input id="apellidoM" name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" required>
                        </div>

                        <div>
                            <label for="fecha_nac">Fecha de Nacimiento</label>
                            <input id="fecha_nac" type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>
                        </div>

                        <div>
                            <label for="usuario">Usuario</label>
                            <input id="usuario" name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" required>
                        </div>

                        <div>
                            <label for="pass">Contraseña</label>
                            <input id="pass" type="password" name="pass" placeholder="Contraseña" required>
                        </div>

                        <div>
                            <label for="pass_confirmation">Confirmar Contraseña</label>
                            <input id="pass_confirmation" type="password" name="pass_confirmation" placeholder="Confirmar contraseña" required>
                        </div>

                        <div>
                            <label for="genero">Género</label>
                            <select id="genero" name="genero" required>
                                <option value="">Selecciona un género</option>
                                <option value="M" {{ old('genero')==='M' ? 'selected' : '' }}>M</option>
                                <option value="F" {{ old('genero')==='F' ? 'selected' : '' }}>F</option>
                                <option value="Otro" {{ old('genero')==='Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>

                        <div>
                            <label for="correo">Correo Electrónico</label>
                            <input id="correo" type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" maxlength="100" required>
                        </div>

                        <div>
                            <label for="telefono">Teléfono</label>
                            <input id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" maxlength="10" required>
                        </div>

                        <div>
                            <label for="direccion">Dirección</label>
                            <input id="direccion" name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" maxlength="100" required>
                        </div>
                    </div>

                    <h3>Datos de Docente</h3>
                    <div class="form-section">
                        <div>
                            <label for="matriculaD">Matrícula Docente</label>
                            <input id="matriculaD" name="matriculaD" value="{{ old('matriculaD') }}" placeholder="Matrícula docente" required>
                        </div>

                        <div>
                            <label for="especialidad">Especialidad</label>
                            <input id="especialidad" name="especialidad" value="{{ old('especialidad') }}" placeholder="Especialidad" required>
                        </div>
                        
                        <div>
                            <label for="cedula">Cédula Profesional</label>
                            <input id="cedula" name="cedula" value="{{ old('cedula') }}" placeholder="Cédula profesional" required>
                        </div>
                        
                        <div>
                            <label for="salario">Salario</label>
                            <input id="salario" type="number" name="salario" value="{{ old('salario') }}" placeholder="Salario" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="{{ route('docentes.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection