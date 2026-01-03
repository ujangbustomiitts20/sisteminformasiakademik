<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

$routes = [
    'faq' => '/pmb-online/faq',
    'kontak' => '/pmb-online/kontak',
];

foreach ($routes as $name => $uri) {
    echo "Testing $name ($uri)...\n";
    try {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        
        echo "   Status: " . $response->getStatusCode() . "\n";
        
        if ($response->getStatusCode() !== 200) {
            $content = $response->getContent();
            // Try to extract error message
            if (preg_match('/<div class="exception-message[^>]*>([^<]+)/s', $content, $matches)) {
                echo "   Error: " . trim($matches[1]) . "\n";
            } elseif (preg_match('/message"?:\s*"([^"]+)/', $content, $matches)) {
                echo "   Error: " . $matches[1] . "\n";
            }
            // Save error page for debugging
            file_put_contents("error_{$name}.html", $content);
            echo "   Full error saved to error_{$name}.html\n";
        } else {
            echo "   ✅ OK\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Exception: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
    echo "\n";
}

echo "Done!\n";
