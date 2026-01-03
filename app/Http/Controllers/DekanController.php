<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\TugasAkhir;
use App\Models\CutiAkademik;
use App\Models\PengajuanKonversi;
use App\Models\TahunAkademik;
use App\Models\PeriodeWisuda;
use App\Models\PendaftaranWisuda;
use App\Models\PengajuanSurat;
use App\Models\PendaftaranKegiatanLapangan;
use App\Models\BimbinganAkademik;
use App\Models\JadwalKuliah;
use App\Models\JadwalUjian;
use App\Models\Krs;
use App\Models\Kurikulum;
use App\Models\MataKuliah;
use App\Models\PeriodeEdom;
use App\Models\RekapEdom;
use App\Models\Yudisium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DekanController extends Controller
{
    /**
     * Get the faculty of the logged-in dean
     */
    private function getFakultas()
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        
        if (!$dosen) {
            return null;
        }
        
        // Dekan is linked via dosen's program studi -> fakultas
        $prodi = ProgramStudi::with('fakultas')->find($dosen->program_studi_id);
        return $prodi ? $prodi->fakultas : null;
    }

    /**
     * Dashboard Dekan
     */
    public function dashboard()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        // Statistik Fakultas
        $stats = [
            'total_prodi' => ProgramStudi::where('fakultas_id', $fakultas->id)->count(),
            'total_mahasiswa' => Mahasiswa::whereIn('program_studi_id', $prodiIds)
                ->where('status', 'Aktif')->count(),
            'total_dosen' => Dosen::whereIn('program_studi_id', $prodiIds)->count(),
            'tugas_akhir_berjalan' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
                ->whereNotIn('status', ['selesai', 'gagal'])->count(),
            'cuti_pending' => CutiAkademik::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
                ->where('status', 'Disetujui Kaprodi') // Sudah disetujui kaprodi, menunggu dekan
                ->count(),
            'wisuda_pending' => PeriodeWisuda::where('status', 'Dibuka')->count(),
            'lulusan_tahun_ini' => Mahasiswa::whereIn('program_studi_id', $prodiIds)
                ->where('status', 'Lulus')
                ->whereYear('updated_at', date('Y'))
                ->count(),
        ];

        // Statistik per prodi
        $statsPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount(['mahasiswa' => fn($q) => $q->where('status', 'aktif')])
            ->withCount(['dosen'])
            ->get();

        return view('dekan.dashboard', compact('fakultas', 'stats', 'statsPerProdi', 'tahunAktif'));
    }

    /**
     * Daftar Program Studi
     */
    public function programStudi()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount(['mahasiswa' => fn($q) => $q->where('status', 'aktif')])
            ->withCount(['dosen'])
            ->orderBy('nama')
            ->get();

        return view('dekan.program-studi.index', compact('fakultas', 'prodis'));
    }

    /**
     * Detail Program Studi
     */
    public function programStudiShow(ProgramStudi $programStudi)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas || $programStudi->fakultas_id != $fakultas->id) {
            abort(403);
        }

        $programStudi->loadCount(['mahasiswa', 'dosen', 'mataKuliah']);
        $programStudi->load(['fakultas']);
        
        // Hitung rata-rata IPK dari mahasiswa aktif
        $mahasiswas = Mahasiswa::where('program_studi_id', $programStudi->id)
            ->where('status', 'Aktif')
            ->with(['krs.nilai', 'krs.jadwalKuliah.mataKuliah'])
            ->get();
        
        $totalIpk = 0;
        $countMhs = 0;
        
        foreach ($mahasiswas as $mhs) {
            $totalSks = 0;
            $totalBobot = 0;
            
            foreach ($mhs->krs as $krs) {
                if ($krs->nilai && $krs->nilai->bobot !== null) {
                    $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                    $totalSks += $sks;
                    $totalBobot += $krs->nilai->bobot * $sks;
                }
            }
            
            if ($totalSks > 0) {
                $totalIpk += $totalBobot / $totalSks;
                $countMhs++;
            }
        }
        
        $rataIpk = $countMhs > 0 ? round($totalIpk / $countMhs, 2) : 0;
        
        // Statistik tambahan
        $stats = [
            'mahasiswa_aktif' => Mahasiswa::where('program_studi_id', $programStudi->id)->where('status', 'Aktif')->count(),
            'mahasiswa_cuti' => Mahasiswa::where('program_studi_id', $programStudi->id)->where('status', 'Cuti')->count(),
            'mahasiswa_lulus' => Mahasiswa::where('program_studi_id', $programStudi->id)->where('status', 'Lulus')->count(),
            'mahasiswa_do' => Mahasiswa::where('program_studi_id', $programStudi->id)->whereIn('status', ['DO', 'Tidak Aktif', 'Mengundurkan Diri'])->count(),
            'rata_ipk' => $rataIpk,
        ];
        
        // Data dosen prodi
        $dosens = Dosen::where('program_studi_id', $programStudi->id)
            ->with('user')
            ->orderBy('nama')
            ->take(10)
            ->get();
        
        // Data mahasiswa per angkatan
        $mahasiswaPerAngkatan = Mahasiswa::where('program_studi_id', $programStudi->id)
            ->where('status', 'Aktif')
            ->select('angkatan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->take(5)
            ->get();
        
        // Kurikulum aktif
        $kurikulumAktif = Kurikulum::where('program_studi_id', $programStudi->id)
            ->where('is_aktif', true)
            ->withCount('mataKuliah')
            ->first();

        return view('dekan.program-studi.show', compact('fakultas', 'programStudi', 'stats', 'dosens', 'mahasiswaPerAngkatan', 'kurikulumAktif'));
    }

    /**
     * Get Mahasiswa per Angkatan (View with Pagination)
     */
    public function programStudiMahasiswa(Request $request, ProgramStudi $programStudi, $angkatan)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas || $programStudi->fakultas_id != $fakultas->id) {
            abort(403);
        }

        $query = Mahasiswa::where('program_studi_id', $programStudi->id)
            ->where('angkatan', $angkatan)
            ->with('dosenWali');

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->orderBy('nama')->paginate(20);

        // Total dan statistik
        $totalMahasiswa = Mahasiswa::where('program_studi_id', $programStudi->id)
            ->where('angkatan', $angkatan)
            ->count();

        $statusCount = Mahasiswa::where('program_studi_id', $programStudi->id)
            ->where('angkatan', $angkatan)
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->mapWithKeys(fn($val, $key) => [strtolower($key) => $val])
            ->toArray();

        return view('dekan.program-studi.mahasiswa', compact('fakultas', 'programStudi', 'mahasiswas', 'angkatan', 'totalMahasiswa', 'statusCount'));
    }

    /**
     * Get Dosen per Program Studi (View with Pagination)
     */
    public function programStudiDosen(Request $request, ProgramStudi $programStudi)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas || $programStudi->fakultas_id != $fakultas->id) {
            abort(403);
        }

        $query = Dosen::where('program_studi_id', $programStudi->id)
            ->with('user')
            ->withCount(['mahasiswaBimbingan' => fn($q) => $q->where('status', 'Aktif')]);

        // Filter jabatan
        if ($request->jabatan) {
            $query->where('jabatan_fungsional', $request->jabatan);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nidn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $dosens = $query->orderBy('nama')->paginate(20);
        
        // Hitung bimbingan TA untuk setiap dosen
        $dosens->getCollection()->transform(function($dosen) {
            $dosen->tugas_akhir_bimbingan_count = TugasAkhir::where(function($q) use ($dosen) {
                    $q->where('pembimbing_1_id', $dosen->id)
                      ->orWhere('pembimbing_2_id', $dosen->id);
                })
                ->whereNotIn('status', ['selesai', 'judul_ditolak'])
                ->count();
            return $dosen;
        });

        // Total dan statistik
        $totalDosen = Dosen::where('program_studi_id', $programStudi->id)->count();

        $statusCount = [
            'aktif' => Dosen::where('program_studi_id', $programStudi->id)
                ->where('status', 'Aktif')
                ->count(),
        ];

        // Total mahasiswa bimbingan dan TA
        $totalMahasiswaBimbingan = Mahasiswa::where('status', 'Aktif')
            ->whereHas('dosenWali', fn($q) => $q->where('program_studi_id', $programStudi->id))
            ->count();

        $totalBimbinganTA = TugasAkhir::whereNotIn('status', ['selesai', 'judul_ditolak'])
            ->where(function($q) use ($programStudi) {
                $q->whereHas('pembimbing1', fn($q) => $q->where('program_studi_id', $programStudi->id))
                  ->orWhereHas('pembimbing2', fn($q) => $q->where('program_studi_id', $programStudi->id));
            })
            ->count();

        // Daftar jabatan untuk filter
        $jabatanList = Dosen::where('program_studi_id', $programStudi->id)
            ->whereNotNull('jabatan_fungsional')
            ->distinct()
            ->pluck('jabatan_fungsional');

        return view('dekan.program-studi.dosen', compact('fakultas', 'programStudi', 'dosens', 'totalDosen', 'statusCount', 'totalMahasiswaBimbingan', 'totalBimbinganTA', 'jabatanList'));
    }

    /**
     * Daftar Dosen Fakultas
     */
    public function dosen(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        $query = Dosen::whereIn('program_studi_id', $prodiIds)
            ->with(['programStudi', 'user']);

        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nidn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $dosens = $query->orderBy('nama')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.dosen.index', compact('fakultas', 'dosens', 'prodis'));
    }

    /**
     * Detail Dosen
     */
    public function dosenShow(Dosen $dosen)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($dosen->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $dosen->load(['user', 'programStudi', 'jadwalKuliah.mataKuliah', 'jadwalKuliah.ruangan']);

        // Statistik dosen
        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();
        
        $stats = [
            'jumlah_matakuliah' => $dosen->jadwalKuliah()
                ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
                ->distinct('mata_kuliah_id')
                ->count('mata_kuliah_id'),
            'jumlah_kelas' => $dosen->jadwalKuliah()
                ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
                ->count(),
            'mahasiswa_bimbingan' => Mahasiswa::where('dosen_wali_id', $dosen->id)->count(),
            'tugas_akhir_bimbingan' => TugasAkhir::where(function($q) use ($dosen) {
                    $q->where('pembimbing_1_id', $dosen->id)
                      ->orWhere('pembimbing_2_id', $dosen->id);
                })
                ->whereNotIn('status', ['selesai', 'judul_ditolak'])
                ->count(),
        ];

        // Jadwal mengajar semester ini
        $jadwalMengajar = $dosen->jadwalKuliah()
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->with(['mataKuliah', 'ruangan'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Mahasiswa bimbingan akademik
        $mahasiswaBimbingan = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->where('status', 'Aktif')
            ->orderBy('angkatan', 'desc')
            ->orderBy('nama')
            ->get();

        // Bimbingan TA
        $bimbinganTA = TugasAkhir::where(function($q) use ($dosen) {
                $q->where('pembimbing_1_id', $dosen->id)
                  ->orWhere('pembimbing_2_id', $dosen->id);
            })
            ->whereNotIn('status', ['selesai', 'judul_ditolak'])
            ->with('mahasiswa')
            ->get();

        return view('dekan.dosen.show', compact('fakultas', 'dosen', 'stats', 'jadwalMengajar', 'mahasiswaBimbingan', 'bimbinganTA', 'tahunAktif'));
    }

    /**
     * Approval Cuti Akademik (Level Dekan)
     */
    public function cutiAkademik(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        $query = CutiAkademik::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi', 'tahunAkademik']);

        // Default: tampilkan yang sudah disetujui kaprodi
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'Disetujui Kaprodi');
        }

        $cutiList = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('dekan.cuti.index', compact('fakultas', 'cutiList'));
    }

    /**
     * Approval Cuti oleh Dekan
     */
    public function cutiApproval(Request $request, CutiAkademik $cuti)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($cuti->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:Disetujui Dekan,Ditolak',
            'catatan' => 'nullable|string',
        ]);

        $cuti->status = $request->status;
        $cuti->catatan_dekan = $request->catatan;
        $cuti->disetujui_dekan_oleh = auth()->id();
        $cuti->tanggal_persetujuan_dekan = now();
        $cuti->save();

        // Jika disetujui, update status mahasiswa
        if ($request->status == 'Disetujui Dekan') {
            $cuti->mahasiswa->update(['status' => 'cuti']);
        }

        return back()->with('success', 'Permohonan cuti berhasil diproses!');
    }

    /**
     * Monitoring Tugas Akhir Fakultas
     */
    public function tugasAkhir(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        $query = TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi', 'pembimbing1', 'pembimbing2']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->prodi) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        $tugasAkhirs = $query->orderBy('created_at', 'desc')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        // Statistik per status
        $stats = [
            'draft' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'draft')->count(),
            'diajukan' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'diajukan')->count(),
            'bimbingan' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'bimbingan')->count(),
            'sidang' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'sidang')->count(),
            'selesai' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'selesai')->count(),
        ];

        return view('dekan.tugas-akhir.index', compact('fakultas', 'tugasAkhirs', 'prodis', 'stats'));
    }

    /**
     * Monitoring Wisuda
     */
    public function wisuda(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $periodeWisudas = PeriodeWisuda::with(['tahunAkademik', 'pendaftaran'])
            ->orderBy('tanggal_wisuda', 'desc')
            ->paginate(10);

        // Statistik pendaftar wisuda dari fakultas
        $totalPendaftar = PendaftaranWisuda::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->count();

        return view('dekan.wisuda.index', compact('fakultas', 'periodeWisudas', 'totalPendaftar'));
    }

    /**
     * Laporan Fakultas
     */
    public function laporan(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        // Statistik Mahasiswa per Prodi
        $mahasiswaPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount(['mahasiswa' => fn($q) => $q->where('status', 'aktif')])
            ->orderBy('nama')
            ->get();

        // IPK Rata-rata per Prodi (kosongkan karena kolom ipk tidak ada)
        $ipkPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->orderBy('nama')
            ->get()
            ->map(function($prodi) {
                $prodi->rata_ipk = 0; // Placeholder
                return $prodi;
            });

        // Kelulusan per Tahun (kosongkan jika kolom tanggal_lulus tidak ada/null)
        $kelulusan = collect();

        // Dosen per Prodi
        $dosenPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount('dosen')
            ->orderBy('nama')
            ->get();

        return view('dekan.laporan.index', compact('fakultas', 'mahasiswaPerProdi', 'ipkPerProdi', 'kelulusan', 'dosenPerProdi'));
    }

    /**
     * Tanda Tangan Dokumen
     */
    public function dokumen(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        // Surat yang perlu ditandatangani (contoh: surat keterangan)
        $suratPending = PengajuanSurat::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->where('status', 'diproses')
            ->with(['mahasiswa.programStudi'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('dekan.dokumen.index', compact('fakultas', 'suratPending'));
    }

    /**
     * Statistik Akademik
     */
    public function statistik()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        // Distribusi Status Mahasiswa
        $distribusiStatus = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->get();

        // Trend Mahasiswa Baru (5 tahun terakhir)
        $trendMahasiswaBaru = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->select('angkatan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->limit(5)
            ->get();

        // Distribusi IPK (placeholder - IPK perlu dihitung dari nilai)
        // Untuk sementara gunakan data dummy atau hitung dari nilai jika diperlukan
        $distribusiIpk = [
            'cumlaude' => 0,
            'sangat_memuaskan' => 0,
            'memuaskan' => 0,
            'cukup' => 0,
            'kurang' => 0,
        ];
        
        // Hitung distribusi IPK dari mahasiswa aktif
        $mahasiswas = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->where('status', 'Aktif')
            ->with(['krs.nilai'])
            ->get();
            
        foreach ($mahasiswas as $mhs) {
            $totalSks = 0;
            $totalBobot = 0;
            
            foreach ($mhs->krs as $krs) {
                if ($krs->nilai && $krs->nilai->bobot !== null) {
                    $sks = $krs->sks ?? 3;
                    $totalSks += $sks;
                    $totalBobot += $krs->nilai->bobot * $sks;
                }
            }
            
            if ($totalSks > 0) {
                $ipk = $totalBobot / $totalSks;
                if ($ipk >= 3.50) $distribusiIpk['cumlaude']++;
                elseif ($ipk >= 3.00) $distribusiIpk['sangat_memuaskan']++;
                elseif ($ipk >= 2.50) $distribusiIpk['memuaskan']++;
                elseif ($ipk >= 2.00) $distribusiIpk['cukup']++;
                else $distribusiIpk['kurang']++;
            }
        }

        return view('dekan.statistik.index', compact('fakultas', 'distribusiStatus', 'trendMahasiswaBaru', 'distribusiIpk'));
    }

    // =====================
    // MAHASISWA
    // =====================

    /**
     * Daftar Mahasiswa Fakultas
     */
    public function mahasiswa(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        $query = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->with(['programStudi', 'dosenWali']);

        // Filter prodi
        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter angkatan
        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->orderBy('angkatan', 'desc')
            ->orderBy('nama')
            ->paginate(20);

        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();
        $angkatans = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        return view('dekan.mahasiswa.index', compact('fakultas', 'mahasiswas', 'prodis', 'angkatans'));
    }

    /**
     * Detail Mahasiswa
     */
    public function mahasiswaShow(Mahasiswa $mahasiswa)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $mahasiswa->load(['programStudi', 'dosenWali', 'krs.jadwalKuliah.mataKuliah', 'krs.nilai']);

        return view('dekan.mahasiswa.show', compact('fakultas', 'mahasiswa'));
    }

    /**
     * Mahasiswa Bermasalah Fakultas
     */
    public function mahasiswaBermasalah()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        // Mahasiswa dengan IPK rendah
        $mahasiswaIpkRendah = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->where('status', 'Aktif')
            ->with(['programStudi', 'krs.nilai', 'krs.jadwalKuliah.mataKuliah', 'dosenWali'])
            ->get()
            ->map(function($mhs) {
                $totalSks = 0;
                $totalBobot = 0;
                
                foreach ($mhs->krs as $krs) {
                    if ($krs->nilai && $krs->nilai->nilai_akhir !== null) {
                        $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                        $bobot = $krs->nilai->bobot ?? 0;
                        $totalSks += $sks;
                        $totalBobot += $bobot * $sks;
                    }
                }
                
                $mhs->ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
                $mhs->total_sks = $totalSks;
                return $mhs;
            })
            ->filter(fn($m) => $m->ipk < 2.0 && $m->total_sks > 0)
            ->sortBy('ipk')
            ->take(30);

        // Mahasiswa cuti
        $mahasiswaCuti = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->where('status', 'Cuti')
            ->with(['programStudi', 'cutiAkademik' => fn($q) => $q->latest()->with('tahunAkademik')])
            ->get()
            ->map(function($mhs) {
                $mhs->cuti = $mhs->cutiAkademik->first();
                return $mhs;
            });

        // Mahasiswa tidak aktif
        $mahasiswaTidakAktif = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->whereIn('status', ['Tidak Aktif', 'Non-Aktif', 'DO', 'Mengundurkan Diri'])
            ->with('programStudi')
            ->orderBy('updated_at', 'desc')
            ->take(30)
            ->get();

        // Summary per prodi
        $summaryPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount([
                'mahasiswa as cuti_count' => fn($q) => $q->where('status', 'Cuti'),
                'mahasiswa as tidak_aktif_count' => fn($q) => $q->whereIn('status', ['Tidak Aktif', 'Non-Aktif', 'DO']),
            ])
            ->get();

        $summary = [
            'ipk_rendah' => $mahasiswaIpkRendah->count(),
            'cuti' => $mahasiswaCuti->count(),
            'tidak_aktif' => $mahasiswaTidakAktif->count(),
        ];

        return view('dekan.mahasiswa.bermasalah', compact('fakultas', 'mahasiswaIpkRendah', 'mahasiswaCuti', 'mahasiswaTidakAktif', 'summary', 'summaryPerProdi'));
    }

    // =====================
    // EDOM
    // =====================

    /**
     * Monitoring EDOM Fakultas
     */
    public function edomIndex(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        // Periode EDOM
        $periodes = PeriodeEdom::with('tahunAkademik')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Rekap per Prodi
        $rekapPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->with(['dosen' => function($q) {
                $q->withCount('rekapEdom');
            }])
            ->get();

        return view('dekan.edom.index', compact('fakultas', 'periodes', 'rekapPerProdi'));
    }

    // =====================
    // YUDISIUM
    // =====================

    /**
     * Monitoring Yudisium Fakultas
     */
    public function yudisiumIndex(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = Yudisium::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi', 'pendaftaranWisuda.periodeWisuda']);

        if ($request->prodi) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $yudisiums = $query->orderBy('created_at', 'desc')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        // Statistik
        $stats = [
            'pending' => Yudisium::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'pending')->count(),
            'lulus' => Yudisium::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'lulus')->count(),
            'tidak_lulus' => Yudisium::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'tidak_lulus')->count(),
        ];

        return view('dekan.yudisium.index', compact('fakultas', 'yudisiums', 'prodis', 'stats'));
    }

    // =====================
    // BIMBINGAN AKADEMIK
    // =====================

    /**
     * Monitoring Bimbingan Akademik Fakultas
     */
    public function bimbinganIndex(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = BimbinganAkademik::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi', 'dosen']);

        if ($request->prodi) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $bimbingans = $query->orderBy('created_at', 'desc')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.bimbingan.index', compact('fakultas', 'bimbingans', 'prodis'));
    }

    // =====================
    // ABSENSI
    // =====================

    /**
     * Monitoring Absensi Fakultas
     */
    public function absensiIndex(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        $query = JadwalKuliah::whereHas('mataKuliah', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->with(['mataKuliah.programStudi', 'dosen', 'ruangan'])
            ->withCount('pertemuan');

        if ($request->prodi) {
            $query->whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        $jadwals = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.absensi.index', compact('fakultas', 'jadwals', 'prodis', 'tahunAktif'));
    }

    // =====================
    // JADWAL UJIAN
    // =====================

    /**
     * Monitoring Jadwal Ujian Fakultas
     */
    public function jadwalUjianIndex(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        $query = JadwalUjian::whereHas('mataKuliah', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->with(['mataKuliah.programStudi', 'ruangan']);

        if ($request->prodi) {
            $query->whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        if ($request->jenis) {
            $query->where('jenis_ujian', $request->jenis);
        }

        $jadwalUjians = $query->orderBy('tanggal')->orderBy('jam_mulai')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.jadwal-ujian.index', compact('fakultas', 'jadwalUjians', 'prodis', 'tahunAktif'));
    }

    // =====================
    // JADWAL KULIAH
    // =====================

    /**
     * Monitoring Jadwal Kuliah Fakultas
     */
    public function jadwalKuliah(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        $query = JadwalKuliah::whereHas('mataKuliah', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->with(['mataKuliah.programStudi', 'dosen', 'ruangan']);

        if ($request->prodi) {
            $query->whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        if ($request->hari) {
            $query->where('hari', $request->hari);
        }

        $jadwals = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.jadwal-kuliah.index', compact('fakultas', 'jadwals', 'prodis', 'tahunAktif'));
    }

    // =====================
    // PKL/MAGANG
    // =====================

    /**
     * Monitoring PKL/Magang Fakultas
     */
    public function pkl(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = PendaftaranKegiatanLapangan::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi', 'periode.jenisKegiatan', 'mitraDiterima']);

        if ($request->prodi) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->orderBy('created_at', 'desc')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        // Statistik
        $stats = [
            'pending' => PendaftaranKegiatanLapangan::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'pending')->count(),
            'berlangsung' => PendaftaranKegiatanLapangan::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'berlangsung')->count(),
            'selesai' => PendaftaranKegiatanLapangan::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))->where('status', 'selesai')->count(),
        ];

        return view('dekan.pkl.index', compact('fakultas', 'pendaftarans', 'prodis', 'stats'));
    }

    // =====================
    // TUGAS AKHIR DETAIL
    // =====================

    /**
     * Detail Tugas Akhir
     */
    public function tugasAkhirShow(TugasAkhir $tugasAkhir)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($tugasAkhir->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $tugasAkhir->load([
            'mahasiswa.programStudi',
            'pembimbing1',
            'pembimbing2',
            'bimbingan' => fn($q) => $q->orderBy('tanggal', 'desc'),
            'seminarProposal',
            'sidang',
            'revisi'
        ]);

        return view('dekan.tugas-akhir.show', compact('fakultas', 'tugasAkhir'));
    }

    // =====================
    // KONVERSI NILAI
    // =====================

    /**
     * Monitoring Konversi Nilai Fakultas
     */
    public function konversiNilai(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = PengajuanKonversi::whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi']);

        if ($request->prodi) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $request->prodi));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $konversis = $query->orderBy('created_at', 'desc')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.konversi-nilai.index', compact('fakultas', 'konversis', 'prodis'));
    }

    // =====================
    // KURIKULUM
    // =====================

    /**
     * Monitoring Kurikulum Fakultas
     */
    public function kurikulumIndex()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $kurikulums = Kurikulum::whereIn('program_studi_id', $prodiIds)
            ->with(['programStudi'])
            ->withCount('mataKuliah')
            ->orderBy('tahun_mulai', 'desc')
            ->paginate(20);

        return view('dekan.kurikulum.index', compact('fakultas', 'kurikulums'));
    }

    /**
     * Detail Kurikulum
     */
    public function kurikulumShow(Kurikulum $kurikulum)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($kurikulum->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $kurikulum->load(['programStudi', 'mataKuliah']);

        return view('dekan.kurikulum.show', compact('fakultas', 'kurikulum'));
    }

    // =====================
    // MONITORING NILAI
    // =====================

    /**
     * Rekap Nilai Fakultas
     */
    public function rekapNilai(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        // Rekap per prodi
        $rekapPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount(['mahasiswa' => fn($q) => $q->where('status', 'Aktif')])
            ->get()
            ->map(function($prodi) use ($tahunAktif) {
                // Hitung rata-rata IPK
                $mahasiswas = Mahasiswa::where('program_studi_id', $prodi->id)
                    ->where('status', 'Aktif')
                    ->with(['krs.nilai', 'krs.jadwalKuliah.mataKuliah'])
                    ->get();
                
                $totalIpk = 0;
                $countMhs = 0;
                
                foreach ($mahasiswas as $mhs) {
                    $totalSks = 0;
                    $totalBobot = 0;
                    
                    foreach ($mhs->krs as $krs) {
                        if ($krs->nilai && $krs->nilai->bobot !== null) {
                            $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                            $totalSks += $sks;
                            $totalBobot += $krs->nilai->bobot * $sks;
                        }
                    }
                    
                    if ($totalSks > 0) {
                        $totalIpk += $totalBobot / $totalSks;
                        $countMhs++;
                    }
                }
                
                $prodi->rata_ipk = $countMhs > 0 ? round($totalIpk / $countMhs, 2) : 0;
                return $prodi;
            });

        return view('dekan.nilai.rekap', compact('fakultas', 'rekapPerProdi', 'tahunAktif'));
    }

    // =====================
    // DETAIL WISUDA
    // =====================

    /**
     * Detail Wisuda - Pendaftar per Periode
     */
    public function wisudaShow(PeriodeWisuda $periodeWisuda)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $pendaftars = PendaftaranWisuda::where('periode_wisuda_id', $periodeWisuda->id)
            ->whereHas('mahasiswa', fn($q) => $q->whereIn('program_studi_id', $prodiIds))
            ->with(['mahasiswa.programStudi', 'yudisium'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistik per prodi
        $statsPerProdi = ProgramStudi::where('fakultas_id', $fakultas->id)
            ->withCount(['mahasiswa as pendaftar_count' => function($q) use ($periodeWisuda) {
                $q->whereHas('pendaftaranWisuda', fn($q2) => $q2->where('periode_wisuda_id', $periodeWisuda->id));
            }])
            ->get();

        $totalPendaftar = $pendaftars->total();

        return view('dekan.wisuda.show', compact('fakultas', 'periodeWisuda', 'pendaftars', 'statsPerProdi', 'totalPendaftar'));
    }

    // =====================
    // DETAIL YUDISIUM & APPROVAL
    // =====================

    /**
     * Detail Yudisium
     */
    public function yudisiumShow(Yudisium $yudisium)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($yudisium->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $yudisium->load([
            'mahasiswa.programStudi',
            'mahasiswa.krs.nilai',
            'mahasiswa.krs.jadwalKuliah.mataKuliah',
            'pendaftaranWisuda.periodeWisuda'
        ]);

        // Hitung IPK
        $totalSks = 0;
        $totalBobot = 0;
        foreach ($yudisium->mahasiswa->krs as $krs) {
            if ($krs->nilai && $krs->nilai->bobot !== null) {
                $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                $totalSks += $sks;
                $totalBobot += $krs->nilai->bobot * $sks;
            }
        }
        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        return view('dekan.yudisium.show', compact('fakultas', 'yudisium', 'ipk', 'totalSks'));
    }

    /**
     * Approval Yudisium oleh Dekan
     */
    public function yudisiumApproval(Request $request, Yudisium $yudisium)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($yudisium->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:lulus,tidak_lulus',
            'predikat' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $yudisium->status = $request->status;
        $yudisium->predikat = $request->predikat;
        $yudisium->catatan = $request->catatan;
        $yudisium->disetujui_oleh = auth()->id();
        $yudisium->tanggal_yudisium = now();
        $yudisium->save();

        // Jika lulus, update status mahasiswa
        if ($request->status == 'lulus') {
            $yudisium->mahasiswa->update(['status' => 'Lulus']);
        }

        return back()->with('success', 'Yudisium berhasil diproses!');
    }

    // =====================
    // DETAIL BIMBINGAN
    // =====================

    /**
     * Detail Bimbingan Akademik
     */
    public function bimbinganShow(BimbinganAkademik $bimbingan)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($bimbingan->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $bimbingan->load(['mahasiswa.programStudi', 'dosen']);

        return view('dekan.bimbingan.show', compact('fakultas', 'bimbingan'));
    }

    // =====================
    // DETAIL ABSENSI
    // =====================

    /**
     * Detail Absensi per Jadwal
     */
    public function absensiShow(JadwalKuliah $jadwalKuliah)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($jadwalKuliah->mataKuliah->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $jadwalKuliah->load([
            'mataKuliah.programStudi',
            'dosen',
            'ruangan',
            'pertemuan' => fn($q) => $q->orderBy('pertemuan_ke'),
            'krs.mahasiswa'
        ]);

        // Statistik kehadiran
        $totalPertemuan = $jadwalKuliah->pertemuan->count();
        $totalMahasiswa = $jadwalKuliah->krs->count();

        return view('dekan.absensi.show', compact('fakultas', 'jadwalKuliah', 'totalPertemuan', 'totalMahasiswa'));
    }

    // =====================
    // DETAIL KONVERSI NILAI
    // =====================

    /**
     * Detail Konversi Nilai
     */
    public function konversiNilaiShow(PengajuanKonversi $pengajuanKonversi)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($pengajuanKonversi->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $pengajuanKonversi->load([
            'mahasiswa.programStudi',
            'detailKonversi.mataKuliahAsal',
            'detailKonversi.mataKuliahTujuan'
        ]);

        return view('dekan.konversi-nilai.show', compact('fakultas', 'pengajuanKonversi'));
    }

    // =====================
    // DETAIL PKL/MAGANG
    // =====================

    /**
     * Detail PKL/Magang
     */
    public function pklShow(PendaftaranKegiatanLapangan $pendaftaran)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($pendaftaran->mahasiswa->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $pendaftaran->load([
            'mahasiswa.programStudi',
            'periode.jenisKegiatan',
            'mitraDiterima',
            'dosenPembimbing',
            'logKegiatan',
            'penilaian'
        ]);

        return view('dekan.pkl.show', compact('fakultas', 'pendaftaran'));
    }

    // =====================
    // MONITORING IPK
    // =====================

    /**
     * Monitoring IPK Fakultas
     */
    public function monitoringIpk(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->where('status', 'Aktif')
            ->with(['programStudi', 'krs.nilai', 'krs.jadwalKuliah.mataKuliah']);

        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswas = $query->get()->map(function($mhs) {
            $totalSks = 0;
            $totalBobot = 0;
            
            foreach ($mhs->krs as $krs) {
                if ($krs->nilai && $krs->nilai->bobot !== null) {
                    $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                    $totalSks += $sks;
                    $totalBobot += $krs->nilai->bobot * $sks;
                }
            }
            
            $mhs->ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
            $mhs->total_sks = $totalSks;
            return $mhs;
        });

        // Filter berdasarkan kategori IPK
        if ($request->kategori) {
            $mahasiswas = $mahasiswas->filter(function($m) use ($request) {
                switch ($request->kategori) {
                    case 'cumlaude': return $m->ipk >= 3.50;
                    case 'sangat_memuaskan': return $m->ipk >= 3.00 && $m->ipk < 3.50;
                    case 'memuaskan': return $m->ipk >= 2.50 && $m->ipk < 3.00;
                    case 'cukup': return $m->ipk >= 2.00 && $m->ipk < 2.50;
                    case 'kurang': return $m->ipk < 2.00 && $m->total_sks > 0;
                    default: return true;
                }
            });
        }

        // Sort
        $sortBy = $request->sort ?? 'ipk_desc';
        if ($sortBy == 'ipk_desc') {
            $mahasiswas = $mahasiswas->sortByDesc('ipk');
        } elseif ($sortBy == 'ipk_asc') {
            $mahasiswas = $mahasiswas->sortBy('ipk');
        } elseif ($sortBy == 'nama') {
            $mahasiswas = $mahasiswas->sortBy('nama');
        }

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $total = $mahasiswas->count();
        $mahasiswas = $mahasiswas->slice(($page - 1) * $perPage, $perPage)->values();
        
        $mahasiswas = new \Illuminate\Pagination\LengthAwarePaginator(
            $mahasiswas, $total, $perPage, $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Statistik
        $distribusiIpk = [
            'cumlaude' => 0,
            'sangat_memuaskan' => 0,
            'memuaskan' => 0,
            'cukup' => 0,
            'kurang' => 0,
        ];

        $allMahasiswa = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->where('status', 'Aktif')
            ->with(['krs.nilai', 'krs.jadwalKuliah.mataKuliah'])
            ->get();

        foreach ($allMahasiswa as $mhs) {
            $totalSks = 0;
            $totalBobot = 0;
            
            foreach ($mhs->krs as $krs) {
                if ($krs->nilai && $krs->nilai->bobot !== null) {
                    $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                    $totalSks += $sks;
                    $totalBobot += $krs->nilai->bobot * $sks;
                }
            }
            
            if ($totalSks > 0) {
                $ipk = $totalBobot / $totalSks;
                if ($ipk >= 3.50) $distribusiIpk['cumlaude']++;
                elseif ($ipk >= 3.00) $distribusiIpk['sangat_memuaskan']++;
                elseif ($ipk >= 2.50) $distribusiIpk['memuaskan']++;
                elseif ($ipk >= 2.00) $distribusiIpk['cukup']++;
                else $distribusiIpk['kurang']++;
            }
        }

        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();
        $angkatans = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        return view('dekan.nilai.monitoring-ipk', compact('fakultas', 'mahasiswas', 'prodis', 'angkatans', 'distribusiIpk'));
    }

    // =====================
    // EXPORT DATA
    // =====================

    /**
     * Halaman Export Mahasiswa
     */
    public function exportMahasiswaIndex()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();
        $angkatans = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        // Statistik
        $totalMahasiswa = Mahasiswa::whereIn('program_studi_id', $prodiIds)->count();
        $totalAktif = Mahasiswa::whereIn('program_studi_id', $prodiIds)->where('status', 'Aktif')->count();

        return view('dekan.export.mahasiswa', compact('fakultas', 'prodis', 'angkatans', 'totalMahasiswa', 'totalAktif'));
    }

    /**
     * Export Mahasiswa Fakultas (Download)
     */
    public function exportMahasiswa(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->with(['programStudi', 'dosenWali']);

        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswas = $query->orderBy('nama')->get();

        // Generate CSV
        $filename = 'mahasiswa_' . $fakultas->kode . '_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($mahasiswas) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['NIM', 'Nama', 'Program Studi', 'Angkatan', 'Status', 'Dosen Wali', 'Email', 'No. HP']);
            
            foreach ($mahasiswas as $mhs) {
                fputcsv($file, [
                    $mhs->nim,
                    $mhs->nama,
                    $mhs->programStudi->nama ?? '-',
                    $mhs->angkatan,
                    $mhs->status,
                    $mhs->dosenWali->nama ?? '-',
                    $mhs->email ?? '-',
                    $mhs->no_hp ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Halaman Export Nilai
     */
    public function exportNilaiIndex()
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();
        $angkatans = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        // Statistik
        $totalMahasiswa = Mahasiswa::whereIn('program_studi_id', $prodiIds)->where('status', 'Aktif')->count();

        return view('dekan.export.nilai', compact('fakultas', 'prodis', 'angkatans', 'totalMahasiswa'));
    }

    /**
     * Export Rekap Nilai Fakultas (Download)
     */
    public function exportNilai(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = Mahasiswa::whereIn('program_studi_id', $prodiIds)
            ->where('status', 'Aktif')
            ->with(['programStudi', 'krs.nilai', 'krs.jadwalKuliah.mataKuliah']);

        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        $mahasiswas = $query->get()->map(function($mhs) {
            $totalSks = 0;
            $totalBobot = 0;
            
            foreach ($mhs->krs as $krs) {
                if ($krs->nilai && $krs->nilai->bobot !== null) {
                    $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                    $totalSks += $sks;
                    $totalBobot += $krs->nilai->bobot * $sks;
                }
            }
            
            $mhs->ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
            $mhs->total_sks = $totalSks;
            return $mhs;
        });

        // Generate CSV
        $filename = 'rekap_nilai_' . $fakultas->kode . '_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($mahasiswas) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['NIM', 'Nama', 'Program Studi', 'Angkatan', 'Total SKS', 'IPK', 'Predikat']);
            
            foreach ($mahasiswas as $mhs) {
                $predikat = '-';
                if ($mhs->total_sks > 0) {
                    if ($mhs->ipk >= 3.50) $predikat = 'Cum Laude';
                    elseif ($mhs->ipk >= 3.00) $predikat = 'Sangat Memuaskan';
                    elseif ($mhs->ipk >= 2.50) $predikat = 'Memuaskan';
                    elseif ($mhs->ipk >= 2.00) $predikat = 'Cukup';
                    else $predikat = 'Kurang';
                }
                
                fputcsv($file, [
                    $mhs->nim,
                    $mhs->nama,
                    $mhs->programStudi->nama ?? '-',
                    $mhs->angkatan,
                    $mhs->total_sks,
                    $mhs->ipk,
                    $predikat,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =====================
    // MATA KULIAH
    // =====================

    /**
     * Daftar Mata Kuliah Fakultas
     */
    public function mataKuliahIndex(Request $request)
    {
        $fakultas = $this->getFakultas();
        
        if (!$fakultas) {
            return redirect()->route('dashboard')->with('error', 'Data fakultas tidak ditemukan!');
        }

        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');

        $query = MataKuliah::whereIn('program_studi_id', $prodiIds)
            ->with(['programStudi']);

        if ($request->prodi) {
            $query->where('program_studi_id', $request->prodi);
        }

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mataKuliahs = $query->orderBy('kode')->paginate(20);
        $prodis = ProgramStudi::where('fakultas_id', $fakultas->id)->orderBy('nama')->get();

        return view('dekan.mata-kuliah.index', compact('fakultas', 'mataKuliahs', 'prodis'));
    }

    /**
     * Detail Mata Kuliah
     */
    public function mataKuliahShow(MataKuliah $mataKuliah)
    {
        $fakultas = $this->getFakultas();
        $prodiIds = ProgramStudi::where('fakultas_id', $fakultas->id)->pluck('id');
        
        if (!$fakultas || !in_array($mataKuliah->program_studi_id, $prodiIds->toArray())) {
            abort(403);
        }

        $mataKuliah->load(['programStudi', 'jadwalKuliah.dosen', 'prasyarat']);

        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        // Jadwal semester ini
        $jadwalSemesterIni = $mataKuliah->jadwalKuliah()
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->with(['dosen', 'ruangan'])
            ->get();

        return view('dekan.mata-kuliah.show', compact('fakultas', 'mataKuliah', 'jadwalSemesterIni', 'tahunAktif'));
    }
}
