<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\RekapKehadiran;
use App\Models\Krs;
use App\Models\MataKuliah;
use App\Models\TahunAkademik;
use App\Models\Pertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KehadiranController extends Controller
{
    /**
     * Tampilkan rekap kehadiran mahasiswa
     */
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Ambil tahun akademik
        $tahunAkademiks = TahunAkademik::whereHas('krs', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();

        // Default tahun akademik aktif atau yang dipilih
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        if ($request->filled('tahun_akademik')) {
            $tahunAkademik = TahunAkademik::find($request->tahun_akademik);
        }

        $rekapList = collect();
        $stats = null;

        if ($tahunAkademik) {
            // Ambil KRS yang sudah disetujui untuk tahun akademik ini dengan relasi jadwalKuliah
            $krsList = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademik->id)
                ->where('status', 'Disetujui')
                ->with('jadwalKuliah.mataKuliah')
                ->get();

            if ($krsList->isNotEmpty()) {
                // Ambil mata kuliah IDs dari jadwal kuliah
                $mataKuliahIds = $krsList->map(function ($krs) {
                    return $krs->jadwalKuliah->mata_kuliah_id ?? null;
                })->filter()->unique()->toArray();

                // Ambil rekap kehadiran atau generate dari absensi
                foreach ($mataKuliahIds as $mkId) {
                    $rekap = RekapKehadiran::with('mataKuliah')
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->where('mata_kuliah_id', $mkId)
                        ->where('tahun_akademik_id', $tahunAkademik->id)
                        ->first();

                    if (!$rekap) {
                        // Generate dari absensi
                        $rekap = $this->generateRekap($mahasiswa->id, $mkId, $tahunAkademik->id);
                        if ($rekap) {
                            $rekap->load('mataKuliah');
                        }
                    }

                    if ($rekap) {
                        $rekapList->push($rekap);
                    }
                }

                // Hitung statistik keseluruhan
                $totalPertemuan = $rekapList->sum('total_pertemuan');
                $totalHadir = $rekapList->sum('jumlah_hadir');
                $totalIzin = $rekapList->sum('jumlah_izin');
                $totalSakit = $rekapList->sum('jumlah_sakit');
                $totalAlpa = $rekapList->sum('jumlah_alpa');
                
                $persentaseTotal = $totalPertemuan > 0 
                    ? round(($totalHadir / $totalPertemuan) * 100, 2) 
                    : 0;

                $stats = [
                    'total_mk' => $rekapList->count(),
                    'total_pertemuan' => $totalPertemuan,
                    'total_hadir' => $totalHadir,
                    'total_izin' => $totalIzin,
                    'total_sakit' => $totalSakit,
                    'total_alpa' => $totalAlpa,
                    'persentase_kehadiran' => $persentaseTotal,
                    'memenuhi_syarat' => $rekapList->filter(fn($r) => $r->persentase_kehadiran >= 75)->count(),
                    'tidak_memenuhi' => $rekapList->filter(fn($r) => $r->persentase_kehadiran < 75)->count(),
                ];
            }
        }

        return view('mahasiswa.kehadiran.index', compact(
            'mahasiswa',
            'tahunAkademiks',
            'tahunAkademik',
            'rekapList',
            'stats'
        ));
    }

    /**
     * Tampilkan detail kehadiran per mata kuliah
     */
    public function detail(Request $request, MataKuliah $mataKuliah)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        if ($request->filled('tahun_akademik')) {
            $tahunAkademik = TahunAkademik::find($request->tahun_akademik);
        }

        // Ambil semua absensi untuk MK ini melalui KRS
        $absensiList = Absensi::with(['pertemuanData', 'krs.jadwalKuliah'])
            ->whereHas('krs', function ($q) use ($mahasiswa, $tahunAkademik) {
                $q->where('mahasiswa_id', $mahasiswa->id);
                if ($tahunAkademik) {
                    $q->where('tahun_akademik_id', $tahunAkademik->id);
                }
            })
            ->whereHas('krs.jadwalKuliah', function ($q) use ($mataKuliah) {
                $q->where('mata_kuliah_id', $mataKuliah->id);
            })
            ->orderBy('tanggal')
            ->orderBy('pertemuan')
            ->get();

        // Rekap
        $rekap = RekapKehadiran::where('mahasiswa_id', $mahasiswa->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->first();

        return view('mahasiswa.kehadiran.detail', compact(
            'mahasiswa',
            'mataKuliah',
            'tahunAkademik',
            'absensiList',
            'rekap'
        ));
    }

    /**
     * Generate rekap kehadiran dari data absensi
     */
    private function generateRekap(int $mahasiswaId, int $mataKuliahId, int $tahunAkademikId): ?RekapKehadiran
    {
        // Ambil semua absensi melalui KRS
        $absensiList = Absensi::whereHas('krs', function ($q) use ($mahasiswaId, $tahunAkademikId) {
            $q->where('mahasiswa_id', $mahasiswaId)
              ->where('tahun_akademik_id', $tahunAkademikId);
        })->whereHas('krs.jadwalKuliah', function ($q) use ($mataKuliahId) {
            $q->where('mata_kuliah_id', $mataKuliahId);
        })->get();

        if ($absensiList->isEmpty()) {
            // Cek apakah ada pertemuan untuk MK ini
            $pertemuanCount = Pertemuan::whereHas('jadwalKuliah', function ($q) use ($mataKuliahId, $tahunAkademikId) {
                $q->where('mata_kuliah_id', $mataKuliahId)
                  ->where('tahun_akademik_id', $tahunAkademikId);
            })->count();

            if ($pertemuanCount == 0) {
                // Buat rekap kosong dengan data default
                return RekapKehadiran::firstOrCreate([
                    'mahasiswa_id' => $mahasiswaId,
                    'mata_kuliah_id' => $mataKuliahId,
                    'tahun_akademik_id' => $tahunAkademikId,
                ], [
                    'total_pertemuan' => 0,
                    'jumlah_hadir' => 0,
                    'jumlah_izin' => 0,
                    'jumlah_sakit' => 0,
                    'jumlah_alpa' => 0,
                    'persentase_kehadiran' => 0,
                ]);
            }

            // Buat rekap kosong
            return RekapKehadiran::firstOrCreate([
                'mahasiswa_id' => $mahasiswaId,
                'mata_kuliah_id' => $mataKuliahId,
                'tahun_akademik_id' => $tahunAkademikId,
            ], [
                'total_pertemuan' => $pertemuanCount,
                'jumlah_hadir' => 0,
                'jumlah_izin' => 0,
                'jumlah_sakit' => 0,
                'jumlah_alpa' => 0,
                'persentase_kehadiran' => 0,
            ]);
        }

        $rekap = RekapKehadiran::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswaId,
                'mata_kuliah_id' => $mataKuliahId,
                'tahun_akademik_id' => $tahunAkademikId,
            ],
            [
                'total_pertemuan' => $absensiList->count(),
                'jumlah_hadir' => $absensiList->where('status', 'Hadir')->count(),
                'jumlah_izin' => $absensiList->where('status', 'Izin')->count(),
                'jumlah_sakit' => $absensiList->where('status', 'Sakit')->count(),
                'jumlah_alpa' => $absensiList->whereIn('status', ['Alpa', 'Alpha'])->count(),
            ]
        );

        $rekap->persentase_kehadiran = $rekap->hitungPersentase();
        $rekap->save();

        return $rekap;
    }
}
