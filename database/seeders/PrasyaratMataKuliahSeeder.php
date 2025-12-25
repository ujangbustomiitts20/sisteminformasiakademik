<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MataKuliah;

class PrasyaratMataKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('prasyarat_mata_kuliah')->truncate();
        
        // Ambil mata kuliah berdasarkan kode
        $mataKuliah = MataKuliah::all()->keyBy('kode');
        
        // Contoh data prasyarat yang realistis
        $prasyaratData = [
            // Pemrograman Lanjut (Semester 2) membutuhkan Algoritma & Pemrograman (Semester 1)
            ['mk' => 'TI102', 'prasyarat' => 'TI101', 'jenis' => 'wajib', 'nilai' => 'D'],
            
            // Basis Data (Semester 3) membutuhkan Pemrograman Lanjut (Semester 2)
            ['mk' => 'TI201', 'prasyarat' => 'TI102', 'jenis' => 'wajib', 'nilai' => 'D'],
            
            // Struktur Data (Semester 3) membutuhkan Pemrograman Lanjut
            ['mk' => 'TI202', 'prasyarat' => 'TI102', 'jenis' => 'wajib', 'nilai' => 'C'],
            
            // Pemrograman Web (Semester 4) membutuhkan Pemrograman Lanjut dan Basis Data
            ['mk' => 'TI301', 'prasyarat' => 'TI102', 'jenis' => 'wajib', 'nilai' => 'D'],
            ['mk' => 'TI301', 'prasyarat' => 'TI201', 'jenis' => 'wajib', 'nilai' => 'D'],
            
            // Rekayasa Perangkat Lunak (Semester 4) membutuhkan Struktur Data
            ['mk' => 'TI302', 'prasyarat' => 'TI202', 'jenis' => 'wajib', 'nilai' => 'D'],
            
            // Jaringan Komputer (Semester 5) membutuhkan Sistem Operasi sebagai pilihan
            ['mk' => 'TI401', 'prasyarat' => 'TI103', 'jenis' => 'pilihan', 'nilai' => null],
            
            // Kecerdasan Buatan (Semester 6) membutuhkan Matematika Diskrit dan Struktur Data
            ['mk' => 'TI501', 'prasyarat' => 'TI104', 'jenis' => 'wajib', 'nilai' => 'C'],
            ['mk' => 'TI501', 'prasyarat' => 'TI202', 'jenis' => 'wajib', 'nilai' => 'C'],
        ];
        
        foreach ($prasyaratData as $data) {
            $mk = $mataKuliah->get($data['mk']);
            $prasyarat = $mataKuliah->get($data['prasyarat']);
            
            if ($mk && $prasyarat) {
                DB::table('prasyarat_mata_kuliah')->insert([
                    'mata_kuliah_id' => $mk->id,
                    'mata_kuliah_prasyarat_id' => $prasyarat->id,
                    'jenis_prasyarat' => $data['jenis'],
                    'nilai_minimal' => $data['nilai'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                echo "Prasyarat: {$data['mk']} <- {$data['prasyarat']} ({$data['jenis']})\n";
            }
        }
        
        echo "\nSeeder prasyarat selesai!\n";
    }
}
