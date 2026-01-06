<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KontrakKerja;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KontrakKerjaController extends Controller
{
    public function index(Request $request)
    {
        $query = KontrakKerja::with(['dosen', 'pegawai', 'createdBy']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_kontrak')) {
            $query->where('jenis_kontrak', $request->jenis_kontrak);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kontrak', 'like', "%{$search}%")
                    ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $kontrakList = $query->orderBy('created_at', 'desc')->paginate(15);

        // Stats
        $stats = [
            'total' => KontrakKerja::count(),
            'aktif' => KontrakKerja::where('status', 'aktif')->count(),
            'akan_berakhir' => KontrakKerja::akanBerakhir(30)->count(),
            'berakhir' => KontrakKerja::where('status', 'berakhir')->count(),
        ];

        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();

        return view('kepegawaian.kontrak.index', compact('kontrakList', 'stats', 'dosenList', 'pegawaiList'));
    }

    public function create()
    {
        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();
        
        return view('kepegawaian.kontrak.create', compact('dosenList', 'pegawaiList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'jenis_kontrak' => 'required|in:tetap,kontrak,honorer,paruh_waktu',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_mulai',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'file_kontrak' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->only(['jenis_kontrak', 'tanggal_mulai', 'tanggal_berakhir', 'gaji_pokok', 'keterangan']);
        $data['status'] = 'draft';
        $data['created_by'] = auth()->id();

        if ($request->tipe_pegawai === 'dosen') {
            $data['dosen_id'] = $request->dosen_id;
        } else {
            $data['pegawai_id'] = $request->pegawai_id;
        }

        if ($request->hasFile('file_kontrak')) {
            $data['file_kontrak'] = $request->file('file_kontrak')->store('kontrak-kerja', 'public');
        }

        KontrakKerja::create($data);

        return redirect()->route('kepegawaian.kontrak.index')
            ->with('success', 'Kontrak kerja berhasil ditambahkan.');
    }

    public function show(KontrakKerja $kontrak)
    {
        $kontrak->load(['dosen', 'pegawai', 'createdBy']);
        return view('kepegawaian.kontrak.show', compact('kontrak'));
    }

    public function edit(KontrakKerja $kontrak)
    {
        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();
        
        return view('kepegawaian.kontrak.edit', compact('kontrak', 'dosenList', 'pegawaiList'));
    }

    public function update(Request $request, KontrakKerja $kontrak)
    {
        $request->validate([
            'jenis_kontrak' => 'required|in:tetap,kontrak,honorer,paruh_waktu',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_mulai',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:draft,aktif,berakhir,diperpanjang,dibatalkan',
            'file_kontrak' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->only(['jenis_kontrak', 'tanggal_mulai', 'tanggal_berakhir', 'gaji_pokok', 'keterangan', 'status']);

        if ($request->hasFile('file_kontrak')) {
            if ($kontrak->file_kontrak) {
                Storage::disk('public')->delete($kontrak->file_kontrak);
            }
            $data['file_kontrak'] = $request->file('file_kontrak')->store('kontrak-kerja', 'public');
        }

        $kontrak->update($data);

        return redirect()->route('kepegawaian.kontrak.index')
            ->with('success', 'Kontrak kerja berhasil diperbarui.');
    }

    public function destroy(KontrakKerja $kontrak)
    {
        if ($kontrak->file_kontrak) {
            Storage::disk('public')->delete($kontrak->file_kontrak);
        }
        
        $kontrak->delete();

        return redirect()->route('kepegawaian.kontrak.index')
            ->with('success', 'Kontrak kerja berhasil dihapus.');
    }

    public function aktivasi(KontrakKerja $kontrak)
    {
        $kontrak->update(['status' => 'aktif']);
        return redirect()->back()->with('success', 'Kontrak kerja berhasil diaktifkan.');
    }

    public function perpanjang(Request $request, KontrakKerja $kontrak)
    {
        $request->validate([
            'tanggal_berakhir_baru' => 'required|date|after:' . $kontrak->tanggal_berakhir,
        ]);

        // Set kontrak lama sebagai diperpanjang
        $kontrak->update(['status' => 'diperpanjang']);

        // Buat kontrak baru
        $kontrakBaru = $kontrak->replicate();
        $kontrakBaru->tanggal_mulai = $kontrak->tanggal_berakhir->addDay();
        $kontrakBaru->tanggal_berakhir = $request->tanggal_berakhir_baru;
        $kontrakBaru->status = 'aktif';
        $kontrakBaru->nomor_kontrak = null; // Will be auto-generated
        $kontrakBaru->file_kontrak = null;
        $kontrakBaru->save();

        return redirect()->route('kepegawaian.kontrak.show', $kontrakBaru)
            ->with('success', 'Kontrak berhasil diperpanjang.');
    }
}
