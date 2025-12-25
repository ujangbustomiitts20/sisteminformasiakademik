<?php

namespace App\Http\Controllers;

use App\Models\PotonganMahasiswa;
use App\Models\JenisPotongan;
use App\Models\PeriodeDiskon;
use App\Models\Mahasiswa;
use App\Models\Tagihan;
use App\Models\RiwayatPotongan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PotonganMahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = PotonganMahasiswa::with(['mahasiswa.programStudi', 'jenisPotongan', 'disetujuiOleh']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa', function ($q2) use ($search) {
                        $q2->where('nim', 'like', "%{$search}%")
                            ->orWhere('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('jenis_potongan_id')) {
            $query->where('jenis_potongan_id', $request->jenis_potongan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $potonganMahasiswa = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => PotonganMahasiswa::count(),
            'pending' => PotonganMahasiswa::pending()->count(),
            'disetujui' => PotonganMahasiswa::disetujui()->count(),
            'ditolak' => PotonganMahasiswa::where('status', 'ditolak')->count(),
        ];

        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();

        return view('keuangan.potongan.mahasiswa.index', compact('potonganMahasiswa', 'stats', 'jenisPotonganList'));
    }

    public function create()
    {
        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();
        $periodeDiskonList = PeriodeDiskon::valid()->orderBy('nama')->get();
        
        return view('keuangan.potongan.mahasiswa.create', compact('jenisPotonganList', 'periodeDiskonList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'periode_diskon_id' => 'nullable|exists:periode_diskon,id',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'alasan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'tagihan_ids' => 'nullable|array',
            'auto_approve' => 'boolean',
        ]);

        $berlakuUntuk = null;
        if ($request->filled('tagihan_ids')) {
            $berlakuUntuk = ['tagihan_ids' => $request->tagihan_ids];
        }

        $potongan = PotonganMahasiswa::create([
            'mahasiswa_id' => $validated['mahasiswa_id'],
            'jenis_potongan_id' => $validated['jenis_potongan_id'],
            'periode_diskon_id' => $validated['periode_diskon_id'] ?? null,
            'tipe_nilai' => $validated['tipe_nilai'],
            'nilai' => $validated['nilai'],
            'nilai_max' => $validated['nilai_max'] ?? null,
            'alasan' => $validated['alasan'] ?? null,
            'catatan' => $validated['catatan'] ?? null,
            'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'berlaku_untuk_tagihan' => $berlakuUntuk,
            'status' => $request->boolean('auto_approve') ? 'disetujui' : 'pending',
            'disetujui_oleh' => $request->boolean('auto_approve') ? Auth::id() : null,
            'disetujui_at' => $request->boolean('auto_approve') ? now() : null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('potongan-mahasiswa.index')
            ->with('success', 'Potongan mahasiswa berhasil ditambahkan.');
    }

    public function show(PotonganMahasiswa $potonganMahasiswa)
    {
        $potonganMahasiswa->load([
            'mahasiswa.programStudi',
            'jenisPotongan',
            'periodeDiskon',
            'disetujuiOleh',
            'createdBy',
            'riwayatPotongan' => function ($q) {
                $q->with('tagihan')->latest();
            }
        ]);

        return view('keuangan.potongan.mahasiswa.show', compact('potonganMahasiswa'));
    }

    public function edit(PotonganMahasiswa $potonganMahasiswa)
    {
        if (!in_array($potonganMahasiswa->status, ['pending', 'disetujui'])) {
            return back()->with('error', 'Potongan tidak dapat diedit.');
        }

        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();
        $periodeDiskonList = PeriodeDiskon::valid()->orderBy('nama')->get();
        
        return view('keuangan.potongan.mahasiswa.edit', compact('potonganMahasiswa', 'jenisPotonganList', 'periodeDiskonList'));
    }

    public function update(Request $request, PotonganMahasiswa $potonganMahasiswa)
    {
        if (!in_array($potonganMahasiswa->status, ['pending', 'disetujui'])) {
            return back()->with('error', 'Potongan tidak dapat diedit.');
        }

        $validated = $request->validate([
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'periode_diskon_id' => 'nullable|exists:periode_diskon,id',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'alasan' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $potonganMahasiswa->update($validated);

        return redirect()->route('potongan-mahasiswa.index')
            ->with('success', 'Potongan mahasiswa berhasil diperbarui.');
    }

    public function destroy(PotonganMahasiswa $potonganMahasiswa)
    {
        if ($potonganMahasiswa->riwayatPotongan()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus potongan yang sudah digunakan.');
        }

        $potonganMahasiswa->delete();

        return redirect()->route('potongan-mahasiswa.index')
            ->with('success', 'Potongan mahasiswa berhasil dihapus.');
    }

    public function approve(Request $request, PotonganMahasiswa $potonganMahasiswa)
    {
        if ($potonganMahasiswa->status !== 'pending') {
            return back()->with('error', 'Potongan tidak dalam status pending.');
        }

        $potonganMahasiswa->approve(Auth::id(), $request->catatan);

        return back()->with('success', 'Potongan berhasil disetujui.');
    }

    public function reject(Request $request, PotonganMahasiswa $potonganMahasiswa)
    {
        if ($potonganMahasiswa->status !== 'pending') {
            return back()->with('error', 'Potongan tidak dalam status pending.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        $potonganMahasiswa->reject(Auth::id(), $request->alasan_penolakan);

        return back()->with('success', 'Potongan berhasil ditolak.');
    }

    public function cancel(PotonganMahasiswa $potonganMahasiswa)
    {
        if (!in_array($potonganMahasiswa->status, ['pending', 'disetujui'])) {
            return back()->with('error', 'Potongan tidak dapat dibatalkan.');
        }

        $potonganMahasiswa->cancel();

        return back()->with('success', 'Potongan berhasil dibatalkan.');
    }

    public function searchMahasiswa(Request $request)
    {
        $search = $request->get('q', '');
        
        $mahasiswa = Mahasiswa::with('programStudi')
            ->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'text' => $m->nim . ' - ' . $m->nama . ' (' . ($m->programStudi->nama ?? '-') . ')',
                    'nim' => $m->nim,
                    'nama' => $m->nama,
                    'program_studi' => $m->programStudi->nama ?? '-',
                ];
            });

        return response()->json(['results' => $mahasiswa]);
    }

    public function searchTagihan(Request $request)
    {
        $mahasiswaId = $request->get('mahasiswa_id');
        
        if (!$mahasiswaId) {
            return response()->json(['results' => []]);
        }

        $tagihan = Tagihan::where('mahasiswa_id', $mahasiswaId)
            ->whereIn('status', ['Belum Bayar', 'Cicilan'])
            ->orderBy('tanggal_jatuh_tempo')
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'text' => $t->no_tagihan . ' - ' . $t->jenis_tagihan . ' (Rp ' . number_format($t->sisa_tagihan, 0, ',', '.') . ')',
                    'nominal' => $t->nominal,
                    'sisa' => $t->sisa_tagihan,
                ];
            });

        return response()->json(['results' => $tagihan]);
    }

    public function applyToTagihan(Request $request)
    {
        $request->validate([
            'potongan_mahasiswa_id' => 'required|exists:potongan_mahasiswa,id',
            'tagihan_id' => 'required|exists:tagihan,id',
        ]);

        $potongan = PotonganMahasiswa::with('jenisPotongan')->findOrFail($request->potongan_mahasiswa_id);
        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        if ($potongan->mahasiswa_id !== $tagihan->mahasiswa_id) {
            return back()->with('error', 'Potongan tidak berlaku untuk mahasiswa ini.');
        }

        if (!$potongan->is_valid) {
            return back()->with('error', 'Potongan tidak valid atau sudah kadaluarsa.');
        }

        // Check if already applied
        $existingRiwayat = RiwayatPotongan::where('potongan_mahasiswa_id', $potongan->id)
            ->where('tagihan_id', $tagihan->id)
            ->exists();

        if ($existingRiwayat) {
            return back()->with('error', 'Potongan sudah diaplikasikan ke tagihan ini.');
        }

        DB::transaction(function () use ($potongan, $tagihan) {
            $nominalPotongan = $potongan->hitungPotongan($tagihan->nominal);

            // Create riwayat
            RiwayatPotongan::create([
                'potongan_mahasiswa_id' => $potongan->id,
                'periode_diskon_id' => $potongan->periode_diskon_id,
                'tagihan_id' => $tagihan->id,
                'mahasiswa_id' => $tagihan->mahasiswa_id,
                'kode_potongan' => $potongan->kode,
                'nama_potongan' => $potongan->jenisPotongan->nama,
                'tipe_nilai' => $potongan->tipe_nilai,
                'nilai' => $potongan->nilai,
                'nominal_tagihan' => $tagihan->nominal,
                'nominal_potongan' => $nominalPotongan,
                'applied_by' => Auth::id(),
            ]);

            // Update tagihan diskon
            $tagihan->update([
                'diskon' => $tagihan->diskon + $nominalPotongan,
            ]);
        });

        return back()->with('success', 'Potongan berhasil diaplikasikan ke tagihan.');
    }

    public function bulkCreate()
    {
        $jenisPotonganList = JenisPotongan::active()->orderBy('nama')->get();
        $periodeDiskonList = PeriodeDiskon::valid()->orderBy('nama')->get();
        
        return view('keuangan.potongan.mahasiswa.bulk-create', compact('jenisPotonganList', 'periodeDiskonList'));
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'mahasiswa_ids' => 'required|array|min:1',
            'mahasiswa_ids.*' => 'exists:mahasiswa,id',
            'jenis_potongan_id' => 'required|exists:jenis_potongan,id',
            'tipe_nilai' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0',
            'nilai_max' => 'nullable|numeric|min:0',
            'alasan' => 'nullable|string|max:255',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'auto_approve' => 'boolean',
        ]);

        $count = 0;
        DB::transaction(function () use ($validated, $request, &$count) {
            foreach ($validated['mahasiswa_ids'] as $mahasiswaId) {
                PotonganMahasiswa::create([
                    'mahasiswa_id' => $mahasiswaId,
                    'jenis_potongan_id' => $validated['jenis_potongan_id'],
                    'tipe_nilai' => $validated['tipe_nilai'],
                    'nilai' => $validated['nilai'],
                    'nilai_max' => $validated['nilai_max'] ?? null,
                    'alasan' => $validated['alasan'] ?? null,
                    'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
                    'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
                    'status' => $request->boolean('auto_approve') ? 'disetujui' : 'pending',
                    'disetujui_oleh' => $request->boolean('auto_approve') ? Auth::id() : null,
                    'disetujui_at' => $request->boolean('auto_approve') ? now() : null,
                    'created_by' => Auth::id(),
                ]);
                $count++;
            }
        });

        return redirect()->route('potongan-mahasiswa.index')
            ->with('success', "{$count} potongan mahasiswa berhasil ditambahkan.");
    }

    public function export(Request $request)
    {
        $query = PotonganMahasiswa::with(['mahasiswa.programStudi', 'jenisPotongan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_potongan_id')) {
            $query->where('jenis_potongan_id', $request->jenis_potongan_id);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        $filename = 'potongan_mahasiswa_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Kode',
                'NIM',
                'Nama Mahasiswa',
                'Program Studi',
                'Jenis Potongan',
                'Tipe Nilai',
                'Nilai',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Status',
                'Alasan',
                'Dibuat',
            ]);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->kode,
                    $row->mahasiswa->nim ?? '',
                    $row->mahasiswa->nama ?? '',
                    $row->mahasiswa->programStudi->nama ?? '',
                    $row->jenisPotongan->nama ?? '',
                    $row->tipe_nilai_label,
                    $row->nilai,
                    $row->tanggal_mulai?->format('Y-m-d'),
                    $row->tanggal_selesai?->format('Y-m-d'),
                    $row->status_label,
                    $row->alasan,
                    $row->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
