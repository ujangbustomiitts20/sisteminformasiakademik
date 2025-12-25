<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use App\Models\KurikulumMataKuliah;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programStudis = ProgramStudi::all();

        foreach ($programStudis as $prodi) {
            // Kurikulum 2020
            $kurikulum2020 = Kurikulum::firstOrCreate([
                'program_studi_id' => $prodi->id,
                'kode' => 'KUR-' . $prodi->kode . '-2020',
            ], [
                'nama' => 'Kurikulum 2020',
                'tahun_mulai' => 2020,
                'tahun_selesai' => 2024,
                'total_sks_wajib' => 110,
                'total_sks_pilihan' => 34,
                'total_sks_lulus' => 144,
                'minimal_semester' => 8,
                'maksimal_semester' => 14,
                'deskripsi' => 'Kurikulum tahun 2020 untuk program studi ' . $prodi->nama,
                'is_aktif' => false,
            ]);

            // Kurikulum 2024
            $kurikulum2024 = Kurikulum::firstOrCreate([
                'program_studi_id' => $prodi->id,
                'kode' => 'KUR-' . $prodi->kode . '-2024',
            ], [
                'nama' => 'Kurikulum 2024 (Merdeka Belajar)',
                'tahun_mulai' => 2024,
                'tahun_selesai' => null,
                'total_sks_wajib' => 100,
                'total_sks_pilihan' => 44,
                'total_sks_lulus' => 144,
                'minimal_semester' => 8,
                'maksimal_semester' => 14,
                'deskripsi' => 'Kurikulum Merdeka Belajar tahun 2024 untuk program studi ' . $prodi->nama,
                'is_aktif' => true,
            ]);

            // Ambil beberapa mata kuliah untuk ditambahkan ke kurikulum
            $mataKuliahs = MataKuliah::inRandomOrder()->take(20)->get();
            
            $semester = 1;
            $count = 0;
            foreach ($mataKuliahs as $mk) {
                // Skip jika sudah ada
                if (KurikulumMataKuliah::where('kurikulum_id', $kurikulum2024->id)->where('mata_kuliah_id', $mk->id)->exists()) {
                    continue;
                }

                KurikulumMataKuliah::create([
                    'kurikulum_id' => $kurikulum2024->id,
                    'mata_kuliah_id' => $mk->id,
                    'semester_rekomendasi' => $semester,
                    'kategori' => $count < 15 ? 'Wajib' : 'Pilihan',
                ]);

                $count++;
                if ($count % 5 == 0) {
                    $semester++;
                }
            }
        }

        $this->command->info('Kurikulum seeder berhasil dijalankan!');
    }
}
