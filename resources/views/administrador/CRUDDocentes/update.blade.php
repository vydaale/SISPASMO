@extends('layouts.encabezados')

@section('title', 'Gestión Docentes')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de docentes</h2>
                <p class="crud-hero-subtitle">Actualización</p>

                <nav class="crud-tabs">
                    <a href="{{ route('docentes.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('docentes.index') }}" class="tab active">Listar docentes</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Actualizar Docente</h1>

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

                <form class="gm-form" method="POST" action="{{ route('docentes.update', $docente) }}">
                    @csrf
                    @method('PUT')

                    <h3>Datos de Usuario</h3>
                    <div>
                        <input name="nombre" value="{{ old('nombre', $docente->usuario->nombre) }}" placeholder="Nombre" required>
                        <input name="apellidoP" value="{{ old('apellidoP', $docente->usuario->apellidoP) }}" placeholder="Apellido paterno" required>
                        <input name="apellidoM" value="{{ old('apellidoM', $docente->usuario->apellidoM) }}" placeholder="Apellido materno" required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', $docente->usuario->fecha_nac) }}" required>
                    </div>

                    <div>
                        <input name="usuario" value="{{ old('usuario', $docente->usuario->usuario) }}" placeholder="Usuario" required>

                        <input type="password" name="pass" placeholder="Nueva contraseña (opcional)">
                        <input type="password" name="pass_confirmation" placeholder="Confirmar nueva contraseña (si la cambias)">
                    </div>

                    <div>
                        @php $generoSel = old('genero', $docente->usuario->genero); @endphp
                        <select name="genero" required>
                            <option value="">Género</option>
                            <option value="M" {{ $generoSel==='M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ $generoSel==='F' ? 'selected' : '' }}>F</option>
                            <option value="Otro" {{ $generoSel==='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                        <input type="email" name="correo" value="{{ old('correo', $docente->usuario->correo) }}" placeholder="Correo" required>
                        <input name="telefono" value="{{ old('telefono', $docente->usuario->telefono) }}" placeholder="Teléfono" required>
                        <input name="direccion" value="{{ old('direccion', $docente->usuario->direccion) }}" placeholder="Dirección" required>

                        <input type="number" name="id_rol" value="{{ old('id_rol', $docente->usuario->id_rol) }}" placeholder="ID Rol (docente)" required>
                    </div>

                    <h3>Datos de Docente</h3>
                    <div>
                        <input name="matriculaD" value="{{ old('matriculaD', $docente->matriculaD) }}" placeholder="Matrícula docente" required>
                        <input name="especialidad" value="{{ old('especialidad', $docente->especialidad) }}" placeholder="Especialidad" required>
                        <input name="cedula" value="{{ old('cedula', $docente->cedula) }}" placeholder="Cédula profesional" required>
                        <input type="number" step="0.01" min="0" name="salario" value="{{ old('salario', $docente->salario) }}" placeholder="Salario" required>
                    </div>

                    <div class="actions">
                        <a href="{{ route('docentes.index') }}" class="btn-ghost">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection