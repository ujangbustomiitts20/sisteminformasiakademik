<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DosenPortalController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\JadwalKuliahController;
use App\Http\Controllers\KrsController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\ProgramStudiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\PertemuanController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KalenderAkademikController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LaporanKeuanganController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TransaksiPembayaranController;
use App\Http\Controllers\BeasiswaController;
use App\Http\Controllers\PengaturanDendaController;
use App\Http\Controllers\SkemaCicilanController;
use App\Http\Controllers\CicilanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\AkunBankController;
use App\Http\Controllers\MutasiBankController;
use App\Http\Controllers\RekonsiliasiBankController;
use App\Http\Controllers\KeuanganDashboardController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\JenisPotonganController;
use App\Http\Controllers\PeriodeDiskonController;
use App\Http\Controllers\PotonganMahasiswaController;
use App\Http\Controllers\MahasiswaPortalController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\Api\WilayahController;
use App\Http\Controllers\PrasyaratMataKuliahController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\BimbinganAkademikController;
use App\Http\Controllers\CutiAkademikController;
use App\Http\Controllers\Mahasiswa\TranskripController;
use App\Http\Controllers\Mahasiswa\KhsController;
use App\Http\Controllers\Mahasiswa\JadwalUjianController;
use App\Http\Controllers\Mahasiswa\KehadiranController;
use App\Http\Controllers\JadwalPenggantiController;
use App\Http\Controllers\WisudaController;
use App\Http\Controllers\KonfigurasiCetakController;
use App\Http\Controllers\YudisiumController;
use App\Http\Controllers\EdomController;
use App\Http\Controllers\EdomMahasiswaController;
use App\Http\Controllers\PeriodeUjianController;
use App\Http\Controllers\KartuUjianController;
use App\Http\Controllers\KonversiNilaiController;
use App\Http\Controllers\KonversiKegiatanController;
use App\Http\Controllers\KegiatanLapanganController;
use App\Http\Controllers\KegiatanLapanganMahasiswaController;
use App\Http\Controllers\TugasAkhirController;
use App\Http\Controllers\TugasAkhirMahasiswaController;
use App\Http\Controllers\TugasAkhirDosenController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\DekanController;
use App\Http\Controllers\Admin\PejabatAkademikController;
use App\Http\Controllers\KepegawaianController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\KepegawaianPegawaiController;
use App\Http\Controllers\Admin\CutiPegawaiController;
use App\Http\Controllers\Admin\PresensiPegawaiController;
use App\Http\Controllers\Admin\PenugasanMutasiController;
use App\Http\Controllers\Admin\KenaikanGajiBerkalaController;
use App\Http\Controllers\Admin\KenaikanPangkatController;
use App\Http\Controllers\Admin\PensiunController;
use App\Http\Controllers\PortalPmbController;
use App\Http\Controllers\Admin\KontenPmbController;

// =====================
// PORTAL PMB PUBLIK (Tanpa Login)
// =====================
Route::prefix('pmb-online')->name('portal-pmb.')->group(function () {
    Route::get('/', [PortalPmbController::class, 'index'])->name('index');
    Route::get('/pendaftaran', [PortalPmbController::class, 'pendaftaran'])->name('pendaftaran');
    Route::post('/pendaftaran', [PortalPmbController::class, 'pendaftaranStore'])->name('pendaftaran.store');
    Route::get('/jalur-seleksi', [PortalPmbController::class, 'jalurSeleksi'])->name('jalur-seleksi');
    Route::get('/program-studi', [PortalPmbController::class, 'programStudi'])->name('program-studi');
    Route::get('/program-studi/{id}', [PortalPmbController::class, 'programStudiDetail'])->name('program-studi.detail');
    Route::get('/biaya', [PortalPmbController::class, 'biaya'])->name('biaya');
    Route::get('/jadwal', [PortalPmbController::class, 'jadwal'])->name('jadwal');
    Route::get('/alur-pendaftaran', [PortalPmbController::class, 'alurPendaftaran'])->name('alur-pendaftaran');
    Route::get('/syarat', [PortalPmbController::class, 'syarat'])->name('syarat');
    Route::get('/berita', [PortalPmbController::class, 'berita'])->name('berita');
    Route::get('/berita/{slug}', [PortalPmbController::class, 'beritaDetail'])->name('berita.detail');
    Route::get('/faq', [PortalPmbController::class, 'faq'])->name('faq');
    Route::get('/galeri', [PortalPmbController::class, 'galeri'])->name('galeri');
    Route::get('/kontak', [PortalPmbController::class, 'kontak'])->name('kontak');
    Route::post('/kontak', [PortalPmbController::class, 'kontakSend'])->name('kontak.send');
    Route::get('/fasilitas', [PortalPmbController::class, 'fasilitas'])->name('fasilitas');
    Route::get('/cek-pengumuman', [PortalPmbController::class, 'cekPengumuman'])->name('cek-pengumuman');
    Route::post('/cek-pengumuman', [PortalPmbController::class, 'cekPengumuman'])->name('cek-pengumuman.result');
    
    // Login Calon Mahasiswa
    Route::get('/login', [PortalPmbController::class, 'loginCamaba'])->name('login');
    Route::post('/login', [PortalPmbController::class, 'loginCamabaPost'])->name('login.post');
    Route::get('/dashboard', [PortalPmbController::class, 'dashboardCamaba'])->name('dashboard-camaba');
    Route::post('/logout', [PortalPmbController::class, 'logoutCamaba'])->name('logout');
});

// Landing page
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // Forgot Password
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // API Wilayah & Sekolah (untuk dropdown dinamis)
    Route::prefix('api')->group(function () {
        Route::get('/provinsi', [WilayahController::class, 'provinsi'])->name('api.provinsi');
        Route::get('/kabupaten', [WilayahController::class, 'kabupaten'])->name('api.kabupaten');
        Route::get('/kecamatan', [WilayahController::class, 'kecamatan'])->name('api.kecamatan');
        Route::get('/kelurahan', [WilayahController::class, 'kelurahan'])->name('api.kelurahan');
        Route::get('/sekolah', [WilayahController::class, 'sekolah'])->name('api.sekolah');
        Route::get('/sekolah/{id}', [WilayahController::class, 'sekolahDetail'])->name('api.sekolah.detail');
    });

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    
    // Pengumuman (semua role bisa lihat)
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/{pengumuman}', [PengumumanController::class, 'show'])->name('pengumuman.show');

    // =====================
    // ADMIN ROUTES
    // =====================
    Route::middleware(['role:admin'])->group(function () {
        // Manajemen User
        Route::resource('user', UserController::class);
        Route::post('/user/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset-password');
        
        // Pejabat Akademik (Kaprodi & Dekan)
        Route::prefix('pejabat-akademik')->name('admin.pejabat-akademik.')->group(function () {
            Route::get('/', [PejabatAkademikController::class, 'index'])->name('index');
            Route::get('/create-kaprodi', [PejabatAkademikController::class, 'createKaprodi'])->name('create-kaprodi');
            Route::post('/store-kaprodi', [PejabatAkademikController::class, 'storeKaprodi'])->name('store-kaprodi');
            Route::get('/create-dekan', [PejabatAkademikController::class, 'createDekan'])->name('create-dekan');
            Route::post('/store-dekan', [PejabatAkademikController::class, 'storeDekan'])->name('store-dekan');
            Route::delete('/remove-kaprodi/{user}', [PejabatAkademikController::class, 'removeKaprodi'])->name('remove-kaprodi');
            Route::delete('/remove-dekan/{user}', [PejabatAkademikController::class, 'removeDekan'])->name('remove-dekan');
            Route::post('/quick-create', [PejabatAkademikController::class, 'quickCreate'])->name('quick-create');
        });
        
        // Master Data - Fakultas
        Route::resource('fakultas', FakultasController::class)->except(['show', 'create', 'edit']);
        
        // Master Data - Program Studi
        Route::resource('program-studi', ProgramStudiController::class)->except(['show', 'create', 'edit']);
        
        // Master Data - Ruangan
        Route::resource('ruangan', RuanganController::class)->except(['show', 'create', 'edit']);
        
        // Master Data - Tahun Akademik
        Route::resource('tahun-akademik', TahunAkademikController::class)->except(['show', 'create', 'edit']);
        Route::post('/tahun-akademik/{tahunAkademik}/activate', [TahunAkademikController::class, 'activate'])->name('tahun-akademik.activate');
        
        // Master Data - Sekolah
        Route::resource('sekolah', SekolahController::class);
        Route::patch('/sekolah/{sekolah}/toggle-active', [SekolahController::class, 'toggleActive'])->name('sekolah.toggle-active');
        
        // Manajemen Mahasiswa
        Route::post('/mahasiswa/generate-nim', [MahasiswaController::class, 'generateNim'])->name('mahasiswa.generate-nim');
        Route::resource('mahasiswa', MahasiswaController::class);
        
        // Manajemen Dosen
        Route::resource('dosen', DosenController::class);
        
        // ============================================
        // MODUL KEPEGAWAIAN
        // ============================================
        
        // Dashboard Kepegawaian
        Route::get('/kepegawaian', [KepegawaianController::class, 'dashboard'])->name('kepegawaian.dashboard');
        
        // Manajemen Pegawai (Tendik)
        Route::resource('kepegawaian/pegawai', PegawaiController::class)->names([
            'index' => 'kepegawaian.pegawai.index',
            'create' => 'kepegawaian.pegawai.create',
            'store' => 'kepegawaian.pegawai.store',
            'show' => 'kepegawaian.pegawai.show',
            'edit' => 'kepegawaian.pegawai.edit',
            'update' => 'kepegawaian.pegawai.update',
            'destroy' => 'kepegawaian.pegawai.destroy',
        ]);
        
        // Manajemen Unit Kerja
        Route::resource('kepegawaian/unit-kerja', UnitKerjaController::class)->names([
            'index' => 'kepegawaian.unit-kerja.index',
            'create' => 'kepegawaian.unit-kerja.create',
            'store' => 'kepegawaian.unit-kerja.store',
            'show' => 'kepegawaian.unit-kerja.show',
            'edit' => 'kepegawaian.unit-kerja.edit',
            'update' => 'kepegawaian.unit-kerja.update',
            'destroy' => 'kepegawaian.unit-kerja.destroy',
        ]);
        
        // ============================================
        // MANAJEMEN SDM (harus sebelum kepegawaian/{dosen})
        // ============================================
        
        // Cuti Pegawai
        Route::prefix('kepegawaian/cuti')->name('kepegawaian.cuti.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'store'])->name('store');
            Route::get('/saldo', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'saldoCuti'])->name('saldo');
            Route::post('/saldo/generate', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'generateSaldoCuti'])->name('saldo.generate');
            Route::put('/saldo/{saldo}', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'updateSaldoCuti'])->name('saldo.update');
            Route::get('/{cuti}', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'show'])->name('show');
            Route::get('/{cuti}/edit', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'edit'])->name('edit');
            Route::put('/{cuti}', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'update'])->name('update');
            Route::delete('/{cuti}', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'destroy'])->name('destroy');
            Route::post('/{cuti}/approve', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'approve'])->name('approve');
            Route::post('/{cuti}/reject', [\App\Http\Controllers\Admin\CutiPegawaiController::class, 'reject'])->name('reject');
        });
        
        // Presensi Pegawai
        Route::prefix('kepegawaian/presensi')->name('kepegawaian.presensi.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'store'])->name('store');
            Route::get('/rekap', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'rekap'])->name('rekap');
            Route::post('/rekap/generate', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'generateRekap'])->name('rekap.generate');
            Route::get('/rekap/{rekap}', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'showRekap'])->name('rekap.show');
            Route::get('/setting', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'settingJamKerja'])->name('setting');
            Route::post('/setting', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'storeSettingJamKerja'])->name('setting.store');
            Route::put('/setting/{setting}', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'updateSettingJamKerja'])->name('setting.update');
            Route::get('/laporan', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'laporan'])->name('laporan');
            // Route dengan parameter harus di bawah
            Route::get('/{presensi}', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'show'])->name('show');
            Route::get('/{presensi}/edit', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'edit'])->name('edit');
            Route::put('/{presensi}', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'update'])->name('update');
            Route::delete('/{presensi}', [\App\Http\Controllers\Admin\PresensiPegawaiController::class, 'destroy'])->name('destroy');
        });
        
        // Penugasan & Mutasi
        Route::prefix('kepegawaian/penugasan')->name('kepegawaian.penugasan.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'store'])->name('store');
            Route::get('/{penugasan}', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'show'])->name('show');
            Route::get('/{penugasan}/edit', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'edit'])->name('edit');
            Route::put('/{penugasan}', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'update'])->name('update');
            Route::delete('/{penugasan}', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'destroy'])->name('destroy');
            Route::post('/{penugasan}/selesaikan', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'selesaikan'])->name('selesaikan');
            Route::post('/{penugasan}/batalkan', [\App\Http\Controllers\Admin\PenugasanMutasiController::class, 'batalkan'])->name('batalkan');
        });
        
        // Kenaikan Gaji Berkala (KGB)
        Route::prefix('kepegawaian/kgb')->name('kepegawaian.kgb.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'store'])->name('store');
            Route::get('/monitoring', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'monitoring'])->name('monitoring');
            Route::post('/generate', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'generateKgb'])->name('generate');
            Route::get('/{kgb}', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'show'])->name('show');
            Route::get('/{kgb}/edit', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'edit'])->name('edit');
            Route::put('/{kgb}', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'update'])->name('update');
            Route::delete('/{kgb}', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'destroy'])->name('destroy');
            Route::post('/{kgb}/approve', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'approve'])->name('approve');
            Route::post('/{kgb}/reject', [\App\Http\Controllers\Admin\KenaikanGajiBerkalaController::class, 'reject'])->name('reject');
        });
        
        // Kenaikan Pangkat
        Route::prefix('kepegawaian/kenaikan-pangkat')->name('kepegawaian.kenaikan-pangkat.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'store'])->name('store');
            Route::get('/monitoring', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'monitoring'])->name('monitoring');
            Route::get('/{kenaikanPangkat}', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'show'])->name('show');
            Route::get('/{kenaikanPangkat}/edit', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'edit'])->name('edit');
            Route::put('/{kenaikanPangkat}', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'update'])->name('update');
            Route::delete('/{kenaikanPangkat}', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'destroy'])->name('destroy');
            Route::post('/{kenaikanPangkat}/verifikasi', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'verifikasi'])->name('verifikasi');
            Route::post('/{kenaikanPangkat}/approve', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'approve'])->name('approve');
            Route::post('/{kenaikanPangkat}/reject', [\App\Http\Controllers\Admin\KenaikanPangkatController::class, 'reject'])->name('reject');
        });
        
        // Pensiun
        Route::prefix('kepegawaian/pensiun')->name('kepegawaian.pensiun.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\PensiunController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\PensiunController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\PensiunController::class, 'store'])->name('store');
            Route::get('/monitoring', [\App\Http\Controllers\Admin\PensiunController::class, 'monitoring'])->name('monitoring');
            Route::post('/generate', [\App\Http\Controllers\Admin\PensiunController::class, 'generatePrediksi'])->name('generate');
            Route::get('/laporan', [\App\Http\Controllers\Admin\PensiunController::class, 'laporan'])->name('laporan');
            Route::get('/{pensiun}', [\App\Http\Controllers\Admin\PensiunController::class, 'show'])->name('show');
            Route::get('/{pensiun}/edit', [\App\Http\Controllers\Admin\PensiunController::class, 'edit'])->name('edit');
            Route::put('/{pensiun}', [\App\Http\Controllers\Admin\PensiunController::class, 'update'])->name('update');
            Route::delete('/{pensiun}', [\App\Http\Controllers\Admin\PensiunController::class, 'destroy'])->name('destroy');
            Route::post('/{pensiun}/proses', [\App\Http\Controllers\Admin\PensiunController::class, 'proses'])->name('proses');
            Route::post('/{pensiun}/selesaikan', [\App\Http\Controllers\Admin\PensiunController::class, 'selesaikan'])->name('selesaikan');
        });
        
        // Kepegawaian Dosen (Riwayat) - HARUS SETELAH route SDM karena {dosen} adalah wildcard
        Route::prefix('kepegawaian/{dosen}')->name('kepegawaian.')->group(function () {
            Route::get('/', [KepegawaianController::class, 'index'])->name('index');
            
            // Riwayat Pendidikan
            Route::get('/pendidikan/create', [KepegawaianController::class, 'createPendidikan'])->name('pendidikan.create');
            Route::post('/pendidikan', [KepegawaianController::class, 'storePendidikan'])->name('pendidikan.store');
            Route::get('/pendidikan/{pendidikan}/edit', [KepegawaianController::class, 'editPendidikan'])->name('pendidikan.edit');
            Route::put('/pendidikan/{pendidikan}', [KepegawaianController::class, 'updatePendidikan'])->name('pendidikan.update');
            Route::delete('/pendidikan/{pendidikan}', [KepegawaianController::class, 'destroyPendidikan'])->name('pendidikan.destroy');
            
            // Riwayat Jabatan
            Route::get('/jabatan/create', [KepegawaianController::class, 'createJabatan'])->name('jabatan.create');
            Route::post('/jabatan', [KepegawaianController::class, 'storeJabatan'])->name('jabatan.store');
            Route::get('/jabatan/{jabatan}/edit', [KepegawaianController::class, 'editJabatan'])->name('jabatan.edit');
            Route::put('/jabatan/{jabatan}', [KepegawaianController::class, 'updateJabatan'])->name('jabatan.update');
            Route::delete('/jabatan/{jabatan}', [KepegawaianController::class, 'destroyJabatan'])->name('jabatan.destroy');
            
            // Riwayat Pangkat
            Route::get('/pangkat/create', [KepegawaianController::class, 'createPangkat'])->name('pangkat.create');
            Route::post('/pangkat', [KepegawaianController::class, 'storePangkat'])->name('pangkat.store');
            Route::get('/pangkat/{pangkat}/edit', [KepegawaianController::class, 'editPangkat'])->name('pangkat.edit');
            Route::put('/pangkat/{pangkat}', [KepegawaianController::class, 'updatePangkat'])->name('pangkat.update');
            Route::delete('/pangkat/{pangkat}', [KepegawaianController::class, 'destroyPangkat'])->name('pangkat.destroy');
            
            // Riwayat Pelatihan
            Route::get('/pelatihan/create', [KepegawaianController::class, 'createPelatihan'])->name('pelatihan.create');
            Route::post('/pelatihan', [KepegawaianController::class, 'storePelatihan'])->name('pelatihan.store');
            Route::get('/pelatihan/{pelatihan}/edit', [KepegawaianController::class, 'editPelatihan'])->name('pelatihan.edit');
            Route::put('/pelatihan/{pelatihan}', [KepegawaianController::class, 'updatePelatihan'])->name('pelatihan.update');
            Route::delete('/pelatihan/{pelatihan}', [KepegawaianController::class, 'destroyPelatihan'])->name('pelatihan.destroy');
            
            // Dokumen Kepegawaian
            Route::get('/dokumen/create', [KepegawaianController::class, 'createDokumen'])->name('dokumen.create');
            Route::post('/dokumen', [KepegawaianController::class, 'storeDokumen'])->name('dokumen.store');
            Route::get('/dokumen/{dokumen}/edit', [KepegawaianController::class, 'editDokumen'])->name('dokumen.edit');
            Route::put('/dokumen/{dokumen}', [KepegawaianController::class, 'updateDokumen'])->name('dokumen.update');
            Route::delete('/dokumen/{dokumen}', [KepegawaianController::class, 'destroyDokumen'])->name('dokumen.destroy');
        });
        
        // Kepegawaian Pegawai/Tendik (Riwayat)
        Route::prefix('kepegawaian/pegawai/{pegawai}/riwayat')->name('kepegawaian.pegawai.riwayat.')->group(function () {
            Route::get('/', [KepegawaianPegawaiController::class, 'index'])->name('index');
            
            // Riwayat Pendidikan
            Route::get('/pendidikan/create', [KepegawaianPegawaiController::class, 'createPendidikan'])->name('pendidikan.create');
            Route::post('/pendidikan', [KepegawaianPegawaiController::class, 'storePendidikan'])->name('pendidikan.store');
            Route::get('/pendidikan/{pendidikan}/edit', [KepegawaianPegawaiController::class, 'editPendidikan'])->name('pendidikan.edit');
            Route::put('/pendidikan/{pendidikan}', [KepegawaianPegawaiController::class, 'updatePendidikan'])->name('pendidikan.update');
            Route::delete('/pendidikan/{pendidikan}', [KepegawaianPegawaiController::class, 'destroyPendidikan'])->name('pendidikan.destroy');
            
            // Riwayat Jabatan
            Route::get('/jabatan/create', [KepegawaianPegawaiController::class, 'createJabatan'])->name('jabatan.create');
            Route::post('/jabatan', [KepegawaianPegawaiController::class, 'storeJabatan'])->name('jabatan.store');
            Route::get('/jabatan/{jabatan}/edit', [KepegawaianPegawaiController::class, 'editJabatan'])->name('jabatan.edit');
            Route::put('/jabatan/{jabatan}', [KepegawaianPegawaiController::class, 'updateJabatan'])->name('jabatan.update');
            Route::delete('/jabatan/{jabatan}', [KepegawaianPegawaiController::class, 'destroyJabatan'])->name('jabatan.destroy');
            
            // Riwayat Pangkat
            Route::get('/pangkat/create', [KepegawaianPegawaiController::class, 'createPangkat'])->name('pangkat.create');
            Route::post('/pangkat', [KepegawaianPegawaiController::class, 'storePangkat'])->name('pangkat.store');
            Route::get('/pangkat/{pangkat}/edit', [KepegawaianPegawaiController::class, 'editPangkat'])->name('pangkat.edit');
            Route::put('/pangkat/{pangkat}', [KepegawaianPegawaiController::class, 'updatePangkat'])->name('pangkat.update');
            Route::delete('/pangkat/{pangkat}', [KepegawaianPegawaiController::class, 'destroyPangkat'])->name('pangkat.destroy');
            
            // Riwayat Pelatihan
            Route::get('/pelatihan/create', [KepegawaianPegawaiController::class, 'createPelatihan'])->name('pelatihan.create');
            Route::post('/pelatihan', [KepegawaianPegawaiController::class, 'storePelatihan'])->name('pelatihan.store');
            Route::get('/pelatihan/{pelatihan}/edit', [KepegawaianPegawaiController::class, 'editPelatihan'])->name('pelatihan.edit');
            Route::put('/pelatihan/{pelatihan}', [KepegawaianPegawaiController::class, 'updatePelatihan'])->name('pelatihan.update');
            Route::delete('/pelatihan/{pelatihan}', [KepegawaianPegawaiController::class, 'destroyPelatihan'])->name('pelatihan.destroy');
            
            // Dokumen Kepegawaian
            Route::get('/dokumen/create', [KepegawaianPegawaiController::class, 'createDokumen'])->name('dokumen.create');
            Route::post('/dokumen', [KepegawaianPegawaiController::class, 'storeDokumen'])->name('dokumen.store');
            Route::get('/dokumen/{dokumen}/edit', [KepegawaianPegawaiController::class, 'editDokumen'])->name('dokumen.edit');
            Route::put('/dokumen/{dokumen}', [KepegawaianPegawaiController::class, 'updateDokumen'])->name('dokumen.update');
            Route::delete('/dokumen/{dokumen}', [KepegawaianPegawaiController::class, 'destroyDokumen'])->name('dokumen.destroy');
        });
        // Manajemen Mata Kuliah
        Route::resource('mata-kuliah', MataKuliahController::class);
        
        // Prasyarat Mata Kuliah
        Route::get('/prasyarat', [PrasyaratMataKuliahController::class, 'index'])->name('prasyarat.index');
        Route::get('/prasyarat/kurikulum', [PrasyaratMataKuliahController::class, 'kurikulum'])->name('prasyarat.kurikulum');
        Route::get('/prasyarat/{mataKuliah}/edit', [PrasyaratMataKuliahController::class, 'edit'])->name('prasyarat.edit');
        Route::put('/prasyarat/{mataKuliah}', [PrasyaratMataKuliahController::class, 'update'])->name('prasyarat.update');
        Route::post('/prasyarat/{mataKuliah}/add', [PrasyaratMataKuliahController::class, 'addPrasyarat'])->name('prasyarat.add');
        Route::delete('/prasyarat/detail/{prasyarat}', [PrasyaratMataKuliahController::class, 'deletePrasyarat'])->name('prasyarat.delete');
        Route::post('/prasyarat/{mataKuliah}/copy', [PrasyaratMataKuliahController::class, 'copyPrasyarat'])->name('prasyarat.copy');
        Route::post('/prasyarat/cek', [PrasyaratMataKuliahController::class, 'cekPrasyaratMahasiswa'])->name('prasyarat.cek');
        
        // Kurikulum
        Route::get('/kurikulum', [KurikulumController::class, 'index'])->name('kurikulum.index');
        Route::get('/kurikulum/create', [KurikulumController::class, 'create'])->name('kurikulum.create');
        Route::post('/kurikulum', [KurikulumController::class, 'store'])->name('kurikulum.store');
        Route::get('/kurikulum/{kurikulum}', [KurikulumController::class, 'show'])->name('kurikulum.show');
        Route::get('/kurikulum/{kurikulum}/edit', [KurikulumController::class, 'edit'])->name('kurikulum.edit');
        Route::put('/kurikulum/{kurikulum}', [KurikulumController::class, 'update'])->name('kurikulum.update');
        Route::delete('/kurikulum/{kurikulum}', [KurikulumController::class, 'destroy'])->name('kurikulum.destroy');
        Route::post('/kurikulum/{kurikulum}/set-aktif', [KurikulumController::class, 'setAktif'])->name('kurikulum.set-aktif');
        Route::post('/kurikulum/{kurikulum}/copy', [KurikulumController::class, 'copy'])->name('kurikulum.copy');
        Route::get('/kurikulum/{kurikulum}/mata-kuliah', [KurikulumController::class, 'mataKuliah'])->name('kurikulum.mata-kuliah');
        Route::post('/kurikulum/{kurikulum}/mata-kuliah', [KurikulumController::class, 'addMataKuliah'])->name('kurikulum.add-mata-kuliah');
        Route::delete('/kurikulum/{kurikulum}/mata-kuliah/{mataKuliah}', [KurikulumController::class, 'removeMataKuliah'])->name('kurikulum.remove-mata-kuliah');
        
        // Bimbingan Akademik (Admin)
        Route::get('/bimbingan', [BimbinganAkademikController::class, 'index'])->name('bimbingan.index');
        Route::get('/bimbingan/create', [BimbinganAkademikController::class, 'create'])->name('bimbingan.create');
        Route::post('/bimbingan', [BimbinganAkademikController::class, 'store'])->name('bimbingan.store');
        Route::get('/bimbingan/{bimbingan}', [BimbinganAkademikController::class, 'show'])->name('bimbingan.show');
        
        // Cuti Akademik (Admin)
        Route::get('/cuti', [CutiAkademikController::class, 'index'])->name('cuti.index');
        Route::get('/cuti/{cuti}', [CutiAkademikController::class, 'show'])->name('cuti.show');
        Route::post('/cuti/{cuti}/approve', [CutiAkademikController::class, 'approve'])->name('cuti.approve');
        Route::post('/cuti/{cuti}/reject', [CutiAkademikController::class, 'reject'])->name('cuti.reject');
        Route::post('/cuti/{cuti}/activate', [CutiAkademikController::class, 'activate'])->name('cuti.activate');
        Route::post('/cuti/{cuti}/end', [CutiAkademikController::class, 'endCuti'])->name('cuti.end');
        Route::get('/cuti/{cuti}/print-surat', [CutiAkademikController::class, 'printSurat'])->name('cuti.print-surat');
        Route::get('/cuti/{cuti}/download-dokumen', [CutiAkademikController::class, 'downloadDokumen'])->name('cuti.download-dokumen');
        Route::get('/cuti-history', [CutiAkademikController::class, 'history'])->name('cuti.history');
        
        // Pengajuan Surat (Admin)
        Route::get('/admin/pengajuan-surat', [PengajuanSuratController::class, 'index'])->name('admin.pengajuan-surat.index');
        Route::get('/admin/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'show'])->name('admin.pengajuan-surat.show');
        Route::post('/admin/pengajuan-surat/{pengajuanSurat}/update-status', [PengajuanSuratController::class, 'updateStatus'])->name('admin.pengajuan-surat.update-status');
        
        // Jadwal Pengganti
        Route::resource('jadwal-pengganti', JadwalPenggantiController::class);
        Route::post('/jadwal-pengganti/{jadwalPengganti}/approve', [JadwalPenggantiController::class, 'approve'])->name('jadwal-pengganti.approve');
        Route::post('/jadwal-pengganti/{jadwalPengganti}/reject', [JadwalPenggantiController::class, 'reject'])->name('jadwal-pengganti.reject');
        Route::post('/jadwal-pengganti/{jadwalPengganti}/complete', [JadwalPenggantiController::class, 'complete'])->name('jadwal-pengganti.complete');
        
        // Wisuda Management
        Route::resource('wisuda', WisudaController::class);
        Route::patch('/wisuda/{wisuda}/toggle-status', [WisudaController::class, 'toggleStatus'])->name('wisuda.toggle-status');
        Route::get('/wisuda/{wisuda}/pendaftaran/create', [WisudaController::class, 'createPendaftaran'])->name('wisuda.pendaftaran.create');
        Route::post('/wisuda/{wisuda}/pendaftaran', [WisudaController::class, 'storePendaftaran'])->name('wisuda.pendaftaran.store');
        Route::get('/wisuda/pendaftaran/{pendaftaran}', [WisudaController::class, 'showPendaftaran'])->name('wisuda.pendaftaran.show');
        Route::post('/wisuda/pendaftaran/{pendaftaran}/verify', [WisudaController::class, 'verifyPendaftaran'])->name('wisuda.pendaftaran.verify');
        Route::get('/wisuda/search-mahasiswa', [WisudaController::class, 'searchMahasiswa'])->name('wisuda.search-mahasiswa');
        
        // Yudisium
        Route::get('/yudisium', [YudisiumController::class, 'index'])->name('yudisium.index');
        Route::get('/yudisium/candidates', [YudisiumController::class, 'candidates'])->name('yudisium.candidates');
        Route::get('/yudisium/create/{pendaftaran}', [YudisiumController::class, 'create'])->name('yudisium.create');
        Route::post('/yudisium/{pendaftaran}', [YudisiumController::class, 'store'])->name('yudisium.store');
        Route::get('/yudisium/{yudisium}', [YudisiumController::class, 'show'])->name('yudisium.show');
        Route::post('/yudisium/{yudisium}/approve', [YudisiumController::class, 'approve'])->name('yudisium.approve');
        Route::post('/yudisium/{yudisium}/reject', [YudisiumController::class, 'reject'])->name('yudisium.reject');
        Route::post('/yudisium/bulk-approve', [YudisiumController::class, 'bulkApprove'])->name('yudisium.bulk-approve');
        Route::get('/yudisium/{yudisium}/print', [YudisiumController::class, 'print'])->name('yudisium.print');
        Route::get('/yudisium/export', [YudisiumController::class, 'export'])->name('yudisium.export');
        
        // EDOM (Evaluasi Dosen oleh Mahasiswa)
        Route::get('/admin/edom', [EdomController::class, 'index'])->name('admin.edom.index');
        Route::get('/admin/edom/create', [EdomController::class, 'create'])->name('admin.edom.create');
        Route::post('/admin/edom', [EdomController::class, 'store'])->name('admin.edom.store');
        Route::get('/admin/edom/{edom}', [EdomController::class, 'show'])->name('admin.edom.show');
        Route::get('/admin/edom/{edom}/edit', [EdomController::class, 'edit'])->name('admin.edom.edit');
        Route::put('/admin/edom/{edom}', [EdomController::class, 'update'])->name('admin.edom.update');
        Route::delete('/admin/edom/{edom}', [EdomController::class, 'destroy'])->name('admin.edom.destroy');
        Route::post('/admin/edom/{edom}/hitung-rekap', [EdomController::class, 'hitungRekap'])->name('admin.edom.hitung-rekap');
        Route::get('/admin/edom/{edom}/rekap-dosen/{dosen}', [EdomController::class, 'rekapDosen'])->name('admin.edom.rekap-dosen');
        
        // Pertanyaan EDOM
        Route::get('/admin/edom-pertanyaan', [EdomController::class, 'pertanyaan'])->name('admin.edom.pertanyaan');
        Route::get('/admin/edom-pertanyaan/create', [EdomController::class, 'createPertanyaan'])->name('admin.edom.pertanyaan.create');
        Route::post('/admin/edom-pertanyaan', [EdomController::class, 'storePertanyaan'])->name('admin.edom.pertanyaan.store');
        Route::get('/admin/edom-pertanyaan/{pertanyaan}/edit', [EdomController::class, 'editPertanyaan'])->name('admin.edom.pertanyaan.edit');
        Route::put('/admin/edom-pertanyaan/{pertanyaan}', [EdomController::class, 'updatePertanyaan'])->name('admin.edom.pertanyaan.update');
        Route::delete('/admin/edom-pertanyaan/{pertanyaan}', [EdomController::class, 'destroyPertanyaan'])->name('admin.edom.pertanyaan.destroy');
        
        // Periode Ujian & Kartu Ujian (Admin)
        Route::get('/admin/periode-ujian', [PeriodeUjianController::class, 'index'])->name('admin.periode-ujian.index');
        Route::get('/admin/periode-ujian/create', [PeriodeUjianController::class, 'create'])->name('admin.periode-ujian.create');
        Route::post('/admin/periode-ujian', [PeriodeUjianController::class, 'store'])->name('admin.periode-ujian.store');
        Route::get('/admin/periode-ujian/{periodeUjian}', [PeriodeUjianController::class, 'show'])->name('admin.periode-ujian.show');
        Route::get('/admin/periode-ujian/{periodeUjian}/edit', [PeriodeUjianController::class, 'edit'])->name('admin.periode-ujian.edit');
        Route::put('/admin/periode-ujian/{periodeUjian}', [PeriodeUjianController::class, 'update'])->name('admin.periode-ujian.update');
        Route::delete('/admin/periode-ujian/{periodeUjian}', [PeriodeUjianController::class, 'destroy'])->name('admin.periode-ujian.destroy');
        Route::post('/admin/periode-ujian/{periodeUjian}/generate-kartu', [PeriodeUjianController::class, 'generateKartu'])->name('admin.periode-ujian.generate-kartu');
        
        // Kartu Ujian Admin
        Route::post('/admin/kartu-ujian/{kartuUjian}/approve', [KartuUjianController::class, 'approve'])->name('admin.kartu-ujian.approve');
        Route::post('/admin/kartu-ujian/{kartuUjian}/revoke', [KartuUjianController::class, 'revoke'])->name('admin.kartu-ujian.revoke');
        Route::get('/admin/kartu-ujian/{kartuUjian}/cetak', [KartuUjianController::class, 'cetak'])->name('admin.kartu-ujian.cetak');
        Route::post('/admin/kartu-ujian/{periodeUjian}/cetak-batch', [KartuUjianController::class, 'cetakBatch'])->name('admin.kartu-ujian.cetak-batch');
        
        // =====================
        // KONVERSI NILAI (Transfer Kredit) - Untuk Calon Mahasiswa Pindahan
        // =====================
        Route::prefix('admin/konversi-nilai')->name('admin.konversi-nilai.')->group(function () {
            Route::get('/', [KonversiNilaiController::class, 'index'])->name('index');
            Route::get('/create', [KonversiNilaiController::class, 'create'])->name('create');
            Route::post('/', [KonversiNilaiController::class, 'store'])->name('store');
            Route::get('/{pengajuanKonversi}', [KonversiNilaiController::class, 'show'])->name('show');
            Route::get('/{pengajuanKonversi}/edit', [KonversiNilaiController::class, 'edit'])->name('edit');
            Route::put('/{pengajuanKonversi}', [KonversiNilaiController::class, 'update'])->name('update');
            Route::delete('/{pengajuanKonversi}', [KonversiNilaiController::class, 'destroy'])->name('destroy');
            Route::post('/{pengajuanKonversi}/proses', [KonversiNilaiController::class, 'proses'])->name('proses');
            Route::post('/{pengajuanKonversi}/ajukan-kaprodi', [KonversiNilaiController::class, 'ajukanKeKaprodi'])->name('ajukan-kaprodi');
            Route::post('/{pengajuanKonversi}/finalisasi', [KonversiNilaiController::class, 'finalisasi'])->name('finalisasi');
            Route::post('/{pengajuanKonversi}/tambah-detail', [KonversiNilaiController::class, 'tambahDetail'])->name('tambah-detail');
            Route::delete('/detail/{detailKonversi}', [KonversiNilaiController::class, 'hapusDetail'])->name('hapus-detail');
        });

        // =====================
        // KONVERSI KEGIATAN (RPL) - Admin
        // Untuk verifikasi pengajuan konversi kegiatan dari mahasiswa
        // =====================
        Route::prefix('admin/konversi-kegiatan')->name('admin.konversi-kegiatan.')->group(function () {
            Route::get('/', [KonversiKegiatanController::class, 'adminIndex'])->name('index');
            Route::get('/{pengajuanKonversiKegiatan}', [KonversiKegiatanController::class, 'adminShow'])->name('show');
            Route::post('/detail/{detail}/verify', [KonversiKegiatanController::class, 'verifyDetail'])->name('verify-detail');
            Route::post('/{pengajuanKonversiKegiatan}/finalize', [KonversiKegiatanController::class, 'finalize'])->name('finalize');
        });
        
        // =====================
        // PKL/MAGANG/KKN (Admin)
        // =====================
        Route::prefix('admin/kegiatan-lapangan')->name('admin.kegiatan-lapangan.')->group(function () {
            // Jenis Kegiatan
            Route::get('/jenis', [KegiatanLapanganController::class, 'jenisIndex'])->name('jenis.index');
            Route::post('/jenis', [KegiatanLapanganController::class, 'jenisStore'])->name('jenis.store');
            Route::put('/jenis/{jenisKegiatanLapangan}', [KegiatanLapanganController::class, 'jenisUpdate'])->name('jenis.update');
            Route::delete('/jenis/{jenisKegiatanLapangan}', [KegiatanLapanganController::class, 'jenisDestroy'])->name('jenis.destroy');
            
            // Mitra Kegiatan
            Route::get('/mitra', [KegiatanLapanganController::class, 'mitraIndex'])->name('mitra.index');
            Route::get('/mitra/create', [KegiatanLapanganController::class, 'mitraCreate'])->name('mitra.create');
            Route::post('/mitra', [KegiatanLapanganController::class, 'mitraStore'])->name('mitra.store');
            Route::get('/mitra/{mitraKegiatan}/edit', [KegiatanLapanganController::class, 'mitraEdit'])->name('mitra.edit');
            Route::put('/mitra/{mitraKegiatan}', [KegiatanLapanganController::class, 'mitraUpdate'])->name('mitra.update');
            Route::delete('/mitra/{mitraKegiatan}', [KegiatanLapanganController::class, 'mitraDestroy'])->name('mitra.destroy');
            
            // Periode Kegiatan
            Route::get('/periode', [KegiatanLapanganController::class, 'periodeIndex'])->name('periode.index');
            Route::get('/periode/create', [KegiatanLapanganController::class, 'periodeCreate'])->name('periode.create');
            Route::post('/periode', [KegiatanLapanganController::class, 'periodeStore'])->name('periode.store');
            Route::get('/periode/{periodeKegiatanLapangan}', [KegiatanLapanganController::class, 'periodeShow'])->name('periode.show');
            Route::get('/periode/{periodeKegiatanLapangan}/edit', [KegiatanLapanganController::class, 'periodeEdit'])->name('periode.edit');
            Route::put('/periode/{periodeKegiatanLapangan}', [KegiatanLapanganController::class, 'periodeUpdate'])->name('periode.update');
            Route::delete('/periode/{periodeKegiatanLapangan}', [KegiatanLapanganController::class, 'periodeDestroy'])->name('periode.destroy');
            
            // Pendaftaran
            Route::get('/pendaftaran', [KegiatanLapanganController::class, 'pendaftaranIndex'])->name('pendaftaran.index');
            Route::get('/pendaftaran/{pendaftaranKegiatanLapangan}', [KegiatanLapanganController::class, 'pendaftaranShow'])->name('pendaftaran.show');
            Route::post('/pendaftaran/{pendaftaranKegiatanLapangan}/proses', [KegiatanLapanganController::class, 'pendaftaranProses'])->name('pendaftaran.proses');
            Route::post('/pendaftaran/{pendaftaranKegiatanLapangan}/mulai', [KegiatanLapanganController::class, 'pendaftaranMulai'])->name('pendaftaran.mulai');
            Route::post('/pendaftaran/{pendaftaranKegiatanLapangan}/selesai', [KegiatanLapanganController::class, 'pendaftaranSelesai'])->name('pendaftaran.selesai');
            
            // Penilaian
            Route::post('/pendaftaran/{pendaftaranKegiatanLapangan}/penilaian', [KegiatanLapanganController::class, 'penilaianStore'])->name('penilaian.store');
            
            // Log Kegiatan
            Route::post('/log/{logKegiatanLapangan}/approve', [KegiatanLapanganController::class, 'logApprove'])->name('log.approve');
            Route::post('/log/{logKegiatanLapangan}/revisi', [KegiatanLapanganController::class, 'logRevisi'])->name('log.revisi');
        });
        
        // =====================
        // TUGAS AKHIR / SKRIPSI (Admin)
        // =====================
        Route::prefix('admin/tugas-akhir')->name('admin.tugas-akhir.')->group(function () {
            Route::get('/', [TugasAkhirController::class, 'index'])->name('index');
            Route::get('/{tugasAkhir}', [TugasAkhirController::class, 'show'])->name('show');
            Route::post('/{tugasAkhir}/approval-judul', [TugasAkhirController::class, 'approvalJudul'])->name('approval-judul');
            Route::post('/{tugasAkhir}/update-status', [TugasAkhirController::class, 'updateStatus'])->name('update-status');
            Route::post('/{tugasAkhir}/bimbingan', [TugasAkhirController::class, 'bimbinganStore'])->name('bimbingan.store');
            Route::put('/bimbingan/{bimbingan}', [TugasAkhirController::class, 'bimbinganUpdate'])->name('bimbingan.update');
            
            // Seminar Proposal
            Route::get('/seminar/list', [TugasAkhirController::class, 'seminarIndex'])->name('seminar.index');
            Route::post('/{tugasAkhir}/seminar-jadwalkan', [TugasAkhirController::class, 'seminarJadwalkan'])->name('seminar.jadwalkan');
            Route::post('/seminar/{seminar}/nilai', [TugasAkhirController::class, 'seminarNilai'])->name('seminar.nilai');
            
            // Sidang
            Route::get('/sidang/list', [TugasAkhirController::class, 'sidangIndex'])->name('sidang.index');
            Route::post('/{tugasAkhir}/sidang-jadwalkan', [TugasAkhirController::class, 'sidangJadwalkan'])->name('sidang.jadwalkan');
            Route::post('/sidang/{sidang}/nilai', [TugasAkhirController::class, 'sidangNilai'])->name('sidang.nilai');
            Route::post('/sidang/{sidang}/revisi', [TugasAkhirController::class, 'revisiStore'])->name('revisi.store');
            Route::post('/revisi/{revisi}/selesai', [TugasAkhirController::class, 'revisiSelesai'])->name('revisi.selesai');
        });
        
        // Jadwal Kuliah
        Route::resource('jadwal-kuliah', JadwalKuliahController::class);
        
        // Pembayaran (Admin)
        Route::resource('pembayaran', PembayaranController::class);
        Route::post('/pembayaran/{pembayaran}/konfirmasi', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
        
        // Pengumuman (Admin CRUD)
        Route::get('/pengumuman/create', [PengumumanController::class, 'create'])->name('pengumuman.create');
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::get('/pengumuman/{pengumuman}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
        Route::put('/pengumuman/{pengumuman}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::delete('/pengumuman/{pengumuman}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
        
        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/mahasiswa', [LaporanController::class, 'mahasiswa'])->name('laporan.mahasiswa');
        Route::get('/laporan/nilai', [LaporanController::class, 'nilai'])->name('laporan.nilai');
        Route::get('/laporan/keuangan', [LaporanController::class, 'keuangan'])->name('laporan.keuangan');
        Route::get('/laporan/absensi', [LaporanController::class, 'absensi'])->name('laporan.absensi');
        Route::get('/laporan/statistik', [LaporanController::class, 'statistik'])->name('laporan.statistik');
        Route::get('/laporan/wisuda', [LaporanController::class, 'wisuda'])->name('laporan.wisuda');
        Route::get('/laporan/ipk', [LaporanController::class, 'ipk'])->name('laporan.ipk');
        Route::get('/laporan/krs', [LaporanController::class, 'krs'])->name('laporan.krs');
        Route::get('/laporan/dosen', [LaporanController::class, 'dosen'])->name('laporan.dosen');
        Route::get('/laporan/kelulusan', [LaporanController::class, 'kelulusan'])->name('laporan.kelulusan');
        
        // Export CSV
        Route::get('/export/mahasiswa', [ExportController::class, 'mahasiswa'])->name('export.mahasiswa');
        Route::get('/export/dosen', [ExportController::class, 'dosen'])->name('export.dosen');
        Route::get('/export/nilai', [ExportController::class, 'nilai'])->name('export.nilai');
        Route::get('/export/pembayaran', [ExportController::class, 'pembayaran'])->name('export.pembayaran');
        Route::get('/export/absensi', [ExportController::class, 'absensi'])->name('export.absensi');
        
        // Import Data
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import/mahasiswa', [ImportController::class, 'mahasiswa'])->name('import.mahasiswa');
        Route::post('/import/dosen', [ImportController::class, 'dosen'])->name('import.dosen');
        Route::get('/import/template/mahasiswa', [ImportController::class, 'templateMahasiswa'])->name('import.template.mahasiswa');
        Route::get('/import/template/dosen', [ImportController::class, 'templateDosen'])->name('import.template.dosen');
        
        // Kalender Akademik (Admin CRUD)
        Route::get('/kalender/list', [KalenderAkademikController::class, 'list'])->name('kalender.list');
        Route::get('/kalender/create', [KalenderAkademikController::class, 'create'])->name('kalender.create');
        Route::post('/kalender', [KalenderAkademikController::class, 'store'])->name('kalender.store');
        Route::get('/kalender/{id}/edit', [KalenderAkademikController::class, 'edit'])->name('kalender.edit');
        Route::put('/kalender/{id}', [KalenderAkademikController::class, 'update'])->name('kalender.update');
        Route::delete('/kalender/{id}', [KalenderAkademikController::class, 'destroy'])->name('kalender.destroy');
        
        // Activity Log
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('/activity-log/export', [ActivityLogController::class, 'export'])->name('activity-log.export');
        Route::post('/activity-log/clear', [ActivityLogController::class, 'clear'])->name('activity-log.clear');
        Route::get('/activity-log/{id}', [ActivityLogController::class, 'show'])->name('activity-log.show');
        
        // System Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/clear-cache', [SettingController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('/settings/test-email', [SettingController::class, 'testEmail'])->name('settings.test-email');
        
        // Konfigurasi Cetak
        Route::get('/konfigurasi-cetak', [KonfigurasiCetakController::class, 'index'])->name('konfigurasi-cetak.index');
        Route::get('/konfigurasi-cetak/{konfigurasiCetak}/edit', [KonfigurasiCetakController::class, 'edit'])->name('konfigurasi-cetak.edit');
        Route::put('/konfigurasi-cetak/{konfigurasiCetak}', [KonfigurasiCetakController::class, 'update'])->name('konfigurasi-cetak.update');
        Route::get('/konfigurasi-cetak/{konfigurasiCetak}/preview', [KonfigurasiCetakController::class, 'preview'])->name('konfigurasi-cetak.preview');
        Route::get('/konfigurasi-cetak/{konfigurasiCetak}/preview-pdf', [KonfigurasiCetakController::class, 'previewPdf'])->name('konfigurasi-cetak.preview-pdf');
        Route::post('/konfigurasi-cetak/{konfigurasiCetak}/reset', [KonfigurasiCetakController::class, 'reset'])->name('konfigurasi-cetak.reset');
        Route::post('/konfigurasi-cetak/reset-all', [KonfigurasiCetakController::class, 'resetAll'])->name('konfigurasi-cetak.reset-all');
        
        // Persetujuan Jadwal Pertemuan (Admin)
        Route::get('/pertemuan-approval', [PertemuanController::class, 'adminApprovalList'])->name('pertemuan.approval');
        Route::get('/admin/pertemuan/{jadwalKuliah}', [PertemuanController::class, 'adminIndex'])->name('pertemuan.admin.index');
        Route::post('/pertemuan/{pertemuan}/approve', [PertemuanController::class, 'approve'])->name('pertemuan.approve');
        Route::post('/pertemuan/{pertemuan}/reject', [PertemuanController::class, 'reject'])->name('pertemuan.reject');
        Route::post('/pertemuan/{pertemuan}/set-tanggal', [PertemuanController::class, 'adminSetTanggal'])->name('pertemuan.admin.set-tanggal');
        Route::post('/admin/pertemuan/{jadwalKuliah}/bulk-set', [PertemuanController::class, 'adminBulkSetTanggal'])->name('pertemuan.admin.bulk-set');
        
        // Backup Database
        Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
        Route::post('/backup', [BackupController::class, 'create'])->name('backup.create');
        Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download');
        Route::post('/backup/{filename}/restore', [BackupController::class, 'restore'])->name('backup.restore');
        Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');
        Route::post('/backup/cleanup', [BackupController::class, 'cleanup'])->name('backup.cleanup');
        
        // =====================
        // KEUANGAN MODULE
        // =====================
        // Dashboard Keuangan
        Route::get('/keuangan/dashboard', [KeuanganDashboardController::class, 'index'])->name('keuangan.dashboard');
        
        // Tarif
        Route::resource('tarif', TarifController::class);
        Route::patch('/tarif/{tarif}/toggle-status', [TarifController::class, 'toggleStatus'])->name('tarif.toggle-status');
        
        // Tagihan
        Route::get('/tagihan/generate', [TagihanController::class, 'generateForm'])->name('tagihan.generate');
        Route::post('/tagihan/generate-massal', [TagihanController::class, 'generateMassal'])->name('tagihan.generate-massal');
        Route::post('/tagihan/update-denda', [TagihanController::class, 'updateDenda'])->name('tagihan.update-denda');
        Route::resource('tagihan', TagihanController::class);
        
        // Transaksi Pembayaran
        Route::get('/transaksi-pembayaran/laporan', [TransaksiPembayaranController::class, 'laporan'])->name('transaksi-pembayaran.laporan');
        Route::get('/transaksi-pembayaran/unmatched', [TransaksiPembayaranController::class, 'getUnmatched'])->name('transaksi-pembayaran.unmatched');
        Route::patch('/transaksi-pembayaran/{transaksiPembayaran}/verify', [TransaksiPembayaranController::class, 'verify'])->name('transaksi-pembayaran.verify');
        Route::patch('/transaksi-pembayaran/{transaksiPembayaran}/reject', [TransaksiPembayaranController::class, 'reject'])->name('transaksi-pembayaran.reject');
        Route::resource('transaksi-pembayaran', TransaksiPembayaranController::class)->except(['edit', 'update', 'destroy']);
        
        // Beasiswa
        Route::get('/beasiswa/penerima', [BeasiswaController::class, 'penerimIndex'])->name('beasiswa.penerima.index');
        Route::get('/beasiswa/penerima/create', [BeasiswaController::class, 'penerimCreate'])->name('beasiswa.penerima.create');
        Route::post('/beasiswa/penerima', [BeasiswaController::class, 'penerimStore'])->name('beasiswa.penerima.store');
        Route::patch('/beasiswa/penerima/{penerima}/approve', [BeasiswaController::class, 'approve'])->name('beasiswa.penerima.approve');
        Route::patch('/beasiswa/penerima/{penerima}/reject', [BeasiswaController::class, 'reject'])->name('beasiswa.penerima.reject');
        Route::patch('/beasiswa/penerima/{penerima}/revoke', [BeasiswaController::class, 'revoke'])->name('beasiswa.penerima.revoke');
        Route::patch('/beasiswa/{beasiswa}/toggle-status', [BeasiswaController::class, 'toggleStatus'])->name('beasiswa.toggle-status');
        Route::resource('beasiswa', BeasiswaController::class);
        
        // Pengaturan Denda
        Route::patch('/pengaturan-denda/{pengaturanDenda}/toggle-status', [PengaturanDendaController::class, 'toggleStatus'])->name('pengaturan-denda.toggle-status');
        Route::resource('pengaturan-denda', PengaturanDendaController::class)->except(['show']);

        // Skema Cicilan
        Route::patch('/skema-cicilan/{skemaCicilan}/toggle-status', [SkemaCicilanController::class, 'toggleStatus'])->name('skema-cicilan.toggle-status');
        Route::resource('skema-cicilan', SkemaCicilanController::class)->except(['show']);

        // Manajemen Cicilan
        Route::get('/cicilan/simulasi', [CicilanController::class, 'simulasi'])->name('cicilan.simulasi');
        Route::get('/cicilan/{detailCicilan}/bayar', [CicilanController::class, 'bayar'])->name('cicilan.bayar');
        Route::post('/cicilan/{detailCicilan}/proses-bayar', [CicilanController::class, 'prosesBayar'])->name('cicilan.proses-bayar');
        Route::delete('/cicilan/{cicilan}/batalkan', [CicilanController::class, 'batalkan'])->name('cicilan.batalkan');
        Route::resource('cicilan', CicilanController::class)->except(['edit', 'update', 'destroy']);

        // Notifikasi Keuangan (Admin)
        Route::get('/admin/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
        Route::post('/admin/notifikasi/{id}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.mark-read');
        Route::post('/admin/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.mark-all-read');
        Route::delete('/admin/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
        Route::post('/admin/notifikasi/bulk-delete', [NotifikasiController::class, 'bulkDelete'])->name('notifikasi.bulk-delete');
        Route::post('/admin/notifikasi/send-manual', [NotifikasiController::class, 'sendManual'])->name('notifikasi.send-manual');

        // Laporan Keuangan
        Route::get('/laporan/dashboard', [LaporanKeuanganController::class, 'dashboard'])->name('laporan.dashboard');
        Route::get('/laporan/pendapatan', [LaporanKeuanganController::class, 'pendapatan'])->name('laporan.pendapatan');
        Route::get('/laporan/pendapatan/excel', [LaporanKeuanganController::class, 'exportPendapatanExcel'])->name('laporan.pendapatan.excel');
        Route::get('/laporan/pendapatan/pdf', [LaporanKeuanganController::class, 'exportPendapatanPdf'])->name('laporan.pendapatan.pdf');
        Route::get('/laporan/tunggakan', [LaporanKeuanganController::class, 'tunggakan'])->name('laporan.tunggakan');
        Route::get('/laporan/tunggakan/excel', [LaporanKeuanganController::class, 'exportTunggakanExcel'])->name('laporan.tunggakan.excel');
        Route::get('/laporan/tunggakan/pdf', [LaporanKeuanganController::class, 'exportTunggakanPdf'])->name('laporan.tunggakan.pdf');
        Route::get('/laporan/beasiswa', [LaporanKeuanganController::class, 'beasiswa'])->name('laporan.beasiswa');
        Route::get('/laporan/beasiswa/excel', [LaporanKeuanganController::class, 'exportBeasiswaExcel'])->name('laporan.beasiswa.excel');
        Route::get('/laporan/beasiswa/pdf', [LaporanKeuanganController::class, 'exportBeasiswaPdf'])->name('laporan.beasiswa.pdf');

        // Invoice & Kwitansi
        Route::get('/invoice/{tagihan}', [InvoiceController::class, 'invoice'])->name('invoice.invoice');
        Route::get('/invoice/{tagihan}/download', [InvoiceController::class, 'downloadInvoice'])->name('invoice.download');
        Route::get('/kwitansi/{transaksi}', [InvoiceController::class, 'kwitansi'])->name('invoice.kwitansi');
        Route::get('/kwitansi/{transaksi}/download', [InvoiceController::class, 'downloadKwitansi'])->name('invoice.kwitansi.download');
        Route::get('/kartu-tagihan/{mahasiswa}', [InvoiceController::class, 'kartuTagihan'])->name('invoice.kartu-tagihan');

        // =====================
        // REKONSILIASI BANK
        // =====================
        // Akun Bank
        Route::patch('/akun-bank/{akunBank}/toggle-status', [AkunBankController::class, 'toggleStatus'])->name('akun-bank.toggle-status');
        Route::resource('akun-bank', AkunBankController::class);

        // Mutasi Bank
        Route::post('/mutasi-bank/import', [MutasiBankController::class, 'import'])->name('mutasi-bank.import');
        Route::post('/mutasi-bank/auto-match', [MutasiBankController::class, 'autoMatch'])->name('mutasi-bank.auto-match');
        Route::post('/mutasi-bank/{mutasiBank}/match', [MutasiBankController::class, 'match'])->name('mutasi-bank.match');
        Route::post('/mutasi-bank/{mutasiBank}/unmatch', [MutasiBankController::class, 'unmatch'])->name('mutasi-bank.unmatch');
        Route::post('/mutasi-bank/{mutasiBank}/mark-manual', [MutasiBankController::class, 'markManual'])->name('mutasi-bank.mark-manual');
        Route::resource('mutasi-bank', MutasiBankController::class)->except(['edit', 'update']);

        // Rekonsiliasi
        Route::get('/rekonsiliasi/{rekonsiliasi}/process', [RekonsiliasiBankController::class, 'process'])->name('rekonsiliasi.process');
        Route::get('/rekonsiliasi/{rekonsiliasi}/report', [RekonsiliasiBankController::class, 'report'])->name('rekonsiliasi.report');
        Route::post('/rekonsiliasi/{rekonsiliasi}/match-detail', [RekonsiliasiBankController::class, 'matchDetail'])->name('rekonsiliasi.match-detail');
        Route::post('/rekonsiliasi/{rekonsiliasi}/unmatch-detail', [RekonsiliasiBankController::class, 'unmatchDetail'])->name('rekonsiliasi.unmatch-detail');
        Route::post('/rekonsiliasi/{rekonsiliasi}/ignore-detail', [RekonsiliasiBankController::class, 'ignoreDetail'])->name('rekonsiliasi.ignore-detail');
        Route::post('/rekonsiliasi/{rekonsiliasi}/mark-manual', [RekonsiliasiBankController::class, 'markManual'])->name('rekonsiliasi.mark-manual');
        Route::post('/rekonsiliasi/{rekonsiliasi}/auto-match', [RekonsiliasiBankController::class, 'autoMatchAll'])->name('rekonsiliasi.auto-match');
        Route::post('/rekonsiliasi/{rekonsiliasi}/complete', [RekonsiliasiBankController::class, 'complete'])->name('rekonsiliasi.complete');
        Route::post('/rekonsiliasi/{rekonsiliasi}/approve', [RekonsiliasiBankController::class, 'approve'])->name('rekonsiliasi.approve');
        Route::resource('rekonsiliasi', RekonsiliasiBankController::class)->except(['edit', 'update']);

        // =====================
        // REFUND / PENGEMBALIAN DANA
        // =====================
        Route::prefix('refund')->name('keuangan.refund.')->group(function () {
            Route::get('/search-transaksi', [RefundController::class, 'searchTransaksi'])->name('search-transaksi');
            Route::get('/search-mahasiswa', [RefundController::class, 'searchMahasiswa'])->name('search-mahasiswa');
            Route::get('/export', [RefundController::class, 'export'])->name('export');
            Route::post('/{refund}/process', [RefundController::class, 'process'])->name('process');
            Route::post('/{refund}/approve', [RefundController::class, 'approve'])->name('approve');
            Route::post('/{refund}/reject', [RefundController::class, 'reject'])->name('reject');
            Route::post('/{refund}/complete', [RefundController::class, 'complete'])->name('complete');
            Route::post('/{refund}/cancel', [RefundController::class, 'cancel'])->name('cancel');
        });
        Route::resource('refund', RefundController::class)->names([
            'index' => 'keuangan.refund.index',
            'create' => 'keuangan.refund.create',
            'store' => 'keuangan.refund.store',
            'show' => 'keuangan.refund.show',
            'edit' => 'keuangan.refund.edit',
            'update' => 'keuangan.refund.update',
            'destroy' => 'keuangan.refund.destroy',
        ]);

        // =====================
        // POTONGAN & DISKON
        // =====================
        // Jenis Potongan
        Route::patch('/jenis-potongan/{jenisPotongan}/toggle-status', [JenisPotonganController::class, 'toggleStatus'])->name('jenis-potongan.toggle-status');
        Route::resource('jenis-potongan', JenisPotonganController::class);

        // Periode Diskon
        Route::patch('/periode-diskon/{periodeDiskon}/toggle-status', [PeriodeDiskonController::class, 'toggleStatus'])->name('periode-diskon.toggle-status');
        Route::resource('periode-diskon', PeriodeDiskonController::class);

        // Potongan Mahasiswa
        Route::get('/potongan-mahasiswa/search-mahasiswa', [PotonganMahasiswaController::class, 'searchMahasiswa'])->name('potongan-mahasiswa.search-mahasiswa');
        Route::get('/potongan-mahasiswa/search-tagihan', [PotonganMahasiswaController::class, 'searchTagihan'])->name('potongan-mahasiswa.search-tagihan');
        Route::get('/potongan-mahasiswa/bulk-create', [PotonganMahasiswaController::class, 'bulkCreate'])->name('potongan-mahasiswa.bulk-create');
        Route::post('/potongan-mahasiswa/bulk-store', [PotonganMahasiswaController::class, 'bulkStore'])->name('potongan-mahasiswa.bulk-store');
        Route::post('/potongan-mahasiswa/apply-to-tagihan', [PotonganMahasiswaController::class, 'applyToTagihan'])->name('potongan-mahasiswa.apply-to-tagihan');
        Route::get('/potongan-mahasiswa/export', [PotonganMahasiswaController::class, 'export'])->name('potongan-mahasiswa.export');
        Route::post('/potongan-mahasiswa/{potonganMahasiswa}/approve', [PotonganMahasiswaController::class, 'approve'])->name('potongan-mahasiswa.approve');
        Route::post('/potongan-mahasiswa/{potonganMahasiswa}/reject', [PotonganMahasiswaController::class, 'reject'])->name('potongan-mahasiswa.reject');
        Route::post('/potongan-mahasiswa/{potonganMahasiswa}/cancel', [PotonganMahasiswaController::class, 'cancel'])->name('potongan-mahasiswa.cancel');
        Route::resource('potongan-mahasiswa', PotonganMahasiswaController::class);

        // =====================
        // MODUL PMB (PENERIMAAN MAHASISWA BARU)
        // =====================
        Route::prefix('pmb')->name('pmb.')->group(function () {
            // Dashboard PMB
            Route::get('/', [\App\Http\Controllers\PmbController::class, 'dashboard'])->name('dashboard');

            // Periode PMB
            Route::post('/periode/{periode}/set-active', [\App\Http\Controllers\PeriodePmbController::class, 'setActive'])->name('periode.set-active');
            Route::resource('periode', \App\Http\Controllers\PeriodePmbController::class)->names([
                'index' => 'periode.index',
                'create' => 'periode.create',
                'store' => 'periode.store',
                'show' => 'periode.show',
                'edit' => 'periode.edit',
                'update' => 'periode.update',
                'destroy' => 'periode.destroy',
            ]);

            // Gelombang PMB
            Route::post('/gelombang/{gelombang}/set-active', [\App\Http\Controllers\GelombangPmbController::class, 'setActive'])->name('gelombang.set-active');
            Route::resource('gelombang', \App\Http\Controllers\GelombangPmbController::class)->names([
                'index' => 'gelombang.index',
                'create' => 'gelombang.create',
                'store' => 'gelombang.store',
                'show' => 'gelombang.show',
                'edit' => 'gelombang.edit',
                'update' => 'gelombang.update',
                'destroy' => 'gelombang.destroy',
            ]);

            // Jalur Seleksi
            Route::post('/jalur-seleksi/{jalur}/toggle-active', [\App\Http\Controllers\JalurSeleksiController::class, 'toggleActive'])->name('jalur-seleksi.toggle-active');
            Route::resource('jalur-seleksi', \App\Http\Controllers\JalurSeleksiController::class)->names([
                'index' => 'jalur-seleksi.index',
                'create' => 'jalur-seleksi.create',
                'store' => 'jalur-seleksi.store',
                'show' => 'jalur-seleksi.show',
                'edit' => 'jalur-seleksi.edit',
                'update' => 'jalur-seleksi.update',
                'destroy' => 'jalur-seleksi.destroy',
            ]);

            // Biaya Pendaftaran
            Route::post('/biaya-pendaftaran/generate-batch', [\App\Http\Controllers\BiayaPendaftaranController::class, 'generateBatch'])->name('biaya-pendaftaran.generate-batch');
            Route::post('/biaya-pendaftaran/copy-gelombang', [\App\Http\Controllers\BiayaPendaftaranController::class, 'copyFromGelombang'])->name('biaya-pendaftaran.copy-gelombang');
            Route::resource('biaya-pendaftaran', \App\Http\Controllers\BiayaPendaftaranController::class)->except(['show', 'create'])->names([
                'index' => 'biaya-pendaftaran.index',
                'store' => 'biaya-pendaftaran.store',
                'edit' => 'biaya-pendaftaran.edit',
                'update' => 'biaya-pendaftaran.update',
                'destroy' => 'biaya-pendaftaran.destroy',
            ]);

            // Kuota PMB
            Route::post('/kuota/generate-batch', [\App\Http\Controllers\KuotaPmbController::class, 'generateBatch'])->name('kuota.generate-batch');
            Route::post('/kuota/copy-gelombang', [\App\Http\Controllers\KuotaPmbController::class, 'copyFromGelombang'])->name('kuota.copy-gelombang');
            Route::post('/kuota/reset-terisi', [\App\Http\Controllers\KuotaPmbController::class, 'resetTerisi'])->name('kuota.reset-terisi');
            Route::resource('kuota', \App\Http\Controllers\KuotaPmbController::class)->except(['show', 'create'])->names([
                'index' => 'kuota.index',
                'store' => 'kuota.store',
                'edit' => 'kuota.edit',
                'update' => 'kuota.update',
                'destroy' => 'kuota.destroy',
            ]);

            // Calon Mahasiswa
            Route::get('/calon-mahasiswa/{calon}/verifikasi-dokumen', [\App\Http\Controllers\CalonMahasiswaController::class, 'verifikasiDokumen'])->name('calon-mahasiswa.verifikasi-dokumen');
            Route::post('/calon-mahasiswa/{calon}/update-verifikasi-dokumen', [\App\Http\Controllers\CalonMahasiswaController::class, 'updateVerifikasiDokumen'])->name('calon-mahasiswa.update-verifikasi-dokumen');
            Route::get('/calon-mahasiswa/{calon}/cetak-kartu-peserta', [\App\Http\Controllers\CalonMahasiswaController::class, 'cetakKartuPeserta'])->name('calon-mahasiswa.cetak-kartu-peserta');
            Route::post('/calon-mahasiswa/{calon}/update-status', [\App\Http\Controllers\CalonMahasiswaController::class, 'updateStatus'])->name('calon-mahasiswa.update-status');
            Route::post('/calon-mahasiswa/{calon}/upload-dokumen', [\App\Http\Controllers\CalonMahasiswaController::class, 'uploadDokumen'])->name('calon-mahasiswa.upload-dokumen');
            Route::delete('/calon-mahasiswa/{calon}/dokumen/{dokumen}', [\App\Http\Controllers\CalonMahasiswaController::class, 'deleteDokumen'])->name('calon-mahasiswa.delete-dokumen');
            Route::post('/calon-mahasiswa/{calon}/set-lulus-administrasi', [\App\Http\Controllers\CalonMahasiswaController::class, 'setLulusAdministrasi'])->name('calon-mahasiswa.set-lulus-administrasi');
            Route::resource('calon-mahasiswa', \App\Http\Controllers\CalonMahasiswaController::class)->names([
                'index' => 'calon-mahasiswa.index',
                'create' => 'calon-mahasiswa.create',
                'store' => 'calon-mahasiswa.store',
                'show' => 'calon-mahasiswa.show',
                'edit' => 'calon-mahasiswa.edit',
                'update' => 'calon-mahasiswa.update',
                'destroy' => 'calon-mahasiswa.destroy',
            ]);

            // Pembayaran PMB
            Route::post('/pembayaran/{pembayaran}/verifikasi', [\App\Http\Controllers\PembayaranPmbController::class, 'verifikasi'])->name('pembayaran.verifikasi');
            Route::post('/pembayaran/{pembayaran}/konfirmasi', [\App\Http\Controllers\PembayaranPmbController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
            Route::post('/pembayaran/batch-verifikasi', [\App\Http\Controllers\PembayaranPmbController::class, 'batchVerifikasi'])->name('pembayaran.batch-verifikasi');
            Route::post('/pembayaran/generate-tagihan', [\App\Http\Controllers\PembayaranPmbController::class, 'generateTagihan'])->name('pembayaran.generate-tagihan');
            Route::get('/pembayaran', [\App\Http\Controllers\PembayaranPmbController::class, 'index'])->name('pembayaran.index');
            Route::get('/pembayaran/{pembayaran}', [\App\Http\Controllers\PembayaranPmbController::class, 'show'])->name('pembayaran.show');

            // Seleksi PMB
            Route::get('/seleksi', [\App\Http\Controllers\SeleksiPmbController::class, 'index'])->name('seleksi.index');
            Route::get('/seleksi/input-nilai', [\App\Http\Controllers\SeleksiPmbController::class, 'inputNilai'])->name('seleksi.input-nilai');
            Route::post('/seleksi/store-nilai', [\App\Http\Controllers\SeleksiPmbController::class, 'storeNilai'])->name('seleksi.store-nilai');
            Route::get('/seleksi/proses', [\App\Http\Controllers\SeleksiPmbController::class, 'prosesSeleksi'])->name('seleksi.proses');
            Route::post('/seleksi/execute', [\App\Http\Controllers\SeleksiPmbController::class, 'executeSeleksi'])->name('seleksi.execute');
            Route::get('/seleksi/hasil', [\App\Http\Controllers\SeleksiPmbController::class, 'hasilSeleksi'])->name('seleksi.hasil');
            Route::get('/seleksi/export-hasil', [\App\Http\Controllers\SeleksiPmbController::class, 'exportHasil'])->name('seleksi.export-hasil');
            Route::post('/seleksi/hasil/{hasil}/update', [\App\Http\Controllers\SeleksiPmbController::class, 'updateHasil'])->name('seleksi.update-hasil');

            // Daftar Ulang
            Route::get('/daftar-ulang/generate', [\App\Http\Controllers\DaftarUlangController::class, 'showGenerate'])->name('daftar-ulang.generate');
            Route::post('/daftar-ulang/generate', [\App\Http\Controllers\DaftarUlangController::class, 'storeGenerate'])->name('daftar-ulang.store-generate');
            Route::post('/daftar-ulang/{daftarUlang}/verifikasi-pembayaran', [\App\Http\Controllers\DaftarUlangController::class, 'verifikasiPembayaran'])->name('daftar-ulang.verifikasi-pembayaran');
            Route::post('/daftar-ulang/{daftarUlang}/proses-mahasiswa', [\App\Http\Controllers\DaftarUlangController::class, 'prosesMahasiswa'])->name('daftar-ulang.proses-mahasiswa');
            Route::post('/daftar-ulang/batch-proses', [\App\Http\Controllers\DaftarUlangController::class, 'batchProsesMahasiswa'])->name('daftar-ulang.batch-proses');
            Route::get('/daftar-ulang', [\App\Http\Controllers\DaftarUlangController::class, 'index'])->name('daftar-ulang.index');
            Route::get('/daftar-ulang/{daftarUlang}', [\App\Http\Controllers\DaftarUlangController::class, 'show'])->name('daftar-ulang.show');
            
            // ============================================
            // KELOLA KONTEN PORTAL PMB
            // ============================================
            Route::prefix('konten-portal')->name('konten-pmb.')->group(function () {
                Route::get('/', [KontenPmbController::class, 'index'])->name('index');
                
                // Slider
                Route::get('/slider', [KontenPmbController::class, 'sliderIndex'])->name('slider.index');
                Route::get('/slider/create', [KontenPmbController::class, 'sliderCreate'])->name('slider.create');
                Route::post('/slider', [KontenPmbController::class, 'sliderStore'])->name('slider.store');
                Route::get('/slider/{slider}/edit', [KontenPmbController::class, 'sliderEdit'])->name('slider.edit');
                Route::put('/slider/{slider}', [KontenPmbController::class, 'sliderUpdate'])->name('slider.update');
                Route::delete('/slider/{slider}', [KontenPmbController::class, 'sliderDestroy'])->name('slider.destroy');
                
                // Berita
                Route::get('/berita', [KontenPmbController::class, 'beritaIndex'])->name('berita.index');
                Route::get('/berita/create', [KontenPmbController::class, 'beritaCreate'])->name('berita.create');
                Route::post('/berita', [KontenPmbController::class, 'beritaStore'])->name('berita.store');
                Route::get('/berita/{berita}/edit', [KontenPmbController::class, 'beritaEdit'])->name('berita.edit');
                Route::put('/berita/{berita}', [KontenPmbController::class, 'beritaUpdate'])->name('berita.update');
                Route::delete('/berita/{berita}', [KontenPmbController::class, 'beritaDestroy'])->name('berita.destroy');
                
                // FAQ
                Route::get('/faq', [KontenPmbController::class, 'faqIndex'])->name('faq.index');
                Route::get('/faq/create', [KontenPmbController::class, 'faqCreate'])->name('faq.create');
                Route::post('/faq', [KontenPmbController::class, 'faqStore'])->name('faq.store');
                Route::get('/faq/{faq}/edit', [KontenPmbController::class, 'faqEdit'])->name('faq.edit');
                Route::put('/faq/{faq}', [KontenPmbController::class, 'faqUpdate'])->name('faq.update');
                Route::delete('/faq/{faq}', [KontenPmbController::class, 'faqDestroy'])->name('faq.destroy');
                
                // Testimoni
                Route::get('/testimoni', [KontenPmbController::class, 'testimoniIndex'])->name('testimoni.index');
                Route::get('/testimoni/create', [KontenPmbController::class, 'testimoniCreate'])->name('testimoni.create');
                Route::post('/testimoni', [KontenPmbController::class, 'testimoniStore'])->name('testimoni.store');
                Route::get('/testimoni/{testimoni}/edit', [KontenPmbController::class, 'testimoniEdit'])->name('testimoni.edit');
                Route::put('/testimoni/{testimoni}', [KontenPmbController::class, 'testimoniUpdate'])->name('testimoni.update');
                Route::delete('/testimoni/{testimoni}', [KontenPmbController::class, 'testimoniDestroy'])->name('testimoni.destroy');
                
                // Galeri
                Route::get('/galeri', [KontenPmbController::class, 'galeriIndex'])->name('galeri.index');
                Route::get('/galeri/create', [KontenPmbController::class, 'galeriCreate'])->name('galeri.create');
                Route::post('/galeri', [KontenPmbController::class, 'galeriStore'])->name('galeri.store');
                Route::get('/galeri/{galeri}/edit', [KontenPmbController::class, 'galeriEdit'])->name('galeri.edit');
                Route::put('/galeri/{galeri}', [KontenPmbController::class, 'galeriUpdate'])->name('galeri.update');
                Route::delete('/galeri/{galeri}', [KontenPmbController::class, 'galeriDestroy'])->name('galeri.destroy');
                
                // Keunggulan
                Route::get('/keunggulan', [KontenPmbController::class, 'keunggulanIndex'])->name('keunggulan.index');
                Route::get('/keunggulan/create', [KontenPmbController::class, 'keunggulanCreate'])->name('keunggulan.create');
                Route::post('/keunggulan', [KontenPmbController::class, 'keunggulanStore'])->name('keunggulan.store');
                Route::get('/keunggulan/{keunggulan}/edit', [KontenPmbController::class, 'keunggulanEdit'])->name('keunggulan.edit');
                Route::put('/keunggulan/{keunggulan}', [KontenPmbController::class, 'keunggulanUpdate'])->name('keunggulan.update');
                Route::delete('/keunggulan/{keunggulan}', [KontenPmbController::class, 'keunggulanDestroy'])->name('keunggulan.destroy');
                
                // Fasilitas
                Route::get('/fasilitas', [KontenPmbController::class, 'fasilitasIndex'])->name('fasilitas.index');
                Route::get('/fasilitas/create', [KontenPmbController::class, 'fasilitasCreate'])->name('fasilitas.create');
                Route::post('/fasilitas', [KontenPmbController::class, 'fasilitasStore'])->name('fasilitas.store');
                Route::get('/fasilitas/{fasilitas}/edit', [KontenPmbController::class, 'fasilitasEdit'])->name('fasilitas.edit');
                Route::put('/fasilitas/{fasilitas}', [KontenPmbController::class, 'fasilitasUpdate'])->name('fasilitas.update');
                Route::delete('/fasilitas/{fasilitas}', [KontenPmbController::class, 'fasilitasDestroy'])->name('fasilitas.destroy');
                
                // Kontak
                Route::get('/kontak', [KontenPmbController::class, 'kontakIndex'])->name('kontak.index');
                Route::get('/kontak/create', [KontenPmbController::class, 'kontakCreate'])->name('kontak.create');
                Route::post('/kontak', [KontenPmbController::class, 'kontakStore'])->name('kontak.store');
                Route::get('/kontak/{kontak}/edit', [KontenPmbController::class, 'kontakEdit'])->name('kontak.edit');
                Route::put('/kontak/{kontak}', [KontenPmbController::class, 'kontakUpdate'])->name('kontak.update');
                Route::delete('/kontak/{kontak}', [KontenPmbController::class, 'kontakDestroy'])->name('kontak.destroy');
                
                // Pengaturan Umum
                Route::get('/pengaturan', [KontenPmbController::class, 'pengaturan'])->name('pengaturan');
                Route::post('/pengaturan', [KontenPmbController::class, 'pengaturanUpdate'])->name('pengaturan.update');
            });
        });
    });

    // =====================
    // DOSEN ROUTES
    // =====================
    Route::middleware(['role:admin,dosen'])->group(function () {
        // Dashboard & Profil Dosen (Self-Service) - using 'portal-dosen' prefix to avoid conflict with admin resource
        Route::get('/portal-dosen/dashboard', [DosenPortalController::class, 'dashboard'])->name('dosen.dashboard');
        Route::get('/portal-dosen/profil', [DosenPortalController::class, 'profil'])->name('dosen.profil');
        Route::get('/portal-dosen/profil/edit', [DosenPortalController::class, 'editProfil'])->name('dosen.profil.edit');
        Route::put('/portal-dosen/profil', [DosenPortalController::class, 'updateProfil'])->name('dosen.profil.update');
        Route::put('/portal-dosen/profil/password', [DosenPortalController::class, 'updatePassword'])->name('dosen.profil.password');
        Route::get('/portal-dosen/kepegawaian', [DosenPortalController::class, 'kepegawaian'])->name('dosen.kepegawaian');
        
        // Presensi Dosen Self-Service
        Route::get('/portal-dosen/presensi', [\App\Http\Controllers\DosenPresensiController::class, 'index'])->name('dosen.presensi.index');
        Route::post('/portal-dosen/presensi/masuk', [\App\Http\Controllers\DosenPresensiController::class, 'clockIn'])->name('dosen.presensi.masuk');
        Route::post('/portal-dosen/presensi/keluar', [\App\Http\Controllers\DosenPresensiController::class, 'clockOut'])->name('dosen.presensi.keluar');
        Route::get('/portal-dosen/presensi/riwayat', [\App\Http\Controllers\DosenPresensiController::class, 'riwayat'])->name('dosen.presensi.riwayat');
        Route::get('/portal-dosen/presensi/status', [\App\Http\Controllers\DosenPresensiController::class, 'status'])->name('dosen.presensi.status');
        
        // Kepegawaian Self-Service (Dosen update sendiri)
        // Riwayat Pendidikan
        Route::post('/portal-dosen/kepegawaian/pendidikan', [DosenPortalController::class, 'storeRiwayatPendidikan'])->name('dosen.kepegawaian.pendidikan.store');
        Route::put('/portal-dosen/kepegawaian/pendidikan/{riwayatPendidikan}', [DosenPortalController::class, 'updateRiwayatPendidikan'])->name('dosen.kepegawaian.pendidikan.update');
        Route::delete('/portal-dosen/kepegawaian/pendidikan/{riwayatPendidikan}', [DosenPortalController::class, 'destroyRiwayatPendidikan'])->name('dosen.kepegawaian.pendidikan.destroy');
        
        // Riwayat Jabatan
        Route::post('/portal-dosen/kepegawaian/jabatan', [DosenPortalController::class, 'storeRiwayatJabatan'])->name('dosen.kepegawaian.jabatan.store');
        Route::put('/portal-dosen/kepegawaian/jabatan/{riwayatJabatan}', [DosenPortalController::class, 'updateRiwayatJabatan'])->name('dosen.kepegawaian.jabatan.update');
        Route::delete('/portal-dosen/kepegawaian/jabatan/{riwayatJabatan}', [DosenPortalController::class, 'destroyRiwayatJabatan'])->name('dosen.kepegawaian.jabatan.destroy');
        
        // Riwayat Pangkat
        Route::post('/portal-dosen/kepegawaian/pangkat', [DosenPortalController::class, 'storeRiwayatPangkat'])->name('dosen.kepegawaian.pangkat.store');
        Route::put('/portal-dosen/kepegawaian/pangkat/{riwayatPangkat}', [DosenPortalController::class, 'updateRiwayatPangkat'])->name('dosen.kepegawaian.pangkat.update');
        Route::delete('/portal-dosen/kepegawaian/pangkat/{riwayatPangkat}', [DosenPortalController::class, 'destroyRiwayatPangkat'])->name('dosen.kepegawaian.pangkat.destroy');
        
        // Riwayat Pelatihan
        Route::post('/portal-dosen/kepegawaian/pelatihan', [DosenPortalController::class, 'storeRiwayatPelatihan'])->name('dosen.kepegawaian.pelatihan.store');
        Route::put('/portal-dosen/kepegawaian/pelatihan/{riwayatPelatihan}', [DosenPortalController::class, 'updateRiwayatPelatihan'])->name('dosen.kepegawaian.pelatihan.update');
        Route::delete('/portal-dosen/kepegawaian/pelatihan/{riwayatPelatihan}', [DosenPortalController::class, 'destroyRiwayatPelatihan'])->name('dosen.kepegawaian.pelatihan.destroy');
        
        // Dokumen Kepegawaian
        Route::post('/portal-dosen/kepegawaian/dokumen', [DosenPortalController::class, 'storeDokumen'])->name('dosen.kepegawaian.dokumen.store');
        Route::put('/portal-dosen/kepegawaian/dokumen/{dokumen}', [DosenPortalController::class, 'updateDokumen'])->name('dosen.kepegawaian.dokumen.update');
        Route::delete('/portal-dosen/kepegawaian/dokumen/{dokumen}', [DosenPortalController::class, 'destroyDokumen'])->name('dosen.kepegawaian.dokumen.destroy');
        
        // Jadwal Mengajar Dosen
        Route::get('/jadwal-mengajar', [JadwalController::class, 'dosen'])->name('jadwal.dosen');
        
        // Input Nilai
        Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::get('/nilai/input/{jadwalKuliah}', [NilaiController::class, 'inputNilai'])->name('nilai.input');
        Route::post('/nilai/input/{jadwalKuliah}', [NilaiController::class, 'store'])->name('nilai.store');
        
        // Persetujuan KRS (Dosen Wali)
        Route::get('/krs/persetujuan', [KrsController::class, 'persetujuan'])->name('krs.persetujuan');
        Route::post('/krs/{krs}/approve', [KrsController::class, 'approve'])->name('krs.approve');
        Route::post('/krs/{krs}/reject', [KrsController::class, 'reject'])->name('krs.reject');
        Route::post('/krs/approve-all/{mahasiswa}', [KrsController::class, 'approveAll'])->name('krs.approve-all');

        // Absensi
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/{jadwalKuliah}', [AbsensiController::class, 'show'])->name('absensi.show');
        Route::get('/absensi/{jadwalKuliah}/create', [AbsensiController::class, 'create'])->name('absensi.create');
        Route::post('/absensi/{jadwalKuliah}', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/absensi/{jadwalKuliah}/edit/{pertemuan}', [AbsensiController::class, 'edit'])->name('absensi.edit');
        Route::put('/absensi/{jadwalKuliah}/update/{pertemuan}', [AbsensiController::class, 'update'])->name('absensi.update');
        Route::delete('/absensi/{jadwalKuliah}/delete/{pertemuan}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
        
        // Absensi Mandiri (Dosen generate kode)
        Route::get('/absensi/{jadwalKuliah}/kode', [AbsensiController::class, 'showKodeAbsensi'])->name('absensi.kode');
        Route::post('/absensi/{jadwalKuliah}/generate-kode', [AbsensiController::class, 'generateKode'])->name('absensi.generate-kode');
        Route::post('/absensi/{jadwalKuliah}/tutup', [AbsensiController::class, 'tutupAbsensi'])->name('absensi.tutup');
        Route::get('/absensi/{jadwalKuliah}/status', [AbsensiController::class, 'getStatusAbsensi'])->name('absensi.status');
        
        // Kelola Pertemuan (Dosen)
        Route::get('/pertemuan/{jadwalKuliah}', [PertemuanController::class, 'index'])->name('pertemuan.index');
        Route::get('/pertemuan/{jadwalKuliah}/create/{pertemuanKe}', [PertemuanController::class, 'create'])->name('pertemuan.create');
        Route::post('/pertemuan/{jadwalKuliah}/store/{pertemuanKe}', [PertemuanController::class, 'store'])->name('pertemuan.store');
        Route::delete('/pertemuan/{jadwalKuliah}/delete/{pertemuanKe}', [PertemuanController::class, 'destroy'])->name('pertemuan.destroy');
        Route::post('/pertemuan/{jadwalKuliah}/toggle/{pertemuanKe}', [PertemuanController::class, 'togglePublish'])->name('pertemuan.toggle');
        
        // Bimbingan Akademik (Dosen)
        Route::get('/bimbingan-dosen', [BimbinganAkademikController::class, 'dosenIndex'])->name('bimbingan.dosen');
        Route::get('/bimbingan-dosen/{bimbingan}', [BimbinganAkademikController::class, 'show'])->name('bimbingan.dosen.show');
        Route::post('/bimbingan-dosen/{bimbingan}/respond', [BimbinganAkademikController::class, 'respond'])->name('bimbingan.respond');
        Route::get('/bimbingan-dosen/buat-jadwal/{mahasiswa}', [BimbinganAkademikController::class, 'buatJadwalForm'])->name('bimbingan.buat-jadwal-form');
        Route::post('/bimbingan-dosen/buat-jadwal/{mahasiswa}', [BimbinganAkademikController::class, 'buatJadwal'])->name('bimbingan.buat-jadwal');
        Route::get('/persetujuan-krs', [BimbinganAkademikController::class, 'persetujuanKrs'])->name('bimbingan.persetujuan-krs');
        Route::get('/persetujuan-krs/{mahasiswa}', [BimbinganAkademikController::class, 'detailKrs'])->name('bimbingan.detail-krs');
        Route::post('/persetujuan-krs/{krs}/approve', [BimbinganAkademikController::class, 'approveKrs'])->name('bimbingan.approve-krs');
        Route::post('/persetujuan-krs/{krs}/reject', [BimbinganAkademikController::class, 'rejectKrs'])->name('bimbingan.reject-krs');
        
        // =====================
        // DOSEN - REKAP & EXPORT
        // =====================
        Route::get('/rekap-absensi', [App\Http\Controllers\DosenRekapController::class, 'rekapAbsensi'])->name('dosen.rekap-absensi');
        Route::get('/rekap-nilai', [App\Http\Controllers\DosenRekapController::class, 'rekapNilai'])->name('dosen.rekap-nilai');
        Route::get('/mahasiswa-wali', [App\Http\Controllers\DosenRekapController::class, 'mahasiswaWali'])->name('dosen.mahasiswa-wali');
        Route::get('/export-mahasiswa-wali-csv', [App\Http\Controllers\DosenRekapController::class, 'exportMahasiswaWaliCsv'])->name('dosen.export-mahasiswa-wali-csv');
        
        // =====================
        // TUGAS AKHIR (Dosen Pembimbing & Penguji)
        // =====================
        Route::prefix('tugas-akhir-dosen')->name('dosen.tugas-akhir.')->group(function () {
            Route::get('/', [TugasAkhirDosenController::class, 'index'])->name('index');
            Route::get('/jadwal-bimbingan', [TugasAkhirDosenController::class, 'jadwalBimbingan'])->name('jadwal-bimbingan');
            Route::get('/riwayat-bimbingan', [TugasAkhirDosenController::class, 'riwayatBimbingan'])->name('riwayat-bimbingan');
            
            // Bimbingan routes
            Route::post('/{tugasAkhir}/bimbingan', [TugasAkhirDosenController::class, 'storeBimbingan'])->name('bimbingan.store');
            Route::post('/bimbingan/{bimbinganTA}/input', [TugasAkhirDosenController::class, 'inputBimbingan'])->name('bimbingan.input');
            Route::post('/bimbingan/{bimbinganTA}/reschedule', [TugasAkhirDosenController::class, 'reschedule'])->name('bimbingan.reschedule');
            
            // Penguji routes - HARUS sebelum {tugasAkhir} wildcard
            Route::get('/penguji/seminar', [TugasAkhirDosenController::class, 'seminarPenguji'])->name('seminar-penguji');
            Route::get('/penguji/sidang', [TugasAkhirDosenController::class, 'sidangPenguji'])->name('sidang-penguji');
            Route::get('/sidang/{sidang}', [TugasAkhirDosenController::class, 'sidangShow'])->name('sidang.show');
            Route::post('/sidang/{sidang}/nilai', [TugasAkhirDosenController::class, 'inputNilaiSidang'])->name('sidang.nilai');
            Route::post('/sidang/{sidang}/revisi', [TugasAkhirDosenController::class, 'tambahRevisi'])->name('sidang.revisi');
            Route::post('/revisi/{revisi}/verifikasi', [TugasAkhirDosenController::class, 'verifikasiRevisi'])->name('revisi.verifikasi');
            Route::post('/seminar/{seminar}/nilai', [TugasAkhirDosenController::class, 'inputNilaiSeminar'])->name('seminar.nilai');
            
            // Wildcard route - HARUS di paling akhir
            Route::get('/{tugasAkhir}', [TugasAkhirDosenController::class, 'show'])->name('show');
        });
    });

    // =====================
    // MAHASISWA ROUTES
    // =====================
    Route::middleware(['role:mahasiswa'])->group(function () {
        // KRS
        Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
        Route::get('/krs/create', [KrsController::class, 'create'])->name('krs.create');
        Route::post('/krs', [KrsController::class, 'store'])->name('krs.store');
        Route::post('/krs/paket', [KrsController::class, 'storePaket'])->name('krs.store-paket');
        Route::delete('/krs/{krs}', [KrsController::class, 'destroy'])->name('krs.destroy');
        
        // Jadwal Kuliah Mahasiswa
        Route::get('/jadwal-saya', [JadwalController::class, 'mahasiswa'])->name('jadwal.mahasiswa');
        
        // KHS & Transkrip - Redirect ke fitur baru
        // Route::get('/khs', [NilaiController::class, 'khs'])->name('khs');
        // Route::get('/transkrip', [NilaiController::class, 'transkrip'])->name('transkrip');
        
        // Pembayaran (Mahasiswa lihat)
        Route::get('/pembayaran-saya', [PembayaranController::class, 'index'])->name('pembayaran.mahasiswa');
        
        // Keuangan Mahasiswa
        Route::get('/tagihan-saya', [TagihanController::class, 'index'])->name('tagihan.mahasiswa');
        Route::get('/bayar/{tagihan}', [TransaksiPembayaranController::class, 'bayar'])->name('pembayaran.bayar');
        Route::post('/bayar/{tagihan}/upload', [TransaksiPembayaranController::class, 'uploadBukti'])->name('pembayaran.upload-bukti');
        Route::get('/riwayat-pembayaran', [TransaksiPembayaranController::class, 'index'])->name('transaksi.mahasiswa');
        Route::get('/beasiswa-tersedia', [BeasiswaController::class, 'available'])->name('beasiswa.available');
        Route::post('/beasiswa/{beasiswa}/ajukan', [BeasiswaController::class, 'ajukan'])->name('beasiswa.ajukan');
        Route::get('/cicilan-saya', [CicilanController::class, 'tracking'])->name('cicilan.tracking');
        Route::get('/notifikasi-saya', [NotifikasiController::class, 'mahasiswa'])->name('notifikasi.mahasiswa');

        // Rekap Kehadiran Mahasiswa - Redirect ke fitur baru
        // Route::get('/kehadiran', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
        
        // Absensi Mandiri Mahasiswa
        Route::get('/absensi-mandiri', [AbsensiController::class, 'absensiMandiri'])->name('absensi.mandiri');
        Route::post('/absensi-mandiri', [AbsensiController::class, 'prosesAbsensiMandiri'])->name('absensi.mandiri.proses');
        
        // Materi Kuliah (Mahasiswa)
        Route::get('/materi-kuliah', [PertemuanController::class, 'mahasiswaIndex'])->name('pertemuan.mahasiswa');
        Route::get('/materi-kuliah/{jadwalKuliah}', [PertemuanController::class, 'mahasiswaDetail'])->name('pertemuan.mahasiswa.detail');

        // Cetak PDF
        Route::get('/cetak/krs', [CetakController::class, 'cetakKrs'])->name('cetak.krs');
        Route::get('/cetak/khs', [CetakController::class, 'cetakKhs'])->name('cetak.khs');
        Route::get('/cetak/transkrip', [CetakController::class, 'cetakTranskrip'])->name('cetak.transkrip');
        
        // Portal Mahasiswa
        Route::get('/profil-saya', [MahasiswaPortalController::class, 'profil'])->name('mahasiswa.profil');
        Route::get('/profil-saya/edit', [MahasiswaPortalController::class, 'editProfil'])->name('mahasiswa.profil.edit');
        Route::put('/profil-saya', [MahasiswaPortalController::class, 'updateProfil'])->name('mahasiswa.profil.update');
        Route::put('/profil-saya/password', [MahasiswaPortalController::class, 'updatePassword'])->name('mahasiswa.profil.password');
        Route::get('/potongan-saya', [MahasiswaPortalController::class, 'potongan'])->name('mahasiswa.potongan');
        Route::get('/kartu-tagihan', [MahasiswaPortalController::class, 'kartuTagihan'])->name('mahasiswa.tagihan');
        Route::get('/kartu-tagihan/download', [MahasiswaPortalController::class, 'downloadKartuTagihan'])->name('mahasiswa.tagihan.download');
        Route::get('/rekap-pembayaran', [MahasiswaPortalController::class, 'riwayatPembayaran'])->name('mahasiswa.rekap-pembayaran');
        Route::get('/rekap-pembayaran/download', [MahasiswaPortalController::class, 'downloadRiwayatPembayaran'])->name('mahasiswa.rekap-pembayaran.download');
        Route::get('/kartu-mahasiswa', [MahasiswaPortalController::class, 'kartuMahasiswa'])->name('mahasiswa.kartu');
        Route::get('/kartu-mahasiswa/download', [MahasiswaPortalController::class, 'downloadKartuMahasiswa'])->name('mahasiswa.kartu.download');

        // Pengajuan Surat
        Route::get('/pengajuan-surat', [PengajuanSuratController::class, 'index'])->name('pengajuan-surat.index');
        Route::get('/pengajuan-surat/create', [PengajuanSuratController::class, 'create'])->name('pengajuan-surat.create');
        Route::post('/pengajuan-surat', [PengajuanSuratController::class, 'store'])->name('pengajuan-surat.store');
        Route::get('/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'show'])->name('pengajuan-surat.show');
        Route::delete('/pengajuan-surat/{pengajuanSurat}', [PengajuanSuratController::class, 'destroy'])->name('pengajuan-surat.destroy');
        Route::get('/pengajuan-surat/{pengajuanSurat}/download', [PengajuanSuratController::class, 'download'])->name('pengajuan-surat.download');
        
        // Bimbingan Akademik (Mahasiswa)
        Route::get('/bimbingan-saya', [BimbinganAkademikController::class, 'mahasiswaIndex'])->name('bimbingan.mahasiswa');
        Route::get('/bimbingan-saya/create', [BimbinganAkademikController::class, 'create'])->name('bimbingan.mahasiswa.create');
        Route::post('/bimbingan-saya', [BimbinganAkademikController::class, 'store'])->name('bimbingan.mahasiswa.store');
        Route::get('/bimbingan-saya/{bimbingan}', [BimbinganAkademikController::class, 'show'])->name('bimbingan.mahasiswa.show');
        
        // Cuti Akademik (Mahasiswa)
        Route::get('/cuti-saya', [CutiAkademikController::class, 'mahasiswaIndex'])->name('cuti.mahasiswa');
        Route::get('/cuti-saya/create', [CutiAkademikController::class, 'create'])->name('cuti.mahasiswa.create');
        Route::post('/cuti-saya', [CutiAkademikController::class, 'store'])->name('cuti.mahasiswa.store');
        Route::get('/cuti-saya/{cuti}', [CutiAkademikController::class, 'show'])->name('cuti.mahasiswa.show');

        // Transkrip Nilai
        Route::get('/transkrip', [TranskripController::class, 'index'])->name('mahasiswa.transkrip');
        Route::get('/transkrip/cetak', [TranskripController::class, 'cetak'])->name('mahasiswa.transkrip.cetak');

        // Kartu Hasil Studi (KHS)
        Route::get('/khs', [KhsController::class, 'index'])->name('mahasiswa.khs');
        Route::get('/khs/{tahunAkademik}/cetak', [KhsController::class, 'cetak'])->name('mahasiswa.khs.cetak');

        // Jadwal Ujian
        Route::get('/jadwal-ujian', [JadwalUjianController::class, 'index'])->name('mahasiswa.jadwal-ujian');
        Route::get('/jadwal-ujian/{jadwalUjian}', [JadwalUjianController::class, 'show'])->name('mahasiswa.jadwal-ujian.show');

        // Kehadiran/Absensi
        Route::get('/kehadiran', [KehadiranController::class, 'index'])->name('mahasiswa.kehadiran');
        Route::get('/kehadiran/{mataKuliah}', [KehadiranController::class, 'detail'])->name('mahasiswa.kehadiran.detail');

        // EDOM (Evaluasi Dosen) - Mahasiswa
        Route::get('/edom', [EdomMahasiswaController::class, 'index'])->name('mahasiswa.edom.index');
        Route::get('/edom/isi/{jadwalKuliah}', [EdomMahasiswaController::class, 'create'])->name('mahasiswa.edom.create');
        Route::post('/edom/isi/{jadwalKuliah}', [EdomMahasiswaController::class, 'store'])->name('mahasiswa.edom.store');
        Route::get('/edom/riwayat', [EdomMahasiswaController::class, 'riwayat'])->name('mahasiswa.edom.riwayat');

        // Kartu Peserta Ujian - Mahasiswa
        Route::get('/kartu-ujian', [KartuUjianController::class, 'index'])->name('mahasiswa.kartu-ujian.index');
        Route::get('/kartu-ujian/{kartuUjian}', [KartuUjianController::class, 'show'])->name('mahasiswa.kartu-ujian.show');
        Route::get('/kartu-ujian/{kartuUjian}/cetak', [KartuUjianController::class, 'cetak'])->name('mahasiswa.kartu-ujian.cetak');
        
        // =====================
        // KONVERSI NILAI - Mahasiswa (Pengajuan + Lihat Hasil Konversi)
        // =====================
        Route::prefix('konversi-nilai')->name('mahasiswa.konversi-nilai.')->group(function () {
            Route::get('/', [KonversiNilaiController::class, 'mahasiswaIndex'])->name('index');
            Route::get('/create', [KonversiNilaiController::class, 'mahasiswaCreate'])->name('create');
            Route::post('/', [KonversiNilaiController::class, 'mahasiswaStore'])->name('store');
            Route::get('/{pengajuanKonversi}', [KonversiNilaiController::class, 'mahasiswaShow'])->name('show');
            Route::post('/{pengajuanKonversi}/tambah-detail', [KonversiNilaiController::class, 'mahasiswaTambahDetail'])->name('tambah-detail');
            Route::delete('/{pengajuanKonversi}/hapus-detail/{detailKonversi}', [KonversiNilaiController::class, 'mahasiswaHapusDetail'])->name('hapus-detail');
            Route::post('/{pengajuanKonversi}/ajukan', [KonversiNilaiController::class, 'mahasiswaAjukan'])->name('ajukan');
        });

        // =====================
        // KONVERSI KEGIATAN (RPL) - Mahasiswa
        // Untuk konversi sertifikasi, lomba, magang, dll ke mata kuliah
        // =====================
        Route::prefix('konversi-kegiatan')->name('konversi-kegiatan.')->group(function () {
            Route::get('/', [KonversiKegiatanController::class, 'mahasiswaIndex'])->name('index');
            Route::get('/create', [KonversiKegiatanController::class, 'create'])->name('create');
            Route::post('/', [KonversiKegiatanController::class, 'store'])->name('store');
            Route::get('/{pengajuanKonversiKegiatan}', [KonversiKegiatanController::class, 'show'])->name('show');
            Route::post('/{pengajuanKonversiKegiatan}/detail', [KonversiKegiatanController::class, 'storeDetail'])->name('store-detail');
            Route::delete('/{pengajuanKonversiKegiatan}/detail/{detail}', [KonversiKegiatanController::class, 'destroyDetail'])->name('destroy-detail');
            Route::post('/{pengajuanKonversiKegiatan}/submit', [KonversiKegiatanController::class, 'submit'])->name('submit');
            Route::get('/bukti/{detail}/download', [KonversiKegiatanController::class, 'downloadBukti'])->name('download-bukti');
        });
        
        // =====================
        // PKL/MAGANG/KKN - Mahasiswa
        // =====================
        Route::prefix('kegiatan-lapangan')->name('mahasiswa.kegiatan-lapangan.')->group(function () {
            Route::get('/', [KegiatanLapanganMahasiswaController::class, 'index'])->name('index');
            Route::get('/create', [KegiatanLapanganMahasiswaController::class, 'create'])->name('create');
            Route::post('/', [KegiatanLapanganMahasiswaController::class, 'store'])->name('store');
            Route::get('/{pendaftaranKegiatanLapangan}', [KegiatanLapanganMahasiswaController::class, 'show'])->name('show');
            
            // Log Kegiatan
            Route::get('/{pendaftaranKegiatanLapangan}/log/create', [KegiatanLapanganMahasiswaController::class, 'logCreate'])->name('log.create');
            Route::post('/{pendaftaranKegiatanLapangan}/log', [KegiatanLapanganMahasiswaController::class, 'logStore'])->name('log.store');
            Route::get('/log/{logKegiatanLapangan}/edit', [KegiatanLapanganMahasiswaController::class, 'logEdit'])->name('log.edit');
            Route::put('/log/{logKegiatanLapangan}', [KegiatanLapanganMahasiswaController::class, 'logUpdate'])->name('log.update');
            Route::delete('/log/{logKegiatanLapangan}', [KegiatanLapanganMahasiswaController::class, 'logDestroy'])->name('log.destroy');
        });
        
        // =====================
        // TUGAS AKHIR - Mahasiswa
        // =====================
        Route::prefix('tugas-akhir')->name('mahasiswa.tugas-akhir.')->group(function () {
            Route::get('/', [TugasAkhirMahasiswaController::class, 'index'])->name('index');
            Route::get('/create', [TugasAkhirMahasiswaController::class, 'create'])->name('create');
            Route::post('/', [TugasAkhirMahasiswaController::class, 'store'])->name('store');
            Route::put('/{tugasAkhir}', [TugasAkhirMahasiswaController::class, 'update'])->name('update');
            Route::post('/{tugasAkhir}/ajukan', [TugasAkhirMahasiswaController::class, 'ajukan'])->name('ajukan');
            Route::post('/{tugasAkhir}/upload-dokumen', [TugasAkhirMahasiswaController::class, 'uploadDokumen'])->name('upload-dokumen');
            Route::post('/{tugasAkhir}/ajukan-seminar', [TugasAkhirMahasiswaController::class, 'ajukanSeminar'])->name('ajukan-seminar');
            Route::post('/{tugasAkhir}/ajukan-sidang', [TugasAkhirMahasiswaController::class, 'ajukanSidang'])->name('ajukan-sidang');
            
            // Bimbingan
            Route::get('/{tugasAkhir}/bimbingan', [TugasAkhirMahasiswaController::class, 'bimbinganIndex'])->name('bimbingan');
            Route::post('/{tugasAkhir}/bimbingan/request', [TugasAkhirMahasiswaController::class, 'bimbinganRequest'])->name('bimbingan.request');
            
            // Revisi
            Route::post('/{tugasAkhir}/upload-revisi', [TugasAkhirMahasiswaController::class, 'uploadRevisi'])->name('upload-revisi');
            
            // Cetak
            Route::get('/{tugasAkhir}/cetak-kartu-bimbingan', [TugasAkhirMahasiswaController::class, 'cetakKartuBimbingan'])->name('cetak-kartu-bimbingan');
        });
    });
    
    // Routes untuk semua role yang authenticated
    // Kalender Akademik (view)
    Route::get('/kalender', [KalenderAkademikController::class, 'index'])->name('kalender.index');
    Route::get('/kalender/events', [KalenderAkademikController::class, 'events'])->name('kalender.events');
    
    // Download Materi Pertemuan (untuk dosen dan mahasiswa)
    Route::get('/pertemuan/download/{pertemuan}', [PertemuanController::class, 'downloadMateri'])->name('pertemuan.download');

    // =====================
    // KETUA PRODI ROUTES
    // =====================
    Route::middleware(['role:kaprodi'])->prefix('kaprodi')->name('kaprodi.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [KaprodiController::class, 'dashboard'])->name('dashboard');
        
        // Mahasiswa Prodi
        Route::get('/mahasiswa', [KaprodiController::class, 'mahasiswa'])->name('mahasiswa.index');
        Route::get('/mahasiswa/bermasalah', [KaprodiController::class, 'mahasiswaBermasalah'])->name('mahasiswa.bermasalah');
        Route::get('/mahasiswa/{mahasiswa}', [KaprodiController::class, 'mahasiswaShow'])->name('mahasiswa.show');
        
        // Dosen Prodi
        Route::get('/dosen', [KaprodiController::class, 'dosen'])->name('dosen.index');
        Route::get('/dosen/{dosen}', [KaprodiController::class, 'dosenShow'])->name('dosen.show');
        
        // Approval KRS
        Route::get('/krs', [KaprodiController::class, 'krsApproval'])->name('krs.index');
        Route::post('/krs/{krs}/approve', [KaprodiController::class, 'krsApprove'])->name('krs.approve');
        Route::post('/krs/{krs}/reject', [KaprodiController::class, 'krsReject'])->name('krs.reject');
        
        // Kurikulum
        Route::get('/kurikulum', [KaprodiController::class, 'kurikulumIndex'])->name('kurikulum.index');
        Route::get('/kurikulum/{kurikulum}', [KaprodiController::class, 'kurikulumShow'])->name('kurikulum.show');
        
        // Mata Kuliah
        Route::get('/mata-kuliah', [KaprodiController::class, 'mataKuliahIndex'])->name('mata-kuliah.index');
        Route::get('/mata-kuliah/{mataKuliah}', [KaprodiController::class, 'mataKuliahShow'])->name('mata-kuliah.show');
        
        // Monitoring Nilai
        Route::get('/nilai/rekap', [KaprodiController::class, 'rekapNilai'])->name('nilai.rekap');
        Route::get('/nilai/monitoring-ipk', [KaprodiController::class, 'monitoringIpk'])->name('nilai.monitoring-ipk');
        Route::get('/nilai/export', [KaprodiController::class, 'exportNilai'])->name('nilai.export');
        
        // Bimbingan Akademik
        Route::get('/bimbingan', [KaprodiController::class, 'bimbinganIndex'])->name('bimbingan.index');
        Route::get('/bimbingan/{bimbingan}', [KaprodiController::class, 'bimbinganShow'])->name('bimbingan.show');
        
        // EDOM
        Route::get('/edom', [KaprodiController::class, 'edomIndex'])->name('edom.index');
        
        // Wisuda & Yudisium
        Route::get('/wisuda', [KaprodiController::class, 'wisudaIndex'])->name('wisuda.index');
        Route::get('/yudisium', [KaprodiController::class, 'yudisiumIndex'])->name('yudisium.index');
        
        // Absensi
        Route::get('/absensi', [KaprodiController::class, 'absensiIndex'])->name('absensi.index');
        Route::get('/absensi/{jadwalKuliah}', [KaprodiController::class, 'absensiShow'])->name('absensi.show');
        
        // Jadwal Ujian
        Route::get('/jadwal-ujian', [KaprodiController::class, 'jadwalUjianIndex'])->name('jadwal-ujian.index');
        
        // Monitoring Tugas Akhir
        Route::get('/tugas-akhir', [KaprodiController::class, 'tugasAkhir'])->name('tugas-akhir.index');
        Route::get('/tugas-akhir/{tugasAkhir}', [KaprodiController::class, 'tugasAkhirShow'])->name('tugas-akhir.show');
        Route::post('/tugas-akhir/{tugasAkhir}/approval', [KaprodiController::class, 'tugasAkhirApproval'])->name('tugas-akhir.approval');
        
        // Proses Konversi Nilai (dari admin)
        Route::get('/konversi-nilai', [KaprodiController::class, 'konversiNilai'])->name('konversi-nilai.index');
        Route::get('/konversi-nilai/api/mata-kuliah', [KaprodiController::class, 'getMataKuliahByKurikulum'])->name('konversi-nilai.api.mata-kuliah');
        Route::get('/konversi-nilai/{pengajuanKonversi}', [KaprodiController::class, 'konversiNilaiShow'])->name('konversi-nilai.show');
        Route::post('/konversi-nilai/{pengajuanKonversi}/proses', [KaprodiController::class, 'konversiNilaiProses'])->name('konversi-nilai.proses');
        
        // Monitoring Cuti Akademik
        Route::get('/cuti', [KaprodiController::class, 'cutiAkademik'])->name('cuti.index');
        Route::post('/cuti/{cuti}/approval', [KaprodiController::class, 'cutiApproval'])->name('cuti.approval');
        
        // Monitoring PKL/Magang
        Route::get('/pkl', [KaprodiController::class, 'pkl'])->name('pkl.index');
        Route::get('/pkl/{pendaftaran}', [KaprodiController::class, 'pklShow'])->name('pkl.show');
        
        // Jadwal Kuliah Prodi
        Route::get('/jadwal', [KaprodiController::class, 'jadwalKuliah'])->name('jadwal.index');
        
        // Statistik & Laporan
        Route::get('/statistik', [KaprodiController::class, 'statistik'])->name('statistik');
        Route::get('/laporan', [KaprodiController::class, 'laporan'])->name('laporan.index');
        
        // Export
        Route::get('/export/mahasiswa', [KaprodiController::class, 'exportMahasiswa'])->name('export.mahasiswa');
        Route::get('/export/nilai', [KaprodiController::class, 'exportNilai'])->name('export.nilai');
    });
    
    // =====================
    // DEKAN ROUTES
    // =====================
    Route::middleware(['role:dekan'])->prefix('dekan')->name('dekan.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DekanController::class, 'dashboard'])->name('dashboard');
        
        // Program Studi
        Route::get('/program-studi', [DekanController::class, 'programStudi'])->name('program-studi.index');
        Route::get('/program-studi/{programStudi}', [DekanController::class, 'programStudiShow'])->name('program-studi.show');
        Route::get('/program-studi/{programStudi}/mahasiswa/{angkatan}', [DekanController::class, 'programStudiMahasiswa'])->name('program-studi.mahasiswa');
        Route::get('/program-studi/{programStudi}/dosen', [DekanController::class, 'programStudiDosen'])->name('program-studi.dosen');
        
        // Mahasiswa Fakultas
        Route::get('/mahasiswa', [DekanController::class, 'mahasiswa'])->name('mahasiswa.index');
        Route::get('/mahasiswa/bermasalah', [DekanController::class, 'mahasiswaBermasalah'])->name('mahasiswa.bermasalah');
        Route::get('/mahasiswa/{mahasiswa}', [DekanController::class, 'mahasiswaShow'])->name('mahasiswa.show');
        
        // Dosen Fakultas
        Route::get('/dosen', [DekanController::class, 'dosen'])->name('dosen.index');
        Route::get('/dosen/{dosen}', [DekanController::class, 'dosenShow'])->name('dosen.show');
        
        // Approval Cuti Akademik
        Route::get('/cuti', [DekanController::class, 'cutiAkademik'])->name('cuti.index');
        Route::post('/cuti/{cuti}/approval', [DekanController::class, 'cutiApproval'])->name('cuti.approval');
        
        // Monitoring Tugas Akhir
        Route::get('/tugas-akhir', [DekanController::class, 'tugasAkhir'])->name('tugas-akhir.index');
        Route::get('/tugas-akhir/{tugasAkhir}', [DekanController::class, 'tugasAkhirShow'])->name('tugas-akhir.show');
        
        // Monitoring Wisuda
        Route::get('/wisuda', [DekanController::class, 'wisuda'])->name('wisuda.index');
        Route::get('/wisuda/{periodeWisuda}', [DekanController::class, 'wisudaShow'])->name('wisuda.show');
        
        // Yudisium
        Route::get('/yudisium', [DekanController::class, 'yudisiumIndex'])->name('yudisium.index');
        Route::get('/yudisium/{yudisium}', [DekanController::class, 'yudisiumShow'])->name('yudisium.show');
        Route::post('/yudisium/{yudisium}/approval', [DekanController::class, 'yudisiumApproval'])->name('yudisium.approval');
        
        // Kurikulum
        Route::get('/kurikulum', [DekanController::class, 'kurikulumIndex'])->name('kurikulum.index');
        Route::get('/kurikulum/{kurikulum}', [DekanController::class, 'kurikulumShow'])->name('kurikulum.show');
        
        // Mata Kuliah
        Route::get('/mata-kuliah', [DekanController::class, 'mataKuliahIndex'])->name('mata-kuliah.index');
        Route::get('/mata-kuliah/{mataKuliah}', [DekanController::class, 'mataKuliahShow'])->name('mata-kuliah.show');
        
        // Monitoring Nilai
        Route::get('/nilai/rekap', [DekanController::class, 'rekapNilai'])->name('nilai.rekap');
        Route::get('/nilai/monitoring-ipk', [DekanController::class, 'monitoringIpk'])->name('nilai.monitoring-ipk');
        
        // Bimbingan Akademik
        Route::get('/bimbingan', [DekanController::class, 'bimbinganIndex'])->name('bimbingan.index');
        Route::get('/bimbingan/{bimbingan}', [DekanController::class, 'bimbinganShow'])->name('bimbingan.show');
        
        // EDOM
        Route::get('/edom', [DekanController::class, 'edomIndex'])->name('edom.index');
        
        // Absensi
        Route::get('/absensi', [DekanController::class, 'absensiIndex'])->name('absensi.index');
        Route::get('/absensi/{jadwalKuliah}', [DekanController::class, 'absensiShow'])->name('absensi.show');
        
        // Jadwal Ujian
        Route::get('/jadwal-ujian', [DekanController::class, 'jadwalUjianIndex'])->name('jadwal-ujian.index');
        
        // Jadwal Kuliah
        Route::get('/jadwal', [DekanController::class, 'jadwalKuliah'])->name('jadwal.index');
        
        // PKL/Magang
        Route::get('/pkl', [DekanController::class, 'pkl'])->name('pkl.index');
        Route::get('/pkl/{pendaftaran}', [DekanController::class, 'pklShow'])->name('pkl.show');
        
        // Konversi Nilai
        Route::get('/konversi-nilai', [DekanController::class, 'konversiNilai'])->name('konversi-nilai.index');
        Route::get('/konversi-nilai/{pengajuanKonversi}', [DekanController::class, 'konversiNilaiShow'])->name('konversi-nilai.show');
        
        // Laporan Fakultas
        Route::get('/laporan', [DekanController::class, 'laporan'])->name('laporan.index');
        
        // Dokumen & Tanda Tangan
        Route::get('/dokumen', [DekanController::class, 'dokumen'])->name('dokumen.index');
        
        // Statistik Akademik
        Route::get('/statistik', [DekanController::class, 'statistik'])->name('statistik.index');
        
        // Export Data
        Route::get('/export/mahasiswa', [DekanController::class, 'exportMahasiswaIndex'])->name('export.mahasiswa');
        Route::post('/export/mahasiswa', [DekanController::class, 'exportMahasiswa'])->name('export.mahasiswa.download');
        Route::get('/export/nilai', [DekanController::class, 'exportNilaiIndex'])->name('export.nilai');
        Route::post('/export/nilai', [DekanController::class, 'exportNilai'])->name('export.nilai.download');
    });
    
    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
});

