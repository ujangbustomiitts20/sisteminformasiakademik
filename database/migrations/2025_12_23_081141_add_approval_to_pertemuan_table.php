<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add approval columns to pertemuan table
        $columns = DB::select("SHOW COLUMNS FROM pertemuan LIKE 'is_approved'");
        if (empty($columns)) {
            DB::statement("ALTER TABLE pertemuan ADD COLUMN is_approved BOOLEAN DEFAULT FALSE AFTER is_published");
            DB::statement("ALTER TABLE pertemuan ADD COLUMN approved_by INT NULL AFTER is_approved");
            DB::statement("ALTER TABLE pertemuan ADD COLUMN approved_at TIMESTAMP NULL AFTER approved_by");
            DB::statement("ALTER TABLE pertemuan ADD COLUMN rejection_note TEXT NULL AFTER approved_at");
        }

        // Add pertemuan_id to absensi table (link absensi to pertemuan)
        $absensiColumns = DB::select("SHOW COLUMNS FROM absensi LIKE 'pertemuan_id'");
        if (empty($absensiColumns)) {
            DB::statement("ALTER TABLE absensi ADD COLUMN pertemuan_id BIGINT UNSIGNED NULL AFTER krs_id");
            DB::statement("ALTER TABLE absensi ADD CONSTRAINT fk_absensi_pertemuan FOREIGN KEY (pertemuan_id) REFERENCES pertemuan(id) ON DELETE SET NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key and column from absensi
        try {
            DB::statement("ALTER TABLE absensi DROP FOREIGN KEY fk_absensi_pertemuan");
        } catch (\Exception $e) {}
        
        $absensiColumns = DB::select("SHOW COLUMNS FROM absensi LIKE 'pertemuan_id'");
        if (!empty($absensiColumns)) {
            DB::statement("ALTER TABLE absensi DROP COLUMN pertemuan_id");
        }

        // Remove approval columns from pertemuan
        $columns = ['rejection_note', 'approved_at', 'approved_by', 'is_approved'];
        foreach ($columns as $col) {
            $exists = DB::select("SHOW COLUMNS FROM pertemuan LIKE '$col'");
            if (!empty($exists)) {
                DB::statement("ALTER TABLE pertemuan DROP COLUMN $col");
            }
        }
    }
};
