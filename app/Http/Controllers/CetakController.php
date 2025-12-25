<?php

namespace App\Http\Controllers;

use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    public function cetakKrs(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
        } else {
            $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
        }

        $tahunAkademik = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id) 
            : TahunAkademik::getAktif();

        $krs = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->where('status', 'Disetujui')
            ->get();

        $totalSks = $krs->sum(fn($k) => $k->jadwalKuliah->mataKuliah->sks);

        $pdf = Pdf::loadView('cetak.krs', compact('mahasiswa', 'tahunAkademik', 'krs', 'totalSks'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('KRS_' . $mahasiswa->nim . '_' . str_replace('/', '-', $tahunAkademik->tahun) . '.pdf');
    }

    public function cetakKhs(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
        } else {
            $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
        }

        $tahunAkademik = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id) 
            : TahunAkademik::getAktif();

        $krs = Krs::with(['jadwalKuliah.mataKuliah', 'nilai'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->where('status', 'Disetujui')
            ->get();

        // Hitung IPK
        $totalSks = 0;
        $totalBobot = 0;
        foreach ($krs as $k) {
            if ($k->nilai) {
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $totalSks += $sks;
                $totalBobot += $sks * $k->nilai->bobot;
            }
        }
        $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        $pdf = Pdf::loadView('cetak.khs', compact('mahasiswa', 'tahunAkademik', 'krs', 'ips', 'totalSks'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('KHS_' . $mahasiswa->nim . '_' . str_replace('/', '-', $tahunAkademik->tahun) . '.pdf');
    }

    public function cetakTranskrip(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
        } else {
            $mahasiswa = Mahasiswa::findOrFail($request->mahasiswa_id);
        }

        $krs = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.tahunAkademik', 'nilai'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Disetujui')
            ->whereHas('nilai')
            ->get()
            ->sortBy(fn($k) => $k->jadwalKuliah->tahunAkademik->tahun . $k->jadwalKuliah->tahunAkademik->semester);

        // Hitung IPK Kumulatif
        $totalSks = 0;
        $totalBobot = 0;
        foreach ($krs as $k) {
            if ($k->nilai) {
                $sks = $k->jadwalKuliah->mataKuliah->sks;
                $totalSks += $sks;
                $totalBobot += $sks * $k->nilai->bobot;
            }
        }
        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        $pdf = Pdf::loadView('cetak.transkrip', compact('mahasiswa', 'krs', 'ipk', 'totalSks'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Transkrip_' . $mahasiswa->nim . '.pdf');
    }
}
