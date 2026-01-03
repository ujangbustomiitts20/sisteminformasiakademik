<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    /**
     * Jadwal kuliah mahasiswa
     */
    public function mahasiswa(Request $request)
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;
        
        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Get list of tahun akademik yang mahasiswa punya KRS
        $tahunAkademikList = TahunAkademik::whereIn('id', function($query) use ($mahasiswa) {
            $query->select('tahun_akademik_id')
                ->from('krs')
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'Disetujui')
                ->distinct();
        })->orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();

        // Default ke tahun akademik aktif atau yang terakhir mahasiswa punya KRS
        $tahunAkademikAktif = TahunAkademik::getAktif();
        
        if ($request->filled('tahun_akademik_id')) {
            $tahunAkademik = TahunAkademik::find($request->tahun_akademik_id);
        } else {
            // Cek apakah mahasiswa punya KRS di tahun aktif
            $hasKrsAktif = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
                ->where('status', 'Disetujui')
                ->exists();
            
            if ($hasKrsAktif) {
                $tahunAkademik = $tahunAkademikAktif;
            } else {
                // Gunakan tahun akademik terakhir yang punya KRS
                $tahunAkademik = $tahunAkademikList->first() ?? $tahunAkademikAktif;
            }
        }
        
        $jadwal = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->where('status', 'Disetujui')
            ->get()
            ->sortBy(function($krs) {
                $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
                return ($hariOrder[$krs->jadwalKuliah->hari] ?? 7) . $krs->jadwalKuliah->jam_mulai;
            });

        return view('jadwal.mahasiswa', compact('jadwal', 'tahunAkademik', 'mahasiswa', 'tahunAkademikList'));
    }

    /**
     * Jadwal mengajar dosen
     */
    public function dosen()
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        
        if (!$dosen) {
            return back()->with('error', 'Data dosen tidak ditemukan');
        }

        $tahunAkademik = TahunAkademik::getAktif();
        
        $jadwal = JadwalKuliah::with(['mataKuliah', 'ruangan', 'krs'])
            ->where('dosen_id', $dosen->id)
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->get()
            ->sortBy(function($j) {
                $hariOrder = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6];
                return ($hariOrder[$j->hari] ?? 7) . $j->jam_mulai;
            });

        return view('jadwal.dosen', compact('jadwal', 'tahunAkademik', 'dosen'));
    }
}
