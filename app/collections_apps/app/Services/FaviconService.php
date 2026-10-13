<?php

namespace App\Services;

use App\Core\PathHandler;

/**
 * Builds the site favicons from the logo uploaded in Admin → Settings.
 * Icons live in public/assets/uploads/favicon/ and are rebuilt automatically
 * the next time a page loads after the logo changes (tracked in source.txt).
 */
class FaviconService
{
    public const SIZES = [16, 32, 180, 192, 512];

    private static function dir(): string
    {
        return PathHandler::publicRoot() . '/assets/uploads/favicon';
    }

    /** Relative paths (under public/) of the icons, or [] when no usable logo exists. */
    public static function ensure(?string $logoPath): array
    {
        $source = self::localFile($logoPath);
        if ($source === null || !function_exists('imagecreatetruecolor')) {
            return [];
        }

        $dir = self::dir();
        $stamp = $logoPath . '|' . filemtime($source);
        $stampFile = $dir . '/source.txt';
        if (!is_file($stampFile) || trim((string) file_get_contents($stampFile)) !== $stamp || !is_file($dir . '/favicon.ico')) {
            if (!self::generate($source, $dir)) {
                return [];
            }
            file_put_contents($stampFile, $stamp);
        }

        $icons = [];
        foreach (self::SIZES as $size) {
            $icons[$size] = 'assets/uploads/favicon/favicon-' . $size . '.png';
        }
        $icons['ico'] = 'assets/uploads/favicon/favicon.ico';
        return $icons;
    }

    public static function icoFile(): ?string
    {
        $file = self::dir() . '/favicon.ico';
        return is_file($file) ? $file : null;
    }

    private static function localFile(?string $logoPath): ?string
    {
        $logoPath = trim((string) $logoPath);
        if ($logoPath === '' || preg_match('#^(https?:|data:)#i', $logoPath)) {
            return null;
        }
        $file = PathHandler::publicRoot() . '/' . ltrim(preg_replace('#^/?public/#', '', $logoPath), '/');
        return is_file($file) ? $file : null;
    }

    private static function generate(string $source, string $dir): bool
    {
        $image = @imagecreatefromstring((string) file_get_contents($source));
        if (!$image) {
            return false;
        }
        if (!is_dir($dir) && !@mkdir($dir, 0775, true)) {
            return false;
        }

        $image = self::trimWhitespace($image);
        $pngs = [];
        foreach (self::SIZES as $size) {
            $canvas = self::squareCanvas($image, $size);
            ob_start();
            imagepng($canvas, null, 9);
            $pngs[$size] = ob_get_clean();
            file_put_contents($dir . '/favicon-' . $size . '.png', $pngs[$size]);
        }

        file_put_contents($dir . '/favicon.ico', self::ico([16 => $pngs[16], 32 => $pngs[32]]));
        return true;
    }

    /** Logo centred on a transparent square, keeping its aspect ratio. */
    private static function squareCanvas(\GdImage $image, int $size): \GdImage
    {
        $canvas = imagecreatetruecolor($size, $size);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagealphablending($canvas, true);

        $w = imagesx($image);
        $h = imagesy($image);
        $scale = $size / max($w, $h);
        $dw = max(1, (int) round($w * $scale));
        $dh = max(1, (int) round($h * $scale));
        imagecopyresampled($canvas, $image, (int) (($size - $dw) / 2), (int) (($size - $dh) / 2), 0, 0, $dw, $dh, $w, $h);
        return $canvas;
    }

    /** Crop surrounding white/transparent margins so the mark fills the icon. */
    private static function trimWhitespace(\GdImage $image): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $minX = $w; $minY = $h; $maxX = -1; $maxY = -1;
        for ($y = 0; $y < $h; $y += 2) {
            for ($x = 0; $x < $w; $x += 2) {
                $c = imagecolorat($image, $x, $y);
                $a = ($c >> 24) & 0x7F;
                $r = ($c >> 16) & 0xFF; $g = ($c >> 8) & 0xFF; $b = $c & 0xFF;
                if ($a < 100 && ($r < 235 || $g < 235 || $b < 235)) {
                    $minX = min($minX, $x); $maxX = max($maxX, $x);
                    $minY = min($minY, $y); $maxY = max($maxY, $y);
                }
            }
        }
        if ($maxX < 0) {
            return $image;
        }
        $pad = 2;
        $rect = ['x' => max(0, $minX - $pad), 'y' => max(0, $minY - $pad)];
        $rect['width'] = min($w, $maxX + $pad + 1) - $rect['x'];
        $rect['height'] = min($h, $maxY + $pad + 1) - $rect['y'];

        $cropped = imagecreatetruecolor($rect['width'], $rect['height']);
        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);
        imagecopy($cropped, $image, 0, 0, $rect['x'], $rect['y'], $rect['width'], $rect['height']);
        // Turn the near-white background into transparency so the icon sits cleanly on browser tabs.
        for ($y = 0; $y < $rect['height']; $y++) {
            for ($x = 0; $x < $rect['width']; $x++) {
                $c = imagecolorat($cropped, $x, $y);
                if ((($c >> 16) & 0xFF) > 245 && (($c >> 8) & 0xFF) > 245 && ($c & 0xFF) > 245) {
                    imagesetpixel($cropped, $x, $y, imagecolorallocatealpha($cropped, 255, 255, 255, 127));
                }
            }
        }
        return $cropped;
    }

    /** Multi-size .ico with embedded PNG frames (supported by all current browsers). */
    private static function ico(array $pngsBySize): string
    {
        $header = pack('vvv', 0, 1, count($pngsBySize));
        $entries = '';
        $data = '';
        $offset = 6 + 16 * count($pngsBySize);
        foreach ($pngsBySize as $size => $png) {
            $entries .= pack('CCCCvvVV', $size % 256, $size % 256, 0, 0, 1, 32, strlen($png), $offset);
            $data .= $png;
            $offset += strlen($png);
        }
        return $header . $entries . $data;
    }
}
