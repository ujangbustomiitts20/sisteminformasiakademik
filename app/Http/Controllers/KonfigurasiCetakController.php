<?php

namespace App\Http\Controllers;

use App\Models\KonfigurasiCetak;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class KonfigurasiCetakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $konfigurasi = KonfigurasiCetak::orderBy('nama')->get();
        
        return view('admin.konfigurasi-cetak.index', compact('konfigurasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KonfigurasiCetak $konfigurasiCetak)
    {
        return view('admin.konfigurasi-cetak.edit', compact('konfigurasiCetak'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KonfigurasiCetak $konfigurasiCetak)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'judul' => 'nullable|string|max:255',
            'ukuran_kertas' => 'required|in:a4,letter,legal,f4',
            'orientasi' => 'required|in:portrait,landscape',
            'margin_top' => 'required|numeric|min:0|max:100',
            'margin_bottom' => 'required|numeric|min:0|max:100',
            'margin_left' => 'required|numeric|min:0|max:100',
            'margin_right' => 'required|numeric|min:0|max:100',
            'tampilkan_kop' => 'boolean',
            'tampilkan_logo' => 'boolean',
            'tampilkan_ttd' => 'boolean',
            'jabatan_ttd' => 'nullable|string|max:255',
            'nama_ttd' => 'nullable|string|max:255',
            'nip_ttd' => 'nullable|string|max:50',
            'tampilkan_footer' => 'boolean',
            'catatan_kaki' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Handle checkboxes
        $validated['tampilkan_kop'] = $request->has('tampilkan_kop');
        $validated['tampilkan_logo'] = $request->has('tampilkan_logo');
        $validated['tampilkan_ttd'] = $request->has('tampilkan_ttd');
        $validated['tampilkan_footer'] = $request->has('tampilkan_footer');
        $validated['is_active'] = $request->has('is_active');

        $konfigurasiCetak->update($validated);

        return redirect()->route('konfigurasi-cetak.index')
            ->with('success', 'Konfigurasi cetak berhasil diperbarui.');
    }

    /**
     * Preview the print configuration (HTML view)
     */
    public function preview(KonfigurasiCetak $konfigurasiCetak)
    {
        return view('admin.konfigurasi-cetak.preview', compact('konfigurasiCetak'));
    }

    /**
     * Preview the print configuration as PDF with dummy data
     */
    public function previewPdf(KonfigurasiCetak $konfigurasiCetak)
    {
        $config = $konfigurasiCetak;
        $dummyData = $this->getDummyDataByKode($konfigurasiCetak->kode);
        
        $viewPath = 'cetak.preview.' . $konfigurasiCetak->kode;
        
        // Check if specific preview view exists, otherwise use generic
        if (!view()->exists($viewPath)) {
            $viewPath = 'admin.konfigurasi-cetak.preview-pdf';
        }
        
        $data = array_merge(['config' => $config], $dummyData);
        
        $pdf = Pdf::loadView($viewPath, $data);
        
        // Set paper size and orientation
        $paperSize = $config->ukuran_kertas;
        if ($paperSize === 'f4') {
            $pdf->setPaper([0, 0, 612, 936], $config->orientasi);
        } else {
            $pdf->setPaper($paperSize, $config->orientasi);
        }
        
        return $pdf->stream('preview_' . $config->kode . '.pdf');
    }

    /**
     * Get dummy data based on document type
     */
    private function getDummyDataByKode(string $kode): array
    {
        $baseMahasiswa = [
            'nim' => '2024001001',
            'nama' => 'AHMAD MAHASISWA CONTOH',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => '2002-05-15',
            'alamat' => 'Jl. Pendidikan No. 123, Jakarta Selatan',
            'telepon' => '081234567890',
            'email' => 'ahmad.mahasiswa@email.com',
            'angkatan' => '2024',
            'semester_aktif' => 3,
            'status' => 'Aktif',
            'jenis_kelamin' => 'Laki-laki',
            'agama' => 'Islam',
        ];

        $baseProgramStudi = [
            'nama' => 'Teknik Informatika',
            'kode' => 'TI',
            'jenjang' => 'S1',
            'kaprodi' => 'Dr. Budi Santoso, M.Kom.',
            'nip_kaprodi' => '198501012010011001',
        ];

        $baseFakultas = [
            'nama' => 'Fakultas Teknik',
            'kode' => 'FT',
            'dekan' => 'Prof. Dr. Ir. Slamet Widodo, M.T.',
            'nip_dekan' => '196812121990011001',
        ];

        $baseDosenWali = [
            'nama' => 'Drs. Cahyo Wibowo, M.Kom.',
            'nidn' => '0312126701',
            'nip' => '196712031992011001',
        ];

        $baseTahunAkademik = [
            'tahun' => '2024/2025',
            'semester' => 'Ganjil',
            'nama_lengkap' => 'Ganjil 2024/2025',
        ];

        // Data mata kuliah contoh
        $mataKuliah = [
            ['kode' => 'TI101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3, 'nilai' => 'A', 'bobot' => 4.00],
            ['kode' => 'TI102', 'nama' => 'Basis Data', 'sks' => 3, 'nilai' => 'A-', 'bobot' => 3.75],
            ['kode' => 'TI103', 'nama' => 'Struktur Data', 'sks' => 3, 'nilai' => 'B+', 'bobot' => 3.50],
            ['kode' => 'TI104', 'nama' => 'Pemrograman Web', 'sks' => 3, 'nilai' => 'A', 'bobot' => 4.00],
            ['kode' => 'TI105', 'nama' => 'Jaringan Komputer', 'sks' => 3, 'nilai' => 'B+', 'bobot' => 3.50],
            ['kode' => 'TI106', 'nama' => 'Matematika Diskrit', 'sks' => 2, 'nilai' => 'B', 'bobot' => 3.00],
            ['kode' => 'TI107', 'nama' => 'Sistem Operasi', 'sks' => 3, 'nilai' => 'A-', 'bobot' => 3.75],
        ];

        $jadwalKuliah = [
            ['hari' => 'Senin', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'ruangan' => 'R.101', 'kelas' => 'A', 'dosen' => 'Dr. Andi Wijaya, M.T.'],
            ['hari' => 'Selasa', 'jam_mulai' => '10:00', 'jam_selesai' => '12:30', 'ruangan' => 'R.102', 'kelas' => 'A', 'dosen' => 'Dra. Siti Aminah, M.Kom.'],
            ['hari' => 'Rabu', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30', 'ruangan' => 'R.103', 'kelas' => 'A', 'dosen' => 'Dr. Budi Santoso, M.Kom.'],
            ['hari' => 'Kamis', 'jam_mulai' => '08:00', 'jam_selesai' => '10:30', 'ruangan' => 'Lab.1', 'kelas' => 'A', 'dosen' => 'Ir. Dewi Putri, M.T.'],
            ['hari' => 'Jumat', 'jam_mulai' => '10:00', 'jam_selesai' => '12:30', 'ruangan' => 'R.104', 'kelas' => 'A', 'dosen' => 'Dr. Eko Prasetyo, M.Si.'],
            ['hari' => 'Senin', 'jam_mulai' => '13:00', 'jam_selesai' => '14:40', 'ruangan' => 'R.105', 'kelas' => 'A', 'dosen' => 'Drs. Fajar Nugroho, M.M.'],
            ['hari' => 'Selasa', 'jam_mulai' => '13:00', 'jam_selesai' => '15:30', 'ruangan' => 'Lab.2', 'kelas' => 'A', 'dosen' => 'Ir. Gita Sari, M.Kom.'],
        ];

        // Gabungkan jadwal dengan mata kuliah
        $krsData = [];
        for ($i = 0; $i < count($mataKuliah); $i++) {
            $krsData[] = array_merge($mataKuliah[$i], $jadwalKuliah[$i]);
        }

        $baseData = [
            'mahasiswa' => (object) array_merge($baseMahasiswa, [
                'programStudi' => (object) array_merge($baseProgramStudi, [
                    'fakultas' => (object) $baseFakultas
                ]),
                'dosenWali' => (object) $baseDosenWali,
            ]),
            'tahunAkademik' => (object) $baseTahunAkademik,
            'programStudi' => (object) array_merge($baseProgramStudi, [
                'fakultas' => (object) $baseFakultas
            ]),
            'fakultas' => (object) $baseFakultas,
        ];

        switch ($kode) {
            case 'krs':
                return array_merge($baseData, [
                    'krs' => collect($krsData)->map(function($item) {
                        return (object)[
                            'jadwalKuliah' => (object)[
                                'mataKuliah' => (object)[
                                    'kode' => $item['kode'],
                                    'nama' => $item['nama'],
                                    'sks' => $item['sks'],
                                ],
                                'dosen' => (object)['nama' => $item['dosen'], 'nidn' => '0312126701'],
                                'ruangan' => (object)['kode' => $item['ruangan'], 'nama' => $item['ruangan']],
                                'hari' => $item['hari'],
                                'jam_mulai' => $item['jam_mulai'],
                                'jam_selesai' => $item['jam_selesai'],
                                'kelas' => $item['kelas'],
                            ],
                        ];
                    }),
                    'totalSks' => collect($mataKuliah)->sum('sks'),
                ]);

            case 'khs':
                return array_merge($baseData, [
                    'krs' => collect($krsData)->map(function($item) {
                        return (object)[
                            'jadwalKuliah' => (object)[
                                'mataKuliah' => (object)[
                                    'kode' => $item['kode'],
                                    'nama' => $item['nama'],
                                    'sks' => $item['sks'],
                                ],
                            ],
                            'nilai' => (object)[
                                'nilai_akhir' => rand(75, 95),
                                'huruf' => $item['nilai'],
                                'bobot' => $item['bobot'],
                            ],
                        ];
                    }),
                    'totalSks' => collect($mataKuliah)->sum('sks'),
                    'ips' => 3.57,
                ]);

            case 'transkrip':
                $allMataKuliah = [];
                $semesters = ['Ganjil 2024/2025', 'Genap 2023/2024', 'Ganjil 2023/2024'];
                foreach ($semesters as $sem) {
                    foreach ($mataKuliah as $mk) {
                        $allMataKuliah[] = array_merge($mk, ['semester' => $sem]);
                    }
                }
                return array_merge($baseData, [
                    'krs' => collect($allMataKuliah)->map(function($item) {
                        return (object)[
                            'jadwalKuliah' => (object)[
                                'mataKuliah' => (object)[
                                    'kode' => $item['kode'],
                                    'nama' => $item['nama'],
                                    'sks' => $item['sks'],
                                ],
                                'tahunAkademik' => (object)[
                                    'tahun' => substr($item['semester'], -9),
                                    'semester' => explode(' ', $item['semester'])[0],
                                ],
                            ],
                            'nilai' => (object)[
                                'huruf' => $item['nilai'],
                                'bobot' => $item['bobot'],
                            ],
                        ];
                    }),
                    'totalSks' => collect($allMataKuliah)->sum('sks'),
                    'ipk' => 3.52,
                ]);

            case 'invoice':
                return array_merge($baseData, [
                    'tagihan' => (object)[
                        'no_tagihan' => 'INV-' . date('Ymd') . '-0001',
                        'tanggal' => now()->format('d F Y'),
                        'jatuh_tempo' => now()->addDays(30)->format('d F Y'),
                        'jenis_tagihan' => 'SPP Semester Ganjil 2024/2025',
                        'nominal' => 5000000,
                        'diskon' => 500000,
                        'denda' => 0,
                        'total_bayar' => 4500000,
                        'jumlah_dibayar' => 0,
                        'sisa_tagihan' => 4500000,
                        'status' => 'Belum Bayar',
                    ],
                    'rincian' => collect([
                        (object)['nama' => 'SPP', 'nominal' => 3500000],
                        (object)['nama' => 'Praktikum', 'nominal' => 1000000],
                        (object)['nama' => 'Kegiatan Mahasiswa', 'nominal' => 500000],
                    ]),
                ]);

            case 'kwitansi':
                return array_merge($baseData, [
                    'transaksi' => (object)[
                        'no_transaksi' => 'TRX-' . date('Ymd') . '-0001',
                        'tanggal' => now()->format('d F Y'),
                        'metode_pembayaran' => 'Transfer Bank',
                        'jumlah' => 4500000,
                        'keterangan' => 'Pembayaran SPP Semester Ganjil 2024/2025',
                        'status' => 'Verified',
                    ],
                    'tagihan' => (object)[
                        'no_tagihan' => 'INV-' . date('Ymd') . '-0001',
                        'jenis_tagihan' => 'SPP Semester Ganjil 2024/2025',
                    ],
                ]);

            case 'surat_cuti':
                return array_merge($baseData, [
                    'cuti' => (object)[
                        'no_surat' => 'SK-CUTI/' . date('Y') . '/001',
                        'tanggal_pengajuan' => now()->subDays(7)->format('d F Y'),
                        'tanggal_disetujui' => now()->format('d F Y'),
                        'alasan' => 'Alasan kesehatan yang memerlukan perawatan intensif',
                        'semester_mulai' => 'Ganjil 2024/2025',
                        'semester_selesai' => 'Genap 2024/2025',
                        'lama_cuti' => '2 Semester',
                    ],
                ]);

            case 'surat_aktif':
                return array_merge($baseData, [
                    'surat' => (object)[
                        'no_surat' => 'SK-AKTIF/' . date('Y') . '/001',
                        'tanggal' => now()->format('d F Y'),
                        'keperluan' => 'Pengajuan Beasiswa',
                        'berlaku_sampai' => now()->addMonths(3)->format('d F Y'),
                    ],
                ]);

            case 'kartu_ujian':
                return array_merge($baseData, [
                    'jadwalUjian' => collect($krsData)->take(5)->map(function($item, $index) {
                        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                        return (object)[
                            'mataKuliah' => (object)[
                                'kode' => $item['kode'],
                                'nama' => $item['nama'],
                            ],
                            'tanggal' => now()->addDays($index)->format('d F Y'),
                            'hari' => $days[$index],
                            'jam_mulai' => '08:00',
                            'jam_selesai' => '10:00',
                            'ruangan' => 'Ruang Ujian ' . ($index + 1),
                            'pengawas' => $item['dosen'],
                        ];
                    }),
                    'periode_ujian' => 'UTS Ganjil 2024/2025',
                    'foto_url' => null,
                ]);

            case 'yudisium':
                return array_merge($baseData, [
                    'yudisium' => (object)[
                        'no_surat' => 'SK-YUD/' . date('Y') . '/001',
                        'tanggal' => now()->format('d F Y'),
                        'tanggal_yudisium' => now()->subDays(7)->format('d F Y'),
                        'ipk' => 3.52,
                        'total_sks' => 144,
                        'predikat' => 'Sangat Memuaskan',
                        'judul_skripsi' => 'Implementasi Sistem Informasi Akademik Berbasis Web dengan Framework Laravel',
                        'tanggal_lulus' => now()->format('d F Y'),
                    ],
                ]);

            case 'laporan':
                return [
                    'judul' => 'LAPORAN REKAP DATA MAHASISWA',
                    'periode' => 'Tahun Akademik 2024/2025',
                    'tanggal_cetak' => now()->format('d F Y'),
                    'data' => collect([
                        (object)['no' => 1, 'prodi' => 'Teknik Informatika', 'mahasiswa_aktif' => 450, 'mahasiswa_cuti' => 10, 'mahasiswa_lulus' => 85],
                        (object)['no' => 2, 'prodi' => 'Sistem Informasi', 'mahasiswa_aktif' => 380, 'mahasiswa_cuti' => 8, 'mahasiswa_lulus' => 72],
                        (object)['no' => 3, 'prodi' => 'Teknik Komputer', 'mahasiswa_aktif' => 220, 'mahasiswa_cuti' => 5, 'mahasiswa_lulus' => 45],
                        (object)['no' => 4, 'prodi' => 'Manajemen Informatika', 'mahasiswa_aktif' => 180, 'mahasiswa_cuti' => 3, 'mahasiswa_lulus' => 35],
                    ]),
                    'total' => (object)['mahasiswa_aktif' => 1230, 'mahasiswa_cuti' => 26, 'mahasiswa_lulus' => 237],
                ];

            case 'daftar_hadir':
                $mahasiswaList = collect([
                    ['nim' => '2024001001', 'nama' => 'Ahmad Mahasiswa'],
                    ['nim' => '2024001002', 'nama' => 'Budi Santoso'],
                    ['nim' => '2024001003', 'nama' => 'Citra Dewi'],
                    ['nim' => '2024001004', 'nama' => 'Dian Permata'],
                    ['nim' => '2024001005', 'nama' => 'Eka Putri'],
                    ['nim' => '2024001006', 'nama' => 'Fajar Nugroho'],
                    ['nim' => '2024001007', 'nama' => 'Gita Sari'],
                    ['nim' => '2024001008', 'nama' => 'Hadi Wijaya'],
                    ['nim' => '2024001009', 'nama' => 'Indah Permatasari'],
                    ['nim' => '2024001010', 'nama' => 'Joko Susilo'],
                ]);
                
                return [
                    'mataKuliah' => (object)['kode' => 'TI101', 'nama' => 'Algoritma dan Pemrograman', 'sks' => 3],
                    'kelas' => 'A',
                    'dosen' => (object)['nama' => 'Dr. Andi Wijaya, M.T.', 'nidn' => '0312126701'],
                    'tahunAkademik' => (object) $baseTahunAkademik,
                    'pertemuan' => 16,
                    'mahasiswaList' => $mahasiswaList->map(function($mhs) {
                        $kehadiran = [];
                        for ($i = 1; $i <= 16; $i++) {
                            $kehadiran[$i] = rand(0, 10) > 1 ? 'H' : (rand(0, 1) ? 'I' : 'A');
                        }
                        return (object) array_merge($mhs, ['kehadiran' => $kehadiran]);
                    }),
                ];

            default:
                return $baseData;
        }
    }

    /**
     * Reset configuration to default
     */
    public function reset(KonfigurasiCetak $konfigurasiCetak)
    {
        $defaults = collect(KonfigurasiCetak::getDefaults())
            ->firstWhere('kode', $konfigurasiCetak->kode);

        if ($defaults) {
            $konfigurasiCetak->update($defaults);
            return redirect()->route('konfigurasi-cetak.index')
                ->with('success', 'Konfigurasi berhasil direset ke default.');
        }

        return redirect()->route('konfigurasi-cetak.index')
            ->with('error', 'Konfigurasi default tidak ditemukan.');
    }

    /**
     * Reset all configurations to default
     */
    public function resetAll()
    {
        $defaults = KonfigurasiCetak::getDefaults();

        foreach ($defaults as $config) {
            KonfigurasiCetak::updateOrCreate(
                ['kode' => $config['kode']],
                array_merge($config, ['is_active' => true])
            );
        }

        return redirect()->route('konfigurasi-cetak.index')
            ->with('success', 'Semua konfigurasi berhasil direset ke default.');
    }
}
