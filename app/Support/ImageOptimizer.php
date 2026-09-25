<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Optimizes uploaded images: downsizes wide images and converts to WebP.
 *
 * Uses plain GD (available on most shared hosting), so no extra
 * Composer packages are required. Falls back to storing the original
 * file when GD or WebP support is missing.
 */
class ImageOptimizer
{
    /**
     * Optimize an uploaded image and store it on the public disk.
     *
     * @return string Relative path on the public disk, e.g. "posts/abc123.webp"
     */
    public static function optimize(UploadedFile $file, string $directory, int $maxWidth = 1600, int $quality = 82): string
    {
        $realPath = $file->getRealPath();
        $info = $realPath ? @getimagesize($realPath) : false;

        if ($info === false || ! extension_loaded('gd')) {
            return $file->store($directory, 'public');
        }

        [$width, $height, $type] = $info;

        $create = match ($type) {
            IMAGETYPE_JPEG => 'imagecreatefromjpeg',
            IMAGETYPE_PNG => 'imagecreatefrompng',
            IMAGETYPE_WEBP => 'imagecreatefromwebp',
            IMAGETYPE_GIF => 'imagecreatefromgif',
            default => null,
        };

        if ($create === null || ! function_exists($create)) {
            return $file->store($directory, 'public');
        }

        $src = @$create($realPath);
        if ($src === false) {
            return $file->store($directory, 'public');
        }

        // Downsize if wider than the max width, keeping aspect ratio
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);
            $src = $dst;
        }

        $disk = Storage::disk('public');
        $filename = Str::random(40);
        $tmp = tempnam(sys_get_temp_dir(), 'imgopt');

        if (function_exists('imagewebp')) {
            $path = $directory . '/' . $filename . '.webp';
            $tmpFile = $tmp . '.webp';
            imagewebp($src, $tmpFile, $quality);
            $disk->put($path, file_get_contents($tmpFile));
            @unlink($tmpFile);
        } else {
            // No WebP support: flatten onto white and save as JPEG
            $path = $directory . '/' . $filename . '.jpg';
            $bg = imagecreatetruecolor(imagesx($src), imagesy($src));
            imagefilledrectangle($bg, 0, 0, imagesx($src), imagesy($src), imagecolorallocate($bg, 255, 255, 255));
            imagecopy($bg, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));
            $tmpFile = $tmp . '.jpg';
            imagejpeg($bg, $tmpFile, $quality);
            imagedestroy($bg);
            $disk->put($path, file_get_contents($tmpFile));
            @unlink($tmpFile);
        }

        imagedestroy($src);
        @unlink($tmp);

        return $path;
    }
}
