@extends('layouts.encabezados') {{-- O tu layout de admin --}}

@section('content')
<div class="container py-4">
    <h1>Gestión de Respaldos (Manual)</h1>
    <p class="text-muted">
        Crea, restaura, descarga y elimina respaldos de la base de datos.
    </p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">1. Crear Nuevo Respaldo</h5>
                    <p class="card-text">
                        Genera un respaldo (.sql) de la base de datos. El proceso puede tardar.
                    </p>
                    <form action="{{ route('admin.backups.manual.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Generar Respaldo Ahora</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100 border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">2. Restaurar desde un Archivo</h5>
                    <div class="alert alert-danger">
                        <strong>¡ADVERTENCIA!</strong> Esta acción es irreversible y sobreescribirá
                        TODA la base de datos actual con los datos del archivo. Úsala con extrema precaución.
                    </div>
                    <form action="{{ route('admin.backups.manual.restore') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="backup_file" class="form-label">Archivo de respaldo (.sql)</label>
                            <input class="form-control" type="file" name="backup_file" id="backup_file" accept=".sql" required>
                        </div>
                        <button type="submit" class="btn btn-warning" onclick="return confirm('¿Estás seguro? Esta acción sobreescribirá la base de datos actual.')">
                            Restaurar Base de Datos
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            Respaldos Existentes
        </div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nombre del Archivo</th>
                        <th>Tamaño</th>
                        <th>Fecha de Creación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                        <tr>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ $backup['file_size'] }}</td>
                            <td>{{ $backup['last_modified'] }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.backups.manual.download', $backup['file_name']) }}" class="btn btn-sm btn-info">
                                    Descargar
                                </a>
                                <form action="{{ route('admin.backups.manual.delete', $backup['file_name']) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este respaldo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">No hay respaldos manuales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection