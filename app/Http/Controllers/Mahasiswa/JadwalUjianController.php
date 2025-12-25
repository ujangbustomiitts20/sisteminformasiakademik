<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\Krs;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalUjianController extends Controller
{
    /**
     * Tampilkan jadwal ujian mahasiswa
     */
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        
        if (!$tahunAkademik) {
            return view('mahasiswa.jadwal-ujian.index', [
                'mahasiswa' => $mahasiswa,
                'jadwalUjian' => collect(),
                'jadwalByJenis' => collect(),
                'tahunAkademik' => null,
                'stats' => [
                    'total' => 0,
                    'uts' => 0,
                    'uas' => 0,
                    'upcoming' => 0,
                ],
            ]);
        }

        // Ambil mata kuliah yang diambil mahasiswa semester ini (hanya yang disetujui)
        $krsList = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->where('status', 'Disetujui')
            ->with('jadwalKuliah')
            ->get();

        $mataKuliahIds = $krsList->map(function ($krs) {
            return $krs->jadwalKuliah->mata_kuliah_id ?? null;
        })->filter()->unique()->toArray();

        // Query jadwal ujian
        $query = JadwalUjian::with(['mataKuliah', 'dosen', 'jadwalKuliah'])
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->whereIn('mata_kuliah_id', $mataKuliahIds);

        // Filter by jenis ujian
        if ($request->filled('jenis')) {
            $query->where('jenis_ujian', $request->jenis);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jadwalUjian = $query->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        // Group by jenis ujian
        $jadwalByJenis = $jadwalUjian->groupBy('jenis_ujian');

        // Statistik
        $stats = [
            'total' => $jadwalUjian->count(),
            'uts' => $jadwalUjian->where('jenis_ujian', 'UTS')->count(),
            'uas' => $jadwalUjian->where('jenis_ujian', 'UAS')->count(),
            'upcoming' => $jadwalUjian->where('tanggal', '>=', now()->toDateString())->where('status', 'Terjadwal')->count(),
        ];

        return view('mahasiswa.jadwal-ujian.index', compact(
            'mahasiswa',
            'jadwalUjian',
            'jadwalByJenis',
            'tahunAkademik',
            'stats'
        ));
    }

    /**
     * Tampilkan detail jadwal ujian
     */
    public function show(JadwalUjian $jadwalUjian)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $jadwalUjian->load(['mataKuliah', 'dosen', 'jadwalKuliah', 'tahunAkademik']);

        return view('mahasiswa.jadwal-ujian.show', compact('mahasiswa', 'jadwalUjian'));
    }
}
