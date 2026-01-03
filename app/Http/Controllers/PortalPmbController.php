<?php

namespace App\Http\Controllers;

use App\Models\KontenPmb;
use App\Models\SliderPmb;
use App\Models\FaqPmb;
use App\Models\TestimoniPmb;
use App\Models\GaleriPmb;
use App\Models\BeritaPmb;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\KontakPmb;
use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\ProgramStudi;
use App\Models\BiayaPendaftaran;
use App\Models\KuotaPmb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class PortalPmbController extends Controller
{
    /**
     * Constructor - Share konten to all views
     */
    public function __construct()
    {
        // Share konten PMB to all portal-pmb views
        View::share('konten', KontenPmb::getAllFlat());
    }

    /**
     * Halaman utama portal PMB
     */
    public function index()
    {
        $data = [
            'sliders' => SliderPmb::active()->get(),
            'keunggulan' => KeunggulanPmb::active()->take(6)->get(),
            'programStudi' => ProgramStudi::with('fakultas')->get(),
            'testimoni' => TestimoniPmb::active()->take(6)->get(),
            'beritaTerbaru' => BeritaPmb::published()->latest('published_at')->take(3)->get(),
            'fasilitas' => FasilitasPmb::active()->take(6)->get(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
            'periodePmb' => PeriodePmb::getActive(),
            'gelombangAktif' => GelombangPmb::active()->pendaftaranBuka()->first(),
            'jalurSeleksi' => JalurSeleksi::active()->get(),
            'faqPopuler' => FaqPmb::active()->take(5)->get(),
        ];

        // Statistik
        $data['statistik'] = [
            'total_prodi' => ProgramStudi::count(),
            'total_mahasiswa' => \App\Models\Mahasiswa::where('status', 'Aktif')->count(),
            'total_dosen' => \App\Models\Dosen::where('status', 'Aktif')->count(),
            'tahun_berdiri' => KontenPmb::getValue('tahun_berdiri', '1990'),
        ];

        return view('portal-pmb.index', $data);
    }

    /**
     * Halaman pendaftaran online
     */
    public function pendaftaran()
    {
        $periodePmb = PeriodePmb::getActive();
        
        if (!$periodePmb) {
            return view('portal-pmb.pendaftaran-tutup', [
                'kontak' => KontakPmb::getKontak(),
                'sosialMedia' => KontakPmb::getSosialMedia(),
            ]);
        }

        $gelombangAktif = GelombangPmb::where('periode_pmb_id', $periodePmb->id)
            ->active()
            ->pendaftaranBuka()
            ->first();

        if (!$gelombangAktif) {
            return view('portal-pmb.pendaftaran-tutup', [
                'periodePmb' => $periodePmb,
                'gelombangBerikutnya' => GelombangPmb::where('periode_pmb_id', $periodePmb->id)
                    ->where('tanggal_mulai_daftar', '>', now())
                    ->orderBy('tanggal_mulai_daftar')
                    ->first(),
                'kontak' => KontakPmb::getKontak(),
                'sosialMedia' => KontakPmb::getSosialMedia(),
            ]);
        }

        $biayaData = BiayaPendaftaran::where('gelombang_pmb_id', $gelombangAktif->id)->get();
        
        return view('portal-pmb.pendaftaran', [
            'periodePmb' => $periodePmb,
            'gelombang' => $gelombangAktif,
            'gelombangAktif' => $gelombangAktif, // alias for view compatibility
            'jalurSeleksi' => JalurSeleksi::active()->get(),
            'programStudi' => ProgramStudi::with('fakultas')->get(),
            'biayaPendaftaran' => $biayaData,
            'biaya' => $biayaData, // alias for view compatibility - send collection
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Proses pendaftaran online
     */
    public function pendaftaranStore(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'jalur_seleksi_id' => 'required|exists:jalur_seleksi,id',
            'program_studi_id' => 'required|exists:program_studi,id',
        ]);

        // TODO: Implement registration process
        // For now, redirect back with success message
        
        return redirect()->route('portal-pmb.pendaftaran')
            ->with('success', 'Pendaftaran berhasil dikirim. Silakan cek email Anda untuk informasi selanjutnya.');
    }

    /**
     * Halaman informasi jalur seleksi
     */
    public function jalurSeleksi()
    {
        return view('portal-pmb.jalur-seleksi', [
            'jalurSeleksi' => JalurSeleksi::active()->get(),
            'periodePmb' => PeriodePmb::getActive(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman program studi
     */
    public function programStudi()
    {
        return view('portal-pmb.program-studi', [
            'fakultas' => \App\Models\Fakultas::with('programStudi')->get(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Detail program studi
     */
    public function programStudiDetail($id)
    {
        $prodi = ProgramStudi::with('fakultas')->findOrFail($id);
        
        return view('portal-pmb.program-studi-detail', [
            'prodi' => $prodi,
            'kuota' => KuotaPmb::where('program_studi_id', $prodi->id)
                ->whereHas('gelombangPmb', function($q) {
                    $q->active();
                })->get(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman biaya kuliah
     */
    public function biaya()
    {
        $periodePmb = PeriodePmb::getActive();
        $gelombangAktif = $periodePmb ? GelombangPmb::where('periode_pmb_id', $periodePmb->id)
            ->active()
            ->first() : null;

        return view('portal-pmb.biaya', [
            'biayaPendaftaran' => $gelombangAktif ? BiayaPendaftaran::where('gelombang_pmb_id', $gelombangAktif->id)
                ->with(['jalurSeleksi', 'programStudi'])
                ->get() : collect(),
            'programStudi' => ProgramStudi::with('fakultas')->get(),
            'periodePmb' => $periodePmb,
            'gelombang' => $gelombangAktif,
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman jadwal PMB
     */
    public function jadwal()
    {
        $periodePmb = PeriodePmb::getActive();
        
        return view('portal-pmb.jadwal', [
            'periodePmb' => $periodePmb,
            'periodeAktif' => $periodePmb, // alias for view compatibility
            'gelombang' => $periodePmb ? GelombangPmb::where('periode_pmb_id', $periodePmb->id)
                ->orderBy('nomor_gelombang')
                ->get() : collect(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman alur pendaftaran
     */
    public function alurPendaftaran()
    {
        return view('portal-pmb.alur-pendaftaran', [
            'konten' => KontenPmb::getByGroup('alur'),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman syarat pendaftaran
     */
    public function syarat()
    {
        return view('portal-pmb.syarat', [
            'jalurSeleksi' => JalurSeleksi::active()->get(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman berita & pengumuman
     */
    public function berita(Request $request)
    {
        $query = BeritaPmb::published();

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('konten', 'like', '%' . $request->search . '%');
            });
        }

        return view('portal-pmb.berita', [
            'berita' => $query->latest('published_at')->paginate(9),
            'beritaPopuler' => BeritaPmb::published()->featured()->take(5)->get(),
            'kategori' => ['berita', 'pengumuman', 'info'],
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Detail berita
     */
    public function beritaDetail($slug)
    {
        $berita = BeritaPmb::where('slug', $slug)->published()->firstOrFail();
        
        return view('portal-pmb.berita-detail', [
            'berita' => $berita,
            'beritaLainnya' => BeritaPmb::published()
                ->where('id', '!=', $berita->id)
                ->latest('published_at')
                ->take(4)
                ->get(),
            'prevBerita' => BeritaPmb::published()
                ->where('published_at', '<', $berita->published_at)
                ->orderBy('published_at', 'desc')
                ->first(),
            'nextBerita' => BeritaPmb::published()
                ->where('published_at', '>', $berita->published_at)
                ->orderBy('published_at', 'asc')
                ->first(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman FAQ
     */
    public function faq()
    {
        $allFaq = FaqPmb::active()->get();
        
        return view('portal-pmb.faq', [
            'faq' => $allFaq,
            'categories' => $allFaq->pluck('kategori')->unique()->filter(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman galeri
     */
    public function galeri(Request $request)
    {
        $query = GaleriPmb::active();

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        $allKategori = GaleriPmb::distinct()->pluck('kategori')->filter();

        return view('portal-pmb.galeri', [
            'galeri' => $query->paginate(12),
            'kategori' => $allKategori,
            'categories' => $allKategori, // alias for view compatibility
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman kontak
     */
    public function kontak()
    {
        return view('portal-pmb.kontak', [
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
            'konten' => KontenPmb::getByGroup('contact'),
        ]);
    }

    /**
     * Kirim pesan kontak
     */
    public function kontakSend(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        // TODO: Save to database or send email
        // For now, just redirect with success message

        return redirect()->route('portal-pmb.kontak')
            ->with('success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }

    /**
     * Halaman fasilitas
     */
    public function fasilitas()
    {
        return view('portal-pmb.fasilitas', [
            'fasilitas' => FasilitasPmb::active()->get(),
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Halaman cek pengumuman kelulusan
     */
    public function cekPengumuman(Request $request)
    {
        $hasil = null;
        
        if ($request->no_pendaftaran) {
            $calon = \App\Models\CalonMahasiswa::where('no_pendaftaran', $request->no_pendaftaran)->first();
            
            if ($calon) {
                $hasil = \App\Models\HasilSeleksi::where('calon_mahasiswa_id', $calon->id)
                    ->with(['calonMahasiswa', 'gelombangPmb'])
                    ->first();
            }
        }

        return view('portal-pmb.cek-pengumuman', [
            'hasil' => $hasil,
            'noPendaftaran' => $request->no_pendaftaran,
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Login calon mahasiswa
     */
    public function loginCamaba()
    {
        return view('portal-pmb.login', [
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Proses login calon mahasiswa
     */
    public function loginCamabaPost(Request $request)
    {
        $request->validate([
            'no_pendaftaran' => 'required',
            'password' => 'required',
        ]);

        $calon = \App\Models\CalonMahasiswa::where('no_pendaftaran', $request->no_pendaftaran)->first();

        if (!$calon || !\Hash::check($request->password, $calon->password)) {
            return back()->withErrors(['login' => 'Nomor pendaftaran atau password salah']);
        }

        session(['camaba' => $calon->id]);
        return redirect()->route('portal-pmb.dashboard-camaba');
    }

    /**
     * Dashboard calon mahasiswa
     */
    public function dashboardCamaba()
    {
        if (!session('camaba')) {
            return redirect()->route('portal-pmb.login');
        }

        $calon = \App\Models\CalonMahasiswa::with([
            'gelombangPmb.periodePmb',
            'jalurSeleksi',
            'programStudi',
            'programStudi2',
            'dokumen',
            'pembayaran',
        ])->find(session('camaba'));

        if (!$calon) {
            session()->forget('camaba');
            return redirect()->route('portal-pmb.login');
        }

        $hasilSeleksi = \App\Models\HasilSeleksi::where('calon_mahasiswa_id', $calon->id)->first();

        return view('portal-pmb.dashboard-camaba', [
            'calon' => $calon,
            'hasilSeleksi' => $hasilSeleksi,
            'kontak' => KontakPmb::getKontak(),
            'sosialMedia' => KontakPmb::getSosialMedia(),
        ]);
    }

    /**
     * Logout calon mahasiswa
     */
    public function logoutCamaba()
    {
        session()->forget('camaba');
        return redirect()->route('portal-pmb.index');
    }
}
