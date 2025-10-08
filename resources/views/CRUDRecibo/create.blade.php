@extends('layouts.encabezadosAl')

@section('title', 'Recibos')

@section('content')
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

                    <h3>Datos del Recibo</h3>

                    <div class="grid-2">
                        <div>
                            <label for="matriculaA">Matrícula</label>
                            <input id="matriculaA" name="matriculaA" value="{{ old('matriculaA', $alumno->matriculaA ?? '') }}"
                                placeholder="Ej. 123456" required>
                            @error('matriculaA') <small class="gm-error">{{ $message }}</small> @enderror
                            <small class="gm-help">Ingresa tu matrícula para validar tu pago.</small>
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
                        <select id="concepto" name="concepto" required>
                            <option value="">-- Seleccione un concepto --</option>
                            @foreach($conceptos as $concepto)
                                <option value="{{ $concepto }}" {{ old('concepto') == $concepto ? 'selected' : '' }}>
                                    {{ $concepto }}
                                </option>
                            @endforeach
                        </select>
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
                        <a href="{{ route('recibos.create') }}" class="btn btn-danger">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection