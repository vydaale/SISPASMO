@extends('layouts.encabezados')

@section('content')
<div class="crud-wrap dash"> 

    <div class="crud-card">
        <div class="crud-hero">
            <h1 class="crud-hero-title">Administración de la base de datos</h1>
            <p class="crud-hero-subtitle">
                Crea, restaura y administra los archivos de la base de datos de SISPASMO.
            </p>
        </div>

        <div class="crud-body">
            
            @if (session('success'))
                <div class="gm-ok">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="gm-errors">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="row g-4 mb-5">
                
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-success">
                        <div class="card-body d-flex flex-column">
                            <h2> Generar nuevo respaldo</h2>
                            <p class="card-text text-muted small">
                                Crea un archivo .sql de la base de datos, lo almacena y lo descarga inmediatamente.
                            </p>
                            <form action="{{ route('admin.backups.manual.store') }}" method="POST" class="mt-auto">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" style="background: var(--color-principal); border-color: var(--color-principal);" title="Almacena y descarga el respaldo">
                                    Generar y descargar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card h-100 shadow-lg border-danger">
                        <div class="card-body">
                            <h2>Restaurar base de datos</h2>
                        
                            <div class="row g-3">
                                <div class="col-lg-6 border-end">
                                    <form action="{{ route('admin.backups.manual.restore_upload') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="card-text text-muted small">Subir archivo .sql</label>
                                            <input class="form-control form-control-sm" type="file" name="backup_file" id="backup_file" accept=".sql" required>
                                        </div>
                                        <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('¿Estás seguro de sobreescribir la base de datos?')" style="background: var(--color-rojo);">
                                            <i class="card-text text-muted small"></i> Subir y estaurar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection