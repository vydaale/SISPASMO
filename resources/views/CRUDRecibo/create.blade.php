<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panel administrador</title>

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
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">Cerrar sesión</a>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dash">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="profile">
                <div class="avatar" aria-hidden="true">👤</div>
                <div class="who">
                    <div class="name">
                        {{ auth()->user()->nombre ?? 'Usuario' }}
                        {{ auth()->user()->apellidoP ?? '' }}
                    </div>
                    <div class="role">{{ auth()->user()->rol->nombre_rol ?? '—' }}</div>
                </div>
            </div>

            <nav class="nav">
                <div class="group">
                    <div class="group-title">USUARIOS</div>
                    <ul class="menu">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle">Alumnos</a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('alumnos.index') }}">Listar Alumnos</a></li>
                                <li><a href="{{ route('alumnos.create') }}">Nuevo Alumno</a></li>
                                <li><a href="{{ route('docentes.create') }}">Nuevo docente</a></li>
                                <li><a href="{{ route('docentes.index') }}">Listar docente</a></li>
                                <li><a href="{{ route('aspirantes.index') }}">Listar aspirantes</a></li>
                                <li><a href="#">Nuevo administrativo</a></li>
                                <li><a href="#">Listar administrativos</a></li>
                                <li><a href="{{ route('coordinadores.create') }}">Nuevo coordinador</a></li>
                                <li><a href="{{ route('coordinadores.index') }}">Listar coordinadores</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="divider"></div>
                <div class="group">
                    <div class="group-title">Funcionalidades</div>
                    <ul class="menu">
                        <li><a class="active" href="{{ route('recibos.create') }}">Recibos</a></li>
                        <li><a href="#">Horarios</a></li>
                        <li><a href="#">Ficha médica</a></li>
                    </ul>
                </div>

                <div class="divider"></div>

                <div class="group">
                    <ul class="menu">
                        <li><a href="#">Módulos</a></li>
                        <li><a href="#">Talleres y prácticas</a></li>
                        <li><a href="#">Dudas y sugerencias</a></li>
                        <li><a href="#">Citas</a></li>
                        <li><a href="#">Calificaciones</a></li>
                        <li><a href="#">Reportes</a></li>
                        <li><a href="{{ route('quejas.index') }}">Queja/sugerencia</a></li>
                        <li><a href="#">Base de datos</a></li>
                        <li><a href="#">Notificaciones</a></li>
                    </ul>
                </div>

                <div class="divider"></div>

                <div class="search">
                    <label for="q">Buscar módulo:</label>
                    <input id="q" type="text" placeholder="Escribe aquí…">
                </div>
            </nav>
        </aside>

        <!-- CONTENIDO -->
        <main class="content">
            <div class="crud-wrap">
                <section class="crud-card">
                    <header class="crud-hero">
                        <h2 class="crud-hero-title">Gestión de Recibos</h2>
                        <p class="crud-hero-subtitle">Registro</p>

                        <nav class="crud-tabs">
                            <a href="{{ route('recibos.create') }}" class="tab active">Registrar</a>
                            <a href="{{ route('recibos.index') }}" class="tab">Listar recibos</a>
                        </nav>
                    </header>

                    <div class="crud-body">
                        <h1>Nuevo Recibo</h1>

                        @if (session('ok'))
                            <div class="gm-ok">{{ session('ok') }}</div>
                        @endif

                        @if ($errors->any())
                            <ul class="gm-errors">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form class="gm-form" method="POST" action="{{ route('recibos.store') }}" enctype="multipart/form-data">
                            @csrf

                            {{-- =========================
                                Datos del Recibo
                            ========================== --}}
                            <h3>Datos del Recibo</h3>

                            <div class="grid-2">
                                <div>
                                    <label for="id_alumno">ID Alumno</label>
                                    <input id="id_alumno" name="id_alumno" value="{{ old('id_alumno') }}"
                                           placeholder="Ej. 123" required>
                                    @error('id_alumno') <small class="gm-error">{{ $message }}</small> @enderror
                                    <small class="gm-help">Usa el <strong>id_alumno</strong> de tu tabla alumnos.</small>
                                </div>

                                <div>
                                    <label for="fecha_pago">Fecha de pago</label>
                                    <input type="date" id="fecha_pago" name="fecha_pago" value="{{ old('fecha_pago') }}" required>
                                    @error('fecha_pago') <small class="gm-error">{{ $message }}</small> @enderror
                                </div>
                            </div>

                            <div class="grid-2">
                                <div>
                                    <label for="concepto">Concepto</label>
                                    <input id="concepto" name="concepto" maxlength="100" value="{{ old('concepto') }}"
                                           placeholder="Inscripción, colegiatura, etc." required>
                                    @error('concepto') <small class="gm-error">{{ $message }}</small> @enderror
                                </div>

                                <div>
                                    <label for="monto">Monto</label>
                                    <input type="number" step="0.01" id="monto" name="monto" value="{{ old('monto') }}"
                                           placeholder="0.00" required>
                                    @error('monto') <small class="gm-error">{{ $message }}</small> @enderror
                                    <small class="gm-help">Usa punto decimal. Ej: 1250.00</small>
                                </div>
                            </div>

                            <div>
                                <label for="comprobante">Comprobante (imagen)</label>
                                <input type="file" id="comprobante" name="comprobante" accept="image/*" required>
                                @error('comprobante') <small class="gm-error">{{ $message }}</small> @enderror>

                                {{-- Vista previa opcional --}}
                                <div id="preview" class="gm-preview" style="margin-top:10px; display:none;">
                                    <img id="previewImg" alt="Vista previa" style="max-width:320px; border:1px solid #e5e7eb; border-radius:10px;">
                                </div>
                                <small class="gm-help">Formatos permitidos: JPG, PNG, WEBP. Máx. 5MB.</small>
                            </div>

                            <div>
                                <label for="comentarios">Comentarios (opcional)</label>
                                <textarea id="comentarios" name="comentarios" rows="3" placeholder="Notas para validación…">{{ old('comentarios') }}</textarea>
                                @error('comentarios') <small class="gm-error">{{ $message }}</small> @enderror
                            </div>

                            <div class="actions">
                                <a href="{{ route('recibos.create') }}" class="btn-ghost">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div> <!-- /dash -->

    <script>
        // Vista previa de la imagen seleccionada
        const input = document.getElementById('comprobante');
        const preview = document.getElementById('preview');
        const previewImg = document.getElementById('previewImg');

        input?.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file) {
                preview.style.display = 'none';
                previewImg.src = '';
                return;
            }
            const url = URL.createObjectURL(file);
            previewImg.src = url;
            preview.style.display = 'block';
        });
    </script>
</body>
</html>
