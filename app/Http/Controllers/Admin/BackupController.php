<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class BackupController extends Controller
{
    public function backup()
    {
        // 1. Obtener el nombre del disco configurado por el paquete (es 'local' según tu config)
        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
        $disk = Storage::disk($diskName);
        
        // 2. Obtener el nombre de la subcarpeta que usa el paquete (config('app.name') por defecto)
        // Este valor es 'laravel-backup' si no lo has cambiado en config/backup.php ni en config/app.php
        $appName = config('backup.backup.name'); 

        // La ruta completa será: storage/app/NOMBRE_APP/archivo.zip
        $files = $disk->files($appName) ?? []; 
        
        $backups = [];

        foreach ($files as $file) {
            // Solo incluimos archivos .zip, que es el formato de respaldo
            if (str_ends_with($file, '.zip')) { 
                // Aseguramos que el archivo existe antes de obtener su información
                if ($disk->exists($file)) {
                    $size = $disk->size($file) ?? 0;
                    $lastModified = $disk->lastModified($file) ?? time();

                    $backups[] = [
                        'file_path'     => $file,
                        'file_name'     => basename($file),
                        'file_size'     => $this->formatBytes($size),
                        'last_modified' => date('Y-m-d H:i:s', $lastModified),
                    ];
                }
            }
        }

        $backups = array_reverse($backups);

        return view('administrador.backups.manual', compact('backups'));
    }

    public function createBackup()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            
            return redirect()->back()->with('success', '¡Respaldo de la base de datos creado exitosamente!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el respaldo: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        $request->validate(['backup_file' => 'required|string']);

        try {
            $diskName = config('backup.backup.destination.disks')[0];
            $disk = Storage::disk($diskName);
            $appName = config('backup.backup.name');
            $fileName = $request->input('backup_file');
            $filePath = storage_path("app/private/{$appName}/{$fileName}");

            $zip = new \ZipArchive;
            if ($zip->open($filePath) === TRUE) {
                $extractPath = storage_path('app/temp_restore');
                if (!is_dir($extractPath)) {
                    mkdir($extractPath, 0755, true);
                }
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                throw new \Exception("No se pudo abrir el archivo ZIP.");
            }

            $sqlFile = glob($extractPath . '/db-dumps/*.sql')[0] ?? null;
            if (!$sqlFile) {
                throw new \Exception("No se encontró el archivo SQL dentro del respaldo.");
            }

            // Configuración de la base de datos
            $dbConfig = config('database.connections.mysql');

            // Ruta de MACOS
            $mysqlPath = '/Applications/XAMPP/xamppfiles/bin/mysql';

            // Construcción del comando
            $command = sprintf(
                '"%s" --user=%s %s --host=%s %s < "%s"',
                $mysqlPath,
                $dbConfig['username'],
                $dbConfig['password'] ? '--password='.$dbConfig['password'] : '',
                $dbConfig['host'],
                $dbConfig['database'],
                $sqlFile
            );

            // Ejecutar
            exec($command, $output, $resultCode);

            if ($resultCode !== 0) {
                throw new \Exception("Error al ejecutar la restauración. Código: {$resultCode}. Salida: " . implode("\n", $output));
            }

            return redirect()->back()->with('success', '¡Restauración completada exitosamente!');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error durante la restauración: ' . $e->getMessage());
        }
    }

    public function deleteBackup(Request $request)
    {
        $request->validate(['backup_file' => 'required|string']);
        $fileName = $request->input('backup_file');
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $appName = config('backup.backup.name');
        $filePath = $appName . '/' . basename($fileName);

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
            return redirect()->back()->with('success', 'Respaldo eliminado correctamente.');
        }
        return redirect()->back()->with('error', 'El archivo de respaldo no existe.');
    }

    private function formatBytes($bytes, $precision = 2) { 
        $units = ['B', 'KB', 'MB', 'GB', 'TB']; 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 
        $bytes /= (1 << (10 * $pow)); 
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}