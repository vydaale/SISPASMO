<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 


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
        $request->validate(['backup_file' => 'required|file|mimes:sql']);

        try {
            $tmpPath = $request->file('backup_file')->getRealPath();

            // Verificamos que el fichero no esté vacío
            if (!is_readable($tmpPath) || filesize($tmpPath) === 0) {
                return back()->with('error', 'El archivo subido está vacío o no se puede leer.');
            }

            return $this->runMysqlWithInput($tmpPath, '¡Base de datos restaurada exitosamente desde el archivo subido!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error al restaurar: '.$e->getMessage());
        }
    }

    public function restoreFromSystem($fileName)
    {
        $path = storage_path('app/'.$this->backupFolder.'/'.$fileName);
        if (!is_readable($path)) {
            return back()->with('error', 'El archivo no existe o no es legible.');
        }
        return $this->runMysqlWithInput($path, "¡Base de datos restaurada exitosamente desde {$fileName}!");
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

    private function mysqlBinary(): string
    {
        $bin = rtrim($this->mysqlPath ?? '', '/\\');
        if ($bin === '') return 'mysql';
        return $bin . (str_ends_with($bin, '/') || str_ends_with($bin, '\\') ? '' : DIRECTORY_SEPARATOR) . 'mysql';
    }

    private function mysqldumpBinary(): string
    {
        $bin = rtrim($this->mysqlPath ?? '', '/\\');
        if ($bin === '') return 'mysqldump';
        return $bin . (str_ends_with($bin, '/') || str_ends_with($bin, '\\') ? '' : DIRECTORY_SEPARATOR) . 'mysqldump';
    }

    // Opcional: úsalo si quieres mejorar createBackupManual (sin redirección >)
    private function dumpCommand(array $dbConfig, string $filePath): Process
    {
        $cmd = [
            $this->mysqldumpBinary(),
            '-u', $dbConfig['username'],
            '-h', $dbConfig['host'],
            $dbConfig['database'],
            '--single-transaction',
            '--add-drop-table',
            '--routines',
            '--events',
            '--triggers',
        ];
        if (!empty($dbConfig['password'])) {
            $cmd[] = '-p' . $dbConfig['password']; // -pXXXX (sin espacio)
        }

        $process = new Process($cmd, null, null, null, 600);
        $process->run(function ($type, $buffer) use ($filePath) {
            file_put_contents($filePath, $buffer, FILE_APPEND);
        });

        return $process;
    }

    private function runMysqlWithInput(string $sqlPath, string $successMessage)
    {
        $db = config('database.connections.mysql');

        // desactiva FKs
        $pre = new Process([$this->mysqlBinary(), '-u', $db['username'], '-h', $db['host'], $db['database']]);
        if (!empty($db['password'])) $pre->setCommandLine($pre->getCommandLine().' -p'.$db['password']);
        $pre->setInput("SET FOREIGN_KEY_CHECKS=0;");
        $pre->setTimeout(600);
        $pre->run();

        // procesa el .sql como input (sin usar <)
        $cmd = [$this->mysqlBinary(), '-u', $db['username'], '-h', $db['host'], $db['database']];
        if (!empty($db['password'])) $cmd[] = '-p'.$db['password'];

        $process = new Process($cmd, null, null, fopen($sqlPath, 'r'), 1800);
        $process->run();

        // reactiva FKs
        $post = new Process([$this->mysqlBinary(), '-u', $db['username'], '-h', $db['host'], $db['database']]);
        if (!empty($db['password'])) $post->setCommandLine($post->getCommandLine().' -p'.$db['password']);
        $post->setInput("SET FOREIGN_KEY_CHECKS=1;");
        $post->setTimeout(600);
        $post->run();

        $stdout = $process->getOutput();
        $stderr = $process->getErrorOutput();
        $exit  = $process->getExitCode();

        // logs (oculta pass si aparece)
        $hidden = !empty($db['password']) ? $db['password'] : null;
        $logErr = $hidden ? str_replace($hidden, '******', $stderr) : $stderr;
        $logOut = $hidden ? str_replace($hidden, '******', $stdout) : $stdout;
        Log::info('DB restore stdout: '.$logOut);
        if ($logErr) Log::warning('DB restore stderr: '.$logErr);

        if ($exit !== 0) {
            DB::purge('mysql');
            return back()->with('error', 'Fallo en la restauración (código '.$exit.'). '.$logErr ?: 'Sin detalles.');
        }

        DB::purge('mysql');
        return back()->with('success', $successMessage);
    }
}