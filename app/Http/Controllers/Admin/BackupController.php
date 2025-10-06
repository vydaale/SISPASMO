<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB; 

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

    public function createBackupManual()
    {
        try {
            $dbConfig = config('database.connections.mysql');
            $fileName = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $storagePath = storage_path('app/' . $this->backupFolder);

            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0777, true);
            }

            $filePath = $storagePath . '/' . $fileName;
            $passwordArg = !empty($dbConfig['password']) ? sprintf('--password=%s', escapeshellarg($dbConfig['password'])) : '';

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
            $process->mustRun(); 

            if (!file_exists($filePath)) {
                return back()->with('error', 'El archivo de respaldo no se generó o no se pudo acceder. Verifique los permisos (chmod 775 storage).');
            }

            return $this->downloadManual($fileName);
            
        } catch (ProcessFailedException $exception) {
            return back()->with('error', 'Ocurrió un error al crear el respaldo. Detalles del comando: ' . $exception->getMessage());
        } catch (\Exception $e) {
             return back()->with('error', 'Error general al crear respaldo: ' . $e->getMessage());
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
        return back()->with('error', 'El archivo no existe.'); 
    }

    private function runRestoration(string $command, string $successMessage)
    {
        $process = Process::fromShellCommandline($command);
        $process->run();

        $exitCode = $process->getExitCode();

        if ($exitCode !== 0) {
            $errorOutput = $process->getErrorOutput() . $process->getOutput();
            $cleanError = str_replace(config('database.connections.mysql.password'), '******', $errorOutput);

            return back()->with('error', 'Fallo en la restauración (código ' . $exitCode . '). Detalles: ' . $cleanError);
        }

        DB::purge('mysql'); 
        return back()->with('success', $successMessage);
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

            return $this->runRestoration($command, '¡Base de datos restaurada exitosamente desde el archivo subido!');
            
        } catch (\Exception $exception) {
            return back()->with('error', 'Ocurrió un error al restaurar la base de datos (Excepción general): ' . $exception->getMessage());
        }
    }

    public function restoreFromSystem($fileName)
    {
        $storagePath = storage_path('app/' . $this->backupFolder);
        $filePath = $storagePath . '/' . $fileName;

        if (!Storage::disk($this->backupDisk)->exists($this->backupFolder . '/' . $fileName)) {
            return back()->with('error', 'El archivo de respaldo no se encontró en el sistema.');
        }
        
        try {
            $dbConfig = config('database.connections.mysql');
            
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

            return $this->runRestoration($command, "¡Base de datos restaurada exitosamente desde el archivo {$fileName}!");
            
        } catch (\Exception $exception) {
            $error = $exception->getMessage();
            return back()->with('error', "Ocurrió un error al restaurar la base de datos desde el sistema (Excepción general): {$error}");
        }
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
}