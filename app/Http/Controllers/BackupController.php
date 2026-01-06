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
            $backupDir = storage_path('app/' . $this->backupPath);
            $filepath = $backupDir . '/' . $filename;

            // Ensure backup directory exists
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $connection = config('database.default');
            
            // Handle SQLite
            if ($connection === 'sqlite') {
                $this->backupSQLite($filepath);
            } else {
                // MySQL backup
                $this->backupMySQL($filepath);
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
            \Log::error('Backup failed: ' . $e->getMessage());
            return redirect()->route('backup.index')
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Backup MySQL database
     */
    protected function backupMySQL($filepath)
    {
        // Get database credentials
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Try mysqldump first (only if not remote and versions match)
        $mysqldumpSuccess = false;
        $isLocalHost = in_array($host, ['localhost', '127.0.0.1', '::1']);
        
        // Skip mysqldump for remote databases due to version mismatch issues
        // between local MariaDB client and remote MySQL/MariaDB server
        if ($isLocalHost) {
            // Check if mysqldump is available
            $checkCommand = 'which mysqldump 2>/dev/null || where mysqldump 2>nul';
            $checkProcess = Process::fromShellCommandline($checkCommand);
            $checkProcess->run();
            
            if ($checkProcess->isSuccessful() && !empty(trim($checkProcess->getOutput()))) {
                // Create temp file for password to avoid command line exposure
                $cnfFile = tempnam(sys_get_temp_dir(), 'mysql_');
                file_put_contents($cnfFile, "[client]\npassword=\"{$password}\"\n");
                chmod($cnfFile, 0600);
                
                try {
                    // Build mysqldump command with defaults-extra-file
                    // Use --skip-ssl for MariaDB client compatibility
                    $command = sprintf(
                        'mysqldump --defaults-extra-file=%s --host=%s --port=%s --user=%s --skip-ssl --single-transaction --routines --triggers %s 2>/dev/null > %s',
                        escapeshellarg($cnfFile),
                        escapeshellarg($host),
                        escapeshellarg($port),
                        escapeshellarg($username),
                        escapeshellarg($database),
                        escapeshellarg($filepath)
                    );

                    $process = Process::fromShellCommandline($command);
                    $process->setTimeout(300);
                    $process->run();

                    if (file_exists($filepath) && filesize($filepath) > 100) {
                        $mysqldumpSuccess = true;
                    }
                } finally {
                    // Always delete temp config file
                    @unlink($cnfFile);
                }
            }
        }

        // Fallback to PHP method if mysqldump failed
        if (!$mysqldumpSuccess) {
            \Log::info('mysqldump not available or failed, using PHP backup method');
            $this->backupWithPHP($filepath);
        }
    }

    /**
     * Backup SQLite database
     */
    protected function backupSQLite($filepath)
    {
        $sqliteFile = config('database.connections.sqlite.database');
        
        if (!file_exists($sqliteFile)) {
            throw new \Exception('SQLite database file not found');
        }

        // For SQLite, we can just copy the file or export as SQL
        $sql = "-- SIAKAD SQLite Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";

        // Get all tables
        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        
        foreach ($tables as $table) {
            $tableName = $table->name;
            
            // Get create table statement
            $createSql = DB::selectOne("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);
            $sql .= "-- Table: {$tableName}\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createSql->sql . ";\n\n";

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

        file_put_contents($filepath, $sql);
    }

    /**
     * Alternative backup method using PHP (streaming to file)
     */
    protected function backupWithPHP($filepath)
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        $tableKey = 'Tables_in_' . $database;

        // Open file for writing
        $handle = fopen($filepath, 'w');
        if (!$handle) {
            throw new \Exception('Cannot create backup file');
        }

        fwrite($handle, "-- SIAKAD Database Backup\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- Database: " . $database . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableName = $tableArray[$tableKey] ?? null;
            
            if (empty($tableName)) continue;
            
            // Get create table statement
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            fwrite($handle, "-- Table: {$tableName}\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createTable[0]->{'Create Table'} . ";\n\n");

            // Get row count first
            $count = DB::table($tableName)->count();
            
            if ($count > 0) {
                // Get columns from first row
                $firstRow = DB::table($tableName)->first();
                $columns = array_keys((array)$firstRow);
                $columnList = '`' . implode('`, `', $columns) . '`';
                
                // Process in chunks to save memory
                $chunkSize = 500;
                $offset = 0;
                
                while ($offset < $count) {
                    $rows = DB::table($tableName)->skip($offset)->take($chunkSize)->get();
                    
                    if ($rows->count() > 0) {
                        $values = [];
                        foreach ($rows as $row) {
                            $rowValues = [];
                            foreach ((array)$row as $value) {
                                if (is_null($value)) {
                                    $rowValues[] = 'NULL';
                                } else {
                                    $rowValues[] = "'" . addslashes((string)$value) . "'";
                                }
                            }
                            $values[] = '(' . implode(', ', $rowValues) . ')';
                        }
                        fwrite($handle, "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n" . implode(",\n", $values) . ";\n");
                    }
                    
                    $offset += $chunkSize;
                    
                    // Free memory
                    unset($rows, $values);
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
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
        $filepath = storage_path('app/' . $this->backupPath . '/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('backup.index')
                ->with('error', 'File backup tidak ditemukan.');
        }

        unlink($filepath);

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
            $connection = config('database.default');
            $restoreSuccess = false;

            if ($connection === 'sqlite') {
                // For SQLite, use PHP restore
                $this->restoreWithPHP($filepath);
                $restoreSuccess = true;
            } else {
                // MySQL restore
                $host = config('database.connections.mysql.host');
                $port = config('database.connections.mysql.port', 3306);
                $database = config('database.connections.mysql.database');
                $username = config('database.connections.mysql.username');
                $password = config('database.connections.mysql.password');

                // Check if mysql client is available
                $checkCommand = 'which mysql 2>/dev/null || where mysql 2>nul';
                $checkProcess = Process::fromShellCommandline($checkCommand);
                $checkProcess->run();

                if ($checkProcess->isSuccessful() && !empty(trim($checkProcess->getOutput()))) {
                    // Create temp file for password to avoid command line exposure
                    $cnfFile = tempnam(sys_get_temp_dir(), 'mysql_');
                    file_put_contents($cnfFile, "[client]\npassword=\"{$password}\"\n");
                    chmod($cnfFile, 0600);

                    try {
                        // Build mysql restore command with defaults-extra-file
                        // Use --skip-ssl for MariaDB client compatibility
                        $command = sprintf(
                            'mysql --defaults-extra-file=%s --host=%s --port=%s --user=%s --skip-ssl %s < %s 2>&1',
                            escapeshellarg($cnfFile),
                            escapeshellarg($host),
                            escapeshellarg($port),
                            escapeshellarg($username),
                            escapeshellarg($database),
                            escapeshellarg($filepath)
                        );

                        $process = Process::fromShellCommandline($command);
                        $process->setTimeout(600);
                        $process->run();

                        if ($process->isSuccessful()) {
                            $restoreSuccess = true;
                        }
                    } finally {
                        @unlink($cnfFile);
                    }
                }

                // Fallback to PHP method
                if (!$restoreSuccess) {
                    \Log::info('mysql client not available or failed, using PHP restore method');
                    $this->restoreWithPHP($filepath);
                    $restoreSuccess = true;
                }
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
            \Log::error('Restore failed: ' . $e->getMessage());
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
        $backupDir = storage_path('app/' . $this->backupPath);
        
        // Ensure directory exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
            return collect([]);
        }

        // Use glob to get SQL files directly
        $files = glob($backupDir . '/*.sql');
        
        return collect($files)->map(function ($filepath) {
            return [
                'name' => basename($filepath),
                'size' => filesize($filepath),
                'size_formatted' => $this->formatBytes(filesize($filepath)),
                'date' => date('Y-m-d H:i:s', filemtime($filepath)),
                'date_formatted' => \Carbon\Carbon::createFromTimestamp(filemtime($filepath))->diffForHumans(),
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
                $filepath = storage_path('app/' . $this->backupPath . '/' . $backup['name']);
                if (file_exists($filepath)) {
                    unlink($filepath);
                    $deleted++;
                }
            }
        }

        return redirect()->route('backup.index')
            ->with('success', "Cleanup selesai. {$deleted} file backup lama dihapus.");
    }
}
