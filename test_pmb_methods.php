<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KontenPmb;
use App\Models\SliderPmb;
use App\Models\KontakPmb;
use App\Models\PeriodePmb;
use App\Models\GelombangPmb;
use App\Models\JalurSeleksi;
use App\Models\ProgramStudi;
use App\Models\KeunggulanPmb;
use App\Models\FasilitasPmb;
use App\Models\TestimoniPmb;
use App\Models\FaqPmb;
use App\Models\BeritaPmb;
use App\Models\GaleriPmb;

echo "===========================================\n";
echo "   TESTING PORTAL PMB METHODS & DATA      \n";
echo "===========================================\n\n";

$errors = [];

// Test 1: KontenPmb methods
echo "1. Testing KontenPmb:\n";
try {
    $kontenGrouped = KontenPmb::getAllGrouped();
    echo "   ✅ getAllGrouped() OK - Count: " . $kontenGrouped->flatten()->count() . "\n";
    
    $kontenByGroup = KontenPmb::getByGroup('alur');
    echo "   ✅ getByGroup('alur') OK - Count: " . $kontenByGroup->count() . "\n";
    
    $value = KontenPmb::getValue('nama_institusi', 'Default');
    echo "   ✅ getValue() OK - Value: " . ($value ?: 'null') . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "KontenPmb: " . $e->getMessage();
}

// Test 2: KontakPmb methods
echo "\n2. Testing KontakPmb:\n";
try {
    $kontak = KontakPmb::getKontak();
    echo "   ✅ getKontak() OK - Count: " . $kontak->count() . "\n";
    
    $sosmed = KontakPmb::getSosialMedia();
    echo "   ✅ getSosialMedia() OK - Count: " . $sosmed->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "KontakPmb: " . $e->getMessage();
}

// Test 3: SliderPmb scope
echo "\n3. Testing SliderPmb:\n";
try {
    $sliders = SliderPmb::active()->get();
    echo "   ✅ active() scope OK - Count: " . $sliders->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "SliderPmb: " . $e->getMessage();
}

// Test 4: PeriodePmb methods
echo "\n4. Testing PeriodePmb:\n";
try {
    $periode = PeriodePmb::getActive();
    echo "   ✅ getActive() OK - " . ($periode ? $periode->nama : 'null') . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "PeriodePmb: " . $e->getMessage();
}

// Test 5: GelombangPmb scopes
echo "\n5. Testing GelombangPmb:\n";
try {
    $gelombangActive = GelombangPmb::active()->get();
    echo "   ✅ active() scope OK - Count: " . $gelombangActive->count() . "\n";
    
    $gelombangBuka = GelombangPmb::active()->pendaftaranBuka()->first();
    echo "   ✅ pendaftaranBuka() scope OK - " . ($gelombangBuka ? $gelombangBuka->nama : 'none open') . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "GelombangPmb: " . $e->getMessage();
}

// Test 6: JalurSeleksi
echo "\n6. Testing JalurSeleksi:\n";
try {
    $jalur = JalurSeleksi::active()->get();
    echo "   ✅ active() scope OK - Count: " . $jalur->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "JalurSeleksi: " . $e->getMessage();
}

// Test 7: ProgramStudi with fakultas
echo "\n7. Testing ProgramStudi:\n";
try {
    $prodi = ProgramStudi::with('fakultas')->get();
    echo "   ✅ with('fakultas') OK - Count: " . $prodi->count() . "\n";
    $grouped = $prodi->groupBy('fakultas.nama');
    echo "   ✅ groupBy('fakultas.nama') OK - Groups: " . $grouped->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "ProgramStudi: " . $e->getMessage();
}

// Test 8: KeunggulanPmb
echo "\n8. Testing KeunggulanPmb:\n";
try {
    $keunggulan = KeunggulanPmb::active()->take(6)->get();
    echo "   ✅ active() scope OK - Count: " . $keunggulan->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "KeunggulanPmb: " . $e->getMessage();
}

// Test 9: FasilitasPmb
echo "\n9. Testing FasilitasPmb:\n";
try {
    $fasilitas = FasilitasPmb::active()->get();
    echo "   ✅ active() scope OK - Count: " . $fasilitas->count() . "\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "FasilitasPmb: " . $e->getMessage();
}

// Test 10: TestimoniPmb
echo "\n10. Testing TestimoniPmb:\n";
try {
    $testimoni = TestimoniPmb::active()->take(6)->get();
    echo "    ✅ active() scope OK - Count: " . $testimoni->count() . "\n";
} catch (Exception $e) {
    echo "    ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "TestimoniPmb: " . $e->getMessage();
}

// Test 11: FaqPmb
echo "\n11. Testing FaqPmb:\n";
try {
    $faq = FaqPmb::active()->get();
    echo "    ✅ active() scope OK - Count: " . $faq->count() . "\n";
} catch (Exception $e) {
    echo "    ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "FaqPmb: " . $e->getMessage();
}

// Test 12: BeritaPmb
echo "\n12. Testing BeritaPmb:\n";
try {
    $berita = BeritaPmb::published()->latest('published_at')->take(3)->get();
    echo "    ✅ published() scope OK - Count: " . $berita->count() . "\n";
    
    $beritaFeatured = BeritaPmb::published()->featured()->take(5)->get();
    echo "    ✅ featured() scope OK - Count: " . $beritaFeatured->count() . "\n";
} catch (Exception $e) {
    echo "    ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "BeritaPmb: " . $e->getMessage();
}

// Test 13: GaleriPmb
echo "\n13. Testing GaleriPmb:\n";
try {
    $galeri = GaleriPmb::active()->get();
    echo "    ✅ active() scope OK - Count: " . $galeri->count() . "\n";
    
    $kategori = GaleriPmb::distinct()->pluck('kategori')->filter();
    echo "    ✅ distinct pluck OK - Count: " . $kategori->count() . "\n";
} catch (Exception $e) {
    echo "    ❌ Error: " . $e->getMessage() . "\n";
    $errors[] = "GaleriPmb: " . $e->getMessage();
}

echo "\n===========================================\n";
if (empty($errors)) {
    echo "   SEMUA TEST BERHASIL ✅                 \n";
} else {
    echo "   TERDAPAT " . count($errors) . " ERROR ❌                 \n";
    foreach ($errors as $error) {
        echo "   - " . $error . "\n";
    }
}
echo "===========================================\n";
