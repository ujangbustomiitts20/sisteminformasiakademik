<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Krs;
use App\Models\TahunAkademik;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TranskripController extends Controller
{
    /**
     * Tampilkan transkrip nilai mahasiswa
     */
    public function index()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Ambil semua KRS yang sudah ada nilainya melalui relasi
        $krsList = Krs::with(['nilai', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('nilai', function($q) {
                $q->whereNotNull('huruf');
            })
            ->orderBy('tahun_akademik_id')
            ->get();

        // Group by tahun akademik
        $nilaiPerSemester = $krsList->groupBy('tahun_akademik_id');

        // Hitung IPK
        $totalSks = 0;
        $totalBobot = 0;
        
        foreach ($krsList as $krs) {
            $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
            $bobot = $krs->nilai->bobot ?? 0;
            $totalSks += $sks;
            $totalBobot += ($sks * $bobot);
        }
        
        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        // Statistik
        $stats = [
            'total_sks_lulus' => $totalSks,
            'total_mk_lulus' => $krsList->count(),
            'ipk' => $ipk,
            'predikat' => $this->getPredikat($ipk),
        ];

        return view('mahasiswa.transkrip.index', compact('mahasiswa', 'nilaiPerSemester', 'stats'));
    }

    /**
     * Cetak transkrip PDF
     */
    public function cetak()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        
        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        $krsList = Krs::with(['nilai', 'jadwalKuliah.mataKuliah', 'tahunAkademik'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('nilai', function($q) {
                $q->whereNotNull('huruf');
            })
            ->orderBy('tahun_akademik_id')
            ->get();

        // Group by tahun akademik
        $nilaiPerSemester = $krsList->groupBy('tahun_akademik_id');

        // Hitung IPK
        $totalSks = 0;
        $totalBobot = 0;
        
        foreach ($krsList as $krs) {
            $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
            $bobot = $krs->nilai->bobot ?? 0;
            $totalSks += $sks;
            $totalBobot += ($sks * $bobot);
        }
        
        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        $stats = [
            'total_sks_lulus' => $totalSks,
            'total_mk_lulus' => $krsList->count(),
            'ipk' => $ipk,
            'predikat' => $this->getPredikat($ipk),
        ];

        $pdf = Pdf::loadView('mahasiswa.transkrip.cetak', compact('mahasiswa', 'nilaiPerSemester', 'stats'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('transkrip-' . $mahasiswa->nim . '.pdf');
    }

    /**
     * Konversi nilai huruf ke bobot
     */
    private function konversiNilaiKeBobot(string $nilai): float
    {
        return match(strtoupper($nilai)) {
            'A' => 4.0,
            'A-' => 3.75,
            'B+' => 3.5,
            'B' => 3.0,
            'B-' => 2.75,
            'C+' => 2.5,
            'C' => 2.0,
            'C-' => 1.75,
            'D+' => 1.5,
            'D' => 1.0,
            'E' => 0,
            default => 0,
        };
    }

    /**
     * Get predikat berdasarkan IPK
     */
    private function getPredikat(float $ipk): string
    {
        if ($ipk >= 3.51) return 'Cum Laude';
        if ($ipk >= 3.01) return 'Sangat Memuaskan';
        if ($ipk >= 2.76) return 'Memuaskan';
        if ($ipk >= 2.00) return 'Cukup';
        return 'Kurang';
    }
}
