<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SertifikasiDosen;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SertifikasiDosenController extends Controller
{
    public function index(Request $request)
    {
        $query = SertifikasiDosen::with('dosen');

        if ($request->filled('jenis_sertifikasi')) {
            $query->where('jenis_sertifikasi', $request->jenis_sertifikasi);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_sertifikasi', 'like', "%{$search}%")
                    ->orWhere('nomor_sertifikat', 'like', "%{$search}%")
                    ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $sertifikasiList = $query->orderBy('created_at', 'desc')->paginate(15);
        $dosenList = Dosen::orderBy('nama')->get();

        $stats = [
            'total' => SertifikasiDosen::count(),
            'aktif' => SertifikasiDosen::where('status', 'aktif')->count(),
            'serdos' => SertifikasiDosen::where('jenis_sertifikasi', 'serdos')->where('status', 'aktif')->count(),
            'akan_expired' => SertifikasiDosen::akanExpired(90)->count(),
        ];

        return view('kepegawaian.sertifikasi.index', compact('sertifikasiList', 'dosenList', 'stats'));
    }

    public function create()
    {
        $dosenList = Dosen::orderBy('nama')->get();
        return view('kepegawaian.sertifikasi.create', compact('dosenList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'jenis_sertifikasi' => 'required|in:serdos,kompetensi,profesi,keahlian,lainnya',
            'nama_sertifikasi' => 'required|string|max:200',
            'nomor_sertifikat' => 'nullable|string|max:100',
            'penerbit' => 'nullable|string|max:200',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_expired' => 'nullable|date|after_or_equal:tanggal_berlaku',
            'bidang_studi' => 'nullable|string|max:200',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except('file_sertifikat');
        $data['status'] = 'aktif';

        if ($request->hasFile('file_sertifikat')) {
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('sertifikasi-dosen', 'public');
        }

        SertifikasiDosen::create($data);

        return redirect()->route('kepegawaian.sertifikasi.index')
            ->with('success', 'Sertifikasi dosen berhasil ditambahkan.');
    }

    public function show(SertifikasiDosen $sertifikasi)
    {
        $sertifikasi->load('dosen');
        return view('kepegawaian.sertifikasi.show', compact('sertifikasi'));
    }

    public function edit(SertifikasiDosen $sertifikasi)
    {
        $dosenList = Dosen::orderBy('nama')->get();
        return view('kepegawaian.sertifikasi.edit', compact('sertifikasi', 'dosenList'));
    }

    public function update(Request $request, SertifikasiDosen $sertifikasi)
    {
        $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'jenis_sertifikasi' => 'required|in:serdos,kompetensi,profesi,keahlian,lainnya',
            'nama_sertifikasi' => 'required|string|max:200',
            'nomor_sertifikat' => 'nullable|string|max:100',
            'penerbit' => 'nullable|string|max:200',
            'tanggal_terbit' => 'nullable|date',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_expired' => 'nullable|date|after_or_equal:tanggal_berlaku',
            'bidang_studi' => 'nullable|string|max:200',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => 'required|in:aktif,expired,dicabut',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except('file_sertifikat');

        if ($request->hasFile('file_sertifikat')) {
            if ($sertifikasi->file_sertifikat) {
                Storage::disk('public')->delete($sertifikasi->file_sertifikat);
            }
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('sertifikasi-dosen', 'public');
        }

        $sertifikasi->update($data);

        return redirect()->route('kepegawaian.sertifikasi.index')
            ->with('success', 'Sertifikasi dosen berhasil diperbarui.');
    }

    public function destroy(SertifikasiDosen $sertifikasi)
    {
        if ($sertifikasi->file_sertifikat) {
            Storage::disk('public')->delete($sertifikasi->file_sertifikat);
        }
        
        $sertifikasi->delete();

        return redirect()->route('kepegawaian.sertifikasi.index')
            ->with('success', 'Sertifikasi dosen berhasil dihapus.');
    }
}
