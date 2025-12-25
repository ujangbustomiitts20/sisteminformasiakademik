<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tahunAkademik = TahunAkademik::orderBy('tahun', 'desc')->get();
        $tahunAkademikAktif = $request->tahun_akademik 
            ? TahunAkademik::find($request->tahun_akademik) 
            : TahunAkademik::getAktif();

        if ($user->isDosen()) {
            $jadwalKuliah = JadwalKuliah::with(['mataKuliah', 'ruangan'])
                ->where('dosen_id', $user->dosen->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
                ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                ->get();

            return view('absensi.dosen-index', compact('jadwalKuliah', 'tahunAkademik', 'tahunAkademikAktif'));
        }

        // Admin view
        $jadwalKuliah = JadwalKuliah::with(['mataKuliah', 'dosen', 'ruangan'])
            ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
            ->orderBy('dosen_id')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->paginate(20);

        return view('absensi.index', compact('jadwalKuliah', 'tahunAkademik', 'tahunAkademikAktif'));
    }

    public function create(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load('mataKuliah');
        $jumlahPertemuan = $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16;
        
        $mahasiswa = Krs::with('mahasiswa')
            ->where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->get();

        // Get pertemuan terakhir
        $lastPertemuan = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })->max('pertemuan') ?? 0;

        $pertemuan = min($lastPertemuan + 1, $jumlahPertemuan);
        
        // Check if all pertemuan sudah terisi
        if ($lastPertemuan >= $jumlahPertemuan) {
            return redirect()->route('absensi.show', $jadwalKuliah)
                ->with('error', 'Semua pertemuan (' . $jumlahPertemuan . 'x) sudah terisi!');
        }

        return view('absensi.create', compact('jadwalKuliah', 'mahasiswa', 'pertemuan', 'jumlahPertemuan'));
    }

    public function store(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $jumlahPertemuan = $jadwalKuliah->mataKuliah->jumlah_pertemuan ?? 16;
        
        $request->validate([
            'pertemuan' => 'required|integer|min:1|max:' . $jumlahPertemuan,
            'tanggal' => 'required|date',
            'materi' => 'nullable|string|max:255',
            'absensi' => 'required|array',
            'absensi.*.krs_id' => 'required|exists:krs,id',
            'absensi.*.status' => 'required|in:Hadir,Izin,Sakit,Alpha',
        ]);

        // Check if pertemuan already exists
        $exists = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })->where('pertemuan', $request->pertemuan)->exists();

        if ($exists) {
            return back()->with('error', 'Absensi untuk pertemuan ini sudah ada!')->withInput();
        }

        // Get pertemuan data if exists (for linking)
        $pertemuanData = \App\Models\Pertemuan::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('pertemuan_ke', $request->pertemuan)
            ->first();

        foreach ($request->absensi as $data) {
            Absensi::create([
                'krs_id' => $data['krs_id'],
                'pertemuan_id' => $pertemuanData?->id,
                'pertemuan' => $request->pertemuan,
                'tanggal' => $request->tanggal,
                'status' => $data['status'],
                'keterangan' => $data['keterangan'] ?? null,
                'materi' => $request->materi,
            ]);
        }

        return redirect()->route('absensi.show', $jadwalKuliah)
            ->with('success', 'Absensi pertemuan ke-' . $request->pertemuan . ' berhasil disimpan!');
    }

    public function show(JadwalKuliah $jadwalKuliah)
    {
        $jadwalKuliah->load(['mataKuliah', 'dosen', 'ruangan', 'tahunAkademik']);
        
        $mahasiswa = Krs::with(['mahasiswa', 'absensi'])
            ->where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->get();

        // Get all pertemuan - group by pertemuan number to avoid duplicates
        $pertemuan = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })
            ->selectRaw('pertemuan, MIN(tanggal) as tanggal, MAX(materi) as materi')
            ->groupBy('pertemuan')
            ->orderBy('pertemuan')
            ->get();

        return view('absensi.show', compact('jadwalKuliah', 'mahasiswa', 'pertemuan'));
    }

    public function edit(JadwalKuliah $jadwalKuliah, $pertemuan)
    {
        $mahasiswa = Krs::with(['mahasiswa', 'absensi' => function($q) use ($pertemuan) {
                $q->where('pertemuan', $pertemuan);
            }])
            ->where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->get();

        $absensiData = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })->where('pertemuan', $pertemuan)->first();

        return view('absensi.edit', compact('jadwalKuliah', 'mahasiswa', 'pertemuan', 'absensiData'));
    }

    public function update(Request $request, JadwalKuliah $jadwalKuliah, $pertemuan)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'materi' => 'nullable|string|max:255',
            'absensi' => 'required|array',
        ]);

        foreach ($request->absensi as $krsId => $data) {
            Absensi::where('krs_id', $krsId)
                ->where('pertemuan', $pertemuan)
                ->update([
                    'tanggal' => $request->tanggal,
                    'status' => $data['status'],
                    'keterangan' => $data['keterangan'] ?? null,
                    'materi' => $request->materi,
                ]);
        }

        return redirect()->route('absensi.show', $jadwalKuliah)
            ->with('success', 'Absensi pertemuan ke-' . $pertemuan . ' berhasil diupdate!');
    }

    public function destroy(JadwalKuliah $jadwalKuliah, $pertemuan)
    {
        Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })->where('pertemuan', $pertemuan)->delete();

        return redirect()->route('absensi.show', $jadwalKuliah)
            ->with('success', 'Absensi pertemuan ke-' . $pertemuan . ' berhasil dihapus!');
    }

    // Rekap absensi mahasiswa
    public function rekap(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isMahasiswa()) {
            $mahasiswa = $user->mahasiswa;
            $tahunAkademikAktif = TahunAkademik::getAktif();
            
            $krs = Krs::with(['jadwalKuliah.mataKuliah', 'absensi'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
                ->where('status', 'Disetujui')
                ->get();

            return view('absensi.rekap-mahasiswa', compact('krs', 'mahasiswa', 'tahunAkademikAktif'));
        }

        abort(403);
    }

    /**
     * Halaman absensi mandiri mahasiswa
     */
    public function absensiMandiri()
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $mahasiswa = $user->mahasiswa;
        $tahunAkademikAktif = TahunAkademik::getAktif();
        $today = Carbon::now();
        $hariIni = $this->getHariIndonesia($today->dayOfWeek);

        // Get jadwal hari ini yang sudah diambil mahasiswa
        $jadwalHariIni = Krs::with(['jadwalKuliah.mataKuliah', 'jadwalKuliah.dosen', 'jadwalKuliah.ruangan', 'absensi'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('tahun_akademik_id', $tahunAkademikAktif?->id)
            ->where('status', 'Disetujui')
            ->whereHas('jadwalKuliah', function($q) use ($hariIni) {
                $q->where('hari', $hariIni);
            })
            ->get();

        return view('absensi.mandiri', compact('jadwalHariIni', 'mahasiswa', 'tahunAkademikAktif', 'today', 'hariIni'));
    }

    /**
     * Proses absensi mandiri oleh mahasiswa
     */
    public function prosesAbsensiMandiri(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isMahasiswa()) {
            abort(403);
        }

        $request->validate([
            'krs_id' => 'required|exists:krs,id',
            'kode_absensi' => 'required|string',
        ]);

        $mahasiswa = $user->mahasiswa;
        $krs = Krs::with('jadwalKuliah')
            ->where('id', $request->krs_id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'Disetujui')
            ->firstOrFail();

        $jadwalKuliah = $krs->jadwalKuliah;
        $today = Carbon::now();
        $hariIni = $this->getHariIndonesia($today->dayOfWeek);

        // Validasi: cek apakah jadwal sesuai hari ini
        if ($jadwalKuliah->hari !== $hariIni) {
            return back()->with('error', 'Jadwal kuliah tidak sesuai dengan hari ini.');
        }

        // Validasi: cek waktu (toleransi 30 menit sebelum dan 30 menit setelah jam mulai)
        $jamMulai = Carbon::parse($jadwalKuliah->jam_mulai);
        $jamSelesai = Carbon::parse($jadwalKuliah->jam_selesai);
        $currentTime = Carbon::now()->format('H:i:s');
        
        $batasAwal = $jamMulai->copy()->subMinutes(30)->format('H:i:s');
        $batasAkhir = $jamSelesai->format('H:i:s');

        if ($currentTime < $batasAwal || $currentTime > $batasAkhir) {
            return back()->with('error', 'Absensi hanya dapat dilakukan 30 menit sebelum hingga akhir jam kuliah.');
        }

        // Cek kode absensi (generated by dosen)
        $kodeValid = $this->validateKodeAbsensi($jadwalKuliah->id, $request->kode_absensi);
        if (!$kodeValid) {
            return back()->with('error', 'Kode absensi tidak valid atau sudah kadaluarsa.');
        }

        // Cek pertemuan hari ini (dari cache, bukan session)
        $pertemuan = cache()->get('absensi_pertemuan_' . $jadwalKuliah->id, 1);
        $materi = cache()->get('absensi_materi_' . $jadwalKuliah->id, '');

        // Cek apakah sudah absen hari ini
        $sudahAbsen = Absensi::where('krs_id', $krs->id)
            ->where('pertemuan', $pertemuan)
            ->exists();

        if ($sudahAbsen) {
            return back()->with('error', 'Anda sudah melakukan absensi untuk pertemuan ini.');
        }

        // Simpan absensi
        Absensi::create([
            'krs_id' => $krs->id,
            'pertemuan' => $pertemuan,
            'tanggal' => $today->toDateString(),
            'status' => 'Hadir',
            'keterangan' => 'Absensi mandiri',
            'materi' => $materi,
        ]);

        return back()->with('success', 'Absensi berhasil dicatat! Pertemuan ke-' . $pertemuan);
    }

    /**
     * Dosen: Generate kode absensi untuk mahasiswa
     */
    public function generateKode(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        // Hanya dosen pengampu atau admin
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'pertemuan' => 'required|integer|min:1|max:16',
            'materi' => 'nullable|string|max:255',
            'durasi' => 'required|integer|min:5|max:180', // durasi dalam menit
        ]);

        // Generate unique code
        $kode = strtoupper(substr(md5($jadwalKuliah->id . time() . rand(1000, 9999)), 0, 6));
        $durasiMenit = (int) $request->durasi;
        $expiry = Carbon::now()->addMinutes($durasiMenit);

        // Store in cache for cross-session access (mahasiswa akan akses dari session berbeda)
        cache()->put('absensi_kode_' . $jadwalKuliah->id, $kode, $expiry);
        cache()->put('absensi_expiry_' . $jadwalKuliah->id, $expiry, $expiry);
        cache()->put('absensi_pertemuan_' . $jadwalKuliah->id, (int) $request->pertemuan, $expiry);
        cache()->put('absensi_materi_' . $jadwalKuliah->id, $request->materi ?? '', $expiry);

        return response()->json([
            'success' => true,
            'kode' => $kode,
            'expiry' => $expiry->format('H:i:s'),
            'pertemuan' => (int) $request->pertemuan,
        ]);
    }

    /**
     * Dosen: Tampilkan halaman QR/kode absensi
     */
    public function showKodeAbsensi(JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        // Hanya dosen pengampu atau admin
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $jadwalKuliah->load(['mataKuliah', 'ruangan', 'tahunAkademik']);

        // Get pertemuan terakhir
        $lastPertemuan = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })->max('pertemuan') ?? 0;

        $pertemuan = $lastPertemuan + 1;

        // Get current code if exists
        $kodeAktif = cache()->get('absensi_kode_' . $jadwalKuliah->id);
        $expiry = cache()->get('absensi_expiry_' . $jadwalKuliah->id);
        $pertemuanAktif = cache()->get('absensi_pertemuan_' . $jadwalKuliah->id);

        // Count mahasiswa yang sudah absen
        $sudahAbsen = 0;
        if ($kodeAktif && $pertemuanAktif) {
            $sudahAbsen = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                    $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
                })->where('pertemuan', $pertemuanAktif)->count();
        }

        $totalMahasiswa = Krs::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->count();

        return view('absensi.kode-absensi', compact(
            'jadwalKuliah', 
            'pertemuan', 
            'kodeAktif', 
            'expiry', 
            'pertemuanAktif',
            'sudahAbsen',
            'totalMahasiswa'
        ));
    }

    /**
     * Dosen: Tutup sesi absensi dan tandai yang belum hadir sebagai Alpha
     */
    public function tutupAbsensi(Request $request, JadwalKuliah $jadwalKuliah)
    {
        $user = auth()->user();
        
        // Hanya dosen pengampu atau admin
        if (!$user->isAdmin() && (!$user->isDosen() || $user->dosen->id !== $jadwalKuliah->dosen_id)) {
            abort(403);
        }

        $pertemuan = cache()->get('absensi_pertemuan_' . $jadwalKuliah->id);
        $materi = cache()->get('absensi_materi_' . $jadwalKuliah->id);

        if (!$pertemuan) {
            return back()->with('error', 'Tidak ada sesi absensi aktif.');
        }

        // Get semua KRS yang belum absen
        $krsIds = Krs::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->pluck('id');

        $sudahAbsen = Absensi::whereIn('krs_id', $krsIds)
            ->where('pertemuan', $pertemuan)
            ->pluck('krs_id');

        $belumAbsen = $krsIds->diff($sudahAbsen);

        // Tandai yang belum absen sebagai Alpha
        foreach ($belumAbsen as $krsId) {
            Absensi::create([
                'krs_id' => $krsId,
                'pertemuan' => $pertemuan,
                'tanggal' => Carbon::now()->toDateString(),
                'status' => 'Alpha',
                'keterangan' => 'Tidak hadir (otomatis)',
                'materi' => $materi,
            ]);
        }

        // Clear session/cache
        cache()->forget('absensi_kode_' . $jadwalKuliah->id);
        cache()->forget('absensi_expiry_' . $jadwalKuliah->id);
        cache()->forget('absensi_pertemuan_' . $jadwalKuliah->id);
        cache()->forget('absensi_materi_' . $jadwalKuliah->id);

        return redirect()->route('absensi.show', $jadwalKuliah)
            ->with('success', 'Sesi absensi ditutup. ' . $belumAbsen->count() . ' mahasiswa ditandai Alpha.');
    }

    /**
     * Validate kode absensi
     */
    protected function validateKodeAbsensi($jadwalKuliahId, $kode)
    {
        $storedKode = cache()->get('absensi_kode_' . $jadwalKuliahId);
        $expiry = cache()->get('absensi_expiry_' . $jadwalKuliahId);

        if (!$storedKode || !$expiry) {
            return false;
        }

        if ($storedKode !== strtoupper($kode)) {
            return false;
        }

        if (Carbon::now()->gt($expiry)) {
            return false;
        }

        return true;
    }

    /**
     * Get nama hari Indonesia
     */
    protected function getHariIndonesia($dayOfWeek)
    {
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        return $hari[$dayOfWeek] ?? 'Senin';
    }

    /**
     * API: Get status absensi saat ini
     */
    public function getStatusAbsensi(JadwalKuliah $jadwalKuliah)
    {
        $kodeAktif = cache()->get('absensi_kode_' . $jadwalKuliah->id);
        $expiry = cache()->get('absensi_expiry_' . $jadwalKuliah->id);
        $pertemuan = cache()->get('absensi_pertemuan_' . $jadwalKuliah->id);

        if (!$kodeAktif) {
            return response()->json(['active' => false]);
        }

        $sudahAbsen = Absensi::whereHas('krs', function($q) use ($jadwalKuliah) {
                $q->where('jadwal_kuliah_id', $jadwalKuliah->id);
            })->where('pertemuan', $pertemuan)->count();

        $totalMahasiswa = Krs::where('jadwal_kuliah_id', $jadwalKuliah->id)
            ->where('status', 'Disetujui')
            ->count();

        return response()->json([
            'active' => true,
            'kode' => $kodeAktif,
            'expiry' => $expiry->format('Y-m-d H:i:s'),
            'pertemuan' => $pertemuan,
            'sudah_absen' => $sudahAbsen,
            'total_mahasiswa' => $totalMahasiswa,
        ]);
    }
}
