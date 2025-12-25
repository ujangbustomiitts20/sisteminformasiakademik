<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    protected $backupPath = 'backups';

    /**
     * Display backup management page
     */
    public function index()
    {
        $backups = $this->getBackupFiles();
        $diskSpace = $this->getDiskSpace();
        
        return view('backup.index', compact('backups', 'diskSpace'));
    }

    /**
     * Create new backup
     */
    public function create(Request $request)
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

            // Ensure backup directory exists
            if (!Storage::exists($this->backupPath)) {
                Storage::makeDirectory($this->backupPath);
            }

            // Get database credentials
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Build mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            // Execute backup
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(300); // 5 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                // Try alternative method using PHP
                $this->backupWithPHP($filepath);
            }

            // Check if file was created
            if (file_exists($filepath) && filesize($filepath) > 0) {
                // Log activity
                if (class_exists(\App\Models\ActivityLog::class)) {
                    \App\Models\ActivityLog::create([
                        'user_id' => auth()->id(),
                        'action' => 'backup_create',
                        'description' => 'Created database backup: ' . $filename,
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }

                return redirect()->route('backup.index')
                    ->with('success', 'Backup berhasil dibuat: ' . $filename);
            }

            throw new \Exception('Backup file tidak valid atau kosong');

        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Alternative backup method using PHP
     */
    protected function backupWithPHP($filepath)
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        $tableKey = 'Tables_in_' . $database;

        $sql = "-- SIAKAD Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: " . $database . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableName = $tableArray[$tableKey] ?? null;
            
            if (empty($tableName)) continue;
            
            // Get create table statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- Table: {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            
            if ($rows->count() > 0) {
                $columns = array_keys((array)$rows->first());
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                foreach ($rows->chunk(100) as $chunk) {
                    $values = [];
                    foreach ($chunk as $row) {
                        $rowValues = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    $sql .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n" . implode(",\n", $values) . ";\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        file_put_contents($filepath, $sql);
    }

    /**
     * Download backup file
     */
    public function download($filename)
    {
        $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('backup.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        // Log activity
        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_download',
                'description' => 'Downloaded backup: ' . $filename,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return response()->download($filepath, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Delete backup file
     */
    public function destroy($filename)
    {
        $filepath = $this->backupPath . '/' . $filename;

        if (!Storage::exists($filepath)) {
            return redirect()->route('backup.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        Storage::delete($filepath);

        // Log activity
        if (class_exists(\App\Models\ActivityLog::class)) {
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'backup_delete',
                'description' => 'Deleted backup: ' . $filename,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return redirect()->route('backup.index')
            ->with('success', 'File backup berhasil dihapus.');
    }

    /**
     * Restore from backup
     */
    public function restore(Request $request, $filename)
    {
        $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('backup.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        try {
            // Get database credentials
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port', 3306);
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Build mysql restore command
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            // Execute restore
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(600); // 10 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                // Try alternative method using PHP
                $this->restoreWithPHP($filepath);
            }

            // Log activity
            if (class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'backup_restore',
                    'description' => 'Restored database from: ' . $filename,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }

            return redirect()->route('backup.index')
                ->with('success', 'Database berhasil di-restore dari: ' . $filename);

        } catch (\Exception $e) {
            return redirect()->route('backup.index')
                ->with('error', 'Gagal restore database: ' . $e->getMessage());
        }
    }

    /**
     * Alternative restore method using PHP
     */
    protected function restoreWithPHP($filepath)
    {
        $sql = file_get_contents($filepath);
        
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Split by semicolon
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        foreach ($statements as $statement) {
            if (!empty($statement) && $statement !== 'SET FOREIGN_KEY_CHECKS=0' && $statement !== 'SET FOREIGN_KEY_CHECKS=1') {
                try {
                    DB::unprepared($statement);
                } catch (\Exception $e) {
                    // Log but continue
                    \Log::warning('Restore statement failed: ' . $e->getMessage());
                }
            }
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Get list of backup files
     */
    protected function getBackupFiles()
    {
        if (!Storage::exists($this->backupPath)) {
            Storage::makeDirectory($this->backupPath);
            return collect([]);
        }

        $files = Storage::files($this->backupPath);
        
        return collect($files)->map(function ($file) {
            $filepath = storage_path('app/' . $file);
            return [
                'name' => basename($file),
                'size' => file_exists($filepath) ? filesize($filepath) : 0,
                'size_formatted' => file_exists($filepath) ? $this->formatBytes(filesize($filepath)) : '0 B',
                'date' => file_exists($filepath) ? date('Y-m-d H:i:s', filemtime($filepath)) : null,
                'date_formatted' => file_exists($filepath) ? \Carbon\Carbon::createFromTimestamp(filemtime($filepath))->diffForHumans() : null,
            ];
        })->sortByDesc('date')->values();
    }

    /**
     * Get disk space info
     */
    protected function getDiskSpace()
    {
        $backupDir = storage_path('app/' . $this->backupPath);
        
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $totalSize = 0;
        $files = glob($backupDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
            }
        }

        return [
            'total_backups' => count($files),
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'disk_free' => disk_free_space(storage_path()),
            'disk_free_formatted' => $this->formatBytes(disk_free_space(storage_path())),
        ];
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Delete old backups (keep last N)
     */
    public function cleanup(Request $request)
    {
        $keep = $request->input('keep', 5);
        $backups = $this->getBackupFiles();
        
        $deleted = 0;
        if ($backups->count() > $keep) {
            $toDelete = $backups->slice($keep);
            foreach ($toDelete as $backup) {
                Storage::delete($this->backupPath . '/' . $backup['name']);
                $deleted++;
            }
        }

        return redirect()->route('backup.index')
            ->with('success', "Cleanup selesai. {$deleted} file backup lama dihapus.");
    }
}
