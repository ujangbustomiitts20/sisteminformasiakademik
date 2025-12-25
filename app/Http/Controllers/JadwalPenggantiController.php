<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\JadwalPengganti;
use App\Models\Ruangan;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalPenggantiController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalPengganti::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'ruangan', 'pengaju']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_pengganti', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengganti', $request->tahun);
        }

        $jadwalPengganti = $query->orderBy('tanggal_pengganti', 'desc')->paginate(15);

        $stats = [
            'total' => JadwalPengganti::count(),
            'pending' => JadwalPengganti::pending()->count(),
            'disetujui' => JadwalPengganti::disetujui()->count(),
            'upcoming' => JadwalPengganti::disetujui()->upcoming()->count(),
        ];

        return view('akademik.jadwal-pengganti.index', compact('jadwalPengganti', 'stats'));
    }

    public function create()
    {
        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        $jadwalKuliah = JadwalKuliah::with(['mataKuliah', 'dosen'])
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->orderBy('hari')
            ->get();
        $ruangan = Ruangan::orderBy('nama')->get();
        $alasanList = JadwalPengganti::ALASAN_LIST;

        return view('akademik.jadwal-pengganti.create', compact('jadwalKuliah', 'ruangan', 'alasanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliah,id',
            'tanggal_asli' => 'required|date',
            'tanggal_pengganti' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan_id' => 'required|exists:ruangan,id',
            'alasan' => 'required|in:' . implode(',', array_keys(JadwalPengganti::ALASAN_LIST)),
            'keterangan' => 'nullable|string|max:500',
        ]);

        // Cek konflik ruangan
        $conflict = JadwalPengganti::where('ruangan_id', $validated['ruangan_id'])
            ->where('tanggal_pengganti', $validated['tanggal_pengganti'])
            ->where('status', 'Disetujui')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                    ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']]);
            })
            ->exists();

        if ($conflict) {
            return back()->with('error', 'Ruangan sudah digunakan pada waktu tersebut.')->withInput();
        }

        $validated['diajukan_oleh'] = Auth::id();
        $validated['status'] = 'Pending';

        JadwalPengganti::create($validated);

        return redirect()->route('jadwal-pengganti.index')
            ->with('success', 'Jadwal pengganti berhasil diajukan.');
    }

    public function show(JadwalPengganti $jadwalPengganti)
    {
        $jadwalPengganti->load(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.krs.mahasiswa', 'ruangan', 'pengaju', 'penyetuju']);

        return view('akademik.jadwal-pengganti.show', compact('jadwalPengganti'));
    }

    public function edit(JadwalPengganti $jadwalPengganti)
    {
        if ($jadwalPengganti->status !== 'Pending') {
            return back()->with('error', 'Jadwal pengganti yang sudah diproses tidak dapat diedit.');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        $jadwalKuliah = JadwalKuliah::with(['mataKuliah', 'dosen'])
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->orderBy('hari')
            ->get();
        $ruangan = Ruangan::orderBy('nama')->get();
        $alasanList = JadwalPengganti::ALASAN_LIST;

        return view('akademik.jadwal-pengganti.edit', compact('jadwalPengganti', 'jadwalKuliah', 'ruangan', 'alasanList'));
    }

    public function update(Request $request, JadwalPengganti $jadwalPengganti)
    {
        if ($jadwalPengganti->status !== 'Pending') {
            return back()->with('error', 'Jadwal pengganti yang sudah diproses tidak dapat diedit.');
        }

        $validated = $request->validate([
            'jadwal_kuliah_id' => 'required|exists:jadwal_kuliah,id',
            'tanggal_asli' => 'required|date',
            'tanggal_pengganti' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan_id' => 'required|exists:ruangan,id',
            'alasan' => 'required|in:' . implode(',', array_keys(JadwalPengganti::ALASAN_LIST)),
            'keterangan' => 'nullable|string|max:500',
        ]);

        $jadwalPengganti->update($validated);

        return redirect()->route('jadwal-pengganti.index')
            ->with('success', 'Jadwal pengganti berhasil diperbarui.');
    }

    public function destroy(JadwalPengganti $jadwalPengganti)
    {
        if ($jadwalPengganti->status !== 'Pending') {
            return back()->with('error', 'Jadwal pengganti yang sudah diproses tidak dapat dihapus.');
        }

        $jadwalPengganti->delete();

        return redirect()->route('jadwal-pengganti.index')
            ->with('success', 'Jadwal pengganti berhasil dihapus.');
    }

    public function approve(JadwalPengganti $jadwalPengganti)
    {
        if ($jadwalPengganti->status !== 'Pending') {
            return back()->with('error', 'Jadwal pengganti sudah diproses sebelumnya.');
        }

        $jadwalPengganti->update([
            'status' => 'Disetujui',
            'disetujui_oleh' => Auth::id(),
            'tanggal_persetujuan' => now(),
        ]);

        return back()->with('success', 'Jadwal pengganti berhasil disetujui.');
    }

    public function reject(Request $request, JadwalPengganti $jadwalPengganti)
    {
        if ($jadwalPengganti->status !== 'Pending') {
            return back()->with('error', 'Jadwal pengganti sudah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_tolak' => 'required|string|max:500',
        ]);

        $jadwalPengganti->update([
            'status' => 'Ditolak',
            'disetujui_oleh' => Auth::id(),
            'tanggal_persetujuan' => now(),
            'keterangan' => $jadwalPengganti->keterangan . "\n\nAlasan ditolak: " . $request->alasan_tolak,
        ]);

        return back()->with('success', 'Jadwal pengganti berhasil ditolak.');
    }

    public function complete(JadwalPengganti $jadwalPengganti)
    {
        if ($jadwalPengganti->status !== 'Disetujui') {
            return back()->with('error', 'Hanya jadwal yang disetujui yang dapat diselesaikan.');
        }

        $jadwalPengganti->update(['status' => 'Selesai']);

        return back()->with('success', 'Jadwal pengganti ditandai selesai.');
    }
}
