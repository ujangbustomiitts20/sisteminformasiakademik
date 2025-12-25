<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Pembayaran;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    /**
     * Export Mahasiswa to CSV
     */
    public function mahasiswa(Request $request)
    {
        $query = Mahasiswa::with(['programStudi.fakultas', 'user']);
        
        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswa = $query->orderBy('nim')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_mahasiswa_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($mahasiswa) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['No', 'NIM', 'Nama', 'Email', 'Jenis Kelamin', 'Program Studi', 'Fakultas', 'Angkatan', 'Semester', 'Status', 'Telepon', 'Alamat']);
            
            // Data
            $no = 1;
            foreach ($mahasiswa as $mhs) {
                fputcsv($file, [
                    $no++,
                    $mhs->nim,
                    $mhs->nama,
                    $mhs->email,
                    $mhs->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $mhs->programStudi->nama ?? '-',
                    $mhs->programStudi->fakultas->nama ?? '-',
                    $mhs->angkatan,
                    $mhs->semester_aktif,
                    $mhs->status,
                    $mhs->telepon ?? '-',
                    $mhs->alamat ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Dosen to CSV
     */
    public function dosen(Request $request)
    {
        $dosen = Dosen::with(['programStudi.fakultas'])->orderBy('nidn')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_dosen_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($dosen) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['No', 'NIDN', 'Nama', 'Email', 'Jenis Kelamin', 'Program Studi', 'Jabatan Fungsional', 'Golongan', 'Status', 'Telepon']);
            
            $no = 1;
            foreach ($dosen as $d) {
                fputcsv($file, [
                    $no++,
                    $d->nidn,
                    $d->nama,
                    $d->email,
                    $d->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $d->programStudi->nama ?? '-',
                    $d->jabatan_fungsional ?? '-',
                    $d->golongan ?? '-',
                    $d->status,
                    $d->telepon ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Nilai to CSV
     */
    public function nilai(Request $request)
    {
        $tahunAkademik = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $nilai = Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'nilai'])
            ->whereHas('jadwalKuliah', function($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik?->id);
            })
            ->where('status', 'Disetujui')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_nilai_' . ($tahunAkademik->tahun ?? '') . '_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($nilai) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Kode MK', 'Mata Kuliah', 'SKS', 'Tugas', 'UTS', 'UAS', 'Nilai Akhir', 'Huruf', 'Bobot']);
            
            $no = 1;
            foreach ($nilai as $n) {
                fputcsv($file, [
                    $no++,
                    $n->mahasiswa->nim,
                    $n->mahasiswa->nama,
                    $n->jadwalKuliah->mataKuliah->kode,
                    $n->jadwalKuliah->mataKuliah->nama,
                    $n->jadwalKuliah->mataKuliah->sks,
                    $n->nilai->tugas ?? '-',
                    $n->nilai->uts ?? '-',
                    $n->nilai->uas ?? '-',
                    $n->nilai->nilai_akhir ?? '-',
                    $n->nilai->huruf ?? '-',
                    $n->nilai->bobot ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Pembayaran to CSV
     */
    public function pembayaran(Request $request)
    {
        $tahunAkademik = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $pembayaran = Pembayaran::with(['mahasiswa.programStudi'])
            ->when($tahunAkademik, function($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_pembayaran_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($pembayaran) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Program Studi', 'Jenis Pembayaran', 'Jumlah', 'Status', 'Tanggal Bayar', 'Metode Bayar']);
            
            $no = 1;
            foreach ($pembayaran as $p) {
                fputcsv($file, [
                    $no++,
                    $p->mahasiswa->nim,
                    $p->mahasiswa->nama,
                    $p->mahasiswa->programStudi->nama ?? '-',
                    $p->jenis,
                    $p->jumlah,
                    $p->status,
                    $p->tanggal_bayar ? $p->tanggal_bayar->format('Y-m-d') : '-',
                    $p->metode_bayar ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export Absensi to CSV
     */
    public function absensi(Request $request)
    {
        $tahunAkademik = $request->tahun_akademik_id 
            ? TahunAkademik::find($request->tahun_akademik_id)
            : TahunAkademik::getAktif();

        $absensi = Krs::with(['mahasiswa', 'jadwalKuliah.mataKuliah', 'absensi'])
            ->whereHas('jadwalKuliah', function($q) use ($tahunAkademik) {
                $q->where('tahun_akademik_id', $tahunAkademik?->id);
            })
            ->where('status', 'Disetujui')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="data_absensi_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($absensi) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Mata Kuliah', 'Total Pertemuan', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Persentase']);
            
            $no = 1;
            foreach ($absensi as $a) {
                $total = $a->absensi->count();
                $hadir = $a->absensi->where('status', 'Hadir')->count();
                $izin = $a->absensi->where('status', 'Izin')->count();
                $sakit = $a->absensi->where('status', 'Sakit')->count();
                $alpha = $a->absensi->where('status', 'Alpha')->count();
                $persentase = $total > 0 ? round(($hadir / $total) * 100, 1) : 0;
                
                fputcsv($file, [
                    $no++,
                    $a->mahasiswa->nim,
                    $a->mahasiswa->nama,
                    $a->jadwalKuliah->mataKuliah->nama,
                    $total,
                    $hadir,
                    $izin,
                    $sakit,
                    $alpha,
                    $persentase . '%',
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
