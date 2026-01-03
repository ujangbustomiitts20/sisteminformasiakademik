<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing FAQ route...\n";
try {
    $controller = new App\Http\Controllers\PortalPmbController();
    $view = $controller->faq();
    echo "  View created: " . $view->getName() . "\n";
    $content = $view->render();
    echo "  ✅ Rendered OK (" . strlen($content) . " bytes)\n";
} catch (Exception $e) {
    echo "  ❌ ERROR: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (Throwable $e) {
    echo "  ❌ THROWABLE: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTesting Kontak route...\n";
try {
    $view = $controller->kontak();
    echo "  View created: " . $view->getName() . "\n";
    echo "  View data: ";
    print_r(array_keys($view->getData()));
    echo "\n";
    $content = $view->render();
    echo "  ✅ Rendered OK (" . strlen($content) . " bytes)\n";
    if (strlen($content) == 0) {
        echo "  ⚠️ Content is empty, checking file...\n";
        echo "  File exists: " . (file_exists('resources/views/portal-pmb/kontak.blade.php') ? 'yes' : 'no') . "\n";
        echo "  File size: " . filesize('resources/views/portal-pmb/kontak.blade.php') . " bytes\n";
    }
} catch (Exception $e) {
    echo "  ❌ ERROR: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} catch (Throwable $e) {
    echo "  ❌ THROWABLE: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nDone!\n";
