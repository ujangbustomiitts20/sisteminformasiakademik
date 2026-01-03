<?php

namespace App\Http\Controllers;

use App\Models\HasilSeleksi;
use App\Models\CalonMahasiswa;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\NilaiSeleksi;
use App\Models\KuotaPmb;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeleksiPmbController extends Controller
{
    /**
     * Index halaman seleksi PMB
     */
    public function index()
    {
        // Optimized query menggunakan withCount untuk menghindari N+1 problem
        $statistikGelombang = GelombangPmb::with('periodePmb')
            ->withCount([
                'calonMahasiswa as total_peserta',
                'calonMahasiswa as sudah_dinilai' => function ($query) {
                    $query->whereHas('nilaiSeleksi');
                },
                'hasilSeleksi as lulus' => function ($query) {
                    $query->where('status', 'lulus');
                },
                'hasilSeleksi as tidak_lulus' => function ($query) {
                    $query->where('status', 'tidak_lulus');
                },
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($gelombang) {
                return [
                    'gelombang_id' => $gelombang->id,
                    'hashid' => $gelombang->hashid,
                    'periode' => $gelombang->periodePmb->nama ?? '-',
                    'gelombang' => $gelombang->nama,
                    'total_peserta' => $gelombang->total_peserta,
                    'sudah_dinilai' => $gelombang->sudah_dinilai,
                    'lulus' => $gelombang->lulus,
                    'tidak_lulus' => $gelombang->tidak_lulus,
                ];
            });

        return view('pmb.seleksi.index', compact('statistikGelombang'));
    }

    /**
     * Halaman input nilai seleksi
     */
    public function inputNilai(Request $request)
    {
        $gelombangs = GelombangPmb::with('periodePmb')->active()->orderBy('id', 'desc')->get();
        $jalurs = JalurSeleksi::where('is_active', true)->get();
        $prodis = ProgramStudi::orderBy('nama')->get();
        
        $peserta = collect();
        $komponenNilai = [
            'nilai_rapor' => ['label' => 'Nilai Rapor', 'bobot' => 30],
            'nilai_ujian_tulis' => ['label' => 'Ujian Tulis', 'bobot' => 40],
            'nilai_wawancara' => ['label' => 'Wawancara', 'bobot' => 30],
        ];
        
        if ($request->filled('gelombang')) {
            $query = CalonMahasiswa::with(['programStudi', 'jalurSeleksi', 'nilaiSeleksi'])
                ->where('gelombang_pmb_id', $request->gelombang)
                ->whereIn('status', ['terdaftar', 'mengikuti_ujian']);

            if ($request->filled('jalur')) {
                $query->where('jalur_seleksi_id', $request->jalur);
            }

            if ($request->filled('prodi')) {
                $query->where('program_studi_id', $request->prodi);
            }

            $peserta = $query->orderBy('nama_lengkap')->get();
        }

        return view('pmb.seleksi.input-nilai', compact('peserta', 'gelombangs', 'jalurs', 'prodis', 'komponenNilai'));
    }

    /**
     * Simpan nilai seleksi (batch)
     */
    public function storeNilai(Request $request)
    {
        $request->validate([
            'gelombang_id' => 'required|exists:gelombang_pmb,id',
            'nilai' => 'required|array',
        ]);

        $gelombangId = $request->gelombang_id;

        DB::beginTransaction();
        try {
            foreach ($request->nilai as $camabaId => $komponens) {
                // Delete existing nilai for this calon mahasiswa in this gelombang
                NilaiSeleksi::where('calon_mahasiswa_id', $camabaId)
                    ->where('gelombang_pmb_id', $gelombangId)
                    ->delete();

                foreach ($komponens as $key => $data) {
                    if (!empty($data['nilai'])) {
                        NilaiSeleksi::create([
                            'calon_mahasiswa_id' => $camabaId,
                            'gelombang_pmb_id' => $gelombangId,
                            'komponen_nilai' => $data['komponen'],
                            'nilai' => $data['nilai'],
                            'bobot' => $data['bobot'] / 100, // Convert percentage to decimal
                            'input_by' => auth()->id(),
                        ]);
                    }
                }

                // Update status calon mahasiswa
                $camaba = CalonMahasiswa::find($camabaId);
                if ($camaba && $camaba->nilaiSeleksi()->count() > 0) {
                    $camaba->update([
                        'status' => 'mengikuti_ujian',
                        'status_pendaftaran' => 'mengikuti_ujian'
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Nilai seleksi berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan nilai: ' . $e->getMessage());
        }
    }

    /**
     * Halaman proses seleksi
     */
    public function prosesSeleksi(Request $request)
    {
        $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();
        $selectedGelombang = null;
        $summary = [];
        $kuotaPerProdi = [];

        if ($request->filled('gelombang')) {
            $selectedGelombang = GelombangPmb::with('periodePmb')->find($request->gelombang);
            
            if ($selectedGelombang) {
                // Optimized: gunakan single query dengan aggregate
                $stats = CalonMahasiswa::where('gelombang_pmb_id', $selectedGelombang->id)
                    ->whereIn('status', ['terdaftar', 'mengikuti_ujian'])
                    ->selectRaw('COUNT(*) as total_peserta')
                    ->selectRaw('SUM(CASE WHEN id IN (SELECT DISTINCT calon_mahasiswa_id FROM nilai_seleksi) THEN 1 ELSE 0 END) as sudah_nilai')
                    ->first();

                $summary = [
                    'total_peserta' => $stats->total_peserta ?? 0,
                    'sudah_nilai' => $stats->sudah_nilai ?? 0,
                    'belum_nilai' => ($stats->total_peserta ?? 0) - ($stats->sudah_nilai ?? 0),
                    'total_kuota' => KuotaPmb::where('gelombang_pmb_id', $selectedGelombang->id)->sum('kuota'),
                ];

                // Kuota per prodi + jalur dengan keterangan jalur
                $kuotaPerProdi = KuotaPmb::where('gelombang_pmb_id', $selectedGelombang->id)
                    ->with(['programStudi', 'jalurSeleksi'])
                    ->orderBy('program_studi_id')
                    ->orderBy('jalur_seleksi_id')
                    ->get()
                    ->map(function ($kuota) use ($selectedGelombang) {
                        $pendaftarStats = CalonMahasiswa::where('gelombang_pmb_id', $selectedGelombang->id)
                            ->where('program_studi_id', $kuota->program_studi_id)
                            ->where('jalur_seleksi_id', $kuota->jalur_seleksi_id)
                            ->selectRaw('COUNT(*) as pendaftar')
                            ->selectRaw('SUM(CASE WHEN id IN (SELECT DISTINCT calon_mahasiswa_id FROM nilai_seleksi) THEN 1 ELSE 0 END) as sudah_nilai')
                            ->first();

                        // Ambil detail pendaftar untuk modal
                        $detailPendaftar = CalonMahasiswa::where('gelombang_pmb_id', $selectedGelombang->id)
                            ->where('program_studi_id', $kuota->program_studi_id)
                            ->where('jalur_seleksi_id', $kuota->jalur_seleksi_id)
                            ->with('nilaiSeleksi')
                            ->get()
                            ->map(function ($camaba) {
                                $totalNilai = $camaba->nilaiSeleksi->sum('nilai_akhir');
                                return [
                                    'id' => $camaba->id,
                                    'no_pendaftaran' => $camaba->no_pendaftaran,
                                    'nama' => $camaba->nama_lengkap,
                                    'status' => $camaba->status_pendaftaran,
                                    'sudah_nilai' => $camaba->nilaiSeleksi->count() > 0,
                                    'total_nilai' => round($totalNilai, 2),
                                ];
                            })->toArray();

                        return [
                            'prodi_id' => $kuota->program_studi_id,
                            'jalur_id' => $kuota->jalur_seleksi_id,
                            'prodi' => $kuota->programStudi->nama ?? '-',
                            'jalur' => $kuota->jalurSeleksi->nama ?? '-',
                            'jalur_kode' => $kuota->jalurSeleksi->kode ?? '-',
                            'kuota' => $kuota->kuota,
                            'terisi' => $kuota->terisi ?? 0,
                            'pendaftar' => $pendaftarStats->pendaftar ?? 0,
                            'sudah_nilai' => $pendaftarStats->sudah_nilai ?? 0,
                            'detail' => $detailPendaftar,
                        ];
                    })->toArray();
            }
        }

        return view('pmb.seleksi.proses', compact('gelombangs', 'selectedGelombang', 'summary', 'kuotaPerProdi'));
    }

    /**
     * Eksekusi proses seleksi otomatis
     */
    public function executeSeleksi(Request $request)
    {
        $request->validate([
            'gelombang_id' => 'required|exists:gelombang_pmb,id',
            'passing_grade' => 'nullable|numeric|min:0|max:100',
        ]);

        $gelombangId = $request->gelombang_id;
        $passingGrade = $request->passing_grade ?? 0;

        DB::beginTransaction();
        try {
            // Reset hasil seleksi sebelumnya untuk gelombang ini
            HasilSeleksi::where('gelombang_pmb_id', $gelombangId)->delete();

            // Ambil semua pendaftar yang sudah dinilai
            $pendaftars = CalonMahasiswa::where('gelombang_pmb_id', $gelombangId)
                ->whereHas('nilaiSeleksi')
                ->with(['nilaiSeleksi', 'programStudi'])
                ->get();

            // Hitung total nilai - simpan dalam array terpisah, bukan di model
            $nilaiPendaftar = [];
            foreach ($pendaftars as $p) {
                $nilaiPendaftar[$p->id] = $p->nilaiSeleksi->sum('nilai_akhir');
            }

            // Sort by nilai descending
            arsort($nilaiPendaftar);

            // Proses per prodi
            $prodiGroups = $pendaftars->groupBy('program_studi_id');

            foreach ($prodiGroups as $prodiId => $pendaftarsProdi) {
                // Ambil kuota
                $kuota = KuotaPmb::where('gelombang_pmb_id', $gelombangId)
                    ->where('program_studi_id', $prodiId)
                    ->first();

                $kuotaTersedia = $kuota ? $kuota->kuota : 999;

                // Sort pendaftars by nilai dalam prodi ini
                $sortedPendaftars = $pendaftarsProdi->sortByDesc(function($p) use ($nilaiPendaftar) {
                    return $nilaiPendaftar[$p->id] ?? 0;
                });

                $ranking = 0;
                $diterima = 0;

                foreach ($sortedPendaftars as $pendaftar) {
                    $ranking++;
                    $totalNilai = $nilaiPendaftar[$pendaftar->id] ?? 0;
                    
                    // Tentukan status
                    if ($diterima < $kuotaTersedia && $totalNilai >= $passingGrade) {
                        $status = 'lulus';
                        $diterima++;
                    } else {
                        $status = 'tidak_lulus';
                    }

                    // Simpan hasil
                    HasilSeleksi::create([
                        'calon_mahasiswa_id' => $pendaftar->id,
                        'gelombang_pmb_id' => $gelombangId,
                        'nilai_total' => $totalNilai,
                        'ranking' => $ranking,
                        'status' => $status,
                        'program_studi_diterima_id' => $status == 'lulus' ? $prodiId : null,
                        'tanggal_pengumuman' => now(),
                        'diproses_oleh' => auth()->id(),
                    ]);

                    // Update status calon mahasiswa (sinkronkan kedua kolom status)
                    // Gunakan DB::table untuk menghindari model event/attribute issues
                    DB::table('calon_mahasiswa')
                        ->where('id', $pendaftar->id)
                        ->update([
                            'status' => $status,
                            'status_pendaftaran' => $status,
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();

            return redirect()->route('pmb.seleksi.hasil', ['gelombang' => $gelombangId])
                ->with('success', 'Proses seleksi berhasil. Total ' . $pendaftars->count() . ' pendaftar telah diproses.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses seleksi: ' . $e->getMessage());
        }
    }

    /**
     * Halaman hasil seleksi
     */
    public function hasilSeleksi(Request $request)
    {
        $gelombangs = GelombangPmb::with('periodePmb')->orderBy('id', 'desc')->get();
        $prodis = ProgramStudi::orderBy('nama')->get();
        
        $hasilSeleksi = collect();
        $summary = [];
        
        if ($request->filled('gelombang')) {
            $query = HasilSeleksi::with(['calonMahasiswa.programStudi', 'calonMahasiswa.programStudi2', 'programStudiDiterima'])
                ->where('gelombang_pmb_id', $request->gelombang);

            if ($request->filled('prodi')) {
                $query->where('program_studi_diterima_id', $request->prodi);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('calonMahasiswa', function($q) use ($search) {
                    $q->where('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('no_pendaftaran', 'like', "%{$search}%");
                });
            }

            $hasilSeleksi = $query->orderBy('ranking')->paginate(20);

            // Summary
            $summary = [
                'total' => HasilSeleksi::where('gelombang_pmb_id', $request->gelombang)->count(),
                'lulus' => HasilSeleksi::where('gelombang_pmb_id', $request->gelombang)->where('status', 'lulus')->count(),
                'tidak_lulus' => HasilSeleksi::where('gelombang_pmb_id', $request->gelombang)->where('status', 'tidak_lulus')->count(),
                'cadangan' => HasilSeleksi::where('gelombang_pmb_id', $request->gelombang)->where('status', 'cadangan')->count(),
            ];
        }

        return view('pmb.seleksi.hasil', compact('hasilSeleksi', 'gelombangs', 'prodis', 'summary'));
    }

    /**
     * Export hasil seleksi ke Excel
     */
    public function exportHasil(Request $request)
    {
        // TODO: Implement export functionality
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    /**
     * Update status hasil seleksi manual
     */
    public function updateHasil(Request $request, string $hashid)
    {
        $hasil = HasilSeleksi::findByHashidOrFail($hashid);

        $validated = $request->validate([
            'status' => 'required|in:lulus,tidak_lulus,cadangan',
            'catatan' => 'nullable|string',
        ]);

        $hasil->update([
            'status' => $validated['status'],
            'catatan' => $validated['catatan'],
            'diproses_oleh' => auth()->id(),
        ]);

        // Update status calon mahasiswa
        $hasil->calonMahasiswa->update([
            'status_pendaftaran' => $validated['status'],
        ]);

        return back()->with('success', 'Status hasil seleksi berhasil diperbarui.');
    }
}
