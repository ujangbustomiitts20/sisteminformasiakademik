<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "===========================================\n";
echo "   CEK DATABASE REAL                      \n";
echo "===========================================\n\n";

// Konfigurasi database real
$config = [
    'driver' => 'mysql',
    'host' => '192.168.120.121',
    'port' => '3306',
    'username' => 'root',
    'password' => 'bismillaH',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
];

// Test koneksi
try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Koneksi ke server MySQL berhasil!\n\n";
    
    // List databases
    echo "Database yang tersedia:\n";
    echo "------------------------\n";
    $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($databases as $db) {
        // Skip system databases
        if (in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys'])) {
            echo "  - {$db} (system)\n";
        } else {
            // Count tables
            $pdo->exec("USE `{$db}`");
            $tableCount = $pdo->query("SHOW TABLES")->rowCount();
            echo "  📁 {$db} ({$tableCount} tables)\n";
        }
    }
    
    echo "\n";
    
    // Check for SIAKAD related databases
    $siakadDbs = array_filter($databases, function($db) {
        return stripos($db, 'siakad') !== false || 
               stripos($db, 'akademik') !== false ||
               stripos($db, 'kampus') !== false;
    });
    
    if (!empty($siakadDbs)) {
        echo "\n===========================================\n";
        echo "Database yang mungkin terkait SIAKAD:\n";
        echo "===========================================\n";
        
        foreach ($siakadDbs as $db) {
            echo "\n📁 Database: {$db}\n";
            echo str_repeat('-', 40) . "\n";
            
            $pdo->exec("USE `{$db}`");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            echo "Tables (" . count($tables) . "):\n";
            foreach ($tables as $table) {
                $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
                echo "  - {$table}: {$count} rows\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Koneksi gagal: " . $e->getMessage() . "\n";
}
