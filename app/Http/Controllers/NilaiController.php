<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Krs;
use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    // Untuk dosen input nilai
    public function index(Request $request)
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $jadwalMengajar = [];
        if ($dosen && $tahunAkademikAktif) {
            $jadwalMengajar = JadwalKuliah::where('dosen_id', $dosen->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif->id)
                ->with(['mataKuliah', 'ruangan'])
                ->get();
        }

        return view('nilai.index', compact('jadwalMengajar', 'tahunAkademik', 'tahunAkademikAktif'));
    }

    // Form input nilai per kelas
    public function inputNilai(JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        // Validasi akses
        if ($user->isDosen() && $jadwalKuliah->dosen_id != $user->dosen->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke kelas ini!');
        }

        $jadwalKuliah->load(['mataKuliah', 'dosen', 'ruangan', 'tahunAkademik']);
        
        $krsData = Krs::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->with(['mahasiswa', 'nilai'])
            ->orderBy('mahasiswa_id')
            ->get();

        return view('nilai.input', compact('jadwalKuliah', 'krsData'));
    }

    // Simpan nilai
    public function store(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $request->validate([
            'nilai' => 'required|array',
            'nilai.*.krs_id' => 'required|exists:krs,id',
            'nilai.*.tugas' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uts' => 'nullable|numeric|min:0|max:100',
            'nilai.*.uas' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($request->nilai as $data) {
            $nilai = Nilai::firstOrCreate(['krs_id' => $data['krs_id']]);
            
            $nilai->update([
                'tugas' => $data['tugas'] ?? null,
                'uts' => $data['uts'] ?? null,
                'uas' => $data['uas'] ?? null,
            ]);

            // Hitung nilai akhir otomatis
            $nilai->hitungNilaiAkhir();
        }

        return back()->with('success', 'Nilai berhasil disimpan!');
    }

    // Untuk mahasiswa melihat KHS
    public function khs(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        
        $tahunAkademikSelected = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $khs = [];
        $ips = 0;
        $totalSks = 0;

        if ($mahasiswa && $tahunAkademikSelected) {
            $khs = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikSelected->id)
                ->where('status', 'Disetujui')
                ->with(['jadwalKuliah.mataKuliah', 'nilai'])
                ->get();

            // Hitung IPS (Index Prestasi Semester)
            $totalBobot = 0;
            $totalSksSemester = 0;

            foreach ($khs as $k) {
                if ($k->nilai && $k->nilai->bobot !== null) {
                    $sks = $k->jadwalKuliah->mataKuliah->sks;
                    $totalBobot += $k->nilai->bobot * $sks;
                    $totalSksSemester += $sks;
                }
            }

            $ips = $totalSksSemester > 0 ? round($totalBobot / $totalSksSemester, 2) : 0;
            $totalSks = $khs->sum(fn($k) => $k->jadwalKuliah->mataKuliah->sks ?? 0);
        }

        $ipk = $mahasiswa ? $mahasiswa->hitungIPK() : 0;

        return view('nilai.khs', compact('mahasiswa', 'tahunAkademik', 'tahunAkademikSelected', 'khs', 'ips', 'ipk', 'totalSks'));
    }

    // Transkrip nilai
    public function transkrip()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan');
        }

        $transkrip = Krs::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Disetujui')
            ->whereHas('nilai', function($q) {
                $q->whereNotNull('huruf');
            })
            ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.tahunAkademik', 'nilai'])
            ->get()
            ->groupBy(fn($krs) => $krs->jadwalKuliah->tahunAkademik->nama_lengkap);

        $ipk = $mahasiswa->hitungIPK();
        $totalSks = $mahasiswa->totalSksLulus();

        return view('nilai.transkrip', compact('mahasiswa', 'transkrip', 'ipk', 'totalSks'));
    }
}
