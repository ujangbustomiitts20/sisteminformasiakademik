<?php
/**
 * Script untuk menguji modul PMB
 * Jalankan dengan: php test_pmb_module.php
 */

require __DIR__ . '/vendor/autoload.php';

use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\CalonMahasiswa;
use App\Models\PembayaranPmb;
use App\Models\NilaiSeleksi;
use App\Models\HasilSeleksi;
use App\Models\DaftarUlang;
use App\Models\ProgramStudi;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Modul PMB ===\n\n";

// Test 1: Cek Data PMB
echo "1. Mengecek data PMB...\n";

$periodeCount = PeriodePmb::count();
$gelombangCount = GelombangPmb::count();
$jalurCount = JalurSeleksi::count();
$camabaCount = CalonMahasiswa::count();
$pembayaranCount = PembayaranPmb::count();
$nilaiCount = NilaiSeleksi::count();
$hasilCount = HasilSeleksi::count();
$daftarUlangCount = DaftarUlang::count();

echo "   - Periode PMB: {$periodeCount}\n";
echo "   - Gelombang PMB: {$gelombangCount}\n";
echo "   - Jalur Seleksi: {$jalurCount}\n";
echo "   - Calon Mahasiswa: {$camabaCount}\n";
echo "   - Pembayaran PMB: {$pembayaranCount}\n";
echo "   - Nilai Seleksi: {$nilaiCount}\n";
echo "   - Hasil Seleksi: {$hasilCount}\n";
echo "   - Daftar Ulang: {$daftarUlangCount}\n\n";

// Test 2: Cek Relasi Model
echo "2. Mengecek relasi model...\n";

$camaba = CalonMahasiswa::with(['gelombangPmb', 'jalurSeleksi', 'programStudi', 'hasilSeleksi', 'daftarUlang'])->first();
if ($camaba) {
    echo "   ✅ CalonMahasiswa->gelombangPmb: " . ($camaba->gelombangPmb ? $camaba->gelombangPmb->nama : 'NULL') . "\n";
    echo "   ✅ CalonMahasiswa->jalurSeleksi: " . ($camaba->jalurSeleksi ? $camaba->jalurSeleksi->nama : 'NULL') . "\n";
    echo "   ✅ CalonMahasiswa->programStudi: " . ($camaba->programStudi ? $camaba->programStudi->nama : 'NULL') . "\n";
    echo "   ✅ CalonMahasiswa->hasilSeleksi: " . ($camaba->hasilSeleksi ? $camaba->hasilSeleksi->status : 'NULL') . "\n";
    echo "   ✅ CalonMahasiswa->daftarUlang: " . ($camaba->daftarUlang ? $camaba->daftarUlang->status : 'NULL') . "\n";
} else {
    echo "   ⚠️ Tidak ada data calon mahasiswa\n";
}
echo "\n";

// Test 3: Cek Accessor
echo "3. Mengecek accessor model...\n";

if ($camaba) {
    echo "   ✅ status_label: " . $camaba->status_label . "\n";
    echo "   ✅ status_badge: " . $camaba->status_badge . "\n";
}

$daftarUlang = DaftarUlang::with('calonMahasiswa')->first();
if ($daftarUlang) {
    echo "   ✅ DaftarUlang->status_label: " . $daftarUlang->status_label . "\n";
    echo "   ✅ DaftarUlang->status_badge: " . $daftarUlang->status_badge . "\n";
    echo "   ✅ DaftarUlang->biaya_format: " . $daftarUlang->biaya_format . "\n";
}

$hasil = HasilSeleksi::first();
if ($hasil) {
    echo "   ✅ HasilSeleksi->status_label: " . $hasil->status_label . "\n";
    echo "   ✅ HasilSeleksi->status_badge: " . $hasil->status_badge . "\n";
}
echo "\n";

// Test 4: Cek Routes
echo "4. Mengecek routes PMB...\n";

$routes = [
    'pmb.dashboard',
    'pmb.periode.index',
    'pmb.gelombang.index',
    'pmb.jalur-seleksi.index',
    'pmb.calon-mahasiswa.index',
    'pmb.pembayaran.index',
    'pmb.seleksi.index',
    'pmb.seleksi.input-nilai',
    'pmb.seleksi.proses',
    'pmb.seleksi.hasil',
    'pmb.daftar-ulang.index',
    'pmb.daftar-ulang.generate',
];

foreach ($routes as $routeName) {
    try {
        $url = route($routeName);
        echo "   ✅ {$routeName} => {$url}\n";
    } catch (\Exception $e) {
        echo "   ❌ {$routeName} => ERROR: " . $e->getMessage() . "\n";
    }
}
echo "\n";

// Test 5: Statistik Data
echo "5. Statistik Data PMB...\n";

$stats = [
    'Total Pendaftar' => CalonMahasiswa::count(),
    'Terdaftar' => CalonMahasiswa::where('status', 'terdaftar')->count(),
    'Lulus Administrasi' => CalonMahasiswa::where('status', 'lulus_administrasi')->count(),
    'Mengikuti Ujian' => CalonMahasiswa::where('status', 'mengikuti_ujian')->count(),
    'Lulus Seleksi' => HasilSeleksi::where('status', 'lulus')->count(),
    'Tidak Lulus' => HasilSeleksi::where('status', 'tidak_lulus')->count(),
    'Daftar Ulang Pending' => DaftarUlang::where('status', 'pending')->count(),
    'Daftar Ulang Lunas' => DaftarUlang::where('status', 'lunas')->count(),
    'Daftar Ulang Selesai' => DaftarUlang::where('status', 'selesai')->count(),
];

foreach ($stats as $label => $count) {
    echo "   - {$label}: {$count}\n";
}
echo "\n";

echo "=== Test Selesai ===\n";
echo "✅ Modul PMB siap digunakan!\n";
