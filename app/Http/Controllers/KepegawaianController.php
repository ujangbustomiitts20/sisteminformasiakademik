<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Pegawai;
use App\Models\UnitKerja;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPelatihan;
use App\Models\DokumenKepegawaian;
use App\Models\CutiPegawai;
use App\Models\PresensiPegawai;
use App\Models\PenugasanMutasi;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\Pensiun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KepegawaianController extends Controller
{
    /**
     * Display kepegawaian main dashboard
     */
    public function dashboard()
    {
        $totalDosen = Dosen::count();
        $totalPegawai = Pegawai::count();
        $totalUnitKerja = UnitKerja::count();
        
        $dosenAktif = Dosen::where('status', 'Aktif')->count();
        $pegawaiAktif = Pegawai::where('status', 'Aktif')->count();
        $unitKerjaAktif = UnitKerja::where('is_active', true)->count();
        $totalAktif = $dosenAktif + $pegawaiAktif;
        
        // Pegawai terbaru (gabungan dosen dan tendik)
        $dosenBaru = Dosen::with('programStudi')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $tendikBaru = Pegawai::with('unitKerja')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $pegawaiBaru = $dosenBaru->concat($tendikBaru)
            ->sortByDesc('created_at')
            ->take(5);
        
        // Rekap jenis pegawai
        $rekapJenis = collect([
            'Dosen' => $totalDosen,
            'Tenaga Kependidikan' => $totalPegawai,
        ]);
        
        // Rekap status
        $rekapStatus = collect([
            'Aktif' => $totalAktif,
            'Cuti' => Dosen::where('status', 'Cuti')->count() + Pegawai::where('status', 'Cuti')->count(),
            'Non-aktif' => Dosen::where('status', 'Non-aktif')->count() + Pegawai::where('status', 'Non-aktif')->count(),
        ])->filter(fn($val) => $val > 0);
        
        // === DATA TAMBAHAN UNTUK DASHBOARD INFORMATIF ===
        
        // Statistik Cuti
        $cutiStats = $this->getCutiStats();
        
        // Statistik Presensi Hari Ini
        $presensiStats = $this->getPresensiStats();
        
        // Pengajuan yang perlu diproses
        $pendingStats = $this->getPendingStats();
        
        // Ulang Tahun bulan ini
        $ulangTahunBulanIni = $this->getUlangTahunBulanIni();
        
        // Pegawai akan pensiun
        $akanPensiun = $this->getPegawaiAkanPensiun();
        
        // Distribusi Pendidikan Dosen
        $distribusiPendidikan = $this->getDistribusiPendidikan();
        
        // Distribusi per Unit Kerja
        $distribusiUnitKerja = $this->getDistribusiUnitKerja();
        
        // Statistik KGB & Kenaikan Pangkat bulan ini
        $kgbBulanIni = KenaikanGajiBerkala::whereMonth('tmt_kgb', now()->month)
            ->whereYear('tmt_kgb', now()->year)
            ->count();
        $pangkatBulanIni = KenaikanPangkat::whereMonth('tmt_pangkat_baru', now()->month)
            ->whereYear('tmt_pangkat_baru', now()->year)
            ->count();
        
        return view('kepegawaian.dashboard', compact(
            'totalDosen', 
            'totalPegawai', 
            'totalUnitKerja',
            'dosenAktif',
            'pegawaiAktif',
            'unitKerjaAktif',
            'totalAktif',
            'pegawaiBaru',
            'rekapJenis',
            'rekapStatus',
            'cutiStats',
            'presensiStats',
            'pendingStats',
            'ulangTahunBulanIni',
            'akanPensiun',
            'distribusiPendidikan',
            'distribusiUnitKerja',
            'kgbBulanIni',
            'pangkatBulanIni'
        ));
    }

    /**
     * Get cuti statistics
     */
    private function getCutiStats()
    {
        try {
            return [
                'total_pengajuan' => CutiPegawai::count(),
                'diajukan' => CutiPegawai::where('status', 'diajukan')->count(),
                'disetujui' => CutiPegawai::where('status', 'disetujui')->count(),
                'ditolak' => CutiPegawai::where('status', 'ditolak')->count(),
                'sedang_cuti' => CutiPegawai::where('status', 'disetujui')
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_selesai', '>=', now())
                    ->count(),
            ];
        } catch (\Exception $e) {
            return ['total_pengajuan' => 0, 'diajukan' => 0, 'disetujui' => 0, 'ditolak' => 0, 'sedang_cuti' => 0];
        }
    }

    /**
     * Get presensi statistics for today
     */
    private function getPresensiStats()
    {
        try {
            $today = now()->toDateString();
            $totalPegawai = Dosen::where('status', 'Aktif')->count() + Pegawai::where('status', 'Aktif')->count();
            
            $hadir = PresensiPegawai::whereDate('tanggal', $today)->count();
            $terlambat = PresensiPegawai::whereDate('tanggal', $today)->where('status', 'Terlambat')->count();
            
            return [
                'total_pegawai' => $totalPegawai,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'belum_hadir' => max(0, $totalPegawai - $hadir),
                'persentase_hadir' => $totalPegawai > 0 ? round(($hadir / $totalPegawai) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            return ['total_pegawai' => 0, 'hadir' => 0, 'terlambat' => 0, 'belum_hadir' => 0, 'persentase_hadir' => 0];
        }
    }

    /**
     * Get pending approval statistics
     */
    private function getPendingStats()
    {
        try {
            return [
                'cuti_pending' => CutiPegawai::where('status', 'diajukan')->count(),
                'mutasi_pending' => PenugasanMutasi::whereIn('status', ['draft', 'diajukan'])->count(),
                'kgb_pending' => KenaikanGajiBerkala::whereIn('status', ['pending', 'diajukan'])->count(),
                'pangkat_pending' => KenaikanPangkat::whereIn('status', ['pending', 'diajukan', 'diusulkan'])->count(),
            ];
        } catch (\Exception $e) {
            return ['cuti_pending' => 0, 'mutasi_pending' => 0, 'kgb_pending' => 0, 'pangkat_pending' => 0];
        }
    }

    /**
     * Get employees with birthday this month
     */
    private function getUlangTahunBulanIni()
    {
        try {
            $currentMonth = now()->month;
            
            $dosenUltah = Dosen::whereMonth('tanggal_lahir', $currentMonth)
                ->where('status', 'Aktif')
                ->select('nama', 'tanggal_lahir', DB::raw("'Dosen' as jenis"))
                ->get();
            
            $pegawaiUltah = Pegawai::whereMonth('tanggal_lahir', $currentMonth)
                ->where('status', 'Aktif')
                ->select('nama', 'tanggal_lahir', DB::raw("'Tendik' as jenis"))
                ->get();
            
            return $dosenUltah->concat($pegawaiUltah)
                ->sortBy(fn($p) => Carbon::parse($p->tanggal_lahir)->day)
                ->take(10);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get employees approaching retirement
     */
    private function getPegawaiAkanPensiun()
    {
        try {
            $batasPensiun = now()->addMonths(12); // Dalam 12 bulan ke depan
            $tahunPensiun = 60; // Usia pensiun
            
            $dosenPensiun = Dosen::whereNotNull('tanggal_lahir')
                ->where('status', 'Aktif')
                ->whereRaw('DATE_ADD(tanggal_lahir, INTERVAL ? YEAR) <= ?', [$tahunPensiun, $batasPensiun])
                ->whereRaw('DATE_ADD(tanggal_lahir, INTERVAL ? YEAR) >= ?', [$tahunPensiun, now()])
                ->select('nama', 'tanggal_lahir', 'nidn', DB::raw("'Dosen' as jenis"))
                ->get();
            
            $pegawaiPensiun = Pegawai::whereNotNull('tanggal_lahir')
                ->where('status', 'Aktif')
                ->whereRaw('DATE_ADD(tanggal_lahir, INTERVAL ? YEAR) <= ?', [$tahunPensiun, $batasPensiun])
                ->whereRaw('DATE_ADD(tanggal_lahir, INTERVAL ? YEAR) >= ?', [$tahunPensiun, now()])
                ->select('nama', 'tanggal_lahir', 'nip', DB::raw("'Tendik' as jenis"))
                ->get();
            
            return $dosenPensiun->concat($pegawaiPensiun)
                ->sortBy('tanggal_lahir')
                ->take(5);
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get education distribution for dosen
     */
    private function getDistribusiPendidikan()
    {
        try {
            return Dosen::select('pendidikan_terakhir', DB::raw('COUNT(*) as jumlah'))
                ->whereNotNull('pendidikan_terakhir')
                ->groupBy('pendidikan_terakhir')
                ->orderByRaw("FIELD(pendidikan_terakhir, 'S3', 'S2', 'S1', 'D4', 'D3', 'Profesi', 'Spesialis')")
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Get distribution per unit kerja
     */
    private function getDistribusiUnitKerja()
    {
        try {
            return UnitKerja::withCount('pegawai')
                ->where('is_active', true)
                ->orderByDesc('pegawai_count')
                ->take(8)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }
    
    /**
     * Display kepegawaian dashboard for a dosen
     */
    public function index(Dosen $dosen)
    {
        $dosen->load([
            'riwayatPendidikan',
            'riwayatJabatan',
            'riwayatPangkat',
            'riwayatPelatihan',
            'dokumenKepegawaian',
            'programStudi'
        ]);

        return view('kepegawaian.index', compact('dosen'));
    }

    // =====================
    // RIWAYAT PENDIDIKAN
    // =====================
    public function createPendidikan(Dosen $dosen)
    {
        return view('kepegawaian.pendidikan.create', compact('dosen'));
    }

    public function storePendidikan(Request $request, Dosen $dosen)
    {
        $request->validate([
            'jenjang' => 'required|in:D3,D4,S1,S2,S3,Profesi,Spesialis',
            'nama_institusi' => 'required|max:255',
            'program_studi' => 'required|max:255',
            'tahun_lulus' => 'required|integer|min:1950|max:' . date('Y'),
            'tahun_masuk' => 'nullable|integer|min:1950|max:' . date('Y'),
            'no_ijazah' => 'nullable|max:100',
            'tanggal_ijazah' => 'nullable|date',
            'judul_tugas_akhir' => 'nullable|max:500',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_transkrip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['file_ijazah', 'file_transkrip']);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_ijazah')) {
            $data['file_ijazah'] = $request->file('file_ijazah')->store('kepegawaian/ijazah', 'public');
        }

        if ($request->hasFile('file_transkrip')) {
            $data['file_transkrip'] = $request->file('file_transkrip')->store('kepegawaian/transkrip', 'public');
        }

        RiwayatPendidikan::create($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pendidikan berhasil ditambahkan!');
    }

    public function editPendidikan(Dosen $dosen, RiwayatPendidikan $pendidikan)
    {
        return view('kepegawaian.pendidikan.edit', compact('dosen', 'pendidikan'));
    }

    public function updatePendidikan(Request $request, Dosen $dosen, RiwayatPendidikan $pendidikan)
    {
        $request->validate([
            'jenjang' => 'required|in:D3,D4,S1,S2,S3,Profesi,Spesialis',
            'nama_institusi' => 'required|max:255',
            'program_studi' => 'required|max:255',
            'tahun_lulus' => 'required|integer|min:1950|max:' . date('Y'),
            'tahun_masuk' => 'nullable|integer|min:1950|max:' . date('Y'),
            'no_ijazah' => 'nullable|max:100',
            'tanggal_ijazah' => 'nullable|date',
            'judul_tugas_akhir' => 'nullable|max:500',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'file_ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_transkrip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->except(['file_ijazah', 'file_transkrip', '_token', '_method']);

        if ($request->hasFile('file_ijazah')) {
            if ($pendidikan->file_ijazah) {
                Storage::disk('public')->delete($pendidikan->file_ijazah);
            }
            $data['file_ijazah'] = $request->file('file_ijazah')->store('kepegawaian/ijazah', 'public');
        }

        if ($request->hasFile('file_transkrip')) {
            if ($pendidikan->file_transkrip) {
                Storage::disk('public')->delete($pendidikan->file_transkrip);
            }
            $data['file_transkrip'] = $request->file('file_transkrip')->store('kepegawaian/transkrip', 'public');
        }

        $pendidikan->update($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pendidikan berhasil diperbarui!');
    }

    public function destroyPendidikan(Dosen $dosen, RiwayatPendidikan $pendidikan)
    {
        if ($pendidikan->file_ijazah) {
            Storage::disk('public')->delete($pendidikan->file_ijazah);
        }
        if ($pendidikan->file_transkrip) {
            Storage::disk('public')->delete($pendidikan->file_transkrip);
        }
        
        $pendidikan->delete();

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pendidikan berhasil dihapus!');
    }

    // =====================
    // RIWAYAT JABATAN
    // =====================
    public function createJabatan(Dosen $dosen)
    {
        return view('kepegawaian.jabatan.create', compact('dosen'));
    }

    public function storeJabatan(Request $request, Dosen $dosen)
    {
        $request->validate([
            'jabatan_fungsional' => 'required|max:100',
            'no_sk' => 'required|max:100',
            'tanggal_sk' => 'required|date',
            'tmt_jabatan' => 'required|date',
            'pejabat_penetap' => 'nullable|max:255',
            'angka_kredit' => 'nullable|numeric|min:0',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_sk']);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_sk')) {
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk-jabatan', 'public');
        }

        RiwayatJabatan::create($data);

        // Update jabatan fungsional di dosen
        $dosen->update(['jabatan_fungsional' => $request->jabatan_fungsional]);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat jabatan berhasil ditambahkan!');
    }

    public function editJabatan(Dosen $dosen, RiwayatJabatan $jabatan)
    {
        return view('kepegawaian.jabatan.edit', compact('dosen', 'jabatan'));
    }

    public function updateJabatan(Request $request, Dosen $dosen, RiwayatJabatan $jabatan)
    {
        $request->validate([
            'jabatan_fungsional' => 'required|max:100',
            'no_sk' => 'required|max:100',
            'tanggal_sk' => 'required|date',
            'tmt_jabatan' => 'required|date',
            'pejabat_penetap' => 'nullable|max:255',
            'angka_kredit' => 'nullable|numeric|min:0',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_sk', '_token', '_method']);

        if ($request->hasFile('file_sk')) {
            if ($jabatan->file_sk) {
                Storage::disk('public')->delete($jabatan->file_sk);
            }
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk-jabatan', 'public');
        }

        $jabatan->update($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat jabatan berhasil diperbarui!');
    }

    public function destroyJabatan(Dosen $dosen, RiwayatJabatan $jabatan)
    {
        if ($jabatan->file_sk) {
            Storage::disk('public')->delete($jabatan->file_sk);
        }
        
        $jabatan->delete();

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat jabatan berhasil dihapus!');
    }

    // =====================
    // RIWAYAT PANGKAT
    // =====================
    public function createPangkat(Dosen $dosen)
    {
        return view('kepegawaian.pangkat.create', compact('dosen'));
    }

    public function storePangkat(Request $request, Dosen $dosen)
    {
        $request->validate([
            'pangkat' => 'required|max:100',
            'golongan' => 'required|max:20',
            'no_sk' => 'required|max:100',
            'tanggal_sk' => 'required|date',
            'tmt_pangkat' => 'required|date',
            'pejabat_penetap' => 'nullable|max:255',
            'masa_kerja_tahun' => 'nullable|numeric|min:0',
            'masa_kerja_bulan' => 'nullable|numeric|min:0|max:12',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_sk']);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_sk')) {
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk-pangkat', 'public');
        }

        RiwayatPangkat::create($data);

        // Update golongan di dosen
        $dosen->update(['golongan' => $request->golongan]);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pangkat berhasil ditambahkan!');
    }

    public function editPangkat(Dosen $dosen, RiwayatPangkat $pangkat)
    {
        return view('kepegawaian.pangkat.edit', compact('dosen', 'pangkat'));
    }

    public function updatePangkat(Request $request, Dosen $dosen, RiwayatPangkat $pangkat)
    {
        $request->validate([
            'pangkat' => 'required|max:100',
            'golongan' => 'required|max:20',
            'no_sk' => 'required|max:100',
            'tanggal_sk' => 'required|date',
            'tmt_pangkat' => 'required|date',
            'pejabat_penetap' => 'nullable|max:255',
            'masa_kerja_tahun' => 'nullable|numeric|min:0',
            'masa_kerja_bulan' => 'nullable|numeric|min:0|max:12',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_sk', '_token', '_method']);

        if ($request->hasFile('file_sk')) {
            if ($pangkat->file_sk) {
                Storage::disk('public')->delete($pangkat->file_sk);
            }
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/sk-pangkat', 'public');
        }

        $pangkat->update($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pangkat berhasil diperbarui!');
    }

    public function destroyPangkat(Dosen $dosen, RiwayatPangkat $pangkat)
    {
        if ($pangkat->file_sk) {
            Storage::disk('public')->delete($pangkat->file_sk);
        }
        
        $pangkat->delete();

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pangkat berhasil dihapus!');
    }

    // =====================
    // RIWAYAT PELATIHAN
    // =====================
    public function createPelatihan(Dosen $dosen)
    {
        return view('kepegawaian.pelatihan.create', compact('dosen'));
    }

    public function storePelatihan(Request $request, Dosen $dosen)
    {
        $request->validate([
            'jenis' => 'required|in:Diklat,Workshop,Seminar,Sertifikasi,Kursus,Lainnya',
            'nama_pelatihan' => 'required|max:255',
            'penyelenggara' => 'required|max:255',
            'tempat' => 'nullable|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jumlah_jam' => 'nullable|integer|min:0',
            'no_sertifikat' => 'nullable|max:100',
            'tahun' => 'required|integer|min:1990|max:' . date('Y'),
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_sertifikat']);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_sertifikat')) {
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('kepegawaian/sertifikat', 'public');
        }

        RiwayatPelatihan::create($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pelatihan berhasil ditambahkan!');
    }

    public function editPelatihan(Dosen $dosen, RiwayatPelatihan $pelatihan)
    {
        return view('kepegawaian.pelatihan.edit', compact('dosen', 'pelatihan'));
    }

    public function updatePelatihan(Request $request, Dosen $dosen, RiwayatPelatihan $pelatihan)
    {
        $request->validate([
            'jenis' => 'required|in:Diklat,Workshop,Seminar,Sertifikasi,Kursus,Lainnya',
            'nama_pelatihan' => 'required|max:255',
            'penyelenggara' => 'required|max:255',
            'tempat' => 'nullable|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jumlah_jam' => 'nullable|integer|min:0',
            'no_sertifikat' => 'nullable|max:100',
            'tahun' => 'required|integer|min:1990|max:' . date('Y'),
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_sertifikat', '_token', '_method']);

        if ($request->hasFile('file_sertifikat')) {
            if ($pelatihan->file_sertifikat) {
                Storage::disk('public')->delete($pelatihan->file_sertifikat);
            }
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('kepegawaian/sertifikat', 'public');
        }

        $pelatihan->update($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pelatihan berhasil diperbarui!');
    }

    public function destroyPelatihan(Dosen $dosen, RiwayatPelatihan $pelatihan)
    {
        if ($pelatihan->file_sertifikat) {
            Storage::disk('public')->delete($pelatihan->file_sertifikat);
        }
        
        $pelatihan->delete();

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Riwayat pelatihan berhasil dihapus!');
    }

    // =====================
    // DOKUMEN KEPEGAWAIAN
    // =====================
    public function createDokumen(Dosen $dosen)
    {
        return view('kepegawaian.dokumen.create', compact('dosen'));
    }

    public function storeDokumen(Request $request, Dosen $dosen)
    {
        $request->validate([
            'jenis_dokumen' => 'required|max:100',
            'nama_dokumen' => 'required|max:255',
            'no_dokumen' => 'nullable|max:100',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku' => 'nullable|date',
            'penerbit' => 'nullable|max:255',
            'file_dokumen' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_dokumen']);
        $data['dosen_id'] = $dosen->id;

        $data['file_dokumen'] = $request->file('file_dokumen')->store('kepegawaian/dokumen', 'public');

        DokumenKepegawaian::create($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Dokumen kepegawaian berhasil ditambahkan!');
    }

    public function editDokumen(Dosen $dosen, DokumenKepegawaian $dokumen)
    {
        return view('kepegawaian.dokumen.edit', compact('dosen', 'dokumen'));
    }

    public function updateDokumen(Request $request, Dosen $dosen, DokumenKepegawaian $dokumen)
    {
        $request->validate([
            'jenis_dokumen' => 'required|max:100',
            'nama_dokumen' => 'required|max:255',
            'no_dokumen' => 'nullable|max:100',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku' => 'nullable|date',
            'penerbit' => 'nullable|max:255',
            'file_dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'keterangan' => 'nullable|max:500',
        ]);

        $data = $request->except(['file_dokumen', '_token', '_method']);

        if ($request->hasFile('file_dokumen')) {
            Storage::disk('public')->delete($dokumen->file_dokumen);
            $data['file_dokumen'] = $request->file('file_dokumen')->store('kepegawaian/dokumen', 'public');
        }

        $dokumen->update($data);

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Dokumen kepegawaian berhasil diperbarui!');
    }

    public function destroyDokumen(Dosen $dosen, DokumenKepegawaian $dokumen)
    {
        Storage::disk('public')->delete($dokumen->file_dokumen);
        $dokumen->delete();

        return redirect()->route('kepegawaian.index', $dosen)
            ->with('success', 'Dokumen kepegawaian berhasil dihapus!');
    }
}
