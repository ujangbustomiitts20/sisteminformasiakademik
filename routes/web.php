<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\DosenController;
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
use App\Http\Controllers\RefundController;
use App\Http\Controllers\JenisPotonganController;
use App\Http\Controllers\PeriodeDiskonController;
use App\Http\Controllers\PotonganMahasiswaController;
use App\Http\Controllers\MahasiswaPortalController;
use App\Http\Controllers\PengajuanSuratController;
use App\Http\Controllers\PrasyaratMataKuliahController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\BimbinganAkademikController;
use App\Http\Controllers\CutiAkademikController;
use App\Http\Controllers\Mahasiswa\TranskripController;
use App\Http\Controllers\Mahasiswa\KhsController;
use App\Http\Controllers\Mahasiswa\JadwalUjianController;
use App\Http\Controllers\Mahasiswa\KehadiranController;

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
        
        // Master Data - Fakultas
        Route::resource('fakultas', FakultasController::class)->except(['show', 'create', 'edit']);
        
        // Master Data - Program Studi
        Route::resource('program-studi', ProgramStudiController::class)->except(['show', 'create', 'edit']);
        
        // Master Data - Ruangan
        Route::resource('ruangan', RuanganController::class)->except(['show', 'create', 'edit']);
        
        // Master Data - Tahun Akademik
        Route::resource('tahun-akademik', TahunAkademikController::class)->except(['show', 'create', 'edit']);
        Route::post('/tahun-akademik/{tahunAkademik}/activate', [TahunAkademikController::class, 'activate'])->name('tahun-akademik.activate');
        
        // Manajemen Mahasiswa
        Route::resource('mahasiswa', MahasiswaController::class);
        
        // Manajemen Dosen
        Route::resource('dosen', DosenController::class);
        
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
    });

    // =====================
    // DOSEN ROUTES
    // =====================
    Route::middleware(['role:admin,dosen'])->group(function () {
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
    });

    // =====================
    // MAHASISWA ROUTES
    // =====================
    Route::middleware(['role:mahasiswa'])->group(function () {
        // KRS
        Route::get('/krs', [KrsController::class, 'index'])->name('krs.index');
        Route::get('/krs/create', [KrsController::class, 'create'])->name('krs.create');
        Route::post('/krs', [KrsController::class, 'store'])->name('krs.store');
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
        Route::get('/potongan-saya', [MahasiswaPortalController::class, 'potongan'])->name('mahasiswa.potongan');
        Route::get('/download/kartu-tagihan', [MahasiswaPortalController::class, 'downloadKartuTagihan'])->name('mahasiswa.download-kartu-tagihan');
        Route::get('/download/riwayat-pembayaran', [MahasiswaPortalController::class, 'downloadRiwayatPembayaran'])->name('mahasiswa.download-riwayat-pembayaran');
        Route::get('/download/kartu-mahasiswa', [MahasiswaPortalController::class, 'downloadKartuMahasiswa'])->name('mahasiswa.download-kartu');

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
    });
    
    // Routes untuk semua role yang authenticated
    // Kalender Akademik (view)
    Route::get('/kalender', [KalenderAkademikController::class, 'index'])->name('kalender.index');
    Route::get('/kalender/events', [KalenderAkademikController::class, 'events'])->name('kalender.events');
    
    // Download Materi Pertemuan (untuk dosen dan mahasiswa)
    Route::get('/pertemuan/download/{pertemuan}', [PertemuanController::class, 'downloadMateri'])->name('pertemuan.download');
    
    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
});

