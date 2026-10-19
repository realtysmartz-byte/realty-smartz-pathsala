<?php
$src = __DIR__ . '/../public/assets/img/logo/Pathshala.webp';
$dst = __DIR__ . '/../public/assets/img/logo/Pathshala.png';
if (file_exists($src)) {
    $im = imagecreatefromwebp($src);
    if ($im) {
        imagepalettetotruecolor($im);
        imagealphablending($im, true);
        imagesavealpha($im, true);
        imagepng($im, $dst);
        imagedestroy($im);
        echo "Successfully generated: " . realpath($dst) . "\n";
    } else {
        echo "imagecreatefromwebp returned false\n";
    }
} else {
    echo "Source not found: $src\n";
}
