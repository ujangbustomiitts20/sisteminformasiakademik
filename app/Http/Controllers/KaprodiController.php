<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\TugasAkhir;
use App\Models\PengajuanKonversi;
use App\Models\DetailKonversi;
use App\Models\MataKuliah;
use App\Models\Kurikulum;
use App\Models\CutiAkademik;
use App\Models\BimbinganAkademik;
use App\Models\JadwalKuliah;
use App\Models\TahunAkademik;
use App\Models\PendaftaranKegiatanLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaprodiController extends Controller
{
    /**
     * Get the program studi of the logged-in kaprodi
     */
    private function getProdi()
    {
        $user = auth()->user();
        $dosen = $user->dosen;
        
        if (!$dosen) {
            return null;
        }
        
        // Kaprodi is linked via dosen's program_studi_id
        return ProgramStudi::with('fakultas')->find($dosen->program_studi_id);
    }

    /**
     * Dashboard Ketua Prodi
     */
    public function dashboard()
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();
        
        // Statistik
        $stats = [
            'total_mahasiswa' => Mahasiswa::where('program_studi_id', $prodi->id)
                ->where('status', 'aktif')->count(),
            'total_dosen' => Dosen::where('program_studi_id', $prodi->id)->count(),
            'krs_pending' => Krs::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
                ->where('status', 'Pending')->count(),
            'tugas_akhir_berjalan' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
                ->whereNotIn('status', ['selesai', 'gagal'])->count(),
            'cuti_pending' => CutiAkademik::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
                ->where('status', 'Pending')->count(),
            'pkl_berjalan' => PendaftaranKegiatanLapangan::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
                ->where('status', 'berlangsung')->count(),
        ];

        // Hitung konversi pending (dengan fallback jika kolom belum ada)
        try {
            $stats['konversi_pending'] = PengajuanKonversi::where('program_studi_tujuan_id', $prodi->id)
                ->whereIn('status', ['menunggu_kaprodi', 'diproses_kaprodi'])->count();
        } catch (\Exception $e) {
            // Fallback jika kolom program_studi_tujuan_id belum ada
            $stats['konversi_pending'] = PengajuanKonversi::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
                ->whereIn('status', ['diajukan', 'menunggu_kaprodi', 'diproses_kaprodi'])->count();
        }

        // Mahasiswa bermasalah (untuk sementara kosongkan karena tabel tidak punya kolom ipk)
        $mahasiswaBermasalah = collect();

        // Aktivitas terbaru
        $aktivitasTerbaru = collect();
        
        // KRS terbaru
        $krsTerbaru = Krs::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with('mahasiswa')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($krs) => [
                'type' => 'krs',
                'message' => $krs->mahasiswa->nama . ' mengajukan KRS',
                'status' => $krs->status,
                'date' => $krs->created_at
            ]);

        return view('kaprodi.dashboard', compact('prodi', 'stats', 'mahasiswaBermasalah', 'tahunAktif'));
    }

    /**
     * Daftar mahasiswa prodi
     */
    public function mahasiswa(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $query = Mahasiswa::where('program_studi_id', $prodi->id)
            ->with(['dosenWali']);

        // Filter status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter angkatan
        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $mahasiswas = $query->orderBy('angkatan', 'desc')
            ->orderBy('nama')
            ->paginate(20);

        // Get angkatan list
        $angkatans = Mahasiswa::where('program_studi_id', $prodi->id)
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        return view('kaprodi.mahasiswa.index', compact('prodi', 'mahasiswas', 'angkatans'));
    }

    /**
     * Detail mahasiswa
     */
    public function mahasiswaShow(Mahasiswa $mahasiswa)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $mahasiswa->load(['programStudi', 'dosenWali', 'krs.jadwalKuliah.mataKuliah', 'krs.nilai']);

        return view('kaprodi.mahasiswa.show', compact('prodi', 'mahasiswa'));
    }

    /**
     * Daftar dosen prodi
     */
    public function dosen()
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $dosens = Dosen::where('program_studi_id', $prodi->id)
            ->with(['user'])
            ->orderBy('nama')
            ->paginate(20);

        return view('kaprodi.dosen.index', compact('prodi', 'dosens'));
    }

    /**
     * Detail Dosen
     */
    public function dosenShow(Dosen $dosen)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $dosen->program_studi_id != $prodi->id) {
            abort(403);
        }

        $dosen->load(['user', 'programStudi', 'jadwalKuliah.mataKuliah', 'jadwalKuliah.ruangan']);

        // Statistik dosen
        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();
        
        $stats = [
            'jumlah_matakuliah' => $dosen->jadwalKuliah()
                ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
                ->distinct('mata_kuliah_id')
                ->count('mata_kuliah_id'),
            'jumlah_kelas' => $dosen->jadwalKuliah()
                ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
                ->count(),
            'mahasiswa_bimbingan' => Mahasiswa::where('dosen_wali_id', $dosen->id)->count(),
            'tugas_akhir_bimbingan' => TugasAkhir::where(function($q) use ($dosen) {
                    $q->where('pembimbing_1_id', $dosen->id)
                      ->orWhere('pembimbing_2_id', $dosen->id);
                })
                ->whereNotIn('status', ['selesai', 'judul_ditolak'])
                ->count(),
        ];

        // Jadwal mengajar semester ini
        $jadwalMengajar = $dosen->jadwalKuliah()
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->with(['mataKuliah', 'ruangan'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        // Mahasiswa bimbingan akademik
        $mahasiswaBimbingan = Mahasiswa::where('dosen_wali_id', $dosen->id)
            ->where('status', 'Aktif')
            ->orderBy('angkatan', 'desc')
            ->orderBy('nama')
            ->get();

        // Bimbingan TA
        $bimbinganTA = TugasAkhir::where(function($q) use ($dosen) {
                $q->where('pembimbing_1_id', $dosen->id)
                  ->orWhere('pembimbing_2_id', $dosen->id);
            })
            ->whereNotIn('status', ['selesai', 'judul_ditolak'])
            ->with(['mahasiswa' => function($q) {
                $q->select('id', 'nim', 'nama', 'program_studi_id');
            }])
            ->get();

        return view('kaprodi.dosen.show', compact('prodi', 'dosen', 'stats', 'jadwalMengajar', 'mahasiswaBimbingan', 'bimbinganTA', 'tahunAktif'));
    }

    /**
     * Approval KRS
     */
    public function krsApproval(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        $query = Krs::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['mahasiswa', 'jadwalKuliah.mataKuliah'])
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $krsList = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('kaprodi.krs.index', compact('prodi', 'krsList', 'tahunAktif'));
    }

    /**
     * Approve KRS
     */
    public function krsApprove(Krs $krs)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $krs->mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $krs->status = 'Disetujui';
        $krs->tanggal_persetujuan = now();
        $krs->save();

        return back()->with('success', 'KRS berhasil disetujui!');
    }

    /**
     * Reject KRS
     */
    public function krsReject(Request $request, Krs $krs)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $krs->mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $krs->status = 'Ditolak';
        $krs->save();

        return back()->with('success', 'KRS berhasil ditolak!');
    }

    /**
     * Monitoring Tugas Akhir
     */
    public function tugasAkhir(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $query = TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['mahasiswa', 'pembimbing1', 'pembimbing2']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tugasAkhirs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistik
        $stats = [
            'draft' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'draft')->count(),
            'diajukan' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'diajukan')->count(),
            'bimbingan' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'bimbingan')->count(),
            'sidang' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'sidang')->count(),
            'selesai' => TugasAkhir::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'selesai')->count(),
        ];

        return view('kaprodi.tugas-akhir.index', compact('prodi', 'tugasAkhirs', 'stats'));
    }

    /**
     * Detail Tugas Akhir
     */
    public function tugasAkhirShow(TugasAkhir $tugasAkhir)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $tugasAkhir->mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $tugasAkhir->load(['mahasiswa', 'pembimbing1', 'pembimbing2', 'bimbingan', 'seminarProposal', 'sidang']);

        return view('kaprodi.tugas-akhir.show', compact('prodi', 'tugasAkhir'));
    }

    /**
     * Approval Judul TA
     */
    public function tugasAkhirApproval(Request $request, TugasAkhir $tugasAkhir)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $tugasAkhir->mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'catatan' => 'nullable|string',
        ]);

        if ($request->status == 'disetujui') {
            $tugasAkhir->status = 'judul_disetujui';
            $tugasAkhir->tanggal_approval_judul = now();
        } else {
            $tugasAkhir->status = 'judul_ditolak';
        }

        if ($request->catatan) {
            $tugasAkhir->catatan_pembimbing = $request->catatan;
        }
        
        $tugasAkhir->save();

        return back()->with('success', 'Status tugas akhir berhasil diperbarui!');
    }

    /**
     * Daftar Konversi Nilai yang perlu diproses Kaprodi
     */
    public function konversiNilai(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        try {
            // Coba query dengan kolom baru (program_studi_tujuan_id)
            $query = PengajuanKonversi::where('program_studi_tujuan_id', $prodi->id)
                ->with(['programStudiTujuan', 'detailKonversi']);

            if ($request->status) {
                $query->where('status', $request->status);
            } else {
                // Default tampilkan yang menunggu kaprodi
                $query->whereIn('status', ['menunggu_kaprodi', 'diproses_kaprodi']);
            }

            $pengajuans = $query->orderBy('created_at', 'desc')->paginate(20);
        } catch (\Exception $e) {
            // Fallback ke struktur lama jika kolom belum ada
            $query = PengajuanKonversi::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
                ->with(['mahasiswa', 'detailKonversi']);

            if ($request->status) {
                $query->where('status', $request->status);
            } else {
                $query->whereIn('status', ['diajukan', 'menunggu_kaprodi', 'diproses_kaprodi']);
            }

            $pengajuans = $query->orderBy('created_at', 'desc')->paginate(20);
        }

        return view('kaprodi.konversi-nilai.index', compact('prodi', 'pengajuans'));
    }

    /**
     * Detail Konversi Nilai untuk diproses Kaprodi
     */
    public function konversiNilaiShow(PengajuanKonversi $pengajuanKonversi)
    {
        $prodi = $this->getProdi();
        
        // Cek akses berdasarkan program_studi_tujuan_id atau mahasiswa
        $hasAccess = false;
        if ($pengajuanKonversi->program_studi_tujuan_id) {
            $hasAccess = $pengajuanKonversi->program_studi_tujuan_id == $prodi->id;
        } elseif ($pengajuanKonversi->mahasiswa) {
            $hasAccess = $pengajuanKonversi->mahasiswa->program_studi_id == $prodi->id;
        }
        
        if (!$prodi || !$hasAccess) {
            abort(403);
        }

        $pengajuanKonversi->load(['programStudiTujuan', 'mahasiswa', 'detailKonversi.mataKuliah', 'prosesOleh', 'prosesKaprodiOleh']);
        
        // Ambil kurikulum prodi untuk dipilih
        $kurikulums = Kurikulum::where('program_studi_id', $prodi->id)
            ->orderBy('tahun_mulai', 'desc')
            ->get();

        // Aliasing untuk view
        $pengajuan = $pengajuanKonversi;

        return view('kaprodi.konversi-nilai.show', compact('prodi', 'pengajuan', 'kurikulums'));
    }

    /**
     * API untuk mendapatkan mata kuliah berdasarkan kurikulum (AJAX)
     */
    public function getMataKuliahByKurikulum(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kurikulumId = $request->kurikulum_id;
        
        if (!$kurikulumId) {
            return response()->json([]);
        }

        $kurikulum = Kurikulum::where('id', $kurikulumId)
            ->where('program_studi_id', $prodi->id)
            ->first();

        if (!$kurikulum) {
            return response()->json(['error' => 'Kurikulum tidak ditemukan'], 404);
        }

        $mataKuliah = $kurikulum->mataKuliah()
            ->orderBy('kurikulum_mata_kuliah.semester_rekomendasi')
            ->orderBy('kode')
            ->get()
            ->map(function ($mk) {
                return [
                    'id' => $mk->id,
                    'kode' => $mk->kode,
                    'nama' => $mk->nama,
                    'sks' => $mk->sks,
                    'semester' => $mk->pivot->semester_rekomendasi ?? '-',
                    'kategori' => $mk->pivot->kategori ?? '-',
                    'label' => $mk->kode . ' - ' . $mk->nama . ' (' . $mk->sks . ' SKS, Smt ' . ($mk->pivot->semester_rekomendasi ?? '-') . ')',
                ];
            });

        return response()->json($mataKuliah);
    }

    /**
     * Proses Konversi Nilai oleh Kaprodi (mapping MK dan nilai)
     */
    public function konversiNilaiProses(Request $request, PengajuanKonversi $pengajuanKonversi)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $pengajuanKonversi->program_studi_tujuan_id != $prodi->id) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:simpan,setujui,tolak',
            'details' => 'nullable|array',
            'details.*.mata_kuliah_id' => 'nullable|exists:mata_kuliah,id',
            'details.*.nilai_konversi' => 'nullable|string',
            'details.*.status_konversi' => 'nullable|in:pending,disetujui,ditolak',
            'catatan_kaprodi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update detail konversi
            if ($request->details) {
                foreach ($request->details as $id => $data) {
                    $detail = DetailKonversi::find($data['id'] ?? $id);
                    if ($detail && $detail->pengajuan_konversi_id == $pengajuanKonversi->id) {
                        $detail->mata_kuliah_id = $data['mata_kuliah_id'] ?? null;
                        $detail->nilai_konversi = $data['nilai_konversi'] ?? null;
                        $detail->bobot_konversi = DetailKonversi::nilaiKeBobot($data['nilai_konversi'] ?? '');
                        $detail->status = $data['status_konversi'] ?? 'pending'; // Field di DB adalah 'status'
                        $detail->disetujui_oleh = auth()->id();
                        $detail->save();
                    }
                }
            }

            // Update status pengajuan berdasarkan action
            if ($request->action == 'setujui') {
                $pengajuanKonversi->status = 'disetujui_kaprodi';
            } elseif ($request->action == 'tolak') {
                $pengajuanKonversi->status = 'ditolak_kaprodi';
            } else {
                $pengajuanKonversi->status = 'diproses_kaprodi';
            }

            $pengajuanKonversi->catatan_kaprodi = $request->catatan_kaprodi;
            $pengajuanKonversi->diproses_kaprodi_oleh = auth()->id();
            $pengajuanKonversi->tanggal_diproses_kaprodi = now();
            $pengajuanKonversi->save();

            DB::commit();
            
            $message = match($request->action) {
                'setujui' => 'Konversi nilai berhasil disetujui! Menunggu finalisasi admin.',
                'tolak' => 'Konversi nilai ditolak.',
                default => 'Data konversi nilai berhasil disimpan.',
            };

            return redirect()->route('kaprodi.konversi-nilai.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses: ' . $e->getMessage());
        }
    }

    /**
     * Monitoring Cuti Akademik
     */
    public function cutiAkademik(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $query = CutiAkademik::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['mahasiswa', 'tahunAkademik']);

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Default tampilkan yang Pending
            $query->where('status', 'Pending');
        }

        $cutiList = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('kaprodi.cuti.index', compact('prodi', 'cutiList'));
    }

    /**
     * Approval Cuti
     */
    public function cutiApproval(Request $request, CutiAkademik $cuti)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $cuti->mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:Disetujui Kaprodi,Ditolak',
            'catatan' => 'nullable|string',
        ]);

        $cuti->status = $request->status;
        $cuti->catatan_kaprodi = $request->catatan;
        $cuti->disetujui_kaprodi_oleh = auth()->id();
        $cuti->tanggal_persetujuan_kaprodi = now();
        $cuti->save();

        return back()->with('success', 'Permohonan cuti berhasil diproses!');
    }

    /**
     * Monitoring PKL/Magang
     */
    public function pkl(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $query = PendaftaranKegiatanLapangan::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['mahasiswa', 'periode.jenisKegiatan', 'mitraDiterima', 'dosenPembimbing']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('kaprodi.pkl.index', compact('prodi', 'pendaftarans'));
    }

    /**
     * Jadwal Kuliah Prodi
     */
    public function jadwalKuliah()
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAktif = TahunAkademik::where('is_aktif', true)->first();

        $jadwals = JadwalKuliah::whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['mataKuliah', 'dosen', 'ruangan'])
            ->where('tahun_akademik_id', $tahunAktif->id ?? 0)
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('kaprodi.jadwal.index', compact('prodi', 'jadwals', 'tahunAktif'));
    }

    /**
     * Laporan Akademik Prodi
     */
    public function laporan(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        // Statistik per angkatan (tanpa IPK karena kolom tidak ada)
        $ipkPerAngkatan = Mahasiswa::where('program_studi_id', $prodi->id)
            ->where('status', 'Aktif')
            ->select('angkatan', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->get()
            ->map(function($item) {
                $item->rata_ipk = 0; // Placeholder karena kolom ipk tidak ada
                return $item;
            });

        // Status mahasiswa
        $statusMahasiswa = Mahasiswa::where('program_studi_id', $prodi->id)
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->get();

        // Kelulusan per tahun (kosongkan jika kolom tanggal_lulus tidak ada)
        $kelulusan = collect();

        return view('kaprodi.laporan.index', compact('prodi', 'ipkPerAngkatan', 'statusMahasiswa', 'kelulusan'));
    }

    // =====================
    // KURIKULUM & MATA KULIAH
    // =====================

    /**
     * Daftar Kurikulum Prodi
     */
    public function kurikulumIndex()
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $kurikulums = Kurikulum::where('program_studi_id', $prodi->id)
            ->withCount('mataKuliah')
            ->orderBy('tahun_mulai', 'desc')
            ->get();

        return view('kaprodi.kurikulum.index', compact('prodi', 'kurikulums'));
    }

    /**
     * Detail Kurikulum
     */
    public function kurikulumShow(Kurikulum $kurikulum)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $kurikulum->program_studi_id != $prodi->id) {
            abort(403);
        }

        $kurikulum->load(['mataKuliah' => function($q) {
            $q->orderBy('kurikulum_mata_kuliah.semester_rekomendasi')
              ->orderBy('kode');
        }]);

        // Group mata kuliah by semester
        $mataKuliahBySemester = $kurikulum->mataKuliah->groupBy('pivot.semester_rekomendasi');

        return view('kaprodi.kurikulum.show', compact('prodi', 'kurikulum', 'mataKuliahBySemester'));
    }

    /**
     * Daftar Mata Kuliah Prodi
     */
    public function mataKuliahIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $query = MataKuliah::where('program_studi_id', $prodi->id);

        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kode', 'like', "%{$request->search}%")
                  ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        $mataKuliahs = $query->orderBy('semester')->orderBy('kode')->paginate(20);

        return view('kaprodi.mata-kuliah.index', compact('prodi', 'mataKuliahs'));
    }

    /**
     * Detail Mata Kuliah
     */
    public function mataKuliahShow(MataKuliah $mataKuliah)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $mataKuliah->program_studi_id != $prodi->id) {
            abort(403);
        }

        $mataKuliah->load(['prasyarat.mataKuliahPrasyarat', 'kurikulum']);

        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();

        // Get nilai for this mata kuliah
        $nilais = Nilai::where('mata_kuliah_id', $mataKuliah->id)
            ->with('mahasiswa')
            ->orderBy('nilai_huruf')
            ->get();

        // Statistik
        $statistik = [
            'total_mahasiswa' => $nilais->count(),
            'rata_nilai' => $nilais->avg('nilai_angka') ?? 0,
            'lulus' => $nilais->whereNotIn('nilai_huruf', ['E', null])->count(),
            'tidak_lulus' => $nilais->where('nilai_huruf', 'E')->count(),
        ];

        // Distribusi nilai
        $distribusiNilai = $nilais->groupBy('nilai_huruf')->map->count()->toArray();

        return view('kaprodi.mata-kuliah.show', compact('prodi', 'mataKuliah', 'nilais', 'statistik', 'distribusiNilai', 'tahunAkademiks'));
    }

    // =====================
    // MONITORING NILAI & AKADEMIK
    // =====================

    /**
     * Rekap Nilai Mahasiswa
     */
    public function rekapNilai(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();
        $tahunAkademikId = $request->tahun_akademik_id ?? $tahunAkademikAktif?->id;

        $mataKuliahs = MataKuliah::where('program_studi_id', $prodi->id)->orderBy('kode')->get();
        $angkatans = Mahasiswa::where('program_studi_id', $prodi->id)->distinct()->pluck('angkatan')->sort()->reverse();

        $query = Nilai::whereHas('krs.mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['krs.mahasiswa', 'krs.jadwalKuliah.mataKuliah']);

        if ($tahunAkademikId) {
            $query->whereHas('krs', fn($q) => $q->where('tahun_akademik_id', $tahunAkademikId));
        }

        if ($request->mata_kuliah_id) {
            $query->whereHas('krs.jadwalKuliah', fn($q) => $q->where('mata_kuliah_id', $request->mata_kuliah_id));
        }

        if ($request->angkatan) {
            $query->whereHas('krs.mahasiswa', fn($q) => $q->where('angkatan', $request->angkatan));
        }

        // Summary - using correct column 'huruf' instead of 'nilai_huruf'
        $summary = [
            'total' => $query->clone()->count(),
            'rata_ipk' => 0,
            'lulus' => $query->clone()->whereNotIn('huruf', ['E'])->whereNotNull('huruf')->count(),
            'tidak_lulus' => $query->clone()->where('huruf', 'E')->count(),
        ];

        $nilais = $query->orderBy('created_at', 'desc')->paginate(50)->appends($request->query());
        
        // Transform nilai to add mahasiswa and mataKuliah accessors for view
        $nilais->getCollection()->transform(function($nilai) {
            $nilai->mahasiswa = $nilai->krs->mahasiswa ?? null;
            $nilai->mataKuliah = $nilai->krs->jadwalKuliah->mataKuliah ?? null;
            $nilai->nilai_angka = $nilai->nilai_akhir;
            $nilai->nilai_huruf = $nilai->huruf;
            return $nilai;
        });

        return view('kaprodi.nilai.rekap', compact('prodi', 'nilais', 'summary', 'tahunAkademiks', 'tahunAkademikAktif', 'mataKuliahs', 'angkatans'));
    }

    /**
     * Monitoring IPK Mahasiswa
     */
    public function monitoringIpk(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $query = Mahasiswa::where('program_studi_id', $prodi->id)
            ->where('status', 'Aktif')
            ->with(['krs.nilai', 'krs.jadwalKuliah.mataKuliah']);

        if ($request->angkatan) {
            $query->where('angkatan', $request->angkatan);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                  ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        $allMahasiswas = $query->orderBy('nama')->get()->map(function($mhs) {
            // Hitung IPK manual dari nilai
            $totalSks = 0;
            $totalBobot = 0;
            
            foreach ($mhs->krs as $krs) {
                if ($krs->nilai && $krs->nilai->nilai_akhir !== null) {
                    $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                    $bobot = $krs->nilai->bobot ?? 0;
                    $totalSks += $sks;
                    $totalBobot += $bobot * $sks;
                }
            }
            
            $mhs->ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
            $mhs->total_sks = $totalSks;
            return $mhs;
        });

        // Filter IPK range
        if ($request->range) {
            $allMahasiswas = $allMahasiswas->filter(function($m) use ($request) {
                return match($request->range) {
                    'cumlaude' => $m->ipk >= 3.50,
                    'sangat_memuaskan' => $m->ipk >= 3.00 && $m->ipk < 3.50,
                    'memuaskan' => $m->ipk >= 2.50 && $m->ipk < 3.00,
                    'cukup' => $m->ipk >= 2.00 && $m->ipk < 2.50,
                    'rendah' => $m->ipk < 2.00,
                    default => true
                };
            });
        }

        // Summary
        $summary = [
            'cumlaude' => $allMahasiswas->where('ipk', '>=', 3.50)->count(),
            'sangat_memuaskan' => $allMahasiswas->whereBetween('ipk', [3.00, 3.49])->count(),
            'memuaskan' => $allMahasiswas->whereBetween('ipk', [2.50, 2.99])->count(),
            'cukup' => $allMahasiswas->whereBetween('ipk', [2.00, 2.49])->count(),
            'rendah' => $allMahasiswas->where('ipk', '<', 2.00)->count(),
            'rata_rata' => $allMahasiswas->avg('ipk') ?? 0,
        ];

        $angkatans = Mahasiswa::where('program_studi_id', $prodi->id)
            ->distinct()->pluck('angkatan')->sort()->reverse();

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 20;
        $mahasiswas = new \Illuminate\Pagination\LengthAwarePaginator(
            $allMahasiswas->forPage($page, $perPage),
            $allMahasiswas->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('kaprodi.nilai.monitoring-ipk', compact('prodi', 'mahasiswas', 'summary', 'angkatans'));
    }

    /**
     * Mahasiswa Bermasalah (IPK rendah, terancam DO)
     */
    public function mahasiswaBermasalah()
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        // Mahasiswa dengan IPK rendah
        $mahasiswaIpkRendah = Mahasiswa::where('program_studi_id', $prodi->id)
            ->where('status', 'Aktif')
            ->with(['krs.nilai', 'krs.jadwalKuliah.mataKuliah', 'dosenWali'])
            ->get()
            ->map(function($mhs) {
                $totalSks = 0;
                $totalBobot = 0;
                
                foreach ($mhs->krs as $krs) {
                    if ($krs->nilai && $krs->nilai->nilai_akhir !== null) {
                        $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                        $bobot = $krs->nilai->bobot ?? 0;
                        $totalSks += $sks;
                        $totalBobot += $bobot * $sks;
                    }
                }
                
                $mhs->ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
                $mhs->total_sks = $totalSks;
                return $mhs;
            })
            ->filter(fn($m) => $m->ipk < 2.0 && $m->total_sks > 0)
            ->sortBy('ipk')
            ->take(20);

        // Mahasiswa cuti
        $mahasiswaCuti = Mahasiswa::where('program_studi_id', $prodi->id)
            ->where('status', 'Cuti')
            ->with(['cutiAkademik' => fn($q) => $q->latest()->with('tahunAkademik')])
            ->get()
            ->map(function($mhs) {
                $mhs->cuti = $mhs->cutiAkademik->first();
                return $mhs;
            });

        // Mahasiswa tidak aktif
        $mahasiswaTidakAktif = Mahasiswa::where('program_studi_id', $prodi->id)
            ->whereIn('status', ['Tidak Aktif', 'Non-Aktif', 'DO', 'Mengundurkan Diri'])
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        // Summary
        $summary = [
            'ipk_rendah' => $mahasiswaIpkRendah->count(),
            'cuti' => $mahasiswaCuti->count(),
            'tidak_aktif' => $mahasiswaTidakAktif->count(),
        ];

        return view('kaprodi.mahasiswa.bermasalah', compact('prodi', 'mahasiswaIpkRendah', 'mahasiswaCuti', 'mahasiswaTidakAktif', 'summary'));
    }

    // =====================
    // BIMBINGAN AKADEMIK
    // =====================

    /**
     * Riwayat Bimbingan Akademik
     */
    public function bimbinganIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $dosens = Dosen::where('program_studi_id', $prodi->id)->orderBy('nama')->get();

        $query = BimbinganAkademik::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->with(['mahasiswa', 'dosen']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->dosen_id) {
            $query->where('dosen_id', $request->dosen_id);
        }

        if ($request->search) {
            $query->whereHas('mahasiswa', function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                  ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        // Summary
        $summary = [
            'total' => BimbinganAkademik::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->count(),
            'pending' => BimbinganAkademik::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'pending')->count(),
            'selesai' => BimbinganAkademik::whereHas('mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))->where('status', 'selesai')->count(),
            'dosen_count' => $dosens->count(),
        ];

        $bimbingans = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());

        return view('kaprodi.bimbingan.index', compact('prodi', 'bimbingans', 'dosens', 'summary'));
    }

    /**
     * Detail Bimbingan
     */
    public function bimbinganShow(BimbinganAkademik $bimbingan)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $bimbingan->mahasiswa->program_studi_id != $prodi->id) {
            abort(403);
        }

        $bimbingan->load(['mahasiswa', 'dosen']);

        return view('kaprodi.bimbingan.show', compact('prodi', 'bimbingan'));
    }

    // =====================
    // EDOM (EVALUASI DOSEN)
    // =====================

    /**
     * Hasil EDOM Dosen di Prodi
     */
    public function edomIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();

        // Coba ambil data EDOM jika ada
        $edoms = collect();
        $summary = ['total_dosen' => 0, 'total_responden' => 0, 'rata_rata' => 0];
        
        try {
            $edoms = DB::table('rekap_edom')
                ->join('dosen', 'rekap_edom.dosen_id', '=', 'dosen.id')
                ->where('dosen.program_studi_id', $prodi->id)
                ->select('rekap_edom.*', 'dosen.nama as dosen_nama', 'dosen.nidn', 'dosen.nip')
                // use rata_rata_total column that exists in rekap_edom
                ->orderBy('rekap_edom.rata_rata_total', 'desc')
                ->get()
                ->map(function($e) {
                    $e->dosen = (object)['nama' => $e->dosen_nama, 'nidn' => $e->nidn, 'nip' => $e->nip];
                    return $e;
                });

            $summary = [
                'total_dosen' => $edoms->count(),
                'total_responden' => $edoms->sum('jumlah_responden'),
                // use rata_rata_total for average
                'rata_rata' => $edoms->avg('rata_rata_total') ?? 0,
            ];
        } catch (\Exception $e) {
            // Table doesn't exist
        }

        return view('kaprodi.edom.index', compact('prodi', 'edoms', 'summary', 'tahunAkademiks', 'tahunAkademikAktif'));
    }

    // =====================
    // WISUDA & YUDISIUM
    // =====================

    /**
     * Monitoring Calon Wisudawan
     */
    public function wisudaIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $wisudawans = collect();
        $summary = ['total' => 0, 'pending' => 0, 'disetujui' => 0, 'ditolak' => 0];
        $periodes = [];

        try {
            $query = DB::table('pendaftaran_wisuda')
                ->join('mahasiswa', 'pendaftaran_wisuda.mahasiswa_id', '=', 'mahasiswa.id')
                ->where('mahasiswa.program_studi_id', $prodi->id);

            if ($request->periode) {
                // use periode_wisuda_id column
                $query->where('pendaftaran_wisuda.periode_wisuda_id', $request->periode);
            }

            if ($request->status) {
                // case-insensitive comparison for status
                $query->whereRaw('LOWER(pendaftaran_wisuda.status) = ?', [strtolower($request->status)]);
            }

            $wisudawans = $query->select('pendaftaran_wisuda.*', 'mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.ipk', 'mahasiswa.total_sks')
                ->orderBy('pendaftaran_wisuda.created_at', 'desc')
                ->paginate(20)
                ->through(function($w) {
                    $w->mahasiswa = (object)['nim' => $w->nim, 'nama' => $w->nama, 'ipk' => $w->ipk ?? 0, 'total_sks' => $w->total_sks ?? 0];
                    return $w;
                });

            $periodes = DB::table('pendaftaran_wisuda')
                ->join('mahasiswa', 'pendaftaran_wisuda.mahasiswa_id', '=', 'mahasiswa.id')
                ->where('mahasiswa.program_studi_id', $prodi->id)
                ->distinct()
                ->pluck('periode_wisuda_id');

            $summary = [
                'total' => $wisudawans->total(),
                'pending' => DB::table('pendaftaran_wisuda')
                    ->join('mahasiswa', 'pendaftaran_wisuda.mahasiswa_id', '=', 'mahasiswa.id')
                    ->where('mahasiswa.program_studi_id', $prodi->id)
                    ->whereRaw('LOWER(pendaftaran_wisuda.status) = ?', [strtolower('Pending')])->count(),
                'disetujui' => DB::table('pendaftaran_wisuda')
                    ->join('mahasiswa', 'pendaftaran_wisuda.mahasiswa_id', '=', 'mahasiswa.id')
                    ->where('mahasiswa.program_studi_id', $prodi->id)
                    ->whereRaw('LOWER(pendaftaran_wisuda.status) = ?', [strtolower('Verifikasi Berkas')])->count(),
                'ditolak' => DB::table('pendaftaran_wisuda')
                    ->join('mahasiswa', 'pendaftaran_wisuda.mahasiswa_id', '=', 'mahasiswa.id')
                    ->where('mahasiswa.program_studi_id', $prodi->id)
                    ->whereRaw('LOWER(pendaftaran_wisuda.status) = ?', [strtolower('Ditolak')])->count(),
            ];
        } catch (\Exception $e) {
            // Table doesn't exist
            $wisudawans = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('kaprodi.wisuda.index', compact('prodi', 'wisudawans', 'summary', 'periodes'));
    }

    /**
     * Monitoring Yudisium
     */
    public function yudisiumIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $yudisiums = collect();
        $summary = ['total' => 0, 'lulus' => 0, 'tidak_lulus' => 0, 'rata_ipk' => 0];
        $predikat = ['cumlaude' => 0, 'sangat_memuaskan' => 0, 'memuaskan' => 0, 'cukup' => 0];
        $tahuns = [];

        try {
            $query = DB::table('yudisium')
                ->join('mahasiswa', 'yudisium.mahasiswa_id', '=', 'mahasiswa.id')
                ->where('mahasiswa.program_studi_id', $prodi->id);

            if ($request->tahun) {
                $query->whereYear('yudisium.tanggal_yudisium', $request->tahun);
            }

            if ($request->status) {
                // case-insensitive status filter
                $query->whereRaw('LOWER(yudisium.status) = ?', [strtolower($request->status)]);
            }

            $yudisiums = $query->select('yudisium.*', 'mahasiswa.nama', 'mahasiswa.nim', 'mahasiswa.ipk', 'mahasiswa.total_sks')
                ->orderBy('yudisium.tanggal_yudisium', 'desc')
                ->paginate(20)
                ->through(function($y) {
                    $y->mahasiswa = (object)['nim' => $y->nim, 'nama' => $y->nama, 'ipk' => $y->ipk ?? 0, 'total_sks' => $y->total_sks ?? 0];
                    $y->tanggal_yudisium = $y->tanggal_yudisium ? \Carbon\Carbon::parse($y->tanggal_yudisium) : null;
                    return $y;
                });

            $tahuns = DB::table('yudisium')
                ->join('mahasiswa', 'yudisium.mahasiswa_id', '=', 'mahasiswa.id')
                ->where('mahasiswa.program_studi_id', $prodi->id)
                ->selectRaw('YEAR(yudisium.tanggal_yudisium) as tahun')
                ->distinct()
                ->pluck('tahun');

            $allYudisiums = DB::table('yudisium')
                ->join('mahasiswa', 'yudisium.mahasiswa_id', '=', 'mahasiswa.id')
                ->where('mahasiswa.program_studi_id', $prodi->id)
                ->select('mahasiswa.ipk', 'yudisium.status')
                ->get();

            // normalize status when computing summary
            $summary = [
                'total' => $allYudisiums->count(),
                'lulus' => $allYudisiums->filter(fn($y) => strtolower($y->status) === 'lulus')->count(),
                'tidak_lulus' => $allYudisiums->filter(fn($y) => strtolower($y->status) === 'tidak_lulus' || strtolower($y->status) === 'tidak lulus')->count(),
                'rata_ipk' => $allYudisiums->avg('ipk') ?? 0,
            ];

            $predikat = [
                'cumlaude' => $allYudisiums->where('ipk', '>=', 3.50)->count(),
                'sangat_memuaskan' => $allYudisiums->whereBetween('ipk', [3.00, 3.49])->count(),
                'memuaskan' => $allYudisiums->whereBetween('ipk', [2.50, 2.99])->count(),
                'cukup' => $allYudisiums->where('ipk', '<', 2.50)->count(),
            ];
        } catch (\Exception $e) {
            // Table doesn't exist
            $yudisiums = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('kaprodi.yudisium.index', compact('prodi', 'yudisiums', 'summary', 'predikat', 'tahuns'));
    }

    // =====================
    // ABSENSI & PERKULIAHAN
    // =====================

    /**
     * Monitoring Absensi
     */
    public function absensiIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();
        $tahunAkademikId = $request->tahun_akademik_id ?? $tahunAkademikAktif?->id;

        $dosens = Dosen::where('program_studi_id', $prodi->id)->orderBy('nama')->get();

        // Query jadwal with absensi count through KRS relationship
        $query = JadwalKuliah::whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->where('tahun_akademik_id', $tahunAkademikId ?? 0)
            ->with(['mataKuliah', 'dosen']);

        if ($request->dosen_id) {
            $query->where('dosen_id', $request->dosen_id);
        }

        $jadwals = $query->orderBy('hari')->paginate(20)->appends($request->query());
        
        // Add absensi count manually through KRS
        $jadwals->getCollection()->transform(function($jadwal) {
            $jadwal->absensi_count = \App\Models\Absensi::whereHas('krs', fn($q) => $q->where('jadwal_kuliah_id', $jadwal->id))->count();
            $totalKrs = $jadwal->krs()->where('status', 'Disetujui')->count();
            $totalAbsensi = $jadwal->absensi_count;
            $targetPertemuan = 16;
            $jadwal->rata_kehadiran = $totalKrs > 0 && $totalAbsensi > 0 
                ? round((\App\Models\Absensi::whereHas('krs', fn($q) => $q->where('jadwal_kuliah_id', $jadwal->id))->where('status', 'Hadir')->count() / $totalAbsensi) * 100, 1) 
                : 0;
            return $jadwal;
        });

        // Summary
        $totalJadwal = JadwalKuliah::whereHas('mataKuliah', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->where('tahun_akademik_id', $tahunAkademikId ?? 0)->count();
        
        $summary = [
            'total_jadwal' => $totalJadwal,
            'total_pertemuan' => 0,
            'rata_kehadiran' => 0,
            'perlu_perhatian' => 0,
        ];

        return view('kaprodi.absensi.index', compact('prodi', 'jadwals', 'tahunAkademiks', 'tahunAkademikAktif', 'dosens', 'summary'));
    }

    /**
     * Detail Absensi per Jadwal
     */
    public function absensiShow(JadwalKuliah $jadwalKuliah)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi || $jadwalKuliah->mataKuliah->program_studi_id != $prodi->id) {
            abort(403);
        }

        $jadwal = $jadwalKuliah;
        $jadwal->load(['mataKuliah', 'dosen', 'ruangan', 'krs.mahasiswa']);

        // Get all absensi through KRS
        $krsIds = $jadwal->krs->pluck('id');
        $allAbsensi = \App\Models\Absensi::whereIn('krs_id', $krsIds)
            ->with(['krs.mahasiswa'])
            ->get();

        // Rekap kehadiran per mahasiswa
        $rekapMahasiswa = $allAbsensi
            ->groupBy(fn($a) => $a->krs->mahasiswa_id)
            ->map(function($absensis) {
                $mahasiswa = $absensis->first()->krs->mahasiswa ?? null;
                return (object)[
                    'mahasiswa' => $mahasiswa,
                    'hadir' => $absensis->where('status', 'Hadir')->count(),
                    'izin' => $absensis->where('status', 'Izin')->count(),
                    'sakit' => $absensis->where('status', 'Sakit')->count(),
                    'alpha' => $absensis->where('status', 'Alpha')->count(),
                    'total' => $absensis->count(),
                ];
            })->values();

        // Detail per pertemuan
        $pertemuans = $allAbsensi
            ->groupBy('pertemuan')
            ->map(function($absensis, $pertemuan) {
                return (object)[
                    'pertemuan_ke' => $pertemuan,
                    'tanggal' => $absensis->first()->tanggal ? \Carbon\Carbon::parse($absensis->first()->tanggal) : null,
                    'materi' => $absensis->first()->materi ?? '',
                    'hadir' => $absensis->where('status', 'Hadir')->count(),
                    'izin' => $absensis->where('status', 'Izin')->count(),
                    'sakit' => $absensis->where('status', 'Sakit')->count(),
                    'alpha' => $absensis->where('status', 'Alpha')->count(),
                ];
            })->sortKeys()->values();

        // Statistik
        $totalMahasiswa = $rekapMahasiswa->count();
        $rataKehadiran = $totalMahasiswa > 0 ? $rekapMahasiswa->avg(function($r) {
            $total = $r->hadir + $r->izin + $r->sakit + $r->alpha;
            return $total > 0 ? ($r->hadir / $total) * 100 : 0;
        }) : 0;

        $statistik = [
            'total_pertemuan' => $pertemuans->count(),
            'rata_kehadiran' => $rataKehadiran,
            'total_mahasiswa' => $totalMahasiswa,
            'mhs_bermasalah' => $rekapMahasiswa->filter(function($r) {
                $total = $r->hadir + $r->izin + $r->sakit + $r->alpha;
                return $total > 0 && ($r->hadir / $total) < 0.75;
            })->count(),
        ];

        return view('kaprodi.absensi.show', compact('prodi', 'jadwal', 'rekapMahasiswa', 'pertemuans', 'statistik'));
    }

    // =====================
    // JADWAL UJIAN
    // =====================

    /**
     * Jadwal Ujian Prodi
     */
    public function jadwalUjianIndex(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAkademiks = TahunAkademik::orderBy('tahun', 'desc')->orderBy('semester', 'desc')->get();
        $tahunAkademikAktif = TahunAkademik::where('is_aktif', true)->first();

        $jadwalUjians = collect();
        $summary = ['total' => 0, 'uts' => 0, 'uas' => 0];

        try {
            $query = DB::table('jadwal_ujian')
                ->join('mata_kuliah', 'jadwal_ujian.mata_kuliah_id', '=', 'mata_kuliah.id')
                ->leftJoin('dosen', 'jadwal_ujian.dosen_id', '=', 'dosen.id')
                ->leftJoin('ruangan', 'jadwal_ujian.ruangan_id', '=', 'ruangan.id')
                ->where('mata_kuliah.program_studi_id', $prodi->id);

            if ($request->jenis) {
                $query->where('jadwal_ujian.jenis', $request->jenis);
            }

            $jadwalUjians = $query->select(
                    'jadwal_ujian.*', 
                    'mata_kuliah.kode as mk_kode', 
                    'mata_kuliah.nama as mk_nama',
                    'dosen.nama as dosen_nama',
                    'ruangan.nama as ruangan_nama'
                )
                ->orderBy('jadwal_ujian.tanggal')
                ->orderBy('jadwal_ujian.jam_mulai')
                ->paginate(20)
                ->through(function($j) {
                    $j->tanggal = $j->tanggal ? \Carbon\Carbon::parse($j->tanggal) : null;
                    $j->mataKuliah = (object)['kode' => $j->mk_kode, 'nama' => $j->mk_nama];
                    $j->dosen = (object)['nama' => $j->dosen_nama];
                    $j->ruangan = (object)['nama' => $j->ruangan_nama];
                    return $j;
                });

            $summary = [
                'total' => DB::table('jadwal_ujian')
                    ->join('mata_kuliah', 'jadwal_ujian.mata_kuliah_id', '=', 'mata_kuliah.id')
                    ->where('mata_kuliah.program_studi_id', $prodi->id)->count(),
                'uts' => DB::table('jadwal_ujian')
                    ->join('mata_kuliah', 'jadwal_ujian.mata_kuliah_id', '=', 'mata_kuliah.id')
                    ->where('mata_kuliah.program_studi_id', $prodi->id)
                    ->where('jenis', 'UTS')->count(),
                'uas' => DB::table('jadwal_ujian')
                    ->join('mata_kuliah', 'jadwal_ujian.mata_kuliah_id', '=', 'mata_kuliah.id')
                    ->where('mata_kuliah.program_studi_id', $prodi->id)
                    ->where('jenis', 'UAS')->count(),
            ];
        } catch (\Exception $e) {
            $jadwalUjians = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('kaprodi.jadwal-ujian.index', compact('prodi', 'jadwalUjians', 'summary', 'tahunAkademiks', 'tahunAkademikAktif'));
    }

    // =====================
    // STATISTIK & EXPORT
    // =====================

    /**
     * Statistik Akademik Prodi
     */
    public function statistik()
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        // Statistik utama
        $statistik = [
            'total_mahasiswa' => Mahasiswa::where('program_studi_id', $prodi->id)->count(),
            'mahasiswa_aktif' => Mahasiswa::where('program_studi_id', $prodi->id)->where('status', 'Aktif')->count(),
            'total_dosen' => Dosen::where('program_studi_id', $prodi->id)->count(),
            'total_mata_kuliah' => MataKuliah::where('program_studi_id', $prodi->id)->count(),
            'rata_ipk' => 0,
        ];

        // Mahasiswa per angkatan dengan perhitungan IPK
        $mahasiswaPerAngkatan = Mahasiswa::where('program_studi_id', $prodi->id)
            ->select('angkatan', DB::raw('COUNT(*) as total'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->get();

        // Status mahasiswa
        $statusData = Mahasiswa::where('program_studi_id', $prodi->id)
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $statusMahasiswa = [
            'aktif' => $statusData['Aktif'] ?? 0,
            'cuti' => $statusData['Cuti'] ?? 0,
            'lulus' => $statusData['Lulus'] ?? 0,
            'do' => $statusData['DO'] ?? 0,
            'mengundurkan_diri' => $statusData['Mengundurkan Diri'] ?? 0,
            'tidak_aktif' => $statusData['Tidak Aktif'] ?? ($statusData['Non-Aktif'] ?? 0),
        ];

        // Distribusi IPK
        $mahasiswaAktif = Mahasiswa::where('program_studi_id', $prodi->id)
            ->where('status', 'Aktif')
            ->with(['krs.nilai', 'krs.jadwalKuliah.mataKuliah'])
            ->get()
            ->map(function($mhs) {
                $totalSks = 0;
                $totalBobot = 0;
                
                foreach ($mhs->krs as $krs) {
                    if ($krs->nilai && $krs->nilai->nilai_akhir !== null) {
                        $sks = $krs->jadwalKuliah->mataKuliah->sks ?? 0;
                        $bobot = $krs->nilai->bobot ?? 0;
                        $totalSks += $sks;
                        $totalBobot += $bobot * $sks;
                    }
                }
                
                $mhs->ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;
                return $mhs;
            });

        $distribusiIpk = [
            'cumlaude' => $mahasiswaAktif->where('ipk', '>=', 3.50)->count(),
            'sangat_memuaskan' => $mahasiswaAktif->filter(fn($m) => $m->ipk >= 3.00 && $m->ipk < 3.50)->count(),
            'memuaskan' => $mahasiswaAktif->filter(fn($m) => $m->ipk >= 2.50 && $m->ipk < 3.00)->count(),
            'rendah' => $mahasiswaAktif->where('ipk', '<', 2.50)->count(),
        ];

        $statistik['rata_ipk'] = $mahasiswaAktif->avg('ipk') ?? 0;

        return view('kaprodi.statistik.index', compact('prodi', 'statistik', 'mahasiswaPerAngkatan', 'statusMahasiswa', 'distribusiIpk'));
    }

    /**
     * Export Data Mahasiswa Prodi
     */
    public function exportMahasiswa(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $mahasiswas = Mahasiswa::where('program_studi_id', $prodi->id)
            ->with('dosenWali')
            ->orderBy('nim')
            ->get();

        $filename = 'mahasiswa_' . $prodi->kode . '_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($mahasiswas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['NIM', 'Nama', 'Jenis Kelamin', 'Angkatan', 'Status', 'Dosen Wali', 'Email', 'Telepon']);
            
            foreach ($mahasiswas as $mhs) {
                fputcsv($file, [
                    $mhs->nim,
                    $mhs->nama,
                    $mhs->jenis_kelamin,
                    $mhs->angkatan,
                    $mhs->status,
                    $mhs->dosenWali->nama ?? '-',
                    $mhs->email ?? '-',
                    $mhs->telepon ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Data Nilai Prodi
     */
    public function exportNilai(Request $request)
    {
        $prodi = $this->getProdi();
        
        if (!$prodi) {
            return redirect()->route('dashboard')->with('error', 'Data program studi tidak ditemukan!');
        }

        $tahunAkademikId = $request->tahun_akademik_id ?? TahunAkademik::where('is_aktif', true)->first()?->id;

        $nilais = Nilai::whereHas('krs.mahasiswa', fn($q) => $q->where('program_studi_id', $prodi->id))
            ->whereHas('krs', fn($q) => $q->where('tahun_akademik_id', $tahunAkademikId))
            ->with(['krs.mahasiswa', 'krs.mataKuliah'])
            ->get();

        $filename = 'nilai_' . $prodi->kode . '_' . date('Ymd_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($nilais) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['NIM', 'Nama Mahasiswa', 'Kode MK', 'Nama MK', 'SKS', 'Nilai Angka', 'Nilai Huruf']);
            
            foreach ($nilais as $nilai) {
                fputcsv($file, [
                    $nilai->krs->mahasiswa->nim ?? '-',
                    $nilai->krs->mahasiswa->nama ?? '-',
                    $nilai->krs->mataKuliah->kode ?? '-',
                    $nilai->krs->mataKuliah->nama ?? '-',
                    $nilai->krs->mataKuliah->sks ?? 0,
                    $nilai->nilai_angka ?? '-',
                    $nilai->nilai_huruf ?? '-',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
