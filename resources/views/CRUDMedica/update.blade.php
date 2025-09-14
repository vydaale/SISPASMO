{{-- resources/views/CRUDMedica/update_mine.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Editar mi ficha médica</title>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/crud.css')
    @vite('resources/css/dashboard.css')
    @vite(['resources/js/dashboard.js'])
</head>

<body>

    <header class="site-header">
        <div class="header-container">
            <div class="logo">
                <img src="{{ asset('images/logoprincipal.png') }}" alt="Grupo Morelos" />
                <span>GRUPO MORELOS</span>
            </div>
            <nav>
                <ul class="nav-links">
                    <li>
                        <form method="POST" action="{{ route('alumno.logout') }}">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar
                                sesión</a>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    @php
        $alerg = old('alergias', $ficha->alergias?->toArray() ?? []);
        $enfs = old('enfermedades', $ficha->enfermedades?->toArray() ?? []);
        $cont = old('contacto', $ficha->contacto?->toArray() ?? []);
    @endphp

    <main class="content">
        <div class="crud-wrap">
            <section class="crud-card">
                <header class="crud-hero">
                    <h2 class="crud-hero-title">Mi ficha médica</h2>
                    <p class="crud-hero-subtitle">Edición</p>

                    <nav class="crud-tabs">
                        <a href="{{ route('mi_ficha.show') }}" class="tab">Ver</a>
                        <a href="{{ route('mi_ficha.edit') }}" class="tab active">Editar</a>
                    </nav>
                </header>

                <div class="crud-body">
                    <h1>Editar ficha médica</h1>

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

                    <form class="gm-form" method="POST" action="{{ route('mi_ficha.update') }}">
                        @csrf
                        @method('PUT')

                        {{-- *** SIN sección "Alumno": se toma del usuario autenticado *** --}}

                        {{-- =========================
                 Alergias
               ========================= --}}
                        <h3>Alergias</h3>
                        <div>
                            <input type="hidden" name="alergias[polvo]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[polvo]" value="1"
                                    {{ !empty($alerg['polvo']) ? 'checked' : '' }}>
                                Polvo
                            </label>

                            <input type="hidden" name="alergias[polen]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[polen]" value="1"
                                    {{ !empty($alerg['polen']) ? 'checked' : '' }}>
                                Polen
                            </label>

                            <input type="hidden" name="alergias[alimentos]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[alimentos]" value="1"
                                    {{ !empty($alerg['alimentos']) ? 'checked' : '' }}>
                                Alimentos
                            </label>
                            <input name="alergias[alimentos_detalle]" value="{{ $alerg['alimentos_detalle'] ?? '' }}"
                                placeholder="Detalle alimentos" maxlength="255">

                            <input type="hidden" name="alergias[animales]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[animales]" value="1"
                                    {{ !empty($alerg['animales']) ? 'checked' : '' }}>
                                Animales
                            </label>
                            <input name="alergias[animales_detalle]" value="{{ $alerg['animales_detalle'] ?? '' }}"
                                placeholder="Detalle animales" maxlength="255">

                            <input type="hidden" name="alergias[insectos]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[insectos]" value="1"
                                    {{ !empty($alerg['insectos']) ? 'checked' : '' }}>
                                Insectos
                            </label>
                            <input name="alergias[insectos_detalle]" value="{{ $alerg['insectos_detalle'] ?? '' }}"
                                placeholder="Detalle insectos" maxlength="255">

                            <input type="hidden" name="alergias[medicamentos]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[medicamentos]" value="1"
                                    {{ !empty($alerg['medicamentos']) ? 'checked' : '' }}>
                                Medicamentos
                            </label>
                            <input name="alergias[medicamentos_detalle]"
                                value="{{ $alerg['medicamentos_detalle'] ?? '' }}" placeholder="Detalle medicamentos"
                                maxlength="255">

                            <input type="hidden" name="alergias[otro]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="alergias[otro]" value="1"
                                    {{ !empty($alerg['otro']) ? 'checked' : '' }}>
                                Otro
                            </label>
                            <input name="alergias[otro_detalle]" value="{{ $alerg['otro_detalle'] ?? '' }}"
                                placeholder="Detalle otro" maxlength="255">
                        </div>

                        {{-- =========================
                 Enfermedades
               ========================= --}}
                        <h3>Enfermedades</h3>
                        <div>
                            <input type="hidden" name="enfermedades[enfermedad_cronica]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="enfermedades[enfermedad_cronica]" value="1"
                                    {{ !empty($enfs['enfermedad_cronica']) ? 'checked' : '' }}>
                                ¿Enfermedad crónica?
                            </label>
                            <input name="enfermedades[enfermedad_cronica_detalle]"
                                value="{{ $enfs['enfermedad_cronica_detalle'] ?? '' }}"
                                placeholder="Detalle enfermedad crónica" maxlength="255">

                            <input type="hidden" name="enfermedades[toma_medicamentos]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="enfermedades[toma_medicamentos]" value="1"
                                    {{ !empty($enfs['toma_medicamentos']) ? 'checked' : '' }}>
                                ¿Toma medicamentos?
                            </label>
                            <input name="enfermedades[toma_medicamentos_detalle]"
                                value="{{ $enfs['toma_medicamentos_detalle'] ?? '' }}"
                                placeholder="Detalle medicamentos que toma" maxlength="255">

                            <input type="hidden" name="enfermedades[visita_medico]" value="0">
                            <label class="chk">
                                <input type="checkbox" name="enfermedades[visita_medico]" value="1"
                                    {{ !empty($enfs['visita_medico']) ? 'checked' : '' }}>
                                ¿Visita al médico?
                            </label>
                            <input name="enfermedades[visita_medico_detalle]"
                                value="{{ $enfs['visita_medico_detalle'] ?? '' }}"
                                placeholder="Detalle de visitas al médico" maxlength="255">

                            <input name="enfermedades[nombre_medico]" value="{{ $enfs['nombre_medico'] ?? '' }}"
                                placeholder="Nombre del médico" maxlength="100">
                            <input name="enfermedades[telefono_medico]" value="{{ $enfs['telefono_medico'] ?? '' }}"
                                placeholder="Teléfono del médico" maxlength="20">
                        </div>

                        {{-- =========================
                 Contacto de emergencia
               ========================= --}}
                        <h3>Contacto de emergencia</h3>
                        <div>
                            <input name="contacto[nombre]" value="{{ $cont['nombre'] ?? '' }}" placeholder="Nombre"
                                maxlength="50">
                            <input name="contacto[apellidos]" value="{{ $cont['apellidos'] ?? '' }}"
                                placeholder="Apellidos" maxlength="50">
                            <input name="contacto[domicilio]" value="{{ $cont['domicilio'] ?? '' }}"
                                placeholder="Domicilio" maxlength="100">
                            <input name="contacto[telefono]" value="{{ $cont['telefono'] ?? '' }}"
                                placeholder="Teléfono" maxlength="50">
                            <input name="contacto[parentesco]" value="{{ $cont['parentesco'] ?? '' }}"
                                placeholder="Parentesco" maxlength="50">

                            @php $inst = $cont['institucion'] ?? ''; @endphp
                            <select name="contacto[institucion]" required>
                                <option value="">Institución</option>
                                <option value="IMSS" {{ $inst === 'IMSS' ? 'selected' : '' }}>IMSS</option>
                                <option value="Cruz Roja" {{ $inst === 'Cruz Roja' ? 'selected' : '' }}>Cruz Roja
                                </option>
                                <option value="Privado" {{ $inst === 'Privado' ? 'selected' : '' }}>Privado</option>
                            </select>
                        </div>

                        <div class="actions">
                            <a href="{{ route('mi_ficha.show') }}" class="btn-ghost">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </main>

</body>

</html>
