<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\PresensiPegawai;
use App\Models\SettingJamKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DosenPresensiController extends Controller
{
    /**
     * Get current logged in dosen
     */
    private function getDosen()
    {
        return Auth::user()->dosen;
    }

    /**
     * Dashboard Presensi - menampilkan status presensi hari ini
     */
    public function index()
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan');
        }

        $today = Carbon::today();
        $jamKerja = SettingJamKerja::getActive();
        
        // Cek presensi hari ini
        $presensiHariIni = PresensiPegawai::where('dosen_id', $dosen->id)
            ->whereDate('tanggal', $today)
            ->first();

        // Statistik bulan ini
        $bulanIni = Carbon::now();
        $statistik = $this->getStatistikBulan($dosen->id, $bulanIni->month, $bulanIni->year);

        // Presensi 7 hari terakhir
        $presensiTerakhir = PresensiPegawai::where('dosen_id', $dosen->id)
            ->whereDate('tanggal', '>=', $today->copy()->subDays(7))
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('dosen.presensi.index', compact(
            'dosen',
            'jamKerja',
            'presensiHariIni',
            'statistik',
            'presensiTerakhir',
            'today'
        ));
    }

    /**
     * Proses Clock-in (Absen Masuk)
     */
    public function clockIn(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan'], 404);
        }

        $today = Carbon::today();
        
        // Cek apakah sudah absen masuk hari ini
        $existingPresensi = PresensiPegawai::where('dosen_id', $dosen->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingPresensi && $existingPresensi->jam_masuk) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda sudah melakukan absen masuk hari ini pada pukul ' . $existingPresensi->jam_masuk
            ], 400);
        }

        $jamKerja = SettingJamKerja::getActive();
        $jamMasuk = Carbon::now();
        
        // Tentukan status (hadir/terlambat)
        $status = 'hadir';
        if ($jamKerja) {
            $jamKerjaStart = Carbon::parse($jamKerja->jam_masuk);
            $toleransi = $jamKerja->toleransi_terlambat ?? 15;
            
            if ($jamMasuk->format('H:i:s') > $jamKerjaStart->addMinutes($toleransi)->format('H:i:s')) {
                $status = 'terlambat';
            }
        }

        // Validasi request
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'lokasi' => 'nullable|string|max:255',
            'foto' => 'nullable|string', // Base64 encoded image
        ]);

        // Handle foto selfie (base64)
        $fotoPath = null;
        if ($request->filled('foto')) {
            $fotoPath = $this->saveBase64Image($request->foto, 'presensi/masuk');
        }

        // Buat atau update presensi
        $data = [
            'dosen_id' => $dosen->id,
            'tanggal' => $today,
            'jam_masuk' => $jamMasuk->format('H:i:s'),
            'status' => $status,
            'latitude_masuk' => $request->latitude,
            'longitude_masuk' => $request->longitude,
            'lokasi_masuk' => $request->lokasi,
            'foto_masuk' => $fotoPath,
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'is_manual' => false,
        ];

        if ($existingPresensi) {
            $existingPresensi->update($data);
            $presensi = $existingPresensi;
        } else {
            $presensi = PresensiPegawai::create($data);
        }

        $message = $status === 'terlambat' 
            ? 'Absen masuk berhasil dicatat (Terlambat)' 
            : 'Absen masuk berhasil dicatat';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'jam_masuk' => $presensi->jam_masuk,
                    'status' => $presensi->status,
                    'status_label' => $presensi->status_label,
                ]
            ]);
        }

        return redirect()->route('dosen.presensi.index')->with('success', $message);
    }

    /**
     * Proses Clock-out (Absen Keluar)
     */
    public function clockOut(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan'], 404);
        }

        $today = Carbon::today();
        
        // Cek apakah sudah absen masuk hari ini
        $presensi = PresensiPegawai::where('dosen_id', $dosen->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$presensi || !$presensi->jam_masuk) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda belum melakukan absen masuk hari ini'
            ], 400);
        }

        if ($presensi->jam_keluar) {
            return response()->json([
                'success' => false, 
                'message' => 'Anda sudah melakukan absen keluar hari ini pada pukul ' . $presensi->jam_keluar
            ], 400);
        }

        // Validasi request
        $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'lokasi' => 'nullable|string|max:255',
            'foto' => 'nullable|string', // Base64 encoded image
        ]);

        // Handle foto selfie (base64)
        $fotoPath = null;
        if ($request->filled('foto')) {
            $fotoPath = $this->saveBase64Image($request->foto, 'presensi/keluar');
        }

        $jamKeluar = Carbon::now();

        $presensi->update([
            'jam_keluar' => $jamKeluar->format('H:i:s'),
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
            'lokasi_keluar' => $request->lokasi,
            'foto_keluar' => $fotoPath,
        ]);

        $message = 'Absen keluar berhasil dicatat';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'jam_keluar' => $presensi->jam_keluar,
                    'durasi_kerja' => $presensi->durasi_kerja,
                ]
            ]);
        }

        return redirect()->route('dosen.presensi.index')->with('success', $message);
    }

    /**
     * Riwayat Presensi
     */
    public function riwayat(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan');
        }

        $bulan = $request->bulan ?? Carbon::now()->month;
        $tahun = $request->tahun ?? Carbon::now()->year;

        $query = PresensiPegawai::where('dosen_id', $dosen->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $presensi = $query->orderBy('tanggal', 'desc')->paginate(31);

        // Statistik
        $statistik = $this->getStatistikBulan($dosen->id, $bulan, $tahun);

        // List bulan untuk filter
        $listBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // List tahun (5 tahun terakhir)
        $currentYear = Carbon::now()->year;
        $listTahun = range($currentYear - 4, $currentYear);

        $statusList = PresensiPegawai::STATUS;

        return view('dosen.presensi.riwayat', compact(
            'dosen',
            'presensi',
            'statistik',
            'bulan',
            'tahun',
            'listBulan',
            'listTahun',
            'statusList'
        ));
    }

    /**
     * Get statistik presensi per bulan
     */
    private function getStatistikBulan($dosenId, $bulan, $tahun)
    {
        $baseQuery = PresensiPegawai::where('dosen_id', $dosenId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        return [
            'total_hari_kerja' => $this->getJumlahHariKerja($bulan, $tahun),
            'hadir' => (clone $baseQuery)->where('status', 'hadir')->count(),
            'terlambat' => (clone $baseQuery)->where('status', 'terlambat')->count(),
            'sakit' => (clone $baseQuery)->where('status', 'sakit')->count(),
            'izin' => (clone $baseQuery)->where('status', 'izin')->count(),
            'cuti' => (clone $baseQuery)->where('status', 'cuti')->count(),
            'alpha' => (clone $baseQuery)->where('status', 'alpha')->count(),
            'dinas_luar' => (clone $baseQuery)->where('status', 'dinas_luar')->count(),
        ];
    }

    /**
     * Hitung jumlah hari kerja dalam sebulan (Senin-Jumat)
     */
    private function getJumlahHariKerja($bulan, $tahun)
    {
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        // Jika bulan ini, hitung sampai hari ini
        if ($startDate->month == Carbon::now()->month && $startDate->year == Carbon::now()->year) {
            $endDate = Carbon::now();
        }

        $hariKerja = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Senin = 1, Jumat = 5
            if ($currentDate->dayOfWeek >= 1 && $currentDate->dayOfWeek <= 5) {
                $hariKerja++;
            }
            $currentDate->addDay();
        }

        return $hariKerja;
    }

    /**
     * Save base64 image to storage
     */
    private function saveBase64Image($base64Image, $path)
    {
        try {
            // Remove data:image/xxx;base64, prefix if exists
            if (strpos($base64Image, 'base64,') !== false) {
                $base64Image = explode('base64,', $base64Image)[1];
            }

            $imageData = base64_decode($base64Image);
            
            if ($imageData === false) {
                return null;
            }

            $filename = $path . '/' . uniqid() . '_' . date('Ymd_His') . '.jpg';
            
            Storage::disk('public')->put($filename, $imageData);
            
            return $filename;
        } catch (\Exception $e) {
            \Log::error('Error saving presensi photo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get status presensi hari ini (untuk AJAX)
     */
    public function status()
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return response()->json(['success' => false, 'message' => 'Data dosen tidak ditemukan'], 404);
        }

        $today = Carbon::today();
        $jamKerja = SettingJamKerja::getActive();
        
        $presensi = PresensiPegawai::where('dosen_id', $dosen->id)
            ->whereDate('tanggal', $today)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'sudah_masuk' => $presensi && $presensi->jam_masuk ? true : false,
                'sudah_keluar' => $presensi && $presensi->jam_keluar ? true : false,
                'jam_masuk' => $presensi?->jam_masuk,
                'jam_keluar' => $presensi?->jam_keluar,
                'status' => $presensi?->status,
                'status_label' => $presensi?->status_label,
                'jam_kerja' => $jamKerja ? [
                    'masuk' => $jamKerja->jam_masuk,
                    'keluar' => $jamKerja->jam_keluar,
                    'toleransi' => $jamKerja->toleransi_terlambat,
                ] : null,
                'waktu_sekarang' => Carbon::now()->format('H:i:s'),
            ]
        ]);
    }
}
