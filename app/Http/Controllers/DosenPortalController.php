<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalKuliah;
use App\Models\TugasAkhir;
use App\Models\BimbinganAkademik;
use App\Models\BimbinganTA;
use App\Models\TahunAkademik;
use App\Models\Absensi;
use App\Models\Nilai;
use App\Models\Krs;
use App\Models\Pengumuman;
use App\Models\RiwayatPendidikan;
use App\Models\RiwayatJabatan;
use App\Models\RiwayatPangkat;
use App\Models\RiwayatPelatihan;
use App\Models\DokumenKepegawaian;
use App\Models\CutiPegawai;
use App\Models\SaldoCuti;
use App\Models\SlipGaji;
use App\Models\RekapEdom;
use App\Models\PeriodeEdom;
use App\Models\SkpPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DosenPortalController extends Controller
{
    /**
     * Get current logged in dosen
     */
    private function getDosen()
    {
        return Auth::user()->dosen;
    }

    // ==================== VALIDATION RULES & MESSAGES ====================

    /**
     * Validation rules for Riwayat Pendidikan
     */
    private function pendidikanRules($isUpdate = false)
    {
        return [
            'jenjang' => ['required', 'string', 'max:20', Rule::in(['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Profesi', 'Spesialis'])],
            'nama_institusi' => 'required|string|max:255|min:3',
            'program_studi' => 'required|string|max:255|min:3',
            'tahun_masuk' => 'required|integer|min:1950|max:' . date('Y'),
            'tahun_lulus' => 'nullable|integer|min:1950|max:' . (date('Y') + 10) . '|gte:tahun_masuk',
            'no_ijazah' => 'nullable|string|max:100',
            'judul_tugas_akhir' => 'nullable|string|max:500',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'file_ijazah' => ($isUpdate ? 'nullable' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Validation rules for Riwayat Jabatan
     */
    private function jabatanRules($isUpdate = false)
    {
        return [
            'nama_jabatan' => 'required|string|max:255|min:3',
            'jenis_jabatan' => ['required', Rule::in(['Struktural', 'Fungsional'])],
            'tmt_jabatan' => 'required|date|before_or_equal:today',
            'no_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date|before_or_equal:today|before_or_equal:tmt_jabatan',
            'pejabat_penetap' => 'nullable|string|max:255',
            'file_sk' => ($isUpdate ? 'nullable' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Validation rules for Riwayat Pangkat
     */
    private function pangkatRules($isUpdate = false)
    {
        return [
            'golongan' => ['required', 'string', 'max:20', Rule::in(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'])],
            'pangkat' => 'required|string|max:100|min:3',
            'tmt_pangkat' => 'required|date|before_or_equal:today',
            'no_sk' => 'nullable|string|max:100',
            'tanggal_sk' => 'nullable|date|before_or_equal:today|before_or_equal:tmt_pangkat',
            'pejabat_penetap' => 'nullable|string|max:255',
            'masa_kerja_tahun' => 'nullable|integer|min:0|max:50',
            'masa_kerja_bulan' => 'nullable|integer|min:0|max:11',
            'file_sk' => ($isUpdate ? 'nullable' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Validation rules for Riwayat Pelatihan
     */
    private function pelatihanRules($isUpdate = false)
    {
        return [
            'nama_pelatihan' => 'required|string|max:255|min:3',
            'jenis_pelatihan' => ['required', 'string', 'max:100', Rule::in(['Workshop', 'Seminar', 'Kursus', 'Diklat', 'Sertifikasi', 'Lokakarya', 'Bimtek', 'Lainnya'])],
            'penyelenggara' => 'required|string|max:255|min:3',
            'tanggal_mulai' => 'required|date|before_or_equal:today',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai|before_or_equal:today',
            'tempat' => 'nullable|string|max:255',
            'jam_pelatihan' => 'nullable|integer|min:1|max:1000',
            'no_sertifikat' => 'nullable|string|max:100',
            'file_sertifikat' => ($isUpdate ? 'nullable' : 'nullable') . '|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Validation rules for Dokumen Kepegawaian
     */
    private function dokumenRules($isUpdate = false)
    {
        return [
            'jenis_dokumen' => ['required', 'string', 'max:100', Rule::in(['KTP', 'KK', 'NPWP', 'BPJS Kesehatan', 'BPJS Ketenagakerjaan', 'SK CPNS', 'SK PNS', 'SK Pengangkatan', 'Sertifikat Pendidik', 'Sertifikat Profesi', 'Ijazah', 'Transkrip', 'Akta Kelahiran', 'Surat Nikah', 'Lainnya'])],
            'nama_dokumen' => 'required|string|max:255|min:3',
            'no_dokumen' => 'nullable|string|max:100',
            'tanggal_terbit' => 'nullable|date|before_or_equal:today',
            'tanggal_berlaku' => 'nullable|date|after_or_equal:tanggal_terbit',
            'instansi_penerbit' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string|max:500',
            'file_dokumen' => ($isUpdate ? 'nullable' : 'required') . '|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ];
    }

    /**
     * Custom validation messages in Indonesian
     */
    private function validationMessages()
    {
        return [
            // General
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka.',
            'numeric' => ':attribute harus berupa angka.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min karakter.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'file' => ':attribute harus berupa file.',
            'mimes' => ':attribute harus berformat: :values.',
            'in' => ':attribute yang dipilih tidak valid.',
            
            // Specific
            'tahun_masuk.min' => 'Tahun masuk tidak boleh kurang dari 1950.',
            'tahun_masuk.max' => 'Tahun masuk tidak boleh lebih dari tahun sekarang.',
            'tahun_lulus.gte' => 'Tahun lulus harus lebih besar atau sama dengan tahun masuk.',
            'tahun_lulus.max' => 'Tahun lulus tidak valid.',
            'ipk.min' => 'IPK tidak boleh kurang dari 0.',
            'ipk.max' => 'IPK tidak boleh lebih dari 4.',
            'file_ijazah.max' => 'Ukuran file ijazah maksimal 5MB.',
            
            'tmt_jabatan.before_or_equal' => 'TMT Jabatan tidak boleh lebih dari hari ini.',
            'tanggal_sk.before_or_equal' => 'Tanggal SK tidak boleh lebih dari TMT.',
            'file_sk.max' => 'Ukuran file SK maksimal 5MB.',
            
            'tmt_pangkat.before_or_equal' => 'TMT Pangkat tidak boleh lebih dari hari ini.',
            'masa_kerja_tahun.max' => 'Masa kerja tahun maksimal 50 tahun.',
            'masa_kerja_bulan.max' => 'Masa kerja bulan maksimal 11 bulan.',
            
            'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari ini.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'tanggal_selesai.before_or_equal' => 'Tanggal selesai tidak boleh lebih dari hari ini.',
            'jam_pelatihan.min' => 'Jam pelatihan minimal 1 jam.',
            'jam_pelatihan.max' => 'Jam pelatihan maksimal 1000 jam.',
            'file_sertifikat.max' => 'Ukuran file sertifikat maksimal 5MB.',
            
            'tanggal_terbit.before_or_equal' => 'Tanggal terbit tidak boleh lebih dari hari ini.',
            'tanggal_berlaku.after_or_equal' => 'Tanggal berlaku harus sama atau setelah tanggal terbit.',
            'file_dokumen.required' => 'File dokumen wajib diunggah.',
            'file_dokumen.max' => 'Ukuran file dokumen maksimal 10MB.',
        ];
    }

    /**
     * Custom attribute names in Indonesian
     */
    private function validationAttributes()
    {
        return [
            'jenjang' => 'Jenjang Pendidikan',
            'nama_institusi' => 'Nama Institusi',
            'program_studi' => 'Program Studi',
            'tahun_masuk' => 'Tahun Masuk',
            'tahun_lulus' => 'Tahun Lulus',
            'no_ijazah' => 'No. Ijazah',
            'judul_tugas_akhir' => 'Judul Tugas Akhir',
            'ipk' => 'IPK',
            'file_ijazah' => 'File Ijazah',
            'nama_jabatan' => 'Nama Jabatan',
            'jenis_jabatan' => 'Jenis Jabatan',
            'tmt_jabatan' => 'TMT Jabatan',
            'no_sk' => 'No. SK',
            'tanggal_sk' => 'Tanggal SK',
            'pejabat_penetap' => 'Pejabat Penetap',
            'file_sk' => 'File SK',
            'golongan' => 'Golongan',
            'pangkat' => 'Pangkat',
            'tmt_pangkat' => 'TMT Pangkat',
            'masa_kerja_tahun' => 'Masa Kerja (Tahun)',
            'masa_kerja_bulan' => 'Masa Kerja (Bulan)',
            'nama_pelatihan' => 'Nama Pelatihan',
            'jenis_pelatihan' => 'Jenis Pelatihan',
            'penyelenggara' => 'Penyelenggara',
            'tanggal_mulai' => 'Tanggal Mulai',
            'tanggal_selesai' => 'Tanggal Selesai',
            'tempat' => 'Tempat',
            'jam_pelatihan' => 'Jam Pelatihan',
            'no_sertifikat' => 'No. Sertifikat',
            'file_sertifikat' => 'File Sertifikat',
            'jenis_dokumen' => 'Jenis Dokumen',
            'nama_dokumen' => 'Nama Dokumen',
            'no_dokumen' => 'No. Dokumen',
            'tanggal_terbit' => 'Tanggal Terbit',
            'tanggal_berlaku' => 'Tanggal Berlaku',
            'instansi_penerbit' => 'Instansi Penerbit',
            'keterangan' => 'Keterangan',
            'file_dokumen' => 'File Dokumen',
        ];
    }

    /**
     * Dashboard Dosen - Unified Dashboard
     */
    public function dashboard()
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $tahunAkademik = TahunAkademik::where('is_aktif', true)->first();
        
        // Pengumuman
        $pengumuman = Pengumuman::aktif()->latest()->take(5)->get();
        
        // Statistik Mengajar
        $jadwalMengajar = JadwalKuliah::where('dosen_id', $dosen->id)
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->with(['mataKuliah', 'ruangan'])
            ->get();
        
        $totalMataKuliah = $jadwalMengajar->pluck('mata_kuliah_id')->unique()->count();
        $totalSks = $jadwalMengajar->sum(fn($j) => $j->mataKuliah->sks ?? 0);
        $totalKelas = $jadwalMengajar->count();
        
        // Total mahasiswa yang diampu semester ini
        $jadwalIds = $jadwalMengajar->pluck('id');
        $totalMahasiswaDiampu = Krs::whereIn('jadwal_kuliah_id', $jadwalIds)
            ->where('status', 'Disetujui')
            ->distinct('mahasiswa_id')
            ->count('mahasiswa_id');
        
        // Mahasiswa Wali
        $mahasiswaWali = $dosen->mahasiswaWali()->where('status', 'Aktif')->count();
        $mahasiswaWaliIds = $dosen->mahasiswaWali()->pluck('id');
        $krsMenunggu = Krs::whereIn('mahasiswa_id', $mahasiswaWaliIds)
            ->where('tahun_akademik_id', $tahunAkademik?->id)
            ->where('status', 'Menunggu')
            ->count();
        
        // Bimbingan TA
        $bimbinganTA = TugasAkhir::where(function($q) use ($dosen) {
                $q->where('pembimbing_1_id', $dosen->id)
                  ->orWhere('pembimbing_2_id', $dosen->id);
            })
            ->whereNotIn('status', ['Lulus', 'Gagal'])
            ->count();
        
        // Jadwal Bimbingan TA Pending
        $jadwalBimbinganPending = BimbinganTA::where('dosen_id', $dosen->id)
            ->where('status', 'dijadwalkan')
            ->count();
        
        // Bimbingan Akademik belum direspon
        $bimbinganAkademik = BimbinganAkademik::whereHas('mahasiswa', function($q) use ($dosen) {
                $q->where('dosen_wali_id', $dosen->id);
            })
            ->where('status', 'Menunggu')
            ->count();
        
        // Jadwal hari ini
        $hariIni = now()->locale('id')->dayName;
        $jadwalHariIni = $jadwalMengajar->filter(fn($j) => strtolower($j->hari) === strtolower($hariIni))
            ->sortBy('jam_mulai');
        
        // Nilai yang belum diinput
        $krsIds = Krs::whereIn('jadwal_kuliah_id', $jadwalIds)
            ->where('status', 'Disetujui')
            ->pluck('id');
        $nilaiPending = Nilai::whereIn('krs_id', $krsIds)
            ->whereNull('nilai_akhir')
            ->count();
        
        $nilaiTerisi = Nilai::whereIn('krs_id', $krsIds)
            ->whereNotNull('nilai_akhir')
            ->count();
        
        // Rekap Absensi per Mata Kuliah
        $rekapAbsensi = $jadwalMengajar->map(function($jadwal) {
            $krsIds = Krs::where('jadwal_kuliah_id', $jadwal->id)
                ->where('status', 'Disetujui')
                ->pluck('id');
            
            $totalPertemuan = Absensi::whereIn('krs_id', $krsIds)
                ->distinct('pertemuan')
                ->count('pertemuan');
            $totalMahasiswa = $krsIds->count();
            
            return [
                'mata_kuliah' => $jadwal->mataKuliah->nama ?? '-',
                'kode' => $jadwal->mataKuliah->kode ?? '-',
                'sks' => $jadwal->mataKuliah->sks ?? 0,
                'kelas' => $jadwal->kelas,
                'hari' => $jadwal->hari,
                'jam' => substr($jadwal->jam_mulai, 0, 5) . ' - ' . substr($jadwal->jam_selesai, 0, 5),
                'ruangan' => $jadwal->ruangan->nama ?? '-',
                'total_pertemuan' => $totalPertemuan,
                'total_mahasiswa' => $totalMahasiswa,
                'jadwal' => $jadwal
            ];
        });
        
        // Progress Mahasiswa Tugas Akhir yang dibimbing
        $progressTA = TugasAkhir::where(function($q) use ($dosen) {
                $q->where('pembimbing_1_id', $dosen->id)
                  ->orWhere('pembimbing_2_id', $dosen->id);
            })
            ->with(['mahasiswa', 'mahasiswa.programStudi'])
            ->orderByRaw("FIELD(status, 'Pengajuan', 'Bimbingan', 'Sidang Proposal', 'Revisi Proposal', 'Penelitian', 'Sidang Hasil', 'Revisi Hasil', 'Sidang Akhir', 'Revisi Akhir', 'Lulus')")
            ->take(5)
            ->get();

        return view('dosen.portal.dashboard', compact(
            'dosen',
            'tahunAkademik',
            'pengumuman',
            'jadwalMengajar',
            'totalMataKuliah',
            'totalSks',
            'totalKelas',
            'totalMahasiswaDiampu',
            'mahasiswaWali',
            'krsMenunggu',
            'bimbinganTA',
            'jadwalBimbinganPending',
            'bimbinganAkademik',
            'jadwalHariIni',
            'nilaiPending',
            'nilaiTerisi',
            'rekapAbsensi',
            'progressTA'
        ));
    }

    /**
     * Profil Dosen
     */
    public function profil()
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $dosen->load([
            'programStudi.fakultas',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'kelurahan',
            'riwayatPendidikan',
            'riwayatJabatan',
            'riwayatPangkat',
        ]);

        return view('dosen.portal.profil', compact('dosen'));
    }

    /**
     * Form Edit Profil
     */
    public function editProfil()
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $dosen->load(['provinsi', 'kabupaten', 'kecamatan', 'kelurahan']);

        return view('dosen.portal.edit-profil', compact('dosen'));
    }

    /**
     * Update Profil
     */
    public function updateProfil(Request $request)
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $request->validate([
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:20',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'bidang_keahlian' => 'nullable|string|max:255',
            'sinta_id' => 'nullable|string|max:50',
            'scopus_id' => 'nullable|string|max:50',
            'google_scholar_id' => 'nullable|string|max:100',
            'orcid' => 'nullable|string|max:50',
            'no_rekening' => 'nullable|string|max:50',
            'nama_bank' => 'nullable|string|max:100',
            'atas_nama_rekening' => 'nullable|string|max:100',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'alamat', 'telepon', 'no_hp', 'email',
            'bidang_keahlian', 'sinta_id', 'scopus_id', 'google_scholar_id', 'orcid',
            'no_rekening', 'nama_bank', 'atas_nama_rekening'
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($dosen->foto) {
                Storage::disk('public')->delete($dosen->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-dosen', 'public');
        }

        $dosen->update($data);

        return redirect()->route('dosen.profil')
            ->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai!']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('dosen.profil')
            ->with('success', 'Password berhasil diubah!');
    }

    /**
     * Kepegawaian - Lihat Riwayat
     */
    public function kepegawaian()
    {
        $dosen = $this->getDosen();
        
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $dosen->load([
            'riwayatPendidikan' => fn($q) => $q->orderBy('tahun_lulus', 'desc'),
            'riwayatJabatan' => fn($q) => $q->orderBy('tmt_jabatan', 'desc'),
            'riwayatPangkat' => fn($q) => $q->orderBy('tmt_pangkat', 'desc'),
            'riwayatPelatihan' => fn($q) => $q->orderBy('tanggal_mulai', 'desc'),
            'dokumenKepegawaian' => fn($q) => $q->orderBy('tanggal_terbit', 'desc'),
        ]);

        return view('dosen.portal.kepegawaian', compact('dosen'));
    }

    // ==================== RIWAYAT PENDIDIKAN ====================
    
    /**
     * Store Riwayat Pendidikan
     */
    public function storeRiwayatPendidikan(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate(
            $this->pendidikanRules(false),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'jenjang', 'nama_institusi', 'program_studi', 'tahun_masuk', 
            'tahun_lulus', 'no_ijazah', 'judul_tugas_akhir', 'ipk'
        ]);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_ijazah')) {
            $data['file_ijazah'] = $request->file('file_ijazah')->store('kepegawaian/ijazah', 'public');
        }

        RiwayatPendidikan::create($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pendidikan berhasil ditambahkan!');
    }

    /**
     * Update Riwayat Pendidikan
     */
    public function updateRiwayatPendidikan(Request $request, RiwayatPendidikan $riwayatPendidikan)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatPendidikan->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        $validated = $request->validate(
            $this->pendidikanRules(true),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'jenjang', 'nama_institusi', 'program_studi', 'tahun_masuk', 
            'tahun_lulus', 'no_ijazah', 'judul_tugas_akhir', 'ipk'
        ]);

        if ($request->hasFile('file_ijazah')) {
            if ($riwayatPendidikan->file_ijazah) {
                Storage::disk('public')->delete($riwayatPendidikan->file_ijazah);
            }
            $data['file_ijazah'] = $request->file('file_ijazah')->store('kepegawaian/ijazah', 'public');
        }

        $riwayatPendidikan->update($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pendidikan berhasil diperbarui!');
    }

    /**
     * Delete Riwayat Pendidikan
     */
    public function destroyRiwayatPendidikan(RiwayatPendidikan $riwayatPendidikan)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatPendidikan->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        if ($riwayatPendidikan->file_ijazah) {
            Storage::disk('public')->delete($riwayatPendidikan->file_ijazah);
        }

        $riwayatPendidikan->delete();

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pendidikan berhasil dihapus!');
    }

    // ==================== RIWAYAT JABATAN ====================
    
    /**
     * Store Riwayat Jabatan
     */
    public function storeRiwayatJabatan(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate(
            $this->jabatanRules(false),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'nama_jabatan', 'jenis_jabatan', 'tmt_jabatan', 
            'no_sk', 'tanggal_sk', 'pejabat_penetap'
        ]);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_sk')) {
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/jabatan', 'public');
        }

        RiwayatJabatan::create($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat jabatan berhasil ditambahkan!');
    }

    /**
     * Update Riwayat Jabatan
     */
    public function updateRiwayatJabatan(Request $request, RiwayatJabatan $riwayatJabatan)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatJabatan->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        $validated = $request->validate(
            $this->jabatanRules(true),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'nama_jabatan', 'jenis_jabatan', 'tmt_jabatan', 
            'no_sk', 'tanggal_sk', 'pejabat_penetap'
        ]);

        if ($request->hasFile('file_sk')) {
            if ($riwayatJabatan->file_sk) {
                Storage::disk('public')->delete($riwayatJabatan->file_sk);
            }
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/jabatan', 'public');
        }

        $riwayatJabatan->update($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat jabatan berhasil diperbarui!');
    }

    /**
     * Delete Riwayat Jabatan
     */
    public function destroyRiwayatJabatan(RiwayatJabatan $riwayatJabatan)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatJabatan->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        if ($riwayatJabatan->file_sk) {
            Storage::disk('public')->delete($riwayatJabatan->file_sk);
        }

        $riwayatJabatan->delete();

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat jabatan berhasil dihapus!');
    }

    // ==================== RIWAYAT PANGKAT ====================
    
    /**
     * Store Riwayat Pangkat
     */
    public function storeRiwayatPangkat(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate(
            $this->pangkatRules(false),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'golongan', 'pangkat', 'tmt_pangkat', 
            'no_sk', 'tanggal_sk', 'pejabat_penetap',
            'masa_kerja_tahun', 'masa_kerja_bulan'
        ]);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_sk')) {
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/pangkat', 'public');
        }

        RiwayatPangkat::create($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pangkat berhasil ditambahkan!');
    }

    /**
     * Update Riwayat Pangkat
     */
    public function updateRiwayatPangkat(Request $request, RiwayatPangkat $riwayatPangkat)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatPangkat->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        $validated = $request->validate(
            $this->pangkatRules(true),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'golongan', 'pangkat', 'tmt_pangkat', 
            'no_sk', 'tanggal_sk', 'pejabat_penetap',
            'masa_kerja_tahun', 'masa_kerja_bulan'
        ]);

        if ($request->hasFile('file_sk')) {
            if ($riwayatPangkat->file_sk) {
                Storage::disk('public')->delete($riwayatPangkat->file_sk);
            }
            $data['file_sk'] = $request->file('file_sk')->store('kepegawaian/pangkat', 'public');
        }

        $riwayatPangkat->update($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pangkat berhasil diperbarui!');
    }

    /**
     * Delete Riwayat Pangkat
     */
    public function destroyRiwayatPangkat(RiwayatPangkat $riwayatPangkat)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatPangkat->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        if ($riwayatPangkat->file_sk) {
            Storage::disk('public')->delete($riwayatPangkat->file_sk);
        }

        $riwayatPangkat->delete();

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pangkat berhasil dihapus!');
    }

    // ==================== RIWAYAT PELATIHAN ====================
    
    /**
     * Store Riwayat Pelatihan
     */
    public function storeRiwayatPelatihan(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate(
            $this->pelatihanRules(false),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'nama_pelatihan', 'jenis_pelatihan', 'penyelenggara', 
            'tanggal_mulai', 'tanggal_selesai', 'tempat',
            'jam_pelatihan', 'no_sertifikat'
        ]);
        $data['dosen_id'] = $dosen->id;

        if ($request->hasFile('file_sertifikat')) {
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('kepegawaian/pelatihan', 'public');
        }

        RiwayatPelatihan::create($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pelatihan berhasil ditambahkan!');
    }

    /**
     * Update Riwayat Pelatihan
     */
    public function updateRiwayatPelatihan(Request $request, RiwayatPelatihan $riwayatPelatihan)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatPelatihan->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        $validated = $request->validate(
            $this->pelatihanRules(true),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'nama_pelatihan', 'jenis_pelatihan', 'penyelenggara', 
            'tanggal_mulai', 'tanggal_selesai', 'tempat',
            'jam_pelatihan', 'no_sertifikat'
        ]);

        if ($request->hasFile('file_sertifikat')) {
            if ($riwayatPelatihan->file_sertifikat) {
                Storage::disk('public')->delete($riwayatPelatihan->file_sertifikat);
            }
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('kepegawaian/pelatihan', 'public');
        }

        $riwayatPelatihan->update($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pelatihan berhasil diperbarui!');
    }

    /**
     * Delete Riwayat Pelatihan
     */
    public function destroyRiwayatPelatihan(RiwayatPelatihan $riwayatPelatihan)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $riwayatPelatihan->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        if ($riwayatPelatihan->file_sertifikat) {
            Storage::disk('public')->delete($riwayatPelatihan->file_sertifikat);
        }

        $riwayatPelatihan->delete();

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Riwayat pelatihan berhasil dihapus!');
    }

    // ==================== DOKUMEN KEPEGAWAIAN ====================
    
    /**
     * Store Dokumen Kepegawaian
     */
    public function storeDokumen(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $validated = $request->validate(
            $this->dokumenRules(false),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'jenis_dokumen', 'nama_dokumen', 'no_dokumen', 
            'tanggal_terbit', 'tanggal_berlaku', 'instansi_penerbit', 'keterangan'
        ]);
        $data['dosen_id'] = $dosen->id;
        $data['file_dokumen'] = $request->file('file_dokumen')->store('kepegawaian/dokumen', 'public');

        DokumenKepegawaian::create($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Dokumen berhasil ditambahkan!');
    }

    /**
     * Update Dokumen Kepegawaian
     */
    public function updateDokumen(Request $request, DokumenKepegawaian $dokumen)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $dokumen->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        $validated = $request->validate(
            $this->dokumenRules(true),
            $this->validationMessages(),
            $this->validationAttributes()
        );

        $data = $request->only([
            'jenis_dokumen', 'nama_dokumen', 'no_dokumen', 
            'tanggal_terbit', 'tanggal_berlaku', 'instansi_penerbit', 'keterangan'
        ]);

        if ($request->hasFile('file_dokumen')) {
            if ($dokumen->file_dokumen) {
                Storage::disk('public')->delete($dokumen->file_dokumen);
            }
            $data['file_dokumen'] = $request->file('file_dokumen')->store('kepegawaian/dokumen', 'public');
        }

        $dokumen->update($data);

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Dokumen berhasil diperbarui!');
    }

    /**
     * Delete Dokumen Kepegawaian
     */
    public function destroyDokumen(DokumenKepegawaian $dokumen)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $dokumen->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.kepegawaian')->with('error', 'Akses ditolak!');
        }

        if ($dokumen->file_dokumen) {
            Storage::disk('public')->delete($dokumen->file_dokumen);
        }

        $dokumen->delete();

        return redirect()->route('dosen.kepegawaian')
            ->with('success', 'Dokumen berhasil dihapus!');
    }

    // ==================== IZIN KELUAR ====================

    /**
     * List izin keluar dosen
     */
    public function izinKeluarIndex(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $query = \App\Models\IzinKeluar::where('dosen_id', $dosen->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $izinList = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => \App\Models\IzinKeluar::where('dosen_id', $dosen->id)->count(),
            'pending' => \App\Models\IzinKeluar::where('dosen_id', $dosen->id)->whereIn('status', ['diajukan', 'menunggu_admin'])->count(),
            'disetujui' => \App\Models\IzinKeluar::where('dosen_id', $dosen->id)->where('status', 'disetujui')->count(),
            'ditolak' => \App\Models\IzinKeluar::where('dosen_id', $dosen->id)->where('status', 'ditolak')->count(),
        ];

        return view('dosen.izin-keluar.index', compact('izinList', 'stats', 'dosen'));
    }

    /**
     * Store izin keluar dosen
     */
    public function izinKeluarStore(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan! Pastikan akun Anda terhubung dengan data dosen.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jam_keluar' => 'required',
            'jam_kembali' => 'required|after:jam_keluar',
            'keperluan' => 'required|in:dinas,pribadi,kesehatan,keluarga,lainnya',
            'keterangan' => 'required|string|min:10',
            'tujuan' => 'nullable|string|max:200',
        ], [
            'jam_kembali.after' => 'Jam kembali harus setelah jam keluar.',
            'keterangan.min' => 'Keterangan minimal 10 karakter.',
        ]);

        try {
            \App\Models\IzinKeluar::create([
                'dosen_id' => $dosen->id,
                'tanggal' => $request->tanggal,
                'jam_keluar' => $request->jam_keluar,
                'jam_kembali' => $request->jam_kembali,
                'keperluan' => $request->keperluan,
                'keterangan' => $request->keterangan,
                'tujuan' => $request->tujuan,
                'status' => 'diajukan',
                'status_kaprodi' => 'pending',
                'approval_level' => 'kaprodi',
            ]);

            return redirect()->route('dosen.izin-keluar.index')
                ->with('success', 'Izin keluar berhasil diajukan. Menunggu persetujuan Kaprodi.');
        } catch (\Exception $e) {
            \Log::error('Error creating IzinKeluar: ' . $e->getMessage(), [
                'dosen_id' => $dosen->id,
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Show detail izin keluar
     */
    public function izinKeluarShow(\App\Models\IzinKeluar $izinKeluar)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $izinKeluar->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.izin-keluar.index')->with('error', 'Akses ditolak!');
        }

        return view('dosen.izin-keluar.show', compact('izinKeluar', 'dosen'));
    }

    /**
     * Delete izin keluar (hanya jika masih diajukan)
     */
    public function izinKeluarDestroy(\App\Models\IzinKeluar $izinKeluar)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $izinKeluar->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.izin-keluar.index')->with('error', 'Akses ditolak!');
        }

        if ($izinKeluar->status !== 'diajukan') {
            return redirect()->route('dosen.izin-keluar.index')
                ->with('error', 'Izin keluar tidak dapat dibatalkan karena sudah diproses.');
        }

        $izinKeluar->delete();

        return redirect()->route('dosen.izin-keluar.index')
            ->with('success', 'Izin keluar berhasil dibatalkan.');
    }

    // ==================== LEMBUR ====================

    /**
     * List pengajuan lembur dosen
     */
    public function lemburIndex(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $query = \App\Models\PengajuanLembur::where('dosen_id', $dosen->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lemburList = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => \App\Models\PengajuanLembur::where('dosen_id', $dosen->id)->count(),
            'pending' => \App\Models\PengajuanLembur::where('dosen_id', $dosen->id)->whereIn('status', ['diajukan', 'menunggu_admin'])->count(),
            'disetujui' => \App\Models\PengajuanLembur::where('dosen_id', $dosen->id)->where('status', 'disetujui')->count(),
            'ditolak' => \App\Models\PengajuanLembur::where('dosen_id', $dosen->id)->where('status', 'ditolak')->count(),
            'total_jam_disetujui' => \App\Models\PengajuanLembur::where('dosen_id', $dosen->id)->where('status', 'disetujui')->sum('durasi_jam'),
        ];

        return view('dosen.lembur.index', compact('lemburList', 'stats', 'dosen'));
    }

    /**
     * Store pengajuan lembur dosen
     */
    public function lemburStore(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan! Pastikan akun Anda terhubung dengan data dosen.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'alasan' => 'required|string|min:10',
            'pekerjaan_yang_dilakukan' => 'nullable|string',
        ], [
            'alasan.min' => 'Alasan minimal 10 karakter.',
            'jam_selesai.after' => 'Jam selesai harus setelah jam mulai.',
        ]);

        try {
            \App\Models\PengajuanLembur::create([
                'dosen_id' => $dosen->id,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'alasan' => $request->alasan,
                'pekerjaan_yang_dilakukan' => $request->pekerjaan_yang_dilakukan,
                'status' => 'diajukan',
                'status_kaprodi' => 'pending',
                'approval_level' => 'kaprodi',
            ]);

            return redirect()->route('dosen.lembur.index')
                ->with('success', 'Pengajuan lembur berhasil diajukan. Menunggu persetujuan Kaprodi.');
        } catch (\Exception $e) {
            \Log::error('Error creating PengajuanLembur: ' . $e->getMessage(), [
                'dosen_id' => $dosen->id,
                'request' => $request->all()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Show detail lembur
     */
    public function lemburShow(\App\Models\PengajuanLembur $pengajuanLembur)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $pengajuanLembur->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.lembur.index')->with('error', 'Akses ditolak!');
        }

        $lembur = $pengajuanLembur;
        return view('dosen.lembur.show', compact('lembur', 'dosen'));
    }

    /**
     * Delete lembur (hanya jika masih diajukan)
     */
    public function lemburDestroy(\App\Models\PengajuanLembur $pengajuanLembur)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $pengajuanLembur->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.lembur.index')->with('error', 'Akses ditolak!');
        }

        if ($pengajuanLembur->status !== 'diajukan') {
            return redirect()->route('dosen.lembur.index')
                ->with('error', 'Pengajuan lembur tidak dapat dibatalkan karena sudah diproses.');
        }

        $pengajuanLembur->delete();

        return redirect()->route('dosen.lembur.index')
            ->with('success', 'Pengajuan lembur berhasil dibatalkan.');
    }

    // ==================== CUTI DOSEN ====================

    /**
     * Index cuti dosen
     */
    public function cutiIndex(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $query = CutiPegawai::where('dosen_id', $dosen->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_mulai', $request->tahun);
        }

        $cutiList = $query->orderBy('created_at', 'desc')->paginate(15);

        // Saldo cuti tahun ini
        $tahunIni = date('Y');
        $saldoCuti = SaldoCuti::where('dosen_id', $dosen->id)
            ->where('tahun', $tahunIni)
            ->first();

        if (!$saldoCuti) {
            // Create default saldo cuti jika belum ada
            $saldoCuti = SaldoCuti::create([
                'dosen_id' => $dosen->id,
                'tahun' => $tahunIni,
                'jatah_cuti' => 12,
                'cuti_digunakan' => 0,
                'sisa_cuti' => 12,
            ]);
        }

        $stats = [
            'total' => CutiPegawai::where('dosen_id', $dosen->id)->count(),
            'diajukan' => CutiPegawai::where('dosen_id', $dosen->id)->where('status', 'diajukan')->count(),
            'disetujui' => CutiPegawai::where('dosen_id', $dosen->id)->where('status', 'disetujui')->count(),
            'ditolak' => CutiPegawai::where('dosen_id', $dosen->id)->where('status', 'ditolak')->count(),
            'sisa_cuti' => $saldoCuti->sisa_cuti,
        ];

        return view('dosen.cuti.index', compact('cutiList', 'stats', 'dosen', 'saldoCuti'));
    }

    /**
     * Store pengajuan cuti dosen
     */
    public function cutiStore(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $request->validate([
            'jenis_cuti' => ['required', Rule::in(array_keys(CutiPegawai::JENIS_CUTI))],
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|min:10|max:1000',
            'alamat_selama_cuti' => 'nullable|string|max:500',
            'no_telepon_selama_cuti' => 'nullable|string|max:20',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'jenis_cuti.required' => 'Jenis cuti wajib dipilih.',
            'jenis_cuti.in' => 'Jenis cuti tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai harus hari ini atau setelahnya.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'alasan.min' => 'Alasan minimal 10 karakter.',
        ]);

        // Hitung jumlah hari
        $tanggalMulai = \Carbon\Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($request->tanggal_selesai);
        $jumlahHari = $tanggalMulai->diffInDays($tanggalSelesai) + 1;

        // Cek saldo cuti (hanya untuk cuti tahunan)
        if ($request->jenis_cuti === 'tahunan') {
            $saldoCuti = SaldoCuti::where('dosen_id', $dosen->id)
                ->where('tahun', $tanggalMulai->year)
                ->first();

            if (!$saldoCuti || $saldoCuti->sisa_cuti < $jumlahHari) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Sisa cuti tidak mencukupi. Sisa cuti Anda: ' . ($saldoCuti->sisa_cuti ?? 0) . ' hari.');
            }
        }

        // Upload dokumen jika ada
        $dokumenPath = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $dokumenPath = $request->file('dokumen_pendukung')->store('cuti-dokumen', 'public');
        }

        try {
            CutiPegawai::create([
                'dosen_id' => $dosen->id,
                'jenis_cuti' => $request->jenis_cuti,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'jumlah_hari' => $jumlahHari,
                'alasan' => $request->alasan,
                'alamat_selama_cuti' => $request->alamat_selama_cuti,
                'no_telepon_selama_cuti' => $request->no_telepon_selama_cuti,
                'dokumen_pendukung' => $dokumenPath,
                'status' => 'diajukan',
                'sisa_cuti_sebelum' => $saldoCuti->sisa_cuti ?? 0,
            ]);

            return redirect()->route('dosen.cuti.index')
                ->with('success', 'Pengajuan cuti berhasil diajukan. Menunggu persetujuan.');
        } catch (\Exception $e) {
            \Log::error('Error creating CutiPegawai: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Show detail cuti
     */
    public function cutiShow(CutiPegawai $cuti)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $cuti->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.cuti.index')->with('error', 'Akses ditolak!');
        }

        return view('dosen.cuti.show', compact('cuti', 'dosen'));
    }

    /**
     * Cancel cuti (hanya jika masih diajukan)
     */
    public function cutiDestroy(CutiPegawai $cuti)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $cuti->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.cuti.index')->with('error', 'Akses ditolak!');
        }

        if ($cuti->status !== 'diajukan') {
            return redirect()->route('dosen.cuti.index')
                ->with('error', 'Pengajuan cuti tidak dapat dibatalkan karena sudah diproses.');
        }

        // Hapus dokumen jika ada
        if ($cuti->dokumen_pendukung) {
            Storage::disk('public')->delete($cuti->dokumen_pendukung);
        }

        $cuti->delete();

        return redirect()->route('dosen.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dibatalkan.');
    }

    /**
     * Saldo cuti dosen
     */
    public function saldoCuti(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $tahun = $request->get('tahun', date('Y'));

        $saldoCutiList = SaldoCuti::where('dosen_id', $dosen->id)
            ->orderBy('tahun', 'desc')
            ->get();

        // Jika belum ada saldo tahun ini, buat default
        $saldoTahunIni = $saldoCutiList->where('tahun', date('Y'))->first();
        if (!$saldoTahunIni) {
            $saldoTahunIni = SaldoCuti::create([
                'dosen_id' => $dosen->id,
                'tahun' => date('Y'),
                'jatah_cuti' => 12,
                'cuti_digunakan' => 0,
                'sisa_cuti' => 12,
            ]);
            $saldoCutiList = SaldoCuti::where('dosen_id', $dosen->id)
                ->orderBy('tahun', 'desc')
                ->get();
        }

        // Riwayat cuti per tahun
        $riwayatCuti = CutiPegawai::where('dosen_id', $dosen->id)
            ->whereYear('tanggal_mulai', $tahun)
            ->whereIn('status', ['disetujui', 'disetujui_atasan'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('dosen.cuti.saldo', compact('saldoCutiList', 'saldoTahunIni', 'riwayatCuti', 'dosen', 'tahun'));
    }

    // ==================== SLIP GAJI DOSEN ====================

    /**
     * Index slip gaji dosen
     */
    public function slipGajiIndex(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $query = SlipGaji::where('dosen_id', $dosen->id);

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        $slipGajiList = $query->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->paginate(12);

        // Statistik
        $tahunIni = date('Y');
        $stats = [
            'total_slip' => SlipGaji::where('dosen_id', $dosen->id)->count(),
            'total_gaji_tahun_ini' => SlipGaji::where('dosen_id', $dosen->id)
                ->where('tahun', $tahunIni)
                ->where('status', 'dibayar')
                ->sum('gaji_bersih'),
            'slip_terakhir' => SlipGaji::where('dosen_id', $dosen->id)
                ->orderBy('tahun', 'desc')
                ->orderBy('bulan', 'desc')
                ->first(),
        ];

        // Daftar tahun untuk filter
        $tahunList = SlipGaji::where('dosen_id', $dosen->id)
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('dosen.slip-gaji.index', compact('slipGajiList', 'stats', 'dosen', 'tahunList'));
    }

    /**
     * Show detail slip gaji
     */
    public function slipGajiShow(SlipGaji $slipGaji)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $slipGaji->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.slip-gaji.index')->with('error', 'Akses ditolak!');
        }

        // Get komponen slip gaji via relationship
        $pendapatan = $slipGaji->details()->where('jenis', 'pendapatan')->get();
        $potongan = $slipGaji->details()->where('jenis', 'potongan')->get();

        return view('dosen.slip-gaji.show', compact('slipGaji', 'pendapatan', 'potongan', 'dosen'));
    }

    /**
     * Cetak slip gaji
     */
    public function slipGajiCetak(SlipGaji $slipGaji)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $slipGaji->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.slip-gaji.index')->with('error', 'Akses ditolak!');
        }

        // Get komponen slip gaji via relationship
        $pendapatan = $slipGaji->details()->where('jenis', 'pendapatan')->get();
        $potongan = $slipGaji->details()->where('jenis', 'potongan')->get();

        return view('cetak.slip-gaji', compact('slipGaji', 'pendapatan', 'potongan', 'dosen'));
    }

    // ==================== EDOM (EVALUASI DOSEN) ====================

    /**
     * Index hasil EDOM dosen
     */
    public function edomIndex(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        // Ambil periode EDOM aktif atau sesuai filter
        $periodeId = $request->get('periode_id');
        
        $periodeList = PeriodeEdom::orderBy('tanggal_mulai', 'desc')->get();
        
        $periodeAktif = $periodeId 
            ? PeriodeEdom::find($periodeId)
            : PeriodeEdom::where('status', 'aktif')->first() ?? $periodeList->first();

        // Rekap EDOM
        $rekapEdom = collect();
        if ($periodeAktif) {
            $rekapEdom = RekapEdom::where('dosen_id', $dosen->id)
                ->where('periode_edom_id', $periodeAktif->id)
                ->with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.kelas'])
                ->get();
        }

        // Statistik
        $stats = [
            'total_mk' => $rekapEdom->count(),
            'rata_rata_total' => $rekapEdom->avg('rata_rata_total') ?? 0,
            'total_responden' => $rekapEdom->sum('jumlah_responden'),
            'kategori' => $this->getKategoriEdom($rekapEdom->avg('rata_rata_total') ?? 0),
        ];

        // Rata-rata per aspek
        $aspek = [
            'pedagogik' => $rekapEdom->avg('rata_rata_pedagogik') ?? 0,
            'profesional' => $rekapEdom->avg('rata_rata_profesional') ?? 0,
            'kepribadian' => $rekapEdom->avg('rata_rata_kepribadian') ?? 0,
            'sosial' => $rekapEdom->avg('rata_rata_sosial') ?? 0,
        ];

        return view('dosen.edom.index', compact('rekapEdom', 'stats', 'aspek', 'dosen', 'periodeList', 'periodeAktif'));
    }

    /**
     * Show detail EDOM per mata kuliah
     */
    public function edomShow(RekapEdom $rekapEdom)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $rekapEdom->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.edom.index')->with('error', 'Akses ditolak!');
        }

        $rekapEdom->load(['jadwalKuliah.mataKuliah', 'jadwalKuliah.kelas', 'periodeEdom']);

        // Get komentar/saran dari mahasiswa (tanpa identitas)
        $komentar = \App\Models\JawabanEdom::where('dosen_id', $dosen->id)
            ->where('jadwal_kuliah_id', $rekapEdom->jadwal_kuliah_id)
            ->where('periode_edom_id', $rekapEdom->periode_edom_id)
            ->whereNotNull('komentar')
            ->where('komentar', '!=', '')
            ->select('komentar')
            ->get()
            ->pluck('komentar');

        return view('dosen.edom.show', compact('rekapEdom', 'komentar', 'dosen'));
    }

    /**
     * Helper untuk kategori EDOM
     */
    private function getKategoriEdom($nilai)
    {
        if ($nilai >= 4.5) return ['label' => 'Sangat Baik', 'badge' => 'success'];
        if ($nilai >= 3.5) return ['label' => 'Baik', 'badge' => 'primary'];
        if ($nilai >= 2.5) return ['label' => 'Cukup', 'badge' => 'warning'];
        if ($nilai >= 1.5) return ['label' => 'Kurang', 'badge' => 'danger'];
        return ['label' => 'Sangat Kurang', 'badge' => 'dark'];
    }

    // ==================== SKP / KINERJA DOSEN ====================

    /**
     * Index SKP dosen
     */
    public function skpIndex(Request $request)
    {
        $dosen = $this->getDosen();
        if (!$dosen) {
            return redirect()->route('dosen.dashboard')->with('error', 'Data dosen tidak ditemukan!');
        }

        $tahun = $request->get('tahun', date('Y'));

        $skpList = SkpPegawai::where('dosen_id', $dosen->id)
            ->when($request->filled('tahun'), function ($q) use ($tahun) {
                $q->where('tahun', $tahun);
            })
            ->orderBy('tahun', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Statistik
        $skpTahunIni = SkpPegawai::where('dosen_id', $dosen->id)
            ->where('tahun', date('Y'))
            ->first();

        $stats = [
            'total_skp' => SkpPegawai::where('dosen_id', $dosen->id)->count(),
            'nilai_tahun_ini' => $skpTahunIni?->nilai_akhir ?? '-',
            'predikat_tahun_ini' => $skpTahunIni?->predikat ?? '-',
        ];

        // Daftar tahun untuk filter
        $tahunList = SkpPegawai::where('dosen_id', $dosen->id)
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('dosen.skp.index', compact('skpList', 'stats', 'dosen', 'tahunList', 'tahun'));
    }

    /**
     * Show detail SKP
     */
    public function skpShow(SkpPegawai $skp)
    {
        $dosen = $this->getDosen();
        if (!$dosen || $skp->dosen_id !== $dosen->id) {
            return redirect()->route('dosen.skp.index')->with('error', 'Akses ditolak!');
        }

        // Load target dan realisasi SKP
        $targetSkp = \App\Models\TargetSkp::where('skp_pegawai_id', $skp->id)
            ->orderBy('urutan')
            ->get();

        return view('dosen.skp.show', compact('skp', 'targetSkp', 'dosen'));
    }
}
