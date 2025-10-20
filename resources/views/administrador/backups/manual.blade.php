@extends('layouts.encabezados')
@section('title', 'Respaldo y Restauraciónn de Base de Datos')

@section('content')
    <div class="crud-wrap">
        <section class="crud-card">
            <header class="crud-hero">
                <h2 class="crud-hero-title">Gestión de respaldos</h2>
                <p class="crud-hero-subtitle">Respaldo y restauración de base de datos</p>
            </header>

            <div class="crud-body">
                <h1>Respaldos realizados</h1>

                @if (session('success'))
                    <div class="gm-ok">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="gm-error">{{ session('error') }}</div>
                @endif

                @if (empty($backups)) 
                    <div class="gm-empty">No hay respaldos disponibles.</div>
                @else
                    <div class="table-responsive">
                        <table class="gm-table">
                            <thead>
                                <tr>
                                    <th>Archivo</th>
                                    <th>Tamaño</th>
                                    <th>Última modificación</th>
                                    <th class="th-actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($backups as $backup)
                                    <tr>
                                        <td>{{ $backup['file_name'] ?? 'N/A' }}</td>
                                        <td>{{ $backup['file_size'] ?? 'N/A' }}</td>
                                        <td>{{ $backup['last_modified'] ?? 'N/A' }}</td>
                                        <td>
                                            <div class="table-actions">
                                                <form action="{{ route('admin.backup.restore') }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <input type="hidden" name="backup_file" value="{{ $backup['file_name'] }}">
                                                    <button type="submit" class="btn btn-ghost" onclick="return confirm('ATENCI√ìN: ¬øRestaurar este respaldo? Esto sobrescribir√° PERMANENTEMENTE la base de datos actual.')">Restaurar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <form action="{{ route('admin.backup.create') }}" method="POST" style="margin-bottom:20px;">
                            @csrf
                            <button type="submit" class="btn btn-primary">Crear nuevo respaldo</button>
                        </form>

                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection