<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$routes = [
    'index' => '/pmb-online',
    'faq' => '/pmb-online/faq',
    'kontak' => '/pmb-online/kontak',
    'program-studi' => '/pmb-online/program-studi',
    'biaya' => '/pmb-online/biaya',
    'jadwal' => '/pmb-online/jadwal',
    'galeri' => '/pmb-online/galeri',
    'cek-pengumuman' => '/pmb-online/cek-pengumuman',
    'login' => '/pmb-online/login',
];

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$success = 0;
$failed = 0;

foreach ($routes as $name => $uri) {
    echo "Testing $name ($uri)... ";
    try {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        
        if ($status == 200) {
            echo "✅ OK\n";
            $success++;
        } else {
            echo "❌ Status: $status\n";
            $failed++;
        }
        
        // Terminate the request to clean up
        $kernel->terminate($request, $response);
    } catch (Throwable $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n===================\n";
echo "SUCCESS: $success / " . count($routes) . "\n";
echo "FAILED: $failed / " . count($routes) . "\n";
