<?php

namespace App\Http\Controllers;

use App\Models\JenisKegiatanLapangan;
use App\Models\PeriodeKegiatanLapangan;
use App\Models\MitraKegiatan;
use App\Models\PendaftaranKegiatanLapangan;
use App\Models\LogKegiatanLapangan;
use App\Models\PenilaianKegiatanLapangan;
use App\Models\TahunAkademik;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KegiatanLapanganController extends Controller
{
    // ========== JENIS KEGIATAN ==========
    public function jenisIndex()
    {
        $jenis = JenisKegiatanLapangan::orderBy('nama')->get();
        return view('admin.kegiatan-lapangan.jenis.index', compact('jenis'));
    }

    public function jenisStore(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:jenis_kegiatan_lapangan,kode|max:20',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'sks' => 'required|integer|min:0|max:12',
            'durasi_minggu' => 'required|integer|min:1|max:52',
            'semester_minimal' => 'required|integer|min:1|max:14',
            'sks_minimal' => 'required|integer|min:0|max:160',
        ]);

        JenisKegiatanLapangan::create($request->all());

        return back()->with('success', 'Jenis kegiatan berhasil ditambahkan!');
    }

    public function jenisUpdate(Request $request, JenisKegiatanLapangan $jenisKegiatanLapangan)
    {
        $request->validate([
            'kode' => 'required|max:20|unique:jenis_kegiatan_lapangan,kode,' . $jenisKegiatanLapangan->id,
            'nama' => 'required|string|max:100',
            'sks' => 'required|integer|min:0|max:12',
            'durasi_minggu' => 'required|integer|min:1|max:52',
            'semester_minimal' => 'required|integer|min:1|max:14',
            'sks_minimal' => 'required|integer|min:0|max:160',
        ]);

        $jenisKegiatanLapangan->update($request->all());

        return back()->with('success', 'Jenis kegiatan berhasil diupdate!');
    }

    public function jenisDestroy(JenisKegiatanLapangan $jenisKegiatanLapangan)
    {
        $jenisKegiatanLapangan->delete();
        return back()->with('success', 'Jenis kegiatan berhasil dihapus!');
    }

    // ========== MITRA KEGIATAN ==========
    public function mitraIndex(Request $request)
    {
        $query = MitraKegiatan::orderBy('nama');
        
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('kota', 'like', "%{$request->search}%")
                    ->orWhere('bidang_usaha', 'like', "%{$request->search}%");
            });
        }

        $mitras = $query->paginate(15);
        return view('admin.kegiatan-lapangan.mitra.index', compact('mitras'));
    }

    public function mitraCreate()
    {
        return view('admin.kegiatan-lapangan.mitra.create');
    }

    public function mitraStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'nama_kontak' => 'nullable|string|max:100',
            'jabatan_kontak' => 'nullable|string|max:100',
            'bidang_usaha' => 'nullable|string',
            'kuota_mahasiswa' => 'nullable|integer|min:1',
        ]);

        MitraKegiatan::create([
            'kode' => MitraKegiatan::generateKode(),
            ...$request->all()
        ]);

        return redirect()->route('admin.kegiatan-lapangan.mitra.index')
            ->with('success', 'Mitra berhasil ditambahkan!');
    }

    public function mitraEdit(MitraKegiatan $mitraKegiatan)
    {
        return view('admin.kegiatan-lapangan.mitra.edit', ['mitra' => $mitraKegiatan]);
    }

    public function mitraUpdate(Request $request, MitraKegiatan $mitraKegiatan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
        ]);

        $mitraKegiatan->update($request->all());

        return redirect()->route('admin.kegiatan-lapangan.mitra.index')
            ->with('success', 'Mitra berhasil diupdate!');
    }

    public function mitraDestroy(MitraKegiatan $mitraKegiatan)
    {
        $mitraKegiatan->delete();
        return back()->with('success', 'Mitra berhasil dihapus!');
    }

    // ========== PERIODE KEGIATAN ==========
    public function periodeIndex(Request $request)
    {
        $query = PeriodeKegiatanLapangan::with(['jenisKegiatan', 'tahunAkademik'])
            ->orderBy('created_at', 'desc');

        if ($request->jenis) {
            $query->where('jenis_kegiatan_id', $request->jenis);
        }

        $periodes = $query->paginate(15);
        $jenisKegiatan = JenisKegiatanLapangan::active()->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();

        return view('admin.kegiatan-lapangan.periode.index', compact('periodes', 'jenisKegiatan', 'tahunAkademik'));
    }

    public function periodeCreate()
    {
        $jenisKegiatan = JenisKegiatanLapangan::active()->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        
        return view('admin.kegiatan-lapangan.periode.create', compact('jenisKegiatan', 'tahunAkademik'));
    }

    public function periodeStore(Request $request)
    {
        $request->validate([
            'jenis_kegiatan_id' => 'required|exists:jenis_kegiatan_lapangan,id',
            'tahun_akademik_id' => 'required|exists:tahun_akademik,id',
            'nama' => 'required|string|max:255',
            'tanggal_mulai_daftar' => 'required|date',
            'tanggal_selesai_daftar' => 'required|date|after:tanggal_mulai_daftar',
            'tanggal_mulai_kegiatan' => 'required|date',
            'tanggal_selesai_kegiatan' => 'required|date|after:tanggal_mulai_kegiatan',
            'kuota' => 'nullable|integer|min:1',
        ]);

        PeriodeKegiatanLapangan::create($request->all());

        return redirect()->route('admin.kegiatan-lapangan.periode.index')
            ->with('success', 'Periode kegiatan berhasil ditambahkan!');
    }

    public function periodeShow(PeriodeKegiatanLapangan $periodeKegiatanLapangan)
    {
        $periodeKegiatanLapangan->load(['jenisKegiatan', 'tahunAkademik', 'pendaftaran.mahasiswa', 'pendaftaran.mitraDiterima', 'pendaftaran.dosenPembimbing']);
        $dosens = Dosen::orderBy('nama')->get();
        $mitras = MitraKegiatan::active()->get();

        return view('admin.kegiatan-lapangan.periode.show', ['periode' => $periodeKegiatanLapangan, 'dosens' => $dosens, 'mitras' => $mitras]);
    }

    public function periodeEdit(PeriodeKegiatanLapangan $periodeKegiatanLapangan)
    {
        $jenisKegiatan = JenisKegiatanLapangan::active()->get();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        
        return view('admin.kegiatan-lapangan.periode.edit', ['periode' => $periodeKegiatanLapangan, 'jenisKegiatan' => $jenisKegiatan, 'tahunAkademik' => $tahunAkademik]);
    }

    public function periodeUpdate(Request $request, PeriodeKegiatanLapangan $periodeKegiatanLapangan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'tanggal_mulai_daftar' => 'required|date',
            'tanggal_selesai_daftar' => 'required|date',
            'tanggal_mulai_kegiatan' => 'required|date',
            'tanggal_selesai_kegiatan' => 'required|date',
            'status' => 'required|in:draft,dibuka,ditutup,selesai',
        ]);

        $periodeKegiatanLapangan->update($request->all());

        return redirect()->route('admin.kegiatan-lapangan.periode.index')
            ->with('success', 'Periode kegiatan berhasil diupdate!');
    }

    public function periodeDestroy(PeriodeKegiatanLapangan $periodeKegiatanLapangan)
    {
        $periodeKegiatanLapangan->delete();
        return back()->with('success', 'Periode kegiatan berhasil dihapus!');
    }

    // ========== PENDAFTARAN (ADMIN) ==========
    public function pendaftaranIndex(Request $request)
    {
        $query = PendaftaranKegiatanLapangan::with(['periode.jenisKegiatan', 'mahasiswa', 'mitraDiterima', 'dosenPembimbing'])
            ->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->jenis) {
            $query->whereHas('periode', function($q) use ($request) {
                $q->where('jenis_kegiatan_id', $request->jenis);
            });
        }

        if ($request->search) {
            $query->whereHas('mahasiswa', function($q) use ($request) {
                $q->where('nim', 'like', "%{$request->search}%")
                  ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        $pendaftarans = $query->paginate(15);
        $jenisKegiatan = JenisKegiatanLapangan::active()->get();

        return view('admin.kegiatan-lapangan.pendaftaran.index', compact('pendaftarans', 'jenisKegiatan'));
    }

    public function pendaftaranShow(PendaftaranKegiatanLapangan $pendaftaranKegiatanLapangan)
    {
        $pendaftaranKegiatanLapangan->load([
            'periode.jenisKegiatan', 
            'mahasiswa.programStudi', 
            'mitraPilihan1', 
            'mitraPilihan2', 
            'mitraPilihan3',
            'mitraDiterima', 
            'dosenPembimbing',
            'logKegiatan',
            'penilaian'
        ]);

        $dosens = Dosen::orderBy('nama')->get();
        $mitras = MitraKegiatan::active()->get();

        return view('admin.kegiatan-lapangan.pendaftaran.show', ['pendaftaran' => $pendaftaranKegiatanLapangan, 'dosens' => $dosens, 'mitras' => $mitras]);
    }

    public function pendaftaranProses(Request $request, PendaftaranKegiatanLapangan $pendaftaranKegiatanLapangan)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'mitra_diterima' => 'required_if:status,disetujui|nullable|exists:mitra_kegiatan,id',
            'dosen_pembimbing_id' => 'required_if:status,disetujui|nullable|exists:dosen,id',
            'catatan' => 'nullable|string',
        ]);

        $pendaftaranKegiatanLapangan->update([
            'status' => $request->status,
            'mitra_diterima' => $request->mitra_diterima,
            'dosen_pembimbing_id' => $request->dosen_pembimbing_id,
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Pendaftaran berhasil diproses!');
    }

    public function pendaftaranMulai(PendaftaranKegiatanLapangan $pendaftaranKegiatanLapangan)
    {
        if ($pendaftaranKegiatanLapangan->status != 'disetujui') {
            return back()->with('error', 'Status pendaftaran harus disetujui!');
        }

        $pendaftaranKegiatanLapangan->status = 'berlangsung';
        $pendaftaranKegiatanLapangan->save();

        return back()->with('success', 'Kegiatan dimulai!');
    }

    public function pendaftaranSelesai(PendaftaranKegiatanLapangan $pendaftaranKegiatanLapangan)
    {
        if ($pendaftaranKegiatanLapangan->status != 'berlangsung') {
            return back()->with('error', 'Status pendaftaran harus berlangsung!');
        }

        $pendaftaranKegiatanLapangan->status = 'selesai';
        $pendaftaranKegiatanLapangan->save();

        return back()->with('success', 'Kegiatan selesai!');
    }

    // ========== PENILAIAN ==========
    public function penilaianStore(Request $request, PendaftaranKegiatanLapangan $pendaftaranKegiatanLapangan)
    {
        $request->validate([
            'jenis_penilai' => 'required|in:dosen,lapangan',
            'nilai_kedisiplinan' => 'required|numeric|min:0|max:100',
            'nilai_kerjasama' => 'required|numeric|min:0|max:100',
            'nilai_inisiatif' => 'required|numeric|min:0|max:100',
            'nilai_keterampilan' => 'required|numeric|min:0|max:100',
            'nilai_hasil_kerja' => 'required|numeric|min:0|max:100',
            'nilai_laporan' => 'required|numeric|min:0|max:100',
            'nilai_presentasi' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string',
        ]);

        $penilaian = PenilaianKegiatanLapangan::updateOrCreate(
            [
                'pendaftaran_id' => $pendaftaranKegiatanLapangan->id,
                'jenis_penilai' => $request->jenis_penilai,
            ],
            $request->except('jenis_penilai')
        );

        $penilaian->hitungNilaiAkhir();
        $penilaian->tanggal_penilaian = now();
        $penilaian->save();

        return back()->with('success', 'Penilaian berhasil disimpan!');
    }

    // ========== LOG KEGIATAN (ADMIN) ==========
    public function logApprove(LogKegiatanLapangan $logKegiatanLapangan)
    {
        $logKegiatanLapangan->status = 'disetujui';
        $logKegiatanLapangan->save();

        return back()->with('success', 'Log kegiatan disetujui!');
    }

    public function logRevisi(Request $request, LogKegiatanLapangan $logKegiatanLapangan)
    {
        $request->validate([
            'komentar_pembimbing' => 'required|string',
        ]);

        $logKegiatanLapangan->status = 'revisi';
        $logKegiatanLapangan->komentar_pembimbing = $request->komentar_pembimbing;
        $logKegiatanLapangan->save();

        return back()->with('success', 'Log kegiatan dikembalikan untuk revisi!');
    }
}
