<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use App\Models\PelanggaranPegawai;
use App\Models\SanksiPegawai;
use App\Models\Dosen;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = PelanggaranPegawai::with(['dosen', 'pegawai', 'jenisPelanggaran', 'dilaporkanOleh', 'sanksi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('jenis_pelanggaran_id')) {
            $query->where('jenis_pelanggaran_id', $request->jenis_pelanggaran_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('dosen', fn($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('pegawai', fn($q) => $q->where('nama', 'like', "%{$search}%"));
            });
        }

        $pelanggaranList = $query->orderBy('created_at', 'desc')->paginate(15);
        $jenisPelanggaranList = JenisPelanggaran::active()->get();
        $dosenList = Dosen::orderBy('nama')->get();
        $pegawaiList = Pegawai::orderBy('nama')->get();

        $stats = [
            'total' => PelanggaranPegawai::count(),
            'dilaporkan' => PelanggaranPegawai::where('status', 'dilaporkan')->count(),
            'investigasi' => PelanggaranPegawai::where('status', 'investigasi')->count(),
            'terbukti' => PelanggaranPegawai::where('status', 'terbukti')->count(),
        ];

        return view('kepegawaian.pelanggaran.index', compact('pelanggaranList', 'jenisPelanggaranList', 'dosenList', 'pegawaiList', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_pegawai' => 'required|in:dosen,pegawai',
            'dosen_id' => 'required_if:tipe_pegawai,dosen|nullable|exists:dosen,id',
            'pegawai_id' => 'required_if:tipe_pegawai,pegawai|nullable|exists:pegawai,id',
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,id',
            'tanggal_pelanggaran' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|string',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only(['jenis_pelanggaran_id', 'tanggal_pelanggaran', 'deskripsi', 'bukti']);
        $data['status'] = 'dilaporkan';
        $data['dilaporkan_oleh'] = auth()->id();

        if ($request->tipe_pegawai === 'dosen') {
            $data['dosen_id'] = $request->dosen_id;
        } else {
            $data['pegawai_id'] = $request->pegawai_id;
        }

        if ($request->hasFile('file_bukti')) {
            $data['file_bukti'] = $request->file('file_bukti')->store('pelanggaran', 'public');
        }

        PelanggaranPegawai::create($data);

        return redirect()->route('kepegawaian.pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dilaporkan.');
    }

    public function show(PelanggaranPegawai $pelanggaran)
    {
        $pelanggaran->load(['dosen', 'pegawai', 'jenisPelanggaran', 'dilaporkanOleh', 'sanksi.ditetapkanOleh']);
        return view('kepegawaian.pelanggaran.show', compact('pelanggaran'));
    }

    public function update(Request $request, PelanggaranPegawai $pelanggaran)
    {
        $request->validate([
            'jenis_pelanggaran_id' => 'required|exists:jenis_pelanggaran,id',
            'tanggal_pelanggaran' => 'required|date',
            'deskripsi' => 'required|string',
            'bukti' => 'nullable|string',
            'status' => 'required|in:dilaporkan,investigasi,terbukti,tidak_terbukti,selesai',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = $request->only(['jenis_pelanggaran_id', 'tanggal_pelanggaran', 'deskripsi', 'bukti', 'status']);

        if ($request->hasFile('file_bukti')) {
            if ($pelanggaran->file_bukti) {
                Storage::disk('public')->delete($pelanggaran->file_bukti);
            }
            $data['file_bukti'] = $request->file('file_bukti')->store('pelanggaran', 'public');
        }

        $pelanggaran->update($data);

        return redirect()->route('kepegawaian.pelanggaran.show', $pelanggaran)
            ->with('success', 'Pelanggaran berhasil diperbarui.');
    }

    public function destroy(PelanggaranPegawai $pelanggaran)
    {
        if ($pelanggaran->file_bukti) {
            Storage::disk('public')->delete($pelanggaran->file_bukti);
        }
        
        $pelanggaran->delete();

        return redirect()->route('kepegawaian.pelanggaran.index')
            ->with('success', 'Pelanggaran berhasil dihapus.');
    }

    public function updateStatus(Request $request, PelanggaranPegawai $pelanggaran)
    {
        $request->validate([
            'status' => 'required|in:dilaporkan,investigasi,terbukti,tidak_terbukti,selesai',
        ]);

        $pelanggaran->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status pelanggaran berhasil diperbarui.');
    }

    // Sanksi
    public function sanksiStore(Request $request, PelanggaranPegawai $pelanggaran)
    {
        $request->validate([
            'jenis_sanksi' => 'required|in:teguran_lisan,teguran_tertulis,penundaan_kgb,penundaan_pangkat,penurunan_pangkat,pembebasan_jabatan,pemberhentian_hormat,pemberhentian_tidak_hormat',
            'nomor_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->only(['jenis_sanksi', 'nomor_sk', 'tanggal_sk', 'tanggal_mulai', 'tanggal_berakhir', 'keterangan']);
        $data['pelanggaran_pegawai_id'] = $pelanggaran->id;
        $data['status'] = 'draft';
        $data['ditetapkan_oleh'] = auth()->id();

        if ($request->hasFile('file_sk')) {
            $data['file_sk'] = $request->file('file_sk')->store('sanksi', 'public');
        }

        SanksiPegawai::create($data);

        return redirect()->route('kepegawaian.pelanggaran.show', $pelanggaran)
            ->with('success', 'Sanksi berhasil ditambahkan.');
    }

    public function sanksiUpdate(Request $request, SanksiPegawai $sanksi)
    {
        $request->validate([
            'jenis_sanksi' => 'required|in:teguran_lisan,teguran_tertulis,penundaan_kgb,penundaan_pangkat,penurunan_pangkat,pembebasan_jabatan,pemberhentian_hormat,pemberhentian_tidak_hormat',
            'nomor_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_mulai',
            'status' => 'required|in:draft,aktif,selesai,dibatalkan',
            'keterangan' => 'nullable|string',
            'file_sk' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = $request->only(['jenis_sanksi', 'nomor_sk', 'tanggal_sk', 'tanggal_mulai', 'tanggal_berakhir', 'status', 'keterangan']);

        if ($request->hasFile('file_sk')) {
            if ($sanksi->file_sk) {
                Storage::disk('public')->delete($sanksi->file_sk);
            }
            $data['file_sk'] = $request->file('file_sk')->store('sanksi', 'public');
        }

        $sanksi->update($data);

        return redirect()->back()->with('success', 'Sanksi berhasil diperbarui.');
    }

    public function sanksiDestroy(SanksiPegawai $sanksi)
    {
        $pelanggaran = $sanksi->pelanggaranPegawai;
        
        if ($sanksi->file_sk) {
            Storage::disk('public')->delete($sanksi->file_sk);
        }
        
        $sanksi->delete();

        return redirect()->route('kepegawaian.pelanggaran.show', $pelanggaran)
            ->with('success', 'Sanksi berhasil dihapus.');
    }

    // Jenis Pelanggaran
    public function jenisIndex()
    {
        $jenisList = JenisPelanggaran::paginate(15);
        return view('kepegawaian.pelanggaran.jenis', compact('jenisList'));
    }

    public function jenisStore(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:jenis_pelanggaran,kode',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tingkat' => 'required|in:ringan,sedang,berat,sangat_berat',
        ]);

        JenisPelanggaran::create($request->all());

        return redirect()->route('kepegawaian.pelanggaran.jenis.index')
            ->with('success', 'Jenis pelanggaran berhasil ditambahkan.');
    }

    public function jenisUpdate(Request $request, JenisPelanggaran $jenis)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:jenis_pelanggaran,kode,' . $jenis->id,
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'tingkat' => 'required|in:ringan,sedang,berat,sangat_berat',
            'is_active' => 'required|boolean',
        ]);

        $jenis->update($request->all());

        return redirect()->route('kepegawaian.pelanggaran.jenis.index')
            ->with('success', 'Jenis pelanggaran berhasil diperbarui.');
    }

    public function jenisDestroy(JenisPelanggaran $jenis)
    {
        if ($jenis->pelanggaranPegawai()->exists()) {
            return redirect()->back()->with('error', 'Jenis pelanggaran tidak dapat dihapus karena sudah digunakan.');
        }

        $jenis->delete();
        return redirect()->route('kepegawaian.pelanggaran.jenis.index')
            ->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }
}
