<?php

namespace App\Http\Controllers;

use App\Models\Cicilan;
use App\Models\DetailCicilan;
use App\Models\SkemaCicilan;
use App\Models\Tagihan;
use App\Models\TransaksiPembayaran;
use App\Models\NotifikasiKeuangan;
use App\Models\PengaturanDenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CicilanController extends Controller
{
    public function index(Request $request)
    {
        $query = Cicilan::with(['tagihan.mahasiswa.programStudi', 'skemaCicilan', 'detailCicilan'])
            ->orderBy('created_at', 'desc');

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('tagihan.mahasiswa', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        $cicilan = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => Cicilan::count(),
            'aktif' => Cicilan::where('status', 'Aktif')->count(),
            'lunas' => Cicilan::where('status', 'Lunas')->count(),
            'total_piutang' => DetailCicilan::belumBayar()->sum('nominal'),
        ];

        return view('keuangan.cicilan.index', compact('cicilan', 'stats'));
    }

    public function create(Request $request)
    {
        $tagihan = null;
        if ($request->filled('tagihan_id')) {
            $tagihan = Tagihan::with('mahasiswa.programStudi')
                ->where('id', $request->tagihan_id)
                ->whereIn('status', ['Belum Bayar', 'Cicilan'])
                ->first();

            if (!$tagihan) {
                return redirect()->route('tagihan.index')
                    ->with('error', 'Tagihan tidak valid atau sudah lunas.');
            }
        }

        $skemaCicilan = SkemaCicilan::active()->orderBy('jumlah_cicilan')->get();

        // Get tagihan yang eligible untuk cicilan
        $tagihanList = Tagihan::with('mahasiswa')
            ->whereIn('status', ['Belum Bayar'])
            ->where('sisa_tagihan', '>', 0)
            ->whereDoesntHave('cicilan', fn($q) => $q->where('status', 'Aktif'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('keuangan.cicilan.create', compact('tagihan', 'skemaCicilan', 'tagihanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'skema_cicilan_id' => 'required|exists:skema_cicilan,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
        ]);

        $tagihan = Tagihan::findOrFail($validated['tagihan_id']);
        $skema = SkemaCicilan::findOrFail($validated['skema_cicilan_id']);

        // Validasi
        if ($tagihan->status === 'Lunas') {
            return back()->with('error', 'Tagihan sudah lunas.');
        }

        if ($tagihan->sisa_tagihan < $skema->minimal_tagihan) {
            return back()->with('error', "Minimal tagihan untuk skema ini adalah Rp " . number_format($skema->minimal_tagihan, 0, ',', '.'));
        }

        // Check existing active cicilan
        if ($tagihan->cicilan()->where('status', 'Aktif')->exists()) {
            return back()->with('error', 'Tagihan ini sudah memiliki cicilan aktif.');
        }

        DB::beginTransaction();
        try {
            $sisaTagihan = $tagihan->sisa_tagihan;
            $biayaAdmin = $skema->biaya_admin;
            $totalBunga = $sisaTagihan * ($skema->persentase_bunga / 100) * $skema->jumlah_cicilan;
            $totalHarusDibayar = $sisaTagihan + $biayaAdmin + $totalBunga;
            $nominalPerCicilan = ceil($totalHarusDibayar / $skema->jumlah_cicilan);

            // Create cicilan
            $cicilan = Cicilan::create([
                'tagihan_id' => $tagihan->id,
                'skema_cicilan_id' => $skema->id,
                'total_tagihan_awal' => $sisaTagihan,
                'biaya_admin' => $biayaAdmin,
                'total_bunga' => $totalBunga,
                'total_harus_dibayar' => $totalHarusDibayar,
                'nominal_per_cicilan' => $nominalPerCicilan,
                'jumlah_cicilan' => $skema->jumlah_cicilan,
                'cicilan_terbayar' => 0,
                'status' => 'Aktif',
                'tanggal_mulai' => $validated['tanggal_mulai'],
            ]);

            // Create detail cicilan (jadwal)
            $tanggalJatuhTempo = Carbon::parse($validated['tanggal_mulai']);
            for ($i = 1; $i <= $skema->jumlah_cicilan; $i++) {
                DetailCicilan::create([
                    'cicilan_id' => $cicilan->id,
                    'cicilan_ke' => $i,
                    'nominal' => $nominalPerCicilan,
                    'jatuh_tempo' => $tanggalJatuhTempo->copy(),
                    'status' => 'Belum Bayar',
                ]);

                $tanggalJatuhTempo->addDays($skema->interval_hari);
            }

            // Update status tagihan
            $tagihan->status = 'Cicilan';
            $tagihan->save();

            // Buat notifikasi
            NotifikasiKeuangan::buatNotifikasi(
                $tagihan->mahasiswa_id,
                'reminder',
                'Cicilan Berhasil Diaktifkan',
                "Tagihan {$tagihan->jenis_tagihan} Anda telah diubah menjadi cicilan {$skema->jumlah_cicilan}x. Cicilan pertama jatuh tempo pada " . Carbon::parse($validated['tanggal_mulai'])->format('d M Y'),
                'cicilan',
                $cicilan->id
            );

            DB::commit();

            return redirect()->route('cicilan.show', $cicilan)
                ->with('success', 'Cicilan berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Cicilan $cicilan)
    {
        $cicilan->load(['tagihan.mahasiswa.programStudi', 'skemaCicilan', 'detailCicilan.transaksiPembayaran']);

        return view('keuangan.cicilan.show', compact('cicilan'));
    }

    public function bayar(DetailCicilan $detailCicilan)
    {
        $detailCicilan->load(['cicilan.tagihan.mahasiswa.programStudi', 'cicilan.skemaCicilan']);

        // Hitung denda jika terlambat
        $denda = 0;
        if ($detailCicilan->isTerlambat()) {
            $pengaturanDenda = PengaturanDenda::where('jenis_tagihan', $detailCicilan->cicilan->tagihan->jenis_tagihan)
                ->where('is_active', true)
                ->first();

            if ($pengaturanDenda) {
                $hariTerlambat = $detailCicilan->getHariTerlambat();
                if ($pengaturanDenda->tipe_denda === 'persentase') {
                    $denda = $detailCicilan->nominal * ($pengaturanDenda->nilai_denda / 100) * $hariTerlambat;
                } else {
                    $denda = $pengaturanDenda->nilai_denda * $hariTerlambat;
                }
                $denda = min($denda, $pengaturanDenda->maksimal_denda ?? $denda);
            }
        }

        return view('keuangan.cicilan.bayar', compact('detailCicilan', 'denda'));
    }

    public function prosesBayar(Request $request, DetailCicilan $detailCicilan)
    {
        $validated = $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'metode_pembayaran' => 'required|string',
            'tanggal_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($detailCicilan->status !== 'Belum Bayar') {
            return back()->with('error', 'Cicilan ini sudah dibayar.');
        }

        DB::beginTransaction();
        try {
            $cicilan = $detailCicilan->cicilan;
            $tagihan = $cicilan->tagihan;

            // Hitung denda
            $denda = 0;
            if ($detailCicilan->jatuh_tempo < now()->startOfDay()) {
                $pengaturanDenda = PengaturanDenda::where('jenis_tagihan', $tagihan->jenis_tagihan)
                    ->where('is_active', true)
                    ->first();

                if ($pengaturanDenda) {
                    $hariTerlambat = $detailCicilan->jatuh_tempo->diffInDays(now());
                    if ($pengaturanDenda->tipe_denda === 'persentase') {
                        $denda = $detailCicilan->nominal * ($pengaturanDenda->nilai_denda / 100) * $hariTerlambat;
                    } else {
                        $denda = $pengaturanDenda->nilai_denda * $hariTerlambat;
                    }
                    $denda = min($denda, $pengaturanDenda->maksimal_denda ?? $denda);
                }
            }

            // Buat transaksi pembayaran
            $noTransaksi = 'TRX-CIC-' . date('Ymd') . '-' . str_pad(TransaksiPembayaran::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            $transaksi = TransaksiPembayaran::create([
                'tagihan_id' => $tagihan->id,
                'no_transaksi' => $noTransaksi,
                'jumlah' => $validated['jumlah'],
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'status' => 'Verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
                'keterangan' => "Pembayaran cicilan ke-{$detailCicilan->cicilan_ke}. " . ($validated['keterangan'] ?? ''),
            ]);

            // Update detail cicilan
            $detailCicilan->bayar($transaksi->id, $denda);

            // Update tagihan
            $tagihan->jumlah_dibayar += $detailCicilan->nominal;
            $tagihan->sisa_tagihan = $tagihan->nominal - $tagihan->jumlah_dibayar;
            
            if ($tagihan->sisa_tagihan <= 0) {
                $tagihan->status = 'Lunas';
            }
            $tagihan->save();

            // Notifikasi
            NotifikasiKeuangan::buatNotifikasi(
                $tagihan->mahasiswa_id,
                'pembayaran_berhasil',
                'Pembayaran Cicilan Berhasil',
                "Pembayaran cicilan ke-{$detailCicilan->cicilan_ke} sebesar Rp " . number_format($validated['jumlah'], 0, ',', '.') . " telah diterima.",
                'detail_cicilan',
                $detailCicilan->id
            );

            DB::commit();

            return redirect()->route('cicilan.show', $cicilan)
                ->with('success', 'Pembayaran cicilan berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function batalkan(Cicilan $cicilan)
    {
        if ($cicilan->status !== 'Aktif') {
            return back()->with('error', 'Hanya cicilan aktif yang dapat dibatalkan.');
        }

        if ($cicilan->cicilan_terbayar > 0) {
            return back()->with('error', 'Tidak dapat membatalkan cicilan yang sudah ada pembayaran.');
        }

        DB::beginTransaction();
        try {
            // Delete detail cicilan
            $cicilan->detailCicilan()->delete();

            // Update status cicilan
            $cicilan->status = 'Batal';
            $cicilan->save();

            // Kembalikan status tagihan
            $tagihan = $cicilan->tagihan;
            $tagihan->status = 'Belum Bayar';
            $tagihan->save();

            DB::commit();

            return redirect()->route('cicilan.index')
                ->with('success', 'Cicilan berhasil dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // API untuk simulasi
    public function simulasi(Request $request)
    {
        $nominal = $request->input('nominal', 0);
        $skemaId = $request->input('skema_id');

        if (!$nominal || !$skemaId) {
            return response()->json(['error' => 'Data tidak lengkap'], 400);
        }

        $skema = SkemaCicilan::find($skemaId);
        if (!$skema) {
            return response()->json(['error' => 'Skema tidak ditemukan'], 404);
        }

        $biayaAdmin = $skema->biaya_admin;
        $totalBunga = $nominal * ($skema->persentase_bunga / 100) * $skema->jumlah_cicilan;
        $totalBayar = $nominal + $biayaAdmin + $totalBunga;
        $perCicilan = ceil($totalBayar / $skema->jumlah_cicilan);

        return response()->json([
            'nominal_tagihan' => $nominal,
            'biaya_admin' => $biayaAdmin,
            'total_bunga' => $totalBunga,
            'total_bayar' => $totalBayar,
            'per_cicilan' => $perCicilan,
            'jumlah_cicilan' => $skema->jumlah_cicilan,
            'formatted' => [
                'nominal_tagihan' => 'Rp ' . number_format($nominal, 0, ',', '.'),
                'biaya_admin' => 'Rp ' . number_format($biayaAdmin, 0, ',', '.'),
                'total_bunga' => 'Rp ' . number_format($totalBunga, 0, ',', '.'),
                'total_bayar' => 'Rp ' . number_format($totalBayar, 0, ',', '.'),
                'per_cicilan' => 'Rp ' . number_format($perCicilan, 0, ',', '.'),
            ]
        ]);
    }

    // Tracking cicilan mahasiswa (untuk role mahasiswa)
    public function tracking()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa) {
            return redirect()->route('dashboard')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $cicilan = Cicilan::with(['tagihan', 'skemaCicilan', 'detailCicilan'])
            ->whereHas('tagihan', fn($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->orderBy('created_at', 'desc')
            ->get();

        $cicilanAktif = $cicilan->where('status', 'Aktif');
        $totalSisaCicilan = 0;

        foreach ($cicilanAktif as $c) {
            $totalSisaCicilan += $c->getSisaTagihan();
        }

        return view('keuangan.cicilan.tracking', compact('cicilan', 'totalSisaCicilan'));
    }
}
