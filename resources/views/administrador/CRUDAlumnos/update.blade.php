@extends('layouts.encabezados')

@section('title', 'Gestión Alumnos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de alumnos</h2>
                <p class="crud-hero-subtitle">Actualización de datos</p>

                <nav class="crud-tabs">
                    <a href="{{ route('alumnos.create') }}" class="tab">Registrar</a>
                    <a href="{{ route('alumnos.index') }}" class="tab active">Listar alumnos</a>
                </nav>
            </header>

            <div class="crud-body">
                <h1>Actualización de datos</h1>

                @if ($errors->any())
                    <ul class="gm-errors">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(session('ok')) 
                    <div class="gm-ok">{{ session('ok') }}</div>
                @endif

                <form class="gm-form" method="POST" action="{{ route('alumnos.update', $alumno) }}">
                    @csrf
                    @method('PUT')

                    <h3>Datos de Usuario</h3>
                    <div>
                        <input name="nombre" value="{{ old('nombre', $alumno->usuario->nombre) }}" placeholder="Nombre" required>
                        <input name="apellidoP" value="{{ old('apellidoP', $alumno->usuario->apellidoP) }}" placeholder="Apellido paterno" required>
                        <input name="apellidoM" value="{{ old('apellidoM', $alumno->usuario->apellidoM) }}" placeholder="Apellido materno" required>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', \Carbon\Carbon::parse($alumno->usuario->fecha_nac)->format('Y-m-d')) }}" required>                    
                    </div>

                    <div>
                        <input name="usuario" value="{{ old('usuario', $alumno->usuario->usuario) }}" placeholder="Usuario" required>
                        <input type="password" name="pass" placeholder="Nueva contraseña (opcional)">
                        <input type="password" name="pass_confirmation" placeholder="Confirmar nueva contraseña (si la cambias)">
                    </div>

                    <div>
                        @php $generoSel = old('genero', $alumno->usuario->genero); @endphp
                        <select name="genero" required>
                            <option value="">Género</option>
                            <option value="M" {{ $generoSel==='M' ? 'selected' : '' }}>M</option>
                            <option value="F" {{ $generoSel==='F' ? 'selected' : '' }}>F</option>
                            <option value="Otro" {{ $generoSel==='Otro' ? 'selected' : '' }}>Otro</option>
                        </select>

                        <input type="email" name="correo" value="{{ old('correo', $alumno->usuario->correo) }}" placeholder="Correo" required>
                        <input name="telefono" value="{{ old('telefono', $alumno->usuario->telefono) }}" placeholder="Teléfono" required>
                        <input name="direccion" value="{{ old('direccion', $alumno->usuario->direccion) }}" placeholder="Dirección" required>

                        <input type="number" name="id_rol" value="{{ old('id_rol', $alumno->usuario->id_rol) }}" placeholder="ID Rol" required>
                    </div>
                    
                    {{-- El ID de rol usualmente no se edita desde el formulario, por eso lo dejamos como campo oculto --}}
                    <input type="hidden" name="id_rol" value="{{ old('id_rol', $alumno->usuario->id_rol) }}">

                    <h3>Datos de Alumno</h3>
                    <div class="form-section">
                        <div>
                            <label for="matriculaA">Matrícula</label>
                            <input id="matriculaA" name="matriculaA" value="{{ old('matriculaA', $alumno->matriculaA) }}" placeholder="Matrícula" required>
                        </div>
                        <div>
                            <label for="id_diplomado">Diplomado</label>
                            <select name="id_diplomado" id="id_diplomado" required>
                                <option value="">Selecciona un diplomado</option>
                                @foreach($diplomados as $diplomado)
                                    <option value="{{ $diplomado->id_diplomado }}"
                                        {{ old('id_diplomado', $alumno->id_diplomado) == $diplomado->id_diplomado ? 'selected' : '' }}>
                                        {{ $diplomado->nombre }} ({{ $diplomado->grupo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            @php $estatusSel = old('estatus', $alumno->estatus); @endphp
                            <label for="estatus">Estatus</label>
                            <select id="estatus" name="estatus" required>
                                <option value="activo"   {{ $estatusSel==='activo' ? 'selected' : '' }}>Activo</option>
                                <option value="baja"     {{ $estatusSel==='baja' ? 'selected' : '' }}>Baja</option>
                                <option value="egresado" {{ $estatusSel==='egresado' ? 'selected' : '' }}>Egresado</option>
                            </select>
                        </div>
                    </div>

                    <div class="actions">
                        <a href="{{ route('alumnos.index') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection