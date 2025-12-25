<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Tarif;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use App\Models\PenerimaBeasiswa;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Mahasiswa view
        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $tagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)
                ->with('tahunAkademik')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            $totalTagihan = Tagihan::where('mahasiswa_id', $mahasiswa->id)->belumLunas()->sum('sisa_tagihan');

            return view('keuangan.tagihan.mahasiswa', compact('tagihan', 'mahasiswa', 'totalTagihan'));
        }

        // Admin view
        $query = Tagihan::with(['mahasiswa.programStudi', 'tahunAkademik']);

        if ($request->search) {
            $query->whereHas('mahasiswa', function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%");
            })->orWhere('no_tagihan', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tahun_akademik_id) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->jenis) {
            $query->where('jenis_tagihan', $request->jenis);
        }

        if ($request->overdue === '1') {
            $query->overdue();
        }

        $tagihan = $query->orderBy('created_at', 'desc')->paginate(20);
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        
        // Statistics
        $stats = [
            'total' => Tagihan::count(),
            'belum_bayar' => Tagihan::where('status', 'Belum Bayar')->count(),
            'cicilan' => Tagihan::where('status', 'Cicilan')->count(),
            'lunas' => Tagihan::where('status', 'Lunas')->count(),
            'total_piutang' => Tagihan::belumLunas()->sum('sisa_tagihan'),
        ];

        return view('keuangan.tagihan.index', compact('tagihan', 'tahunAkademik', 'stats'));
    }

    public function create()
    {
        $mahasiswa = Mahasiswa::where('status', 'Aktif')->orderBy('nim')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tarif = Tarif::active()->orderBy('jenis')->get();

        return view('keuangan.tagihan.create', compact('mahasiswa', 'tahunAkademik', 'tarif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mahasiswa_id' => 'required|exists:mahasiswa,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'jenis_tagihan' => 'required|string|max:100',
            'nominal' => 'required|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|date',
            'tarif_id' => 'nullable|exists:tarif,id',
        ]);

        $mahasiswa = Mahasiswa::find($request->mahasiswa_id);
        
        // Check for beasiswa/potongan
        $diskon = 0;
        $penerimaBeasiswa = PenerimaBeasiswa::where('mahasiswa_id', $request->mahasiswa_id)
            ->where('tahun_akademik_id', $request->tahun_akademik_id)
            ->aktif()
            ->with('beasiswa')
            ->first();

        if ($penerimaBeasiswa) {
            $diskon = $penerimaBeasiswa->beasiswa->hitungPotongan($request->nominal);
        }

        $data = $request->all();
        $data['diskon'] = $diskon;
        $data['denda'] = 0;
        $data['total_bayar'] = $request->nominal - $diskon;
        $data['jumlah_dibayar'] = 0;
        $data['sisa_tagihan'] = $request->nominal - $diskon;

        Tagihan::create($data);

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dibuat!');
    }

    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['mahasiswa.programStudi', 'tahunAkademik', 'tarif', 'transaksi.verifier']);
        return view('keuangan.tagihan.show', compact('tagihan'));
    }

    public function edit(Tagihan $tagihan)
    {
        $mahasiswa = Mahasiswa::orderBy('nim')->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tarif = Tarif::active()->orderBy('jenis')->get();

        return view('keuangan.tagihan.edit', compact('tagihan', 'mahasiswa', 'tahunAkademik', 'tarif'));
    }

    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'tanggal_jatuh_tempo' => 'required|date',
            'status' => 'required|in:Belum Bayar,Cicilan,Lunas,Batal',
        ]);

        $tagihan->update($request->all());

        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil diperbarui!');
    }

    public function destroy(Tagihan $tagihan)
    {
        if ($tagihan->jumlah_dibayar > 0) {
            return back()->with('error', 'Tidak dapat menghapus tagihan yang sudah ada pembayaran!');
        }

        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus!');
    }

    /**
     * Generate tagihan massal
     */
    public function generateMassal(Request $request)
    {
        $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'tarif_id' => 'required|exists:tarif,id',
            'tanggal_jatuh_tempo' => 'required|date',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'angkatan' => 'nullable|string|max:4',
        ]);

        $tarif = Tarif::find($request->tarif_id);
        
        $query = Mahasiswa::where('status', 'Aktif');
        
        if ($request->program_studi_id) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        $mahasiswaList = $query->get();
        $created = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            foreach ($mahasiswaList as $mhs) {
                // Check if tagihan already exists
                $exists = Tagihan::where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $request->tahun_akademik_id)
                    ->where('tarif_id', $tarif->id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Check beasiswa
                $diskon = 0;
                $penerimaBeasiswa = PenerimaBeasiswa::where('mahasiswa_id', $mhs->id)
                    ->where('tahun_akademik_id', $request->tahun_akademik_id)
                    ->aktif()
                    ->with('beasiswa')
                    ->first();

                if ($penerimaBeasiswa) {
                    $diskon = $penerimaBeasiswa->beasiswa->hitungPotongan($tarif->nominal);
                }

                Tagihan::create([
                    'mahasiswa_id' => $mhs->id,
                    'tahun_akademik_id' => $request->tahun_akademik_id,
                    'tarif_id' => $tarif->id,
                    'jenis_tagihan' => $tarif->jenis,
                    'keterangan_tagihan' => $tarif->nama_tarif,
                    'nominal' => $tarif->nominal,
                    'diskon' => $diskon,
                    'denda' => 0,
                    'total_bayar' => $tarif->nominal - $diskon,
                    'jumlah_dibayar' => 0,
                    'sisa_tagihan' => $tarif->nominal - $diskon,
                    'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                ]);

                $created++;
            }

            DB::commit();
            return back()->with('success', "Berhasil membuat {$created} tagihan. {$skipped} dilewati (sudah ada).");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    public function generateForm()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tarif = Tarif::active()->orderBy('jenis')->get();
        $programStudi = ProgramStudi::orderBy('nama')->get();

        return view('keuangan.tagihan.generate', compact('tahunAkademik', 'tarif', 'programStudi'));
    }

    /**
     * Update denda untuk tagihan yang terlambat
     */
    public function updateDenda()
    {
        $tagihanTerlambat = Tagihan::overdue()->get();
        $updated = 0;

        foreach ($tagihanTerlambat as $tagihan) {
            $denda = $tagihan->calculateDenda();
            if ($denda != $tagihan->denda) {
                $tagihan->update(['denda' => $denda]);
                $updated++;
            }
        }

        return back()->with('success', "Denda diperbarui untuk {$updated} tagihan.");
    }
}
