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
                    <div>
                        <input name="nombre" value="{{ old('nombre') }}" placeholder="Nombre" required>
                        <input name="apellidoP" value="{{ old('apellidoP') }}" placeholder="Apellido paterno" required>
                        <input name="apellidoM" value="{{ old('apellidoM') }}" placeholder="Apellido materno" required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" required>

                        <input name="usuario" value="{{ old('usuario') }}" placeholder="Usuario" required>

                        <input type="password" name="pass" placeholder="Contraseña" required>
                        <input type="password" name="pass_confirmation" placeholder="Confirmar contraseña" required>

                        <select name="genero" required>
                            <option value="">Género</option>
                            <option value="M" {{ old('genero')==='M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ old('genero')==='F' ? 'selected' : '' }}>F</option>
                            <option value="Otro" {{ old('genero')==='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="Correo" required>
                        <input name="telefono" value="{{ old('telefono') }}" placeholder="Teléfono" required>
                        <input name="direccion" value="{{ old('direccion') }}" placeholder="Dirección" required>

                        <input type="number" name="id_rol" value="{{ old('id_rol') }}" placeholder="ID Rol (docente)" required>
                    </div>

                    <h3>Datos de Docente</h3>
                    <div>
                        <input name="matriculaD" value="{{ old('matriculaD') }}" placeholder="Matrícula docente" required>
                        <input name="especialidad" value="{{ old('especialidad') }}" placeholder="Especialidad" required>
                        <input name="cedula" value="{{ old('cedula') }}" placeholder="Cédula profesional" required>
                        <input type="number" name="salario" value="{{ old('salario') }}" placeholder="Salario" step="0.01" min="0" required>
                    </div>

                    <div class="actions">
                        <a href="{{ route('docentes.index') }}" class="btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection