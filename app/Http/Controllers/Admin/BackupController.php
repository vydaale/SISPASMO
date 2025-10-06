<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB; // Añadir para la utilidad

class BackupController extends Controller
{
    private $mysqlPath = '/Applications/XAMPP/xamppfiles/bin/'; 
    private $backupDisk = 'local';
    private $backupFolder = 'backups';

    public function indexManual()
    {
        $disk = Storage::disk($this->backupDisk);
        $files = $disk->files($this->backupFolder);

        $backups = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'file_path' => $file,
                    'file_name' => basename($file),
                    'file_size' => $this->formatSize($disk->size($file)),
                    'last_modified' => date('d/m/Y H:i:s', $disk->lastModified($file)),
                ];
            }
        }
        $backups = array_reverse($backups);
        return view('administrador.backups.manual', compact('backups'));
    }

    public function createBackupManual()
    {
        try {
            $dbConfig = config('database.connections.mysql');
            $fileName = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $storagePath = storage_path('app/' . $this->backupFolder);

            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filePath = $storagePath . '/' . $fileName;
            $passwordArg = !empty($dbConfig['password'])
                ? sprintf('--password=%s', escapeshellarg($dbConfig['password']))
                : '';

            $command = sprintf(
                '"%smysqldump" --user=%s %s --host=%s %s > %s',
                $this->mysqlPath,
                escapeshellarg($dbConfig['username']),
                $passwordArg,
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($filePath)
            );

            $process = Process::fromShellCommandline($command);
            $process->setTimeout(null); 
            $process->mustRun();

            clearstatcache(true, $filePath);

            if (!file_exists($filePath)) {
                return back()->with('error', 'El archivo de respaldo no se generó.');
            }

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/sql',
            ]);

        } catch (ProcessFailedException $exception) {
            return back()->with('error', 'Ocurrió un error al crear el respaldo. Detalles: ' . $exception->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Error general al crear respaldo: ' . $e->getMessage());
        }
    }

    public function restoreBackupManual(Request $request)
    {
        $request->validate(['backup_file' => 'required|file|mimes:sql',]);

        try {
            $dbConfig = config('database.connections.mysql');
            $filePath = $request->file('backup_file')->getRealPath();
            
            $passwordArg = !empty($dbConfig['password']) ? sprintf('--password=%s', escapeshellarg($dbConfig['password'])) : '';

            $command = sprintf(
                '"%smysql" --user=%s %s --host=%s %s < %s',
                $this->mysqlPath,
                escapeshellarg($dbConfig['username']),
                $passwordArg,
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($filePath) 
            );

            $process = Process::fromShellCommandline($command);
            $process->mustRun();

            return back()->with('success', '¡Base de datos restaurada exitosamente desde el archivo subido!');
        } catch (ProcessFailedException $exception) {
            return back()->with('error', 'Ocurrió un error al restaurar la base de datos. Detalles: ' . $exception->getMessage());
        }
    }

    public function restoreFromSystem($fileName)
    {
        $relative = $this->backupFolder . '/' . $fileName;
        if (!Storage::disk($this->backupDisk)->exists($relative)) {
            return back()->with('error', 'El archivo de respaldo no se encontró en el sistema.');
        }

        $filePath = storage_path('app/' . $relative);
        $db = config('database.connections.mysql');

        // Restauración (sin redirecciones de shell; con checks de FK)
        $sql = 'SET FOREIGN_KEY_CHECKS=0; SOURCE ' . addcslashes($filePath, '\\') . '; SET FOREIGN_KEY_CHECKS=1;';

        $args = [
            $this->mysqlPath . 'mysql',
            '--user=' . $db['username'],
            '--host=' . $db['host'],
            '--default-character-set=utf8mb4',
            '-e', $sql,
            $db['database'],
        ];
        if (!empty($db['password'])) $args[] = '--password=' . $db['password'];
        if (!empty($db['port']))     $args[] = '--port=' . $db['port'];
        if (!empty($db['unix_socket'] ?? null)) $args[] = '--socket=' . $db['unix_socket'];

        try {
            $process = new \Symfony\Component\Process\Process($args);
            $process->setTimeout(null);
            $process->mustRun();

            // === Verificación rápida ===
            // 1) ping
            DB::select('SELECT 1');

            // 2) conteos clave (ajusta la lista si quieres)
            $tablasClave = ['usuarios','alumnos','docentes','diplomados','modulos','horarios'];
            $conteos = [];
            foreach ($tablasClave as $t) {
                try {
                    $conteos[$t] = DB::table($t)->count();
                } catch (\Throwable $e) {
                    $conteos[$t] = 'tabla no existe';
                }
            }

            // 3) mensaje bonito
            $resumen = collect($conteos)
                ->map(fn($v,$k) => "$k: $v")
                ->implode(' | ');

            return back()->with('success', "¡Restauración OK! Verificación rápida: $resumen");

        } catch (\Symfony\Component\Process\Exception\ProcessFailedException $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        } catch (\Throwable $e) {
            return back()->with('error', 'Error durante verificación: ' . $e->getMessage());
        }
    }

    public function downloadManual($fileName)
    {
        $relative = $this->backupFolder . '/' . $fileName;
        $absolute = storage_path('app/' . $relative);

        clearstatcache(true, $absolute);

        if (file_exists($absolute)) {
            return response()->download($absolute, $fileName, [
                'Content-Type' => 'application/sql',
            ]);
        }

        if (Storage::disk($this->backupDisk)->exists($relative)) {
            return Storage::disk($this->backupDisk)->download($relative);
        }

        return back()->with('error', 'El archivo no existe.');
    }

    public function deleteManual($fileName)
    {
        $filePath = $this->backupFolder . '/' . $fileName;
        if (Storage::disk($this->backupDisk)->exists($filePath)) {
            Storage::disk($this->backupDisk)->delete($filePath);
            return back()->with('success', 'Respaldo eliminado exitosamente.');
        }
        return back()->with('error', 'El archivo no existe.');
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}

