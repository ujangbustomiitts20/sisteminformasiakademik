<?php

namespace App\Http\Controllers;

use App\Models\TugasAkhir;
use App\Models\BimbinganTA;
use App\Models\SidangTA;
use App\Models\RevisiTA;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TugasAkhirDosenController extends Controller
{
    /**
     * Daftar mahasiswa bimbingan
     */
    public function index()
    {
        $user = Auth::user();
        $dosen = $user->dosen;
        
        // Jika user tidak punya relasi dosen
        if (!$dosen) {
            abort(403, 'Anda tidak terdaftar sebagai dosen.');
        }

        $tugasAkhirs = TugasAkhir::with(['mahasiswa.programStudi', 'bimbingan'])
            ->where(function ($query) use ($dosen) {
                $query->where('pembimbing_1_id', $dosen->id)
                    ->orWhere('pembimbing_2_id', $dosen->id);
            })
            ->orderByRaw("CASE 
                WHEN status IN ('penelitian', 'penulisan') THEN 1
                WHEN status = 'judul_disetujui' THEN 2
                WHEN status IN ('sidang_diajukan', 'sidang_dijadwalkan') THEN 3
                ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.tugas-akhir.index', compact('tugasAkhirs', 'dosen'));
    }

    /**
     * Detail mahasiswa bimbingan
     */
    public function show(TugasAkhir $tugasAkhir)
    {
        $dosen = Auth::user()->dosen;

        // Cek apakah dosen adalah pembimbing
        if (!in_array($dosen->id, [$tugasAkhir->pembimbing_1_id, $tugasAkhir->pembimbing_2_id])) {
            abort(403);
        }

        $tugasAkhir->load([
            'mahasiswa.programStudi',
            'pembimbing1',
            'pembimbing2',
            'bimbingan.dosen',
            'seminarProposal',
            'sidang.revisi.dosen'
        ]);

        return view('dosen.tugas-akhir.show', compact('tugasAkhir', 'dosen'));
    }

    /**
     * Store new bimbingan schedule
     */
    public function storeBimbingan(Request $request, TugasAkhir $tugasAkhir)
    {
        $dosen = Auth::user()->dosen;

        // Validate dosen is pembimbing for this TA
        $isPembimbing = $tugasAkhir->pembimbing_1_id == $dosen->id || $tugasAkhir->pembimbing_2_id == $dosen->id;
        if (!$isPembimbing) {
            abort(403, 'Anda bukan pembimbing untuk tugas akhir ini');
        }

        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'tempat' => 'nullable|string|max:100',
            'materi_bimbingan' => 'nullable|string|max:255',
        ]);

        BimbinganTA::create([
            'tugas_akhir_id' => $tugasAkhir->id,
            'dosen_id' => $dosen->id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'materi_bimbingan' => $request->materi_bimbingan,
            'status' => 'dijadwalkan',
            'persentase_progress' => 0,
        ]);

        return back()->with('success', 'Jadwal bimbingan berhasil dibuat!');
    }

    /**
     * Jadwal bimbingan yang masuk
     */
    public function jadwalBimbingan()
    {
        $dosen = Auth::user()->dosen;

        $jadwals = BimbinganTA::with(['tugasAkhir.mahasiswa'])
            ->where('dosen_id', $dosen->id)
            ->where('status', 'dijadwalkan')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('dosen.tugas-akhir.jadwal-bimbingan', compact('jadwals', 'dosen'));
    }

    /**
     * Riwayat bimbingan
     */
    public function riwayatBimbingan()
    {
        $dosen = Auth::user()->dosen;

        $riwayat = BimbinganTA::with(['tugasAkhir.mahasiswa'])
            ->where('dosen_id', $dosen->id)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('dosen.tugas-akhir.riwayat-bimbingan', compact('riwayat', 'dosen'));
    }

    /**
     * Input hasil bimbingan
     */
    public function inputBimbingan(Request $request, BimbinganTA $bimbinganTA)
    {
        $dosen = Auth::user()->dosen;

        if ($bimbinganTA->dosen_id != $dosen->id) {
            abort(403);
        }

        $request->validate([
            'hasil_bimbingan' => 'required|string',
            'catatan_dosen' => 'nullable|string',
            'rencana_selanjutnya' => 'nullable|string',
            'persentase_progress' => 'required|integer|min:0|max:100',
            'status' => 'required|in:selesai,dibatalkan',
        ]);

        $bimbinganTA->update($request->all());

        return back()->with('success', 'Hasil bimbingan berhasil disimpan!');
    }

    /**
     * Jadwalkan ulang bimbingan
     */
    public function reschedule(Request $request, BimbinganTA $bimbinganTA)
    {
        $dosen = Auth::user()->dosen;

        if ($bimbinganTA->dosen_id != $dosen->id) {
            abort(403);
        }

        $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'tempat' => 'nullable|string|max:100',
            'catatan_dosen' => 'nullable|string',
        ]);

        $bimbinganTA->update([
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'tempat' => $request->tempat,
            'catatan_dosen' => $request->catatan_dosen,
        ]);

        return back()->with('success', 'Jadwal bimbingan berhasil diubah!');
    }

    // ========== PENGUJI ==========

    /**
     * Daftar sidang sebagai penguji
     */
    public function sidangPenguji()
    {
        $dosen = Auth::user()->dosen;

        $sidangs = SidangTA::with(['tugasAkhir.mahasiswa', 'ketuaPenguji', 'penguji1', 'penguji2'])
            ->where(function ($query) use ($dosen) {
                $query->where('ketua_penguji_id', $dosen->id)
                    ->orWhere('penguji_1_id', $dosen->id)
                    ->orWhere('penguji_2_id', $dosen->id);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('dosen.tugas-akhir.sidang-penguji', compact('sidangs', 'dosen'));
    }

    /**
     * Detail sidang
     */
    public function sidangShow(SidangTA $sidang)
    {
        $dosen = Auth::user()->dosen;

        // Cek apakah dosen adalah penguji
        if (!in_array($dosen->id, [$sidang->ketua_penguji_id, $sidang->penguji_1_id, $sidang->penguji_2_id])) {
            abort(403);
        }

        $sidang->load(['tugasAkhir.mahasiswa', 'tugasAkhir.pembimbing1', 'tugasAkhir.pembimbing2', 'revisi.dosen']);

        return view('dosen.tugas-akhir.sidang-show', compact('sidang', 'dosen'));
    }

    /**
     * Input nilai sidang (per penguji)
     */
    public function inputNilaiSidang(Request $request, SidangTA $sidang)
    {
        $dosen = Auth::user()->dosen;

        $kolom = null;
        if ($sidang->ketua_penguji_id == $dosen->id) {
            $kolom = 'nilai_ketua';
        } elseif ($sidang->penguji_1_id == $dosen->id) {
            $kolom = 'nilai_penguji_1';
        } elseif ($sidang->penguji_2_id == $dosen->id) {
            $kolom = 'nilai_penguji_2';
        }

        if (!$kolom) {
            abort(403);
        }

        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $sidang->$kolom = $request->nilai;
        $sidang->save();

        // Hitung nilai akhir jika semua sudah input
        if ($sidang->nilai_ketua && $sidang->nilai_penguji_1 && $sidang->nilai_penguji_2) {
            // Ambil nilai pembimbing jika ada
            if (!$sidang->nilai_pembimbing_1) {
                return back()->with('success', 'Nilai berhasil disimpan! Menunggu nilai pembimbing.');
            }
            $sidang->hitungNilaiAkhir();
            $sidang->save();
        }

        return back()->with('success', 'Nilai berhasil disimpan!');
    }

    /**
     * Tambah catatan revisi
     */
    public function tambahRevisi(Request $request, SidangTA $sidang)
    {
        $dosen = Auth::user()->dosen;

        // Cek apakah dosen adalah penguji atau pembimbing
        $tugasAkhir = $sidang->tugasAkhir;
        $allowed = in_array($dosen->id, [
            $sidang->ketua_penguji_id,
            $sidang->penguji_1_id,
            $sidang->penguji_2_id,
            $tugasAkhir->pembimbing_1_id,
            $tugasAkhir->pembimbing_2_id,
        ]);

        if (!$allowed) {
            abort(403);
        }

        $request->validate([
            'catatan_revisi' => 'required|string',
        ]);

        RevisiTA::create([
            'sidang_ta_id' => $sidang->id,
            'dosen_id' => $dosen->id,
            'catatan_revisi' => $request->catatan_revisi,
        ]);

        return back()->with('success', 'Catatan revisi berhasil ditambahkan!');
    }

    /**
     * Verifikasi revisi selesai
     */
    public function verifikasiRevisi(RevisiTA $revisi)
    {
        $dosen = Auth::user()->dosen;

        if ($revisi->dosen_id != $dosen->id) {
            abort(403);
        }

        $revisi->sudah_diperbaiki = true;
        $revisi->tanggal_perbaikan = now();
        $revisi->save();

        // Cek apakah semua revisi sudah selesai
        $sidang = $revisi->sidangTA;
        if ($sidang->cekRevisiSelesai()) {
            $sidang->revisi_selesai = true;
            $sidang->tanggal_revisi_selesai = now();
            $sidang->save();

            // Update status TA
            $tugasAkhir = $sidang->tugasAkhir;
            $tugasAkhir->status = 'selesai';
            $tugasAkhir->save();
        }

        return back()->with('success', 'Revisi berhasil diverifikasi!');
    }

    // ========== SEMINAR PROPOSAL (PENGUJI) ==========

    /**
     * Daftar seminar sebagai penguji
     */
    public function seminarPenguji()
    {
        $dosen = Auth::user()->dosen;

        $seminars = \App\Models\SeminarProposal::with(['tugasAkhir.mahasiswa', 'penguji1', 'penguji2'])
            ->where(function ($query) use ($dosen) {
                $query->where('penguji_1_id', $dosen->id)
                    ->orWhere('penguji_2_id', $dosen->id);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('dosen.tugas-akhir.seminar-penguji', compact('seminars', 'dosen'));
    }

    /**
     * Input nilai seminar (per penguji)
     */
    public function inputNilaiSeminar(Request $request, \App\Models\SeminarProposal $seminar)
    {
        $dosen = Auth::user()->dosen;

        $kolom = null;
        if ($seminar->penguji_1_id == $dosen->id) {
            $kolom = 'nilai_penguji_1';
        } elseif ($seminar->penguji_2_id == $dosen->id) {
            $kolom = 'nilai_penguji_2';
        }

        if (!$kolom) {
            abort(403);
        }

        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
        ]);

        $seminar->$kolom = $request->nilai;
        $seminar->save();

        return back()->with('success', 'Nilai berhasil disimpan!');
    }
}
