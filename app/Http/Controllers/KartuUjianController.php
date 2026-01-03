<?php

namespace App\Http\Controllers;

use App\Models\KartuUjian;
use App\Models\PeriodeUjian;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class KartuUjianController extends Controller
{
    /**
     * Display kartu ujian untuk mahasiswa
     */
    public function index()
    {
        $user = auth()->user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            return back()->with('error', 'Data mahasiswa tidak ditemukan!');
        }

        // Ambil periode ujian aktif
        $periodeAktif = PeriodeUjian::aktif()->get();

        // Ambil kartu ujian mahasiswa
        $kartuUjian = KartuUjian::where('mahasiswa_id', $mahasiswa->id)
            ->with(['periodeUjian.tahunAkademik', 'detailKartuUjian.jadwalUjian.mataKuliah'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mahasiswa.kartu-ujian.index', compact('periodeAktif', 'kartuUjian', 'mahasiswa'));
    }

    /**
     * Detail kartu ujian
     */
    public function show(KartuUjian $kartuUjian)
    {
        $user = auth()->user();
        
        // Validasi kepemilikan
        if ($user->isMahasiswa() && $kartuUjian->mahasiswa_id != $user->mahasiswa->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke kartu ujian ini!');
        }

        $kartuUjian->load([
            'mahasiswa.programStudi.fakultas',
            'periodeUjian.tahunAkademik',
            'detailKartuUjian.jadwalUjian.mataKuliah',
            'detailKartuUjian.jadwalUjian.ruangan',
        ]);

        return view('mahasiswa.kartu-ujian.show', compact('kartuUjian'));
    }

    /**
     * Cetak kartu ujian PDF
     */
    public function cetak(KartuUjian $kartuUjian)
    {
        $user = auth()->user();
        
        // Validasi kepemilikan untuk mahasiswa
        if ($user->isMahasiswa() && $kartuUjian->mahasiswa_id != $user->mahasiswa->id) {
            return back()->with('error', 'Anda tidak memiliki akses ke kartu ujian ini!');
        }

        // Cek eligibilitas
        if (!$kartuUjian->eligible && !$user->isAdmin()) {
            return back()->with('error', 'Anda tidak eligible untuk mengikuti ujian! ' . $kartuUjian->alasan_tidak_eligible);
        }

        // Cek periode cetak
        $periode = $kartuUjian->periodeUjian;
        if (!$periode->isBisaCetakKartu() && !$user->isAdmin()) {
            return back()->with('error', 'Periode cetak kartu ujian belum dibuka!');
        }

        $kartuUjian->load([
            'mahasiswa.programStudi.fakultas',
            'periodeUjian.tahunAkademik',
            'detailKartuUjian.jadwalUjian.mataKuliah',
            'detailKartuUjian.jadwalUjian.ruangan',
        ]);

        // Update status jika belum dicetak
        if ($kartuUjian->status !== 'printed') {
            $kartuUjian->update([
                'status' => 'printed',
                'tanggal_cetak' => now(),
            ]);
        }

        $pdf = Pdf::loadView('mahasiswa.kartu-ujian.cetak', compact('kartuUjian'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('kartu-ujian-' . $kartuUjian->nomor_kartu . '.pdf');
    }

    /**
     * Admin: Approve kartu ujian
     */
    public function approve(KartuUjian $kartuUjian)
    {
        $kartuUjian->update([
            'status' => 'approved',
            'eligible' => true,
            'alasan_tidak_eligible' => null,
        ]);

        return back()->with('success', 'Kartu ujian berhasil di-approve!');
    }

    /**
     * Admin: Revoke kartu ujian
     */
    public function revoke(Request $request, KartuUjian $kartuUjian)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        $kartuUjian->update([
            'status' => 'revoked',
            'eligible' => false,
            'alasan_tidak_eligible' => $request->alasan,
        ]);

        return back()->with('success', 'Kartu ujian berhasil di-revoke!');
    }

    /**
     * Admin: Cetak batch kartu ujian
     */
    public function cetakBatch(Request $request, PeriodeUjian $periodeUjian)
    {
        $request->validate([
            'mahasiswa_ids' => 'required|array',
            'mahasiswa_ids.*' => 'exists:kartu_ujian,id',
        ]);

        $kartuList = KartuUjian::whereIn('id', $request->mahasiswa_ids)
            ->where('periode_ujian_id', $periodeUjian->id)
            ->where('eligible', true)
            ->with([
                'mahasiswa.programStudi.fakultas',
                'periodeUjian.tahunAkademik',
                'detailKartuUjian.jadwalUjian.mataKuliah',
                'detailKartuUjian.jadwalUjian.ruangan',
            ])
            ->get();

        if ($kartuList->isEmpty()) {
            return back()->with('error', 'Tidak ada kartu ujian yang dapat dicetak!');
        }

        // Update status
        KartuUjian::whereIn('id', $request->mahasiswa_ids)
            ->where('status', '!=', 'printed')
            ->update([
                'status' => 'printed',
                'tanggal_cetak' => now(),
            ]);

        $pdf = Pdf::loadView('mahasiswa.kartu-ujian.cetak-batch', compact('kartuList', 'periodeUjian'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('kartu-ujian-batch-' . $periodeUjian->jenis . '.pdf');
    }
}
