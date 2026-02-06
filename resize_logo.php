<?php
// Resize logo2.png to a smaller version for PDF use
$src = __DIR__ . '/public/images/logo2.png';
$dst = __DIR__ . '/public/images/logo2_pdf.png';

if (!file_exists($src)) {
    die("Source file not found: $src\n");
}

$img = imagecreatefrompng($src);
$w = imagesx($img);
$h = imagesy($img);

// Resize to 200px width (appropriate for PDF logos)
$newW = 200;
$newH = (int)($h * ($newW / $w));

$resized = imagecreatetruecolor($newW, $newH);

// Preserve transparency
imagealphablending($resized, false);
imagesavealpha($resized, true);

imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);

// Save with maximum compression
imagepng($resized, $dst, 9);

imagedestroy($img);
imagedestroy($resized);

echo "Original size: " . round(filesize($src) / 1024, 2) . " KB\n";
echo "Resized logo saved to: $dst\n";
echo "New size: " . round(filesize($dst) / 1024, 2) . " KB\n";
