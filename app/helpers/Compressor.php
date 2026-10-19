<?php
namespace App\Helpers;

use Exception;

class Compressor {
    /**
     * Compress an image file using GD
     * Returns true if successfully compressed under target size, false otherwise
     */
    public static function compressImage(string $sourcePath, string $destinationPath, int $maxDimension = 1600, int $targetMaxBytes = 2097152): bool {
        if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
            // GD not loaded; copy file directly if under limit
            if (@filesize($sourcePath) <= $targetMaxBytes) {
                return (bool) @copy($sourcePath, $destinationPath);
            }
            return false;
        }

        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            if (@filesize($sourcePath) <= $targetMaxBytes) {
                return (bool) @copy($sourcePath, $destinationPath);
            }
            return false;
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;

        // Load source image
        $sourceImage = null;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $sourceImage = @imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = @imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $sourceImage = @imagecreatefromwebp($sourcePath);
                }
                break;
        }

        if (!$sourceImage) {
            return (filesize($sourcePath) <= $targetMaxBytes);
        }

        // Calculate new dimensions preserving aspect ratio
        $newWidth = $origWidth;
        $newHeight = $origHeight;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            if ($origWidth > $origHeight) {
                $newWidth = $maxDimension;
                $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
            } else {
                $newHeight = $maxDimension;
                $newWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
            }
        }

        // Create canvas
        $newCanvas = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG and WebP
        if ($imageType === IMAGETYPE_PNG || $imageType === IMAGETYPE_WEBP) {
            imagealphablending($newCanvas, false);
            imagesavealpha($newCanvas, true);
            $transparent = imagecolorallocatealpha($newCanvas, 255, 255, 255, 127);
            imagefilledrectangle($newCanvas, 0, 0, $newWidth, $newHeight, $transparent);
        } else {
            // Fill background with white for JPEG
            $white = imagecolorallocate($newCanvas, 255, 255, 255);
            imagefilledrectangle($newCanvas, 0, 0, $newWidth, $newHeight, $white);
        }

        // Resample
        imagecopyresampled($newCanvas, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Adaptive quality loop
        $quality = 85;
        $tempPath = $destinationPath . '.tmp';

        do {
            if (function_exists('imagewebp') && ($imageType === IMAGETYPE_WEBP || str_ends_with(strtolower($destinationPath), '.webp'))) {
                @imagewebp($newCanvas, $tempPath, $quality);
            } elseif ($imageType === IMAGETYPE_PNG && str_ends_with(strtolower($destinationPath), '.png')) {
                // PNG compression level 0-9
                $pngQuality = (int) round((100 - $quality) / 10);
                @imagepng($newCanvas, $tempPath, $pngQuality);
            } else {
                @imagejpeg($newCanvas, $tempPath, $quality);
            }

            clearstatcache(true, $tempPath);
            $curSize = file_exists($tempPath) ? filesize($tempPath) : 0;
            $quality -= 10;
        } while ($curSize > $targetMaxBytes && $quality >= 30);

        imagedestroy($sourceImage);
        imagedestroy($newCanvas);

        if (file_exists($tempPath) && filesize($tempPath) <= $targetMaxBytes) {
            rename($tempPath, $destinationPath);
            return true;
        }

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // If compression failed to meet target, fallback to original if under limit
        if (file_exists($sourcePath) && filesize($sourcePath) <= $targetMaxBytes) {
            return (bool) @copy($sourcePath, $destinationPath);
        }
        return false;
    }
}
