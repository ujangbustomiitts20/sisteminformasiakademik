<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\Pertemuan;
use App\Models\Krs;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PertemuanController extends Controller
{
    /**
     * Dosen: Kelola pertemuan untuk jadwal kuliah
     */
    public function index(JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        // Hanya dosen pengampu atau admin
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $jadwalKuliah->load(['mataKuliah', 'dosen', 'ruangan', 'tahunAkademik', 'pertemuan']);
        $jumlahPertemuan = $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16;
        
        // Get existing pertemuan
        $pertemuanList = $jadwalKuliah->pertemuan->keyBy('pertemuan_ke');

        return view('pertemuan.index', compact('jadwalKuliah', 'jumlahPertemuan', 'pertemuanList'));
    }

    /**
     * Dosen: Form tambah/edit pertemuan
     */
    public function create(JadwalKuliah $jadwalKuliah, $pertemuanKe)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $jadwalKuliah->load(['mataKuliah']);
        $pertemuan = Pertemuan::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        $jenisOptions = Pertemuan::JENIS;

        return view('pertemuan.form', compact('jadwalKuliah', 'pertemuanKe', 'pertemuan', 'jenisOptions'));
    }

    /**
     * Dosen: Simpan pertemuan
     */
    public function store(Request $request, JadwalKuliah $jadwalKuliah, $pertemuanKe)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'jenis' => 'required|in:Materi,Tugas,Quiz,UTS,UAS,Praktikum,Diskusi,Presentasi,Forum,Lainnya',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'file_materi' => 'nullable|file|max:10240', // 10MB
            'link_materi' => 'nullable|url|max:500',
            'is_published' => 'boolean',
        ]);

        // Check if existing pertemuan
        $existingPertemuan = Pertemuan::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        $data = [
            'judul' => $request->judul,
            'jenis' => $request->jenis,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'link_materi' => $request->link_materi,
            'is_published' => $request->boolean('is_published'),
        ];

        // If admin, auto approve; if dosen and tanggal changed, reset approval
        if ($user->isAdmin()) {
            if ($request->tanggal) {
                $data['is_approved'] = true;
                $data['approved_by'] = $user->id;
                $data['approved_at'] = now();
                $data['rejection_note'] = null;
            }
        } else {
            // Dosen: if tanggal changed or new, need approval
            if ($request->tanggal && (!$existingPertemuan || $existingPertemuan->tanggal?->format('Y-m-d') !== $request->tanggal)) {
                $data['is_approved'] = false;
                $data['approved_by'] = null;
                $data['approved_at'] = null;
                $data['rejection_note'] = null;
            }
        }

        $pertemuan = Pertemuan::updateOrCreate(
            [
                'jadwal_kuliah_id' => $jadwalKuliah->id,
                'pertemuan_ke' => $pertemuanKe,
            ],
            $data
        );

        // Handle file upload
        if ($request->hasFile('file_materi')) {
            // Delete old file if exists
            if ($pertemuan->file_materi) {
                Storage::disk('public')->delete($pertemuan->file_materi);
            }
            
            $file = $request->file('file_materi');
            $filename = 'pertemuan_' . $jadwalKuliah->id . '_' . $pertemuanKe . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('materi', $filename, 'public');
            $pertemuan->update(['file_materi' => $path]);
        }

        $message = 'Pertemuan ke-' . $pertemuanKe . ' berhasil disimpan!';
        if (!$user->isAdmin() && $request->tanggal && !$pertemuan->is_approved) {
            $message .= ' Tanggal menunggu persetujuan admin.';
        }

        return redirect()->route('pertemuan.index', $jadwalKuliah)
            ->with('success', $message);
    }

    /**
     * Dosen: Hapus pertemuan
     */
    public function destroy(JadwalKuliah $jadwalKuliah, $pertemuanKe)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $pertemuan = Pertemuan::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        if ($pertemuan) {
            // Delete file if exists
            if ($pertemuan->file_materi) {
                Storage::disk('public')->delete($pertemuan->file_materi);
            }
            $pertemuan->delete();
        }

        return redirect()->route('pertemuan.index', $jadwalKuliah)
            ->with('success', 'Pertemuan ke-' . $pertemuanKe . ' berhasil dihapus!');
    }

    /**
     * Dosen: Toggle publish status
     */
    public function togglePublish(JadwalKuliah $jadwalKuliah, $pertemuanKe)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $pertemuan = Pertemuan::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('pertemuan_ke', $pertemuanKe)
            ->first();

        if ($pertemuan) {
            $pertemuan->update(['is_published' => !$pertemuan->is_published]);
            $status = $pertemuan->is_published ? 'dipublikasikan' : 'disembunyikan';
            return back()->with('success', 'Pertemuan ke-' . $pertemuanKe . ' berhasil ' . $status);
        }

        return back()->with('error', 'Pertemuan tidak ditemukan');
    }

    /**
     * Mahasiswa: Lihat daftar pertemuan mata kuliah
     */
    public function mahasiswaIndex(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $mahasiswa = $user->mahasiswa;
        $tahunAkademikAktif = TahunAkademik::getAktif();

        // Get all KRS mahasiswa dengan jadwal kuliah
        $krsData = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.pertemuan' => function($q) {
                $q->where('is_published', true)->orderBy('pertemuan_ke');
            }])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
            ->where('status', 'Disetujui')
            ->get();

        return view('pertemuan.mahasiswa-index', compact('krsData', 'mahasiswa', 'tahunAkademikAktif'));
    }

    /**
     * Mahasiswa: Detail pertemuan mata kuliah
     */
    public function mahasiswaDetail(JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $mahasiswa = $user->mahasiswa;
        
        // Check if mahasiswa mengambil mata kuliah ini
        $krs = Krs::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Disetujui')
            ->first();

        if (!$krs) {
            abort(403, 'Anda tidak mengambil mata kuliah ini');
        }

        $jadwalKuliah->load(['mataKuliah', 'dosen', 'ruangan']);
        $jumlahPertemuan = $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16;
        
        // Get published pertemuan only
        $pertemuanList = Pertemuan::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('is_published', true)
            ->orderBy('pertemuan_ke')
            ->get()
            ->keyBy('pertemuan_ke');

        return view('pertemuan.mahasiswa-detail', compact('jadwalKuliah', 'jumlahPertemuan', 'pertemuanList', 'krs'));
    }

    /**
     * Download file materi
     */
    public function downloadMateri(Pertemuan $pertemuan)
    {
        $user = auth()->user();
        
        // Check access
        if ($user->isMahasiswa()) {
            $krs = Krs::where('jadwal_kuliah_id', $pertemuan->jadwal_kuliah_id)
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->where('status', 'Disetujui')
                ->first();
            
            if (!$krs || !$pertemuan->is_published) {
                abort(403);
            }
        } elseif ($user->isDosen()) {
            if ($user->dosen->id !== $pertemuan->jadwalKuliah->dosen_id) {
                abort(403);
            }
        } elseif (!$user->isAdmin()) {
            abort(403);
        }

        if (!$pertemuan->file_materi || !Storage::disk('public')->exists($pertemuan->file_materi)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($pertemuan->file_materi);
    }

    /**
     * Admin: Daftar pertemuan yang perlu disetujui
     */
    public function adminApprovalList(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }

        $query = Pertemuan::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'approver'])
            ->whereNotNull('tanggal');
        
        // Filter by status
        if ($request->status === 'pending') {
            $query->where('is_approved', false)->whereNull('rejection_note');
        } elseif ($request->status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($request->status === 'rejected') {
            $query->where('is_approved', false)->whereNotNull('rejection_note');
        }

        $pertemuanList = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('pertemuan.admin-approval', compact('pertemuanList'));
    }

    /**
     * Admin: Setujui tanggal pertemuan
     */
    public function approve(Request $request, Pertemuan $pertemuan)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }

        $pertemuan->update([
            'is_approved' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Pertemuan ke-' . $pertemuan->pertemuan_ke . ' berhasil disetujui');
    }

    /**
     * Admin: Tolak tanggal pertemuan
     */
    public function reject(Request $request, Pertemuan $pertemuan)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $pertemuan->update([
            'is_approved' => false,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_note' => $request->rejection_note,
        ]);

        return back()->with('success', 'Pertemuan ke-' . $pertemuan->pertemuan_ke . ' ditolak');
    }

    /**
     * Admin: Set tanggal pertemuan langsung
     */
    public function adminSetTanggal(Request $request, Pertemuan $pertemuan)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'tanggal' => 'required|date',
        ]);

        $pertemuan->update([
            'tanggal' => $request->tanggal,
            'is_approved' => true,
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_note' => null,
        ]);

        return back()->with('success', 'Tanggal pertemuan ke-' . $pertemuan->pertemuan_ke . ' berhasil diatur');
    }

    /**
     * Admin: Kelola jadwal pertemuan per jadwal kuliah
     */
    public function adminIndex(JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }

        $jadwalKuliah->load(['mataKuliah', 'dosen', 'ruangan', 'tahunAkademik', 'pertemuan']);
        $jumlahPertemuan = $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16;
        
        // Get existing pertemuan
        $pertemuanList = $jadwalKuliah->pertemuan->keyBy('pertemuan_ke');

        return view('pertemuan.admin-index', compact('jadwalKuliah', 'jumlahPertemuan', 'pertemuanList'));
    }

    /**
     * Admin: Bulk set tanggal pertemuan
     */
    public function adminBulkSetTanggal(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'tanggal_mulai' => 'required|date',
            'interval_hari' => 'required|integer|min:1|max:14',
        ]);

        $jumlahPertemuan = $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16;
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);

        for ($i = 1; $i <= $jumlahPertemuan; $i++) {
            Pertemuan::updateOrCreate(
                [
                    'jadwal_kuliah_id' => $jadwalKuliah->id,
                    'pertemuan_ke' => $i,
                ],
                [
                    'tanggal' => $tanggalMulai->copy()->addDays(($i - 1) * $request->interval_hari),
                    'is_approved' => true,
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'jenis' => 'Materi',
                ]
            );
        }

        return back()->with('success', 'Tanggal untuk ' . $jumlahPertemuan . ' pertemuan berhasil diatur');
    }
}
