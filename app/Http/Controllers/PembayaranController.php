<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $pembayaran = Pembayaran::where('mahasiswa_id', $mahasiswa->id)
                ->with('tahunAkademik')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return view('pembayaran.mahasiswa', compact('pembayaran', 'mahasiswa'));
        }

        // Admin view
        $query = Pembayaran::with(['mahasiswa', 'tahunAkademik']);

        if ($request->search) {
            $query->whereHas('mahasiswa', function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        $pembayaran = $query->orderBy('created_at', 'desc')->paginate(15);
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('pembayaran.index', compact('pembayaran', 'tahunAkademik'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::where('status', 'Aktif')->orderBy('nim')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        
        return view('pembayaran.create', compact('mahasiswa', 'tahunAkademik'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'jenis' => 'required|in:SPP,Herregistrasi,Wisuda,Lainnya',
            'jumlah' => 'required|numeric|min:0',
        ]);

        Pembayaran::create($request->all());

        return redirect()->route('pembayaran.index')->with('success', 'Tagihan pembayaran berhasil ditambahkan!');
    }

    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['mahasiswa.programStudi', 'tahunAkademik']);
        return view('pembayaran.show', compact('pembayaran'));
    }

    public function edit(Pembayaran $pembayaran)
    {
        $mahasiswa = Mahasiswa::orderBy('nim')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        
        return view('pembayaran.edit', compact('pembayaran', 'mahasiswa', 'tahunAkademik'));
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'status' => 'required|in:Belum Lunas,Lunas',
            'tanggal_bayar' => 'nullable|date',
            'metode_bayar' => 'nullable|string',
        ]);

        $pembayaran->update($request->all());

        return redirect()->route('pembayaran.index')->with('success', 'Data pembayaran berhasil diperbarui!');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        $pembayaran->delete();
        return redirect()->route('pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus!');
    }

    // Konfirmasi pembayaran (untuk admin)
    public function konfirmasi(Pembayaran $pembayaran)
    {
        $pembayaran->update([
            'status' => 'Lunas',
            'tanggal_bayar' => now(),
        ]);

        return back()->with('success', 'Pembayaran berhasil dikonfirmasi!');
    }
}
