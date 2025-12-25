<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Krs;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class KhsController extends Controller
{
    /**
     * Tampilkan daftar KHS per semester
     */
    public function index(Request $request)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Ambil semua tahun akademik yang mahasiswa pernah mengambil KRS
        $tahunAkademiks = TahunAkademik::whereHas('krs', function($q) use ($mahasiswa) {
            $q->where('mahasiswa_id', $mahasiswa->id);
        })->orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();

        // Default tahun akademik aktif atau yang dipilih
        $selectedTahunAkademik = null;
        if ($request->filled('tahun_akademik')) {
            $selectedTahunAkademik = TahunAkademik::find($request->tahun_akademik);
        }
        
        if (!$selectedTahunAkademik && $tahunAkademiks->isNotEmpty()) {
            $selectedTahunAkademik = $tahunAkademiks->first();
        }

        $khs = null;
        $krsList = collect();
        $stats = null;

        if ($selectedTahunAkademik) {
            // Ambil KRS untuk tahun akademik ini dengan nilai
            $krsList = Krs::with(['nilai', 'jadwalKuliah.mataKuliah'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $selectedTahunAkademik->id)
                ->get();

            if ($krsList->isNotEmpty()) {
                // Hitung IPS (IP Semester)
                $totalSks = 0;
                $totalBobot = 0;
                
                foreach ($krsList as $krs) {
                    if ($krs->nilai && $krs->nilai->huruf) {
                        $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                        $bobot = $krs->nilai->bobot ?? 0;
                        $totalSks += $sks;
                        $totalBobot += ($sks * $bobot);
                    }
                }
                
                $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

                // Hitung IPK kumulatif
                $semuaKrs = Krs::with(['nilai', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->whereHas('nilai', function($q) {
                        $q->whereNotNull('huruf');
                    })
                    ->whereHas('tahunAkademik', function($q) use ($selectedTahunAkademik) {
                        $q->where('tahun', '<=', $selectedTahunAkademik->tahun);
                    })
                    ->get();

                $totalSksKum = 0;
                $totalBobotKum = 0;
                
                foreach ($semuaKrs as $k) {
                    if ($k->nilai && $k->nilai->huruf) {
                        $sks = $k->jadwalKuliah->mataKuliah->sks ?? 0;
                        $bobot = $k->nilai->bobot ?? 0;
                        $totalSksKum += $sks;
                        $totalBobotKum += ($sks * $bobot);
                    }
                }
                
                $ipk = $totalSksKum > 0 ? round($totalBobotKum / $totalSksKum, 2) : 0;

                $stats = [
                    'sks_semester' => $totalSks,
                    'sks_kumulatif' => $totalSksKum,
                    'ips' => $ips,
                    'ipk' => $ipk,
                    'jumlah_mk' => $krsList->count(),
                ];

                $khs = $krsList->first();
            }
        }

        return view('mahasiswa.khs.index', compact(
            'mahasiswa', 
            'tahunAkademiks', 
            'selectedTahunAkademik', 
            'khs', 
            'krsList', 
            'stats'
        ));
    }

    /**
     * Cetak KHS PDF
     */
    public function cetak(TahunAkademik $tahunAkademik)
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $krsList = Krs::with(['nilai', 'jadwalKuliah.mataKuliah'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->get();

        // Hitung IPS
        $totalSks = 0;
        $totalBobot = 0;
        
        foreach ($krsList as $krs) {
            if ($krs->nilai && $krs->nilai->huruf) {
                $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $krs->nilai->bobot ?? 0;
                $totalSks += $sks;
                $totalBobot += ($sks * $bobot);
            }
        }
        
        $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        // Hitung IPK kumulatif
        $semuaKrs = Krs::with(['nilai', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('nilai', function($q) {
                $q->whereNotNull('huruf');
            })
            ->whereHas('tahunAkademik', function($q) use ($tahunAkademik) {
                $q->where('tahun', '<=', $tahunAkademik->tahun);
            })
            ->get();

        $totalSksKum = 0;
        $totalBobotKum = 0;
        
        foreach ($semuaKrs as $k) {
            if ($k->nilai && $k->nilai->huruf) {
                $sks = $k->jadwalKuliah->mataKuliah->sks ?? 0;
                $bobot = $k->nilai->bobot ?? 0;
                $totalSksKum += $sks;
                $totalBobotKum += ($sks * $bobot);
            }
        }
        
        $ipk = $totalSksKum > 0 ? round($totalBobotKum / $totalSksKum, 2) : 0;

        $stats = [
            'sks_semester' => $totalSks,
            'sks_kumulatif' => $totalSksKum,
            'ips' => $ips,
            'ipk' => $ipk,
        ];

        $pdf = Pdf::loadView('mahasiswa.khs.cetak', compact('mahasiswa', 'tahunAkademik', 'krsList', 'stats'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('khs-' . $mahasiswa->nim . '-' . $tahunAkademik->kode . '.pdf');
    }
}
