<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DosenRekapController extends Controller
{
    /**
     * Rekap Absensi per Mata Kuliah
     */
    public function rekapAbsensi(Request $request)
    {
        $dosen = Auth::user()->dosen;
        $tahunAkademik = TahunAkademik::getAktif();
        
        // Handle case when tahunAkademik is null
        if (!$tahunAkademik) {
            return view('dosen.rekap-absensi', [
                'jadwalMengajar' => collect(),
                'selectedJadwal' => null,
                'rekapAbsensi' => [],
                'tahunAkademik' => null
            ]);
        }
        
        // Get jadwal mengajar dosen
        $jadwalMengajar = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->with(['mataKuliah', 'ruangan'])
            ->get();
        
        $selectedJadwal = null;
        $rekapAbsensi = [];
        
        if ($request->jadwal_id) {
            $selectedJadwal = JadwalKuliah::with(['mataKuliah', 'ruangan'])->find($request->jadwal_id);
            
            if ($selectedJadwal && $selectedJadwal->dosen_id == $dosen->id) {
                // Get all mahasiswa in this class via KRS
                $krsItems = Krs::where('jadwal_kuliah_id', $selectedJadwal->id)
                    ->where('status', 'Disetujui')
                    ->with('mahasiswa')
                    ->get();
                
                foreach ($krsItems as $krs) {
                    $mahasiswa = $krs->mahasiswa;
                    
                    // Get absensi records via krs_id
                    $absensiRecords = Absensi::where('krs_id', $krs->id)->get();
                    
                    $hadir = $absensiRecords->where('status', 'Hadir')->count();
                    $sakit = $absensiRecords->where('status', 'Sakit')->count();
                    $izin = $absensiRecords->where('status', 'Izin')->count();
                    $alpha = $absensiRecords->where('status', 'Alpha')->count();
                    $total = $absensiRecords->count();
                    
                    $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
                    
                    $rekapAbsensi[] = [
                        'mahasiswa' => $mahasiswa,
                        'hadir' => $hadir,
                        'sakit' => $sakit,
                        'izin' => $izin,
                        'alpha' => $alpha,
                        'total' => $total,
                        'persentase' => $persentase,
                    ];
                }
            }
        }
        
        return view('dosen.rekap-absensi', compact('jadwalMengajar', 'selectedJadwal', 'rekapAbsensi', 'tahunAkademik'));
    }
    
    /**
     * Rekap Nilai per Mata Kuliah
     */
    public function rekapNilai(Request $request)
    {
        $dosen = Auth::user()->dosen;
        $tahunAkademik = TahunAkademik::getAktif();
        
        // Handle case when tahunAkademik is null
        if (!$tahunAkademik) {
            return view('dosen.rekap-nilai', [
                'jadwalMengajar' => collect(),
                'selectedJadwal' => null,
                'rekapNilai' => [],
                'statistik' => null,
                'tahunAkademik' => null
            ]);
        }
        
        // Get jadwal mengajar dosen
        $jadwalMengajar = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->with(['mataKuliah', 'ruangan'])
            ->get();
        
        $selectedJadwal = null;
        $rekapNilai = [];
        $statistik = null;
        
        if ($request->jadwal_id) {
            $selectedJadwal = JadwalKuliah::with(['mataKuliah', 'ruangan'])->find($request->jadwal_id);
            
            if ($selectedJadwal && $selectedJadwal->dosen_id == $dosen->id) {
                // Get KRS items for this jadwal (contains nilai)
                $krsItems = Krs::where('jadwal_kuliah_id', $selectedJadwal->id)
                    ->where('status', 'Disetujui')
                    ->with(['mahasiswa', 'nilai'])
                    ->get();
                
                foreach ($krsItems as $krs) {
                    $nilai = $krs->nilai;
                    $rekapNilai[] = [
                        'mahasiswa' => $krs->mahasiswa,
                        'tugas' => $nilai->tugas ?? null,
                        'uts' => $nilai->uts ?? null,
                        'uas' => $nilai->uas ?? null,
                        'akhir' => $nilai->nilai_akhir ?? null,
                        'huruf' => $nilai->huruf ?? null,
                    ];
                }
                
                // Statistik
                $nilaiAkhirList = collect($rekapNilai)->whereNotNull('akhir')->pluck('akhir');
                $statistik = [
                    'total' => count($rekapNilai),
                    'sudah_input' => collect($rekapNilai)->whereNotNull('akhir')->count(),
                    'belum_input' => collect($rekapNilai)->whereNull('akhir')->count(),
                    'rata_rata' => $nilaiAkhirList->count() > 0 ? round($nilaiAkhirList->avg(), 2) : 0,
                    'tertinggi' => $nilaiAkhirList->max() ?? 0,
                    'terendah' => $nilaiAkhirList->min() ?? 0,
                    'distribusi' => [
                        'A' => collect($rekapNilai)->whereIn('huruf', ['A', 'A-'])->count(),
                        'B' => collect($rekapNilai)->whereIn('huruf', ['B+', 'B', 'B-'])->count(),
                        'C' => collect($rekapNilai)->whereIn('huruf', ['C+', 'C', 'C-'])->count(),
                        'D' => collect($rekapNilai)->where('huruf', 'D')->count(),
                        'E' => collect($rekapNilai)->where('huruf', 'E')->count(),
                    ],
                ];
            }
        }
        
        return view('dosen.rekap-nilai', compact('jadwalMengajar', 'selectedJadwal', 'rekapNilai', 'statistik', 'tahunAkademik'));
    }
    
    /**
     * Halaman Mahasiswa Wali (Preview sebelum export)
     */
    public function mahasiswaWali()
    {
        $dosen = Auth::user()->dosen;
        
        $mahasiswaWali = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->with(['programStudi', 'user'])
            ->orderBy('nim')
            ->get();
        
        // Statistik
        $statistik = [
            'total' => $mahasiswaWali->count(),
            'aktif' => $mahasiswaWali->where('status', 'Aktif')->count(),
            'cuti' => $mahasiswaWali->where('status', 'Cuti')->count(),
            'non_aktif' => $mahasiswaWali->whereNotIn('status', ['Aktif', 'Cuti'])->count(),
        ];
        
        return view('dosen.mahasiswa-wali', compact('mahasiswaWali', 'statistik', 'dosen'));
    }
    
    /**
     * Export Mahasiswa Wali ke CSV
     */
    public function exportMahasiswaWaliCsv()
    {
        $dosen = Auth::user()->dosen;
        
        $mahasiswaWali = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->with(['programStudi', 'user'])
            ->orderBy('nim')
            ->get();
        
        // Generate CSV
        $filename = 'mahasiswa_wali_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($mahasiswaWali) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['No', 'NIM', 'Nama', 'Program Studi', 'Angkatan', 'Semester', 'Status', 'Email', 'No HP']);
            
            // Data
            $no = 1;
            foreach ($mahasiswaWali as $mhs) {
                fputcsv($file, [
                    $no++,
                    $mhs->nim,
                    $mhs->nama,
                    $mhs->programStudi->nama ?? '-',
                    $mhs->angkatan,
                    $mhs->semester_aktif,
                    $mhs->status,
                    $mhs->email,
                    $mhs->no_hp ?? '-',
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
