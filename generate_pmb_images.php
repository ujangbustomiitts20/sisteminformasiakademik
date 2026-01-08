<?php
/**
 * Script untuk generate placeholder images untuk Konten PMB
 * Jalankan: php generate_pmb_images.php
 */

$baseDir = __DIR__ . '/storage/app/public/pmb';

// Define images to create
$images = [
    // Slider (1920x600)
    'slider/slider-1.jpg' => ['width' => 1920, 'height' => 600, 'text' => 'Slider 1 - Selamat Datang', 'bg' => '1e88e5'],
    'slider/slider-2.jpg' => ['width' => 1920, 'height' => 600, 'text' => 'Slider 2 - Beasiswa', 'bg' => '43a047'],
    'slider/slider-3.jpg' => ['width' => 1920, 'height' => 600, 'text' => 'Slider 3 - Fasilitas', 'bg' => 'f4511e'],
    
    // Berita (800x450)
    'berita/berita-1.jpg' => ['width' => 800, 'height' => 450, 'text' => 'Berita PMB 1', 'bg' => '5c6bc0'],
    'berita/berita-2.jpg' => ['width' => 800, 'height' => 450, 'text' => 'Berita PMB 2', 'bg' => '26a69a'],
    'berita/berita-3.jpg' => ['width' => 800, 'height' => 450, 'text' => 'Berita PMB 3', 'bg' => 'ef5350'],
    'berita/berita-4.jpg' => ['width' => 800, 'height' => 450, 'text' => 'Berita PMB 4', 'bg' => 'ab47bc'],
    
    // Testimoni (300x300)
    'testimoni/testimoni-1.jpg' => ['width' => 300, 'height' => 300, 'text' => 'Ahmad F', 'bg' => '42a5f5'],
    'testimoni/testimoni-2.jpg' => ['width' => 300, 'height' => 300, 'text' => 'Siti N', 'bg' => 'ec407a'],
    'testimoni/testimoni-3.jpg' => ['width' => 300, 'height' => 300, 'text' => 'Budi S', 'bg' => '66bb6a'],
    'testimoni/testimoni-4.jpg' => ['width' => 300, 'height' => 300, 'text' => 'Diana P', 'bg' => 'ffa726'],
    
    // Galeri (800x600)
    'galeri/galeri-1.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Gedung Utama', 'bg' => '5c6bc0'],
    'galeri/galeri-2.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Lab Komputer', 'bg' => '26c6da'],
    'galeri/galeri-3.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Perpustakaan', 'bg' => '9ccc65'],
    'galeri/galeri-4.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Wisuda 2024', 'bg' => 'ffca28'],
    'galeri/galeri-5.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Seminar', 'bg' => 'ef5350'],
    'galeri/galeri-6.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Ruang Kelas', 'bg' => '7e57c2'],
    'galeri/galeri-7.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Area Parkir', 'bg' => '78909c'],
    'galeri/galeri-8.jpg' => ['width' => 800, 'height' => 600, 'text' => 'Kantin', 'bg' => 'ff7043'],
    
    // Keunggulan (600x400)
    'keunggulan/keunggulan-1.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Kurikulum Industri', 'bg' => '1976d2'],
    'keunggulan/keunggulan-2.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Dosen Berpengalaman', 'bg' => '388e3c'],
    'keunggulan/keunggulan-3.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Fasilitas Modern', 'bg' => 'f57c00'],
    'keunggulan/keunggulan-4.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Program Magang', 'bg' => '7b1fa2'],
    'keunggulan/keunggulan-5.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Sertifikasi', 'bg' => 'c2185b'],
    'keunggulan/keunggulan-6.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Biaya Terjangkau', 'bg' => '00796b'],
    
    // Fasilitas (600x400)
    'fasilitas/fasilitas-1.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Lab Komputer', 'bg' => '0288d1'],
    'fasilitas/fasilitas-2.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Perpustakaan', 'bg' => '689f38'],
    'fasilitas/fasilitas-3.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Ruang Kelas', 'bg' => 'fbc02d'],
    'fasilitas/fasilitas-4.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Auditorium', 'bg' => 'd32f2f'],
    'fasilitas/fasilitas-5.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Mushola', 'bg' => '512da8'],
    'fasilitas/fasilitas-6.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Kantin', 'bg' => 'e64a19'],
    'fasilitas/fasilitas-7.jpg' => ['width' => 600, 'height' => 400, 'text' => 'Parkir', 'bg' => '455a64'],
    'fasilitas/fasilitas-8.jpg' => ['width' => 600, 'height' => 400, 'text' => 'WiFi', 'bg' => '00838f'],
];

echo "Generating placeholder images for PMB...\n\n";

foreach ($images as $path => $config) {
    $fullPath = $baseDir . '/' . $path;
    $dir = dirname($fullPath);
    
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Create image using GD
    $img = imagecreatetruecolor($config['width'], $config['height']);
    
    // Parse background color
    $bg = $config['bg'];
    $r = hexdec(substr($bg, 0, 2));
    $g = hexdec(substr($bg, 2, 2));
    $b = hexdec(substr($bg, 4, 2));
    
    $bgColor = imagecolorallocate($img, $r, $g, $b);
    $textColor = imagecolorallocate($img, 255, 255, 255);
    $shadowColor = imagecolorallocate($img, 0, 0, 0);
    
    // Fill background
    imagefill($img, 0, 0, $bgColor);
    
    // Add some visual elements
    // Diagonal lines for texture
    for ($i = -$config['height']; $i < $config['width']; $i += 30) {
        $lineColor = imagecolorallocatealpha($img, 255, 255, 255, 110);
        imageline($img, $i, 0, $i + $config['height'], $config['height'], $lineColor);
    }
    
    // Add gradient overlay at bottom
    for ($y = $config['height'] - 100; $y < $config['height']; $y++) {
        $alpha = (int)(($y - ($config['height'] - 100)) / 100 * 80);
        $overlayColor = imagecolorallocatealpha($img, 0, 0, 0, 127 - $alpha);
        imageline($img, 0, $y, $config['width'], $y, $overlayColor);
    }
    
    // Add text
    $text = $config['text'];
    $fontSize = 5; // Built-in font size (1-5)
    
    // Calculate text position for center
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textHeight = imagefontheight($fontSize);
    $x = ($config['width'] - $textWidth) / 2;
    $y = ($config['height'] - $textHeight) / 2;
    
    // Draw shadow
    imagestring($img, $fontSize, $x + 2, $y + 2, $text, $shadowColor);
    // Draw text
    imagestring($img, $fontSize, $x, $y, $text, $textColor);
    
    // Add dimensions text at bottom
    $dimText = $config['width'] . 'x' . $config['height'];
    $dimWidth = imagefontwidth(2) * strlen($dimText);
    imagestring($img, 2, ($config['width'] - $dimWidth) / 2, $config['height'] - 25, $dimText, $textColor);
    
    // Save as JPEG
    imagejpeg($img, $fullPath, 85);
    imagedestroy($img);
    
    echo "✓ Created: $path\n";
}

echo "\n✅ All placeholder images created successfully!\n";
echo "Total: " . count($images) . " images\n";
