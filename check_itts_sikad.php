<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "===========================================\n";
echo "   DETAIL DATABASE itts_sikad             \n";
echo "===========================================\n\n";

$config = [
    'host' => '192.168.120.121',
    'port' => '3306',
    'username' => 'root',
    'password' => 'bismillaH',
    'database' => 'itts_sikad',
];

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all tables with row count
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Total Tables: " . count($tables) . "\n\n";
    
    // Group tables by prefix/category
    $grouped = [];
    foreach ($tables as $table) {
        // Get row count
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
        } catch (Exception $e) {
            $count = 'error';
        }
        
        // Determine group
        $prefix = explode('_', $table)[0];
        if (!isset($grouped[$prefix])) {
            $grouped[$prefix] = [];
        }
        $grouped[$prefix][] = ['name' => $table, 'count' => $count];
    }
    
    // Sort by prefix
    ksort($grouped);
    
    // Display important tables (with data)
    echo "===========================================\n";
    echo "TABEL DENGAN DATA (> 0 rows):\n";
    echo "===========================================\n";
    
    $tablesWithData = [];
    foreach ($tables as $table) {
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            if ($count > 0) {
                $tablesWithData[$table] = $count;
            }
        } catch (Exception $e) {
            continue;
        }
    }
    
    // Sort by count descending
    arsort($tablesWithData);
    
    foreach ($tablesWithData as $table => $count) {
        echo sprintf("  %-45s %10s rows\n", $table, number_format($count));
    }
    
    echo "\n===========================================\n";
    echo "Total tables dengan data: " . count($tablesWithData) . "\n";
    echo "===========================================\n";
    
    // Show key tables structure
    $keyTables = ['mahasiswa', 'dosen', 'pegawai', 'fakultas', 'prodi', 'matakuliah', 'users', 'krs', 'nilai'];
    
    echo "\n===========================================\n";
    echo "STRUKTUR TABEL UTAMA:\n";
    echo "===========================================\n";
    
    foreach ($keyTables as $key) {
        // Find matching table
        $found = null;
        foreach ($tables as $table) {
            if (stripos($table, $key) !== false) {
                $found = $table;
                break;
            }
        }
        
        if ($found) {
            echo "\n📋 {$found}:\n";
            $columns = $pdo->query("DESCRIBE `{$found}`")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $col) {
                echo "   - {$col['Field']} ({$col['Type']})\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
