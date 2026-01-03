<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PresensiPegawai;
use App\Models\RekapPresensi;
use App\Models\SettingJamKerja;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiPegawaiController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        
        $query = PresensiPegawai::with(['dosen', 'pegawai'])
            ->whereDate('tanggal', $tanggal);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dosen', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                })->orWhereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
            });
        }

        $presensi = $query->orderBy('jam_masuk', 'asc')->paginate(20);

        // Statistics untuk hari ini
        $stats = [
            'total_pegawai' => Dosen::where('status', 'Aktif')->count() + Pegawai::where('status', 'Aktif')->count(),
            'hadir' => PresensiPegawai::whereDate('tanggal', $tanggal)->whereIn('status', ['hadir', 'terlambat'])->count(),
            'terlambat' => PresensiPegawai::whereDate('tanggal', $tanggal)->where('status', 'terlambat')->count(),
            'izin' => PresensiPegawai::whereDate('tanggal', $tanggal)->whereIn('status', ['izin', 'sakit', 'cuti'])->count(),
            'alpha' => PresensiPegawai::whereDate('tanggal', $tanggal)->where('status', 'alpha')->count(),
        ];

        $jamKerja = SettingJamKerja::getActive();

        return view('kepegawaian.presensi.index', compact('presensi', 'stats', 'tanggal', 'jamKerja'));
    }

    public function create()
    {
        $dosens = Dosen::where('status', 'Aktif')->orderBy('nama')->get();
        $pegawais = Pegawai::where('status', 'Aktif')->orderBy('nama')->get();
        $statusList = PresensiPegawai::STATUS;

        return view('kepegawaian.presensi.create', compact('dosens', 'pegawais', 'statusList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'tanggal' => 'required|date',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'status' => 'required|in:' . implode(',', array_keys(PresensiPegawai::STATUS)),
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except(['tipe_pegawai']);
        $data['is_manual'] = true;
        $data['diinput_oleh'] = auth()->id();

        // Check duplicate
        $exists = PresensiPegawai::where('tanggal', $request->tanggal);
        if ($request->tipe_pegawai == 'dosen') {
            $exists->where('dosen_id', $request->dosen_id);
        } else {
            $exists->where('pegawai_id', $request->pegawai_id);
        }

        if ($exists->exists()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Presensi untuk tanggal tersebut sudah ada');
        }

        // Check keterlambatan
        if ($request->filled('jam_masuk') && $request->status == 'hadir') {
            $jamKerja = SettingJamKerja::getActive();
            if ($jamKerja) {
                $jamMasuk = Carbon::parse($request->jam_masuk);
                $jamKerjaStart = Carbon::parse($jamKerja->jam_masuk);
                
                if ($jamMasuk->gt($jamKerjaStart->addMinutes($jamKerja->toleransi_terlambat))) {
                    $data['status'] = 'terlambat';
                }
            }
        }

        PresensiPegawai::create($data);

        return redirect()->route('kepegawaian.presensi.index')
            ->with('success', 'Presensi berhasil ditambahkan');
    }

    public function edit(PresensiPegawai $presensi)
    {
        $statusList = PresensiPegawai::STATUS;
        return view('kepegawaian.presensi.edit', compact('presensi', 'statusList'));
    }

    public function show(PresensiPegawai $presensi)
    {
        $presensi->load(['dosen', 'pegawai', 'diinputOleh']);
        $jamKerja = SettingJamKerja::getActive();
        return view('kepegawaian.presensi.show', compact('presensi', 'jamKerja'));
    }

    public function update(Request $request, PresensiPegawai $presensi)
    {
        $request->validate([
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
            'status' => 'required|in:' . implode(',', array_keys(PresensiPegawai::STATUS)),
            'keterangan' => 'nullable|string',
        ]);

        $presensi->update($request->only(['jam_masuk', 'jam_keluar', 'status', 'keterangan']));

        return redirect()->route('kepegawaian.presensi.index', ['tanggal' => $presensi->tanggal->format('Y-m-d')])
            ->with('success', 'Presensi berhasil diperbarui');
    }

    public function destroy(PresensiPegawai $presensi)
    {
        $tanggal = $presensi->tanggal->format('Y-m-d');
        $presensi->delete();

        return redirect()->route('kepegawaian.presensi.index', ['tanggal' => $tanggal])
            ->with('success', 'Presensi berhasil dihapus');
    }

    // Rekap Presensi Bulanan
    public function rekap(Request $request)
    {
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        $query = RekapPresensi::with(['dosen', 'pegawai'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        // Filter berdasarkan tipe
        if ($request->filled('tipe')) {
            if ($request->tipe == 'dosen') {
                $query->whereNotNull('dosen_id');
            } elseif ($request->tipe == 'pegawai') {
                $query->whereNotNull('pegawai_id');
            }
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('dosen', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nidn', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                })
                ->orWhereHas('pegawai', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%");
                });
            });
        }

        $rekapPresensi = $query->orderByDesc('persentase_kehadiran')->paginate(20)->withQueryString();

        // Summary
        $summary = [
            'total_pegawai' => $rekapPresensi->total(),
            'rata_kehadiran' => RekapPresensi::where('bulan', $bulan)->where('tahun', $tahun)->avg('persentase_kehadiran') ?? 0,
            'total_alpha' => RekapPresensi::where('bulan', $bulan)->where('tahun', $tahun)->sum('alpha'),
            'total_terlambat' => RekapPresensi::where('bulan', $bulan)->where('tahun', $tahun)->sum('terlambat'),
        ];

        // Nama bulan untuk dropdown
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('kepegawaian.presensi.rekap', compact('rekapPresensi', 'bulan', 'tahun', 'summary', 'namaBulan'));
    }

    public function generateRekap(Request $request)
    {
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        // Generate untuk semua dosen aktif
        $dosens = Dosen::where('status', 'Aktif')->get();
        foreach ($dosens as $dosen) {
            RekapPresensi::generateRekap($dosen->id, null, $bulan, $tahun);
        }

        // Generate untuk semua pegawai aktif
        $pegawais = Pegawai::where('status', 'Aktif')->get();
        foreach ($pegawais as $pegawai) {
            RekapPresensi::generateRekap(null, $pegawai->id, $bulan, $tahun);
        }

        return redirect()->route('kepegawaian.presensi.rekap', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Rekap presensi berhasil di-generate');
    }

    // Detail Rekap per Pegawai
    public function showRekap(RekapPresensi $rekap)
    {
        $rekap->load(['dosen', 'pegawai']);
        
        // Ambil data presensi harian untuk bulan tersebut
        $query = PresensiPegawai::whereMonth('tanggal', $rekap->bulan)
            ->whereYear('tanggal', $rekap->tahun);
        
        if ($rekap->dosen_id) {
            $query->where('dosen_id', $rekap->dosen_id);
        } else {
            $query->where('pegawai_id', $rekap->pegawai_id);
        }
        
        $presensiHarian = $query->orderBy('tanggal', 'asc')->get();
        
        // Nama bulan
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        
        $jamKerja = SettingJamKerja::getActive();
        
        return view('kepegawaian.presensi.rekap-detail', compact('rekap', 'presensiHarian', 'namaBulan', 'jamKerja'));
    }

    // Setting Jam Kerja
    public function settingJamKerja()
    {
        $settings = SettingJamKerja::orderBy('is_active', 'desc')->get();
        $setting = SettingJamKerja::getActive();
        return view('kepegawaian.presensi.setting', compact('settings', 'setting'));
    }

    public function storeSettingJamKerja(Request $request)
    {
        $request->validate([
            'nama_setting' => 'required|string',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'toleransi_terlambat' => 'required|integer|min:0',
        ]);

        // Set all others to inactive if this is active
        if ($request->is_active) {
            SettingJamKerja::query()->update(['is_active' => false]);
        }

        SettingJamKerja::create($request->all());

        return redirect()->route('kepegawaian.presensi.setting')
            ->with('success', 'Setting jam kerja berhasil ditambahkan');
    }

    public function updateSettingJamKerja(Request $request, SettingJamKerja $setting)
    {
        $request->validate([
            'nama_setting' => 'required|string',
            'jam_masuk' => 'required|date_format:H:i',
            'jam_keluar' => 'required|date_format:H:i',
            'toleransi_terlambat' => 'required|integer|min:0',
        ]);

        if ($request->is_active) {
            SettingJamKerja::where('id', '!=', $setting->id)->update(['is_active' => false]);
        }

        $setting->update($request->all());

        return redirect()->route('kepegawaian.presensi.setting')
            ->with('success', 'Setting jam kerja berhasil diperbarui');
    }

    // Laporan
    public function laporan(Request $request)
    {
        $bulan = $request->bulan ?? date('n');
        $tahun = $request->tahun ?? date('Y');

        $rekap = RekapPresensi::with(['dosen', 'pegawai'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        // Chart data
        $chartData = [
            'labels' => $rekap->map(fn($r) => $r->nama_pegawai)->take(10)->toArray(),
            'kehadiran' => $rekap->map(fn($r) => $r->persentase_kehadiran)->take(10)->toArray(),
        ];

        return view('kepegawaian.presensi.laporan', compact('rekap', 'bulan', 'tahun', 'chartData'));
    }
}
