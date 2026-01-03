<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

use Illuminate\Http\Request;

echo "===========================================\n";
echo "   TESTING PORTAL PMB ROUTES              \n";
echo "===========================================\n\n";

$routes = [
    '/pmb-online/' => 'Halaman Utama',
    '/pmb-online/pendaftaran' => 'Pendaftaran',
    '/pmb-online/jalur-seleksi' => 'Jalur Seleksi',
    '/pmb-online/program-studi' => 'Program Studi',
    '/pmb-online/biaya' => 'Biaya Kuliah',
    '/pmb-online/jadwal' => 'Jadwal PMB',
    '/pmb-online/alur-pendaftaran' => 'Alur Pendaftaran',
    '/pmb-online/syarat' => 'Syarat',
    '/pmb-online/berita' => 'Berita',
    '/pmb-online/faq' => 'FAQ',
    '/pmb-online/galeri' => 'Galeri',
    '/pmb-online/kontak' => 'Kontak',
    '/pmb-online/fasilitas' => 'Fasilitas',
    '/pmb-online/cek-pengumuman' => 'Cek Pengumuman',
    '/pmb-online/login' => 'Login Camaba',
];

$errors = [];
$success = 0;

foreach ($routes as $uri => $name) {
    try {
        $request = Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        if ($status == 200) {
            echo "✅ [{$status}] {$name}: {$uri}\n";
            $success++;
        } elseif ($status == 302) {
            echo "↪️  [{$status}] {$name}: {$uri} (Redirect)\n";
            $success++;
        } else {
            echo "❌ [{$status}] {$name}: {$uri}\n";
            $errors[] = "{$name} ({$uri}): Status {$status}";
        }
        
        // Terminate for next request
        $kernel->terminate($request, $response);
        
    } catch (Exception $e) {
        echo "❌ [ERR] {$name}: {$uri}\n";
        echo "   Error: " . $e->getMessage() . "\n";
        $errors[] = "{$name} ({$uri}): " . $e->getMessage();
    }
}

echo "\n===========================================\n";
echo "   RESULTS: {$success}/" . count($routes) . " routes OK\n";

if (!empty($errors)) {
    echo "\n   ERRORS:\n";
    foreach ($errors as $error) {
        echo "   - {$error}\n";
    }
}
echo "===========================================\n";
