<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PeriodeUjian;
use App\Models\TahunAkademik;
use App\Models\JadwalUjian;

class PeriodeUjianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil tahun akademik aktif
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->first();
        
        if (!$tahunAkademik) {
            $this->command->warn('Tidak ada tahun akademik. Jalankan TahunAkademikSeeder terlebih dahulu.');
            return;
        }

        // Buat periode UTS
        $uts = PeriodeUjian::firstOrCreate(
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'jenis' => 'UTS',
            ],
            [
                'nama' => 'Ujian Tengah Semester ' . $tahunAkademik->nama,
                'tanggal_mulai' => now()->addMonth(),
                'tanggal_selesai' => now()->addMonth()->addWeeks(2),
                'tanggal_cetak_kartu' => now()->addDays(20),
                'minimal_kehadiran' => 75,
                'cek_pembayaran' => true,
                'status' => 'aktif',
            ]
        );

        // Buat periode UAS
        $uas = PeriodeUjian::firstOrCreate(
            [
                'tahun_akademik_id' => $tahunAkademik->id,
                'jenis' => 'UAS',
            ],
            [
                'nama' => 'Ujian Akhir Semester ' . $tahunAkademik->nama,
                'tanggal_mulai' => now()->addMonths(3),
                'tanggal_selesai' => now()->addMonths(3)->addWeeks(2),
                'tanggal_cetak_kartu' => now()->addMonths(3)->subWeek(),
                'minimal_kehadiran' => 75,
                'cek_pembayaran' => true,
                'status' => 'draft',
            ]
        );

        $this->command->info('Periode UTS dan UAS berhasil dibuat!');

        // Buat jadwal ujian contoh jika ada mata kuliah
        $mataKuliahs = \App\Models\MataKuliah::take(5)->get();
        $ruangan = \App\Models\Ruangan::first();
        $tahunAkademik = \App\Models\TahunAkademik::where('is_aktif', true)->first();

        if ($mataKuliahs->isNotEmpty() && $tahunAkademik) {
            $tanggal = now()->addMonth();
            
            foreach ($mataKuliahs as $i => $mk) {
                // Cek apakah sudah ada
                $existing = JadwalUjian::where('periode_ujian_id', $uts->id)
                    ->where('mata_kuliah_id', $mk->id)
                    ->first();
                
                if (!$existing) {
                    JadwalUjian::create([
                        'periode_ujian_id' => $uts->id,
                        'tahun_akademik_id' => $tahunAkademik->id,
                        'mata_kuliah_id' => $mk->id,
                        'jenis_ujian' => 'UTS',
                        'ruangan' => $ruangan ? $ruangan->nama_ruangan : 'R101',
                        'tanggal' => $tanggal->copy()->addDays($i),
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '10:00',
                    ]);
                }
            }
            
            $this->command->info('Jadwal ujian contoh berhasil dibuat!');
        }
    }
}
