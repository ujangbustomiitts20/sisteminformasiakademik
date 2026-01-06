<?php

require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

ini_set("memory_limit", "512M");
set_time_limit(300);

$filename = "backup_php_test_" . date("Y-m-d_H-i-s") . ".sql";
$filepath = storage_path("app/backups/" . $filename);

// Ensure backup directory exists  
if (!is_dir(dirname($filepath))) {
    mkdir(dirname($filepath), 0755, true);
}

echo "Starting PHP backup method...\n";
$start = microtime(true);

$tables = DB::select("SHOW TABLES");
$database = config("database.connections.mysql.database");
$tableKey = "Tables_in_" . $database;

$handle = fopen($filepath, "w");
fwrite($handle, "-- SIAKAD Database Backup\n");
fwrite($handle, "-- Generated: " . date("Y-m-d H:i:s") . "\n");
fwrite($handle, "-- Database: " . $database . "\n\n");
fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

$totalTables = count($tables);
$processed = 0;

foreach ($tables as $table) {
    $tableArray = (array) $table;
    $tableName = $tableArray[$tableKey] ?? null;
    
    if (empty($tableName)) continue;
    $processed++;
    
    // Get create table statement
    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
    fwrite($handle, "-- Table: {$tableName}\n");
    fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
    fwrite($handle, $createTable[0]->{'Create Table'} . ";\n\n");
    
    // Get row count
    $count = DB::table($tableName)->count();
    
    if ($count > 0) {
        $firstRow = DB::table($tableName)->first();
        $columns = array_keys((array)$firstRow);
        $columnList = '`' . implode('`, `', $columns) . '`';
        
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
        }
        fwrite($handle, "\n");
    }
    
    // Progress
    if ($processed % 20 == 0) {
        echo "Processed $processed/$totalTables tables...\n";
    }
}

fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
fclose($handle);

$elapsed = round(microtime(true) - $start, 2);
$size = filesize($filepath);
echo "\n";
echo "Backup completed in {$elapsed}s\n";
echo "File: $filename\n";
echo "Size: " . number_format($size) . " bytes (" . round($size/1024/1024, 2) . " MB)\n";
