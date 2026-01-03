<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "===========================================\n";
echo "   TRUNCATE DATA (KECUALI ADMIN USER)     \n";
echo "===========================================\n\n";

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0;');

// Tables to skip (keep data)
$skipTables = [
    'migrations',
    'password_reset_tokens',
    'password_resets',
    'sessions',
    'cache',
    'cache_locks',
    'jobs',
    'job_batches',
    'failed_jobs',
    'personal_access_tokens',
];

// Get all tables
$tables = DB::select('SHOW TABLES');
$dbName = env('DB_DATABASE');
$key = "Tables_in_{$dbName}";

$truncatedCount = 0;

foreach ($tables as $table) {
    $tableName = $table->$key;
    
    // Skip certain tables
    if (in_array($tableName, $skipTables)) {
        echo "⏭️  Skip: {$tableName}\n";
        continue;
    }
    
    // Special handling for users table - keep admin only
    if ($tableName === 'users') {
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        $totalBefore = DB::table('users')->count();
        
        // Delete non-admin users
        DB::table('users')->where('role', '!=', 'admin')->delete();
        
        $totalAfter = DB::table('users')->count();
        echo "✅ users: Deleted " . ($totalBefore - $totalAfter) . " non-admin users (kept {$adminCount} admin)\n";
        $truncatedCount++;
        continue;
    }
    
    // Truncate other tables
    try {
        $count = DB::table($tableName)->count();
        DB::table($tableName)->truncate();
        echo "✅ {$tableName}: Truncated ({$count} rows)\n";
        $truncatedCount++;
    } catch (Exception $e) {
        echo "❌ {$tableName}: Error - " . $e->getMessage() . "\n";
    }
}

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

echo "\n===========================================\n";
echo "   SELESAI: {$truncatedCount} tables processed\n";
echo "===========================================\n";

// Verify admin users still exist
$admins = DB::table('users')->where('role', 'admin')->get(['id', 'name', 'email', 'role']);
echo "\nAdmin users yang tersisa:\n";
foreach ($admins as $admin) {
    echo "  - {$admin->name} ({$admin->email})\n";
}
