<?php

namespace App\Http\Controllers;

use App\Models\PeriodeDiskon;
use App\Models\JenisPotongan;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriodeDiskonController extends Controller
{
    public function index(Request $request)
    {
        $query = PeriodeDiskon::with('jenisPotongan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($request->filled('jenis_potongan_id')) {
            $query->where('jenis_potongan_id', $request->jenis_potongan_id);
        }

        if ($request->filled('status')) {
            $now = now()->startOfDay();
            switch ($request->status) {
                case 'aktif':
                    $query->where('is_active', true)
                        ->where('tanggal_mulai', '<=', $now)
                        ->where('tanggal_selesai', '>=', $now);
                    break;
                case 'akan_datang':
                    $query->where('is_active', true)
                        ->where('tanggal_mulai', '>', $now);
                    break;
                case 'berakhir':
                    $query->where('tanggal_selesai', '<', $now);
                    break;
                case 'nonaktif':
                    $query->where('is_active', false);
                    break;
            }
        }

        $periodeDiskon = $query->orderBy('tanggal_mulai', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => PeriodeDiskon::count(),
            'aktif' => PeriodeDiskon::valid()->count(),
            'akan_datang' => PeriodeDiskon::where('is_active', true)
                ->where('tanggal_mulai', '>', now())->count(),
            'berakhir' => PeriodeDiskon::where('tanggal_selesai', '<', now())->count(),
        ];

        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();

        return view('keuangan.potongan.periode.index', compact('periodeDiskon', 'stats', 'jenisPotonganList'));
    }

    public function create()
    {
        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();
        $programStudiList = ProgramStudi::orderBy('nama')->get();
        
        return view('keuangan.potongan.periode.create', compact('jenisPotonganList', 'programStudiList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'min_transaksi' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota' => 'nullable|integer|min:1',
            'program_studi_id' => 'nullable|array',
            'angkatan' => 'nullable|array',
            'jenis_tagihan' => 'nullable|array',
            'syarat_ketentuan' => 'nullable|string',
        ]);

        $berlakuUntuk = [];
        if ($request->filled('program_studi_id')) {
            $berlakuUntuk['program_studi_id'] = $request->program_studi_id;
        }
        if ($request->filled('angkatan')) {
            $berlakuUntuk['angkatan'] = $request->angkatan;
        }
        if ($request->filled('jenis_tagihan')) {
            $berlakuUntuk['jenis_tagihan'] = $request->jenis_tagihan;
        }

        PeriodeDiskon::create([
            'nama' => $validated['nama'],
            'jenis_potongan_id' => $validated['jenis_potongan_id'],
            'tipe_nilai' => $validated['tipe_nilai'],
            'nilai' => $validated['nilai'],
            'nilai_max' => $validated['nilai_max'] ?? null,
            'min_transaksi' => $validated['min_transaksi'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'kuota' => $validated['kuota'] ?? null,
            'berlaku_untuk' => !empty($berlakuUntuk) ? $berlakuUntuk : null,
            'syarat_ketentuan' => $validated['syarat_ketentuan'] ?? null,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('periode-diskon.index')
            ->with('success', 'Periode diskon berhasil ditambahkan.');
    }

    public function show(PeriodeDiskon $periodeDiskon)
    {
        $periodeDiskon->load(['jenisPotongan', 'riwayatPotongan' => function ($q) {
            $q->with(['mahasiswa', 'tagihan'])->latest()->limit(20);
        }]);

        return view('keuangan.potongan.periode.show', compact('periodeDiskon'));
    }

    public function edit(PeriodeDiskon $periodeDiskon)
    {
        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();
        $programStudiList = ProgramStudi::orderBy('nama')->get();
        
        return view('keuangan.potongan.periode.edit', compact('periodeDiskon', 'jenisPotonganList', 'programStudiList'));
    }

    public function update(Request $request, PeriodeDiskon $periodeDiskon)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'min_transaksi' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota' => 'nullable|integer|min:1',
            'program_studi_id' => 'nullable|array',
            'angkatan' => 'nullable|array',
            'jenis_tagihan' => 'nullable|array',
            'syarat_ketentuan' => 'nullable|string',
        ]);

        $berlakuUntuk = [];
        if ($request->filled('program_studi_id')) {
            $berlakuUntuk['program_studi_id'] = $request->program_studi_id;
        }
        if ($request->filled('angkatan')) {
            $berlakuUntuk['angkatan'] = $request->angkatan;
        }
        if ($request->filled('jenis_tagihan')) {
            $berlakuUntuk['jenis_tagihan'] = $request->jenis_tagihan;
        }

        $periodeDiskon->update([
            'nama' => $validated['nama'],
            'jenis_potongan_id' => $validated['jenis_potongan_id'],
            'tipe_nilai' => $validated['tipe_nilai'],
            'nilai' => $validated['nilai'],
            'nilai_max' => $validated['nilai_max'] ?? null,
            'min_transaksi' => $validated['min_transaksi'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $validated['tanggal_selesai'],
            'kuota' => $validated['kuota'] ?? null,
            'berlaku_untuk' => !empty($berlakuUntuk) ? $berlakuUntuk : null,
            'syarat_ketentuan' => $validated['syarat_ketentuan'] ?? null,
        ]);

        return redirect()->route('periode-diskon.index')
            ->with('success', 'Periode diskon berhasil diperbarui.');
    }

    public function destroy(PeriodeDiskon $periodeDiskon)
    {
        if ($periodeDiskon->riwayatPotongan()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus periode diskon yang sudah digunakan.');
        }

        $periodeDiskon->delete();

        return redirect()->route('periode-diskon.index')
            ->with('success', 'Periode diskon berhasil dihapus.');
    }

    public function toggleStatus(PeriodeDiskon $periodeDiskon)
    {
        $periodeDiskon->update([
            'is_active' => !$periodeDiskon->is_active
        ]);

        $status = $periodeDiskon->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Periode diskon berhasil {$status}.");
    }
}
