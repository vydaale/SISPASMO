<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    private $backupDisk = 'local';
    private $backupFolder = 'backups';
    private $mysqlPath = 'C:\xampp\mysql\bin\\'; // Ruta a la carpeta bin de MySQL

    /**
     * Muestra la lista de respaldos manuales.
     */
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
        // Asegúrate de que la ruta de la vista sea correcta para tu proyecto
        return view('administrador.backups.manual', compact('backups'));
    }

    /**
     * Crea un nuevo respaldo de la base de datos.
     */
    public function createBackupManual()
    {
        try {
            $dbConfig = config('database.connections.mysql');
            $fileName = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
            $storagePath = storage_path('app/' . $this->backupFolder);

            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Manejo de contraseña vacía
            $passwordArg = !empty($dbConfig['password']) ? sprintf('--password=%s', escapeshellarg($dbConfig['password'])) : '';

            // Comando mejorado: Se añade --protocol=tcp para forzar la conexión de red
            // y se maneja la contraseña vacía de forma segura.
            $command = sprintf(
                '"%smysqldump.exe" --protocol=tcp --user=%s %s --host=%s %s > %s',
                $this->mysqlPath,
                escapeshellarg($dbConfig['username']),
                $passwordArg,
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($storagePath . '/' . $fileName)
            );

            $process = Process::fromShellCommandline($command);
            $process->mustRun();

            return back()->with('success', '¡Respaldo manual creado exitosamente!');
        } catch (ProcessFailedException $exception) {
            return back()->with('error', 'Ocurrió un error al crear el respaldo: ' . $exception->getMessage());
        }
    }

    /**
     * Restaura la base de datos desde un archivo .sql subido.
     */
    public function restoreBackupManual(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql',
        ]);

        try {
            $dbConfig = config('database.connections.mysql');
            $filePath = $request->file('backup_file')->getRealPath();
            
            // Manejo de contraseña vacía
            $passwordArg = !empty($dbConfig['password']) ? sprintf('--password=%s', escapeshellarg($dbConfig['password'])) : '';

            // Comando de RESTAURACIÓN mejorado con --protocol=tcp
            $command = sprintf(
                '"%smysql.exe" --protocol=tcp --user=%s %s --host=%s %s < %s',
                $this->mysqlPath,
                escapeshellarg($dbConfig['username']),
                $passwordArg,
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($filePath)
            );

            $process = Process::fromShellCommandline($command);
            $process->mustRun();

            return back()->with('success', '¡Base de datos restaurada exitosamente!');
        } catch (ProcessFailedException $exception) {
            return back()->with('error', 'Ocurrió un error al restaurar la base de datos: ' . $exception->getMessage());
        }
    }

    /**
     * Descarga un archivo de respaldo.
     */
    public function downloadManual($fileName)
    {
        $filePath = $this->backupFolder . '/' . $fileName;
        if (Storage::disk($this->backupDisk)->exists($filePath)) {
            return Storage::disk($this->backupDisk)->download($filePath);
        }
        return back()->with('error', 'El archivo no existe.');
    }

    /**
     * Elimina un archivo de respaldo.
     */
    public function deleteManual($fileName)
    {
        $filePath = $this->backupFolder . '/' . $fileName;
        if (Storage::disk($this->backupDisk)->exists($filePath)) {
            Storage::disk($this->backupDisk)->delete($filePath);
            return back()->with('success', 'Respaldo eliminado exitosamente.');
        }
        return back()->with('error', 'El archivo no existe.');
    }

    /**
     * Función auxiliar para formatear el tamaño del archivo.
     */
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

