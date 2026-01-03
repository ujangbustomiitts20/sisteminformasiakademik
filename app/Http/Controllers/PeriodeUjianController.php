<?php

namespace App\Http\Controllers;

use App\Models\PeriodeUjian;
use App\Models\KartuUjian;
use App\Models\DetailKartuUjian;
use App\Models\JadwalUjian;
use App\Models\TahunAkademik;
use App\Models\Mahasiswa;
use App\Models\Krs;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PeriodeUjianController extends Controller
{
    /**
     * Display a listing of periode ujian
     */
    public function index()
    {
        $periodes = PeriodeUjian::with('tahunAkademik')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.periode-ujian.index', compact('periodes'));
    }

    /**
     * Show the form for creating a new periode ujian
     */
    public function create()
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        $jenisOptions = PeriodeUjian::getJenisOptions();

        return view('admin.periode-ujian.create', compact('tahunAkademik', 'jenisOptions'));
    }

    /**
     * Store a newly created periode ujian
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'jenis' => 'required|in:UTS,UAS,Susulan,Remedial',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_cetak_kartu' => 'nullable|date',
            'minimal_kehadiran' => 'required|integer|min:0|max:100',
            'cek_pembayaran' => 'boolean',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        PeriodeUjian::create([
            'nama' => $request->nama,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_cetak_kartu' => $request->tanggal_cetak_kartu,
            'minimal_kehadiran' => $request->minimal_kehadiran,
            'cek_pembayaran' => $request->has('cek_pembayaran'),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.periode-ujian.index')
            ->with('success', 'Periode ujian berhasil dibuat!');
    }

    /**
     * Display the specified periode ujian
     */
    public function show(PeriodeUjian $periodeUjian)
    {
        $periodeUjian->load('tahunAkademik');
        
        $kartuUjian = KartuUjian::where('periode_ujian_id', $periodeUjian->id)
            ->with(['mahasiswa.programStudi'])
            ->paginate(20);

        $statistik = [
            'total_kartu' => KartuUjian::where('periode_ujian_id', $periodeUjian->id)->count(),
            'eligible' => KartuUjian::where('periode_ujian_id', $periodeUjian->id)->where('eligible', true)->count(),
            'tidak_eligible' => KartuUjian::where('periode_ujian_id', $periodeUjian->id)->where('eligible', false)->count(),
            'sudah_cetak' => KartuUjian::where('periode_ujian_id', $periodeUjian->id)->where('status', 'printed')->count(),
        ];

        return view('admin.periode-ujian.show', compact('periodeUjian', 'kartuUjian', 'statistik'));
    }

    /**
     * Show the form for editing periode ujian
     */
    public function edit(PeriodeUjian $periodeUjian)
    {
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();
        $jenisOptions = PeriodeUjian::getJenisOptions();

        return view('admin.periode-ujian.edit', compact('periodeUjian', 'tahunAkademik', 'jenisOptions'));
    }

    /**
     * Update the specified periode ujian
     */
    public function update(Request $request, PeriodeUjian $periodeUjian)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'jenis' => 'required|in:UTS,UAS,Susulan,Remedial',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tanggal_cetak_kartu' => 'nullable|date',
            'minimal_kehadiran' => 'required|integer|min:0|max:100',
            'cek_pembayaran' => 'boolean',
            'status' => 'required|in:draft,aktif,selesai',
        ]);

        $periodeUjian->update([
            'nama' => $request->nama,
            'tahun_akademik_id' => $request->tahun_akademik_id,
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tanggal_cetak_kartu' => $request->tanggal_cetak_kartu,
            'minimal_kehadiran' => $request->minimal_kehadiran,
            'cek_pembayaran' => $request->has('cek_pembayaran'),
            'status' => $request->status,
        ]);

        return redirect()->route('admin.periode-ujian.index')
            ->with('success', 'Periode ujian berhasil diperbarui!');
    }

    /**
     * Remove the specified periode ujian
     */
    public function destroy(PeriodeUjian $periodeUjian)
    {
        $periodeUjian->delete();

        return redirect()->route('admin.periode-ujian.index')
            ->with('success', 'Periode ujian berhasil dihapus!');
    }

    /**
     * Generate kartu ujian untuk semua mahasiswa
     */
    public function generateKartu(PeriodeUjian $periodeUjian)
    {
        // Ambil semua mahasiswa yang punya KRS di tahun akademik ini
        $mahasiswas = Mahasiswa::whereHas('krs', function ($query) use ($periodeUjian) {
            $query->where('tahun_akademik_id', $periodeUjian->tahun_akademik_id)
                ->where('status', 'Disetujui');
        })->get();

        $generated = 0;
        $skipped = 0;

        foreach ($mahasiswas as $mahasiswa) {
            // Cek apakah sudah ada kartu
            $existing = KartuUjian::where('mahasiswa_id', $mahasiswa->id)
                ->where('periode_ujian_id', $periodeUjian->id)
                ->first();

            if ($existing) {
                $skipped++;
                continue;
            }

            // Cek eligibilitas
            $eligibilitas = KartuUjian::cekEligibilitas($mahasiswa->id, $periodeUjian->id);

            // Buat kartu ujian
            $kartu = KartuUjian::create([
                'mahasiswa_id' => $mahasiswa->id,
                'periode_ujian_id' => $periodeUjian->id,
                'persentase_kehadiran' => $eligibilitas['persentase_kehadiran'],
                'eligible' => $eligibilitas['eligible'],
                'alasan_tidak_eligible' => $eligibilitas['alasan'] ?: null,
                'pembayaran_lunas' => $eligibilitas['pembayaran_lunas'],
                'status' => $eligibilitas['eligible'] ? 'approved' : 'pending',
            ]);

            // Generate detail per mata kuliah
            $krsList = Krs::where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $periodeUjian->tahun_akademik_id)
                ->where('status', 'Disetujui')
                ->get();

            foreach ($krsList as $krs) {
                // Cari jadwal ujian untuk mata kuliah ini
                $jadwalUjian = JadwalUjian::where('periode_ujian_id', $periodeUjian->id)
                    ->where('mata_kuliah_id', $krs->jadwalKuliah->mata_kuliah_id ?? null)
                    ->first();

                if ($jadwalUjian) {
                    $kehadiran = DetailKartuUjian::hitungKehadiran($krs->id);
                    $eligibleMk = $kehadiran >= $periodeUjian->minimal_kehadiran;

                    DetailKartuUjian::create([
                        'kartu_ujian_id' => $kartu->id,
                        'jadwal_ujian_id' => $jadwalUjian->id,
                        'krs_id' => $krs->id,
                        'persentase_kehadiran' => $kehadiran,
                        'eligible' => $eligibleMk,
                        'alasan_tidak_eligible' => $eligibleMk ? null : 'Kehadiran kurang dari ' . $periodeUjian->minimal_kehadiran . '%',
                    ]);
                }
            }

            $generated++;
        }

        return back()->with('success', "Kartu ujian berhasil di-generate! ({$generated} dibuat, {$skipped} sudah ada)");
    }
}
