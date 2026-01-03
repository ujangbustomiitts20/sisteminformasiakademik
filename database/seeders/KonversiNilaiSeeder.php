<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanKonversi;
use App\Models\DetailKonversi;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use Carbon\Carbon;

class KonversiNilaiSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = Mahasiswa::take(5)->get();
        $mataKuliahs = MataKuliah::take(10)->get();

        if ($mahasiswas->isEmpty() || $mataKuliahs->isEmpty()) {
            return;
        }

        $universitas = [
            'Universitas Indonesia',
            'Institut Teknologi Bandung',
            'Universitas Gadjah Mada',
            'Universitas Airlangga',
            'Universitas Padjadjaran',
        ];

        $statusList = ['draft', 'diajukan', 'diproses', 'disetujui', 'ditolak'];

        foreach ($mahasiswas as $index => $mahasiswa) {
            $pengajuan = PengajuanKonversi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'universitas_asal' => $universitas[$index % 5],
                'program_studi_asal' => 'Teknik Informatika',
                'nim_asal' => 'A' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'tahun_masuk_asal' => 2020 + ($index % 3),
                'dokumen_transkrip' => null,
                'dokumen_silabus' => null,
                'status' => $statusList[$index % 5],
                'catatan' => $index > 2 ? 'Semua mata kuliah dapat dikonversi' : null,
            ]);

            // Detail konversi (3 mata kuliah per pengajuan)
            $selectedMks = $mataKuliahs->random(3);
            $nilaiAsal = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'];
            $bobotMap = ['A' => 4.00, 'A-' => 3.75, 'B+' => 3.50, 'B' => 3.00, 'B-' => 2.75, 'C+' => 2.50, 'C' => 2.00];
            
            foreach ($selectedMks as $mk) {
                $nilai = $nilaiAsal[array_rand($nilaiAsal)];
                DetailKonversi::create([
                    'pengajuan_konversi_id' => $pengajuan->id,
                    'kode_mk_asal' => 'MK' . rand(100, 999),
                    'nama_mk_asal' => 'Mata Kuliah ' . $mk->nama,
                    'sks_asal' => rand(2, 4),
                    'nilai_asal' => $nilai,
                    'bobot_asal' => $bobotMap[$nilai] ?? 2.00,
                    'mata_kuliah_id' => $mk->id,
                    'status' => $index > 1 ? 'disetujui' : 'pending',
                    'nilai_konversi' => $index > 1 ? $nilai : null,
                    'bobot_konversi' => $index > 1 ? ($bobotMap[$nilai] ?? 2.00) : null,
                    'alasan' => $index > 1 ? 'Silabus equivalent' : null,
                ]);
            }
        }
    }
}
