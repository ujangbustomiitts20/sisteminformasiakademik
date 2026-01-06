<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IzinKeluar;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class IzinKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = IzinKeluar::with(['dosen', 'pegawai', 'disetujuiOleh']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('keperluan')) {
            $query->where('keperluan', $request->keperluan);
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                    ->orWhere('tujuan', 'like', "%{$search}%")
                    ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $izinList = $query->orderBy('created_at', 'desc')->paginate(15);
        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();

        $stats = [
            'total' => IzinKeluar::count(),
            'pending' => IzinKeluar::whereIn('status', ['diajukan', 'menunggu_admin'])->count(),
            'menunggu_kaprodi' => IzinKeluar::where('status', 'diajukan')->where('status_kaprodi', 'pending')->count(),
            'menunggu_admin' => IzinKeluar::where('status', 'menunggu_admin')->count(),
            'hari_ini' => IzinKeluar::whereDate('tanggal', today())->count(),
            'disetujui_bulan_ini' => IzinKeluar::where('status', 'disetujui')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year)
                ->count(),
        ];

        return view('kepegawaian.izin-keluar.index', compact('izinList', 'dosenList', 'pegawaiList', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'tanggal' => 'required|date',
            'jam_keluar' => 'required|date_format:H:i',
            'jam_kembali' => 'required|date_format:H:i|after:jam_keluar',
            'keperluan' => 'required|in:dinas,pribadi,kesehatan,keluarga,lainnya',
            'keterangan' => 'required|string',
            'tujuan' => 'nullable|string|max:200',
        ]);

        $data = $request->only(['tanggal', 'jam_keluar', 'jam_kembali', 'keperluan', 'keterangan', 'tujuan']);
        $data['status'] = 'diajukan';
        $data['status_kaprodi'] = 'pending'; // Set default status kaprodi
        $data['approval_level'] = 'kaprodi'; // Default approval level

        if ($request->tipe_pegawai === 'dosen') {
            $data['dosen_id'] = $request->dosen_id;
        } else {
            $data['pegawai_id'] = $request->pegawai_id;
            // Untuk pegawai non-dosen, langsung ke admin
            $data['status_kaprodi'] = null;
            $data['approval_level'] = 'admin';
        }

        IzinKeluar::create($data);

        return redirect()->route('kepegawaian.izin-keluar.index')
            ->with('success', 'Izin keluar berhasil diajukan.');
    }

    public function show(IzinKeluar $izinKeluar)
    {
        $izinKeluar->load(['dosen', 'pegawai', 'disetujuiOleh']);
        return view('kepegawaian.izin-keluar.show', compact('izinKeluar'));
    }

    public function update(Request $request, IzinKeluar $izinKeluar)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_keluar' => 'required|date_format:H:i',
            'jam_kembali' => 'required|date_format:H:i|after:jam_keluar',
            'keperluan' => 'required|in:dinas,pribadi,kesehatan,keluarga,lainnya',
            'keterangan' => 'required|string',
            'tujuan' => 'nullable|string|max:200',
        ]);

        $izinKeluar->update($request->only(['tanggal', 'jam_keluar', 'jam_kembali', 'keperluan', 'keterangan', 'tujuan']));

        return redirect()->route('kepegawaian.izin-keluar.index')
            ->with('success', 'Izin keluar berhasil diperbarui.');
    }

    public function destroy(IzinKeluar $izinKeluar)
    {
        $izinKeluar->delete();
        return redirect()->route('kepegawaian.izin-keluar.index')
            ->with('success', 'Izin keluar berhasil dihapus.');
    }

    public function approve(Request $request, IzinKeluar $izinKeluar)
    {
        // Admin bisa approve yang sudah disetujui kaprodi atau pengajuan langsung (pegawai non-dosen)
        if (!in_array($izinKeluar->status, ['diajukan', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Izin tidak dalam status yang dapat disetujui.');
        }

        // Jika dosen dan belum disetujui kaprodi, admin tetap bisa approve langsung
        $izinKeluar->update([
            'status' => 'disetujui',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
            'catatan_approval' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Izin keluar berhasil disetujui.');
    }

    public function reject(Request $request, IzinKeluar $izinKeluar)
    {
        if (!in_array($izinKeluar->status, ['diajukan', 'menunggu_admin'])) {
            return redirect()->back()->with('error', 'Izin tidak dalam status yang dapat ditolak.');
        }

        $request->validate([
            'catatan' => 'required|string',
        ]);

        $izinKeluar->update([
            'status' => 'ditolak',
            'disetujui_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
            'catatan_approval' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Izin keluar berhasil ditolak.');
    }

    public function selesai(Request $request, IzinKeluar $izinKeluar)
    {
        if ($izinKeluar->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Izin tidak dalam status disetujui.');
        }

        $izinKeluar->update([
            'status' => 'selesai',
            'jam_kembali_aktual' => $request->jam_kembali_aktual ?? now()->format('H:i'),
        ]);

        return redirect()->back()->with('success', 'Izin keluar telah selesai.');
    }
}
