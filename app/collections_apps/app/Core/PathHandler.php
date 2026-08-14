<?php

namespace App\Core;

/**
 * Builds every public URL (pages, CSS/JS, uploaded images) from the site
 * address in config/paths.php so local and hosted copies do not 404.
 *
 * Also recovers missing /assets/... requests: if Apache cannot find the file
 * at the requested URL, we look under public/ or public_html/ and serve it.
 */
class PathHandler
{
    private static bool $ready = false;
    private static string $appUrl = '';
    private static string $basePath = '';
    private static string $publicRoot = '';

    public static function init(): void
    {
        if (self::$ready) {
            return;
        }

        self::$publicRoot = self::detectPublicRoot();
        $configured = self::configuredAppUrl();
        self::$appUrl = $configured !== '' ? $configured : self::detectAppUrl();
        self::$basePath = self::pathFromAppUrl(self::$appUrl);
        self::$ready = true;
    }

    /** Site origin + folder, no trailing slash. e.g. https://example.com or http://localhost/shop/public */
    public static function appUrl(): string
    {
        self::init();
        return self::$appUrl;
    }

    /** URL path prefix only, e.g. "/shop/public" or "". */
    public static function basePath(): string
    {
        self::init();
        return self::$basePath;
    }

    /** Filesystem folder that holds index.php, assets/, Views/. */
    public static function publicRoot(): string
    {
        self::init();
        return self::$publicRoot;
    }

    public static function to(string $path = '/'): string
    {
        self::init();
        $path = '/' . ltrim(str_replace('\\', '/', $path), '/');
        if ($path === '/') {
            return self::$appUrl === '' ? '/' : self::$appUrl . '/';
        }
        return self::$appUrl . $path;
    }

    public static function asset(string $path): string
    {
        return self::to(self::publicRelative($path) ?? ltrim($path, '/'));
    }

    /**
     * Turn a DB/stored image value into a working URL for the current host.
     * Rewrites leftover localhost / old-domain / public/ prefixes.
     */
    public static function image(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (stripos($path, 'data:') === 0) {
            return $path;
        }

        if (preg_match('#^https?://#i', $path)) {
            if (self::isExternalHost($path)) {
                return $path;
            }
            $path = parse_url($path, PHP_URL_PATH) ?? '';
        }

        $relative = self::publicRelative($path);
        if ($relative === null) {
            return self::to(ltrim(str_replace('\\', '/', $path), '/'));
        }

        return self::to($relative);
    }

    public static function currentPath(): string
    {
        self::init();
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $uri = str_replace('\\', '/', $uri);

        foreach (self::basePrefixes() as $prefix) {
            if ($prefix !== '' && strpos($uri, $prefix) === 0) {
                $uri = substr($uri, strlen($prefix));
                break;
            }
        }

        $uri = '/' . ltrim($uri, '/');
        return rtrim($uri, '/') === '' ? '/' : rtrim($uri, '/');
    }

    /**
     * Serve a public asset that missed Apache's file check (wrong folder prefix).
     * Returns true when a file (or image placeholder) was sent.
     */
    public static function tryServe(string $requestPath): bool
    {
        self::init();
        $relative = self::publicRelative($requestPath);
        if ($relative === null || !preg_match('#^assets/#', $relative)) {
            return false;
        }
        if (strpos($relative, '..') !== false) {
            return false;
        }

        $full = self::$publicRoot . '/' . $relative;
        $realPublic = realpath(self::$publicRoot);
        $realFile = is_file($full) ? realpath($full) : false;
        if ($realPublic && $realFile && strpos($realFile, $realPublic) === 0) {
            self::sendFile($realFile);
            return true;
        }

        if (self::isImagePath($relative)) {
            self::sendMissingImagePlaceholder();
            return true;
        }

        return false;
    }

    private static function configuredAppUrl(): string
    {
        $fromFile = '';
        $file = dirname(__DIR__, 2) . '/config/paths.php';
        if (is_file($file)) {
            $config = include $file;
            if (is_array($config)) {
                $fromFile = (string) ($config['APP_URL'] ?? $config['app_url'] ?? '');
            }
        }

        $fromEnv = (string) Env::get('APP_URL', '');
        $raw = trim($fromFile !== '' ? $fromFile : $fromEnv);
        return self::normalizeAppUrl($raw);
    }

    private static function detectAppUrl(): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443')
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        $scheme = $https ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? '');
        $host = trim(explode(',', $host)[0]);
        if ($host === '') {
            return '';
        }

        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $scriptDir = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

        return self::normalizeAppUrl($scheme . '://' . $host . $scriptDir);
    }

    private static function normalizeAppUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        $url = rtrim($url, '/');
        $url = preg_replace('#/index\.php$#i', '', $url) ?? $url;
        return rtrim($url, '/');
    }

    private static function pathFromAppUrl(string $appUrl): string
    {
        if ($appUrl === '') {
            $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
            return $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
        }
        $path = parse_url($appUrl, PHP_URL_PATH);
        if (!is_string($path) || $path === '/' || $path === '') {
            return '';
        }
        return rtrim($path, '/');
    }

    private static function detectPublicRoot(): string
    {
        $root = dirname(__DIR__, 4);
        foreach (['public', 'public_html'] as $dir) {
            $candidate = $root . '/' . $dir;
            if (is_dir($candidate . '/assets') || is_file($candidate . '/index.php')) {
                return $candidate;
            }
        }
        return $root . '/public';
    }

    /** @return list<string> */
    private static function basePrefixes(): array
    {
        $prefixes = [];
        if (self::$basePath !== '') {
            $prefixes[] = self::$basePath;
        }
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        $scriptDir = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');
        if ($scriptDir !== '' && !in_array($scriptDir, $prefixes, true)) {
            $prefixes[] = $scriptDir;
        }
        return $prefixes;
    }

    /**
     * Map any stored or requested path onto a file under the public folder
     * (assets/...). Returns null when this is not a public-file path.
     */
    private static function publicRelative(string $path): ?string
    {
        $path = str_replace('\\', '/', trim($path));
        if ($path === '') {
            return null;
        }

        $path = preg_replace('#^https?://[^/]+#i', '', $path) ?? $path;
        $path = '/' . ltrim($path, '/');

        foreach (self::basePrefixes() as $prefix) {
            if ($prefix !== '' && strpos($path, $prefix . '/') === 0) {
                $path = substr($path, strlen($prefix));
            }
        }

        if (preg_match('#(?:^|/)(assets/.+)$#', $path, $matches)) {
            return $matches[1];
        }

        $trimmed = ltrim($path, '/');
        $trimmed = preg_replace('#^(?:public_html|public)/#i', '', $trimmed) ?? $trimmed;
        if (strpos($trimmed, 'assets/') === 0) {
            return $trimmed;
        }

        return null;
    }

    private static function isExternalHost(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        $ours = strtolower((string) parse_url(self::appUrl(), PHP_URL_HOST));
        $local = ['localhost', '127.0.0.1', '::1'];
        if (in_array($host, $local, true)) {
            return false;
        }
        if ($ours !== '' && $host === $ours) {
            return false;
        }

        return true;
    }

    private static function isImagePath(string $relative): bool
    {
        return (bool) preg_match('#\.(jpe?g|png|gif|webp|svg)$#i', $relative);
    }

    private static function sendFile(string $fullPath): void
    {
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
        ];
        $type = $types[$ext] ?? 'application/octet-stream';

        header('Content-Type: ' . $type);
        header('Content-Length: ' . (string) filesize($fullPath));
        header('Cache-Control: public, max-age=86400');
        header('X-Content-Type-Options: nosniff');
        readfile($fullPath);
    }

    private static function sendMissingImagePlaceholder(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 1000"><rect width="800" height="1000" fill="#f5f5f5"/><text x="400" y="500" font-family="Arial, sans-serif" font-size="32" fill="#737373" text-anchor="middle">Image not found</text></svg>';
        http_response_code(200);
        header('Content-Type: image/svg+xml; charset=UTF-8');
        header('Cache-Control: no-store');
        echo $svg;
    }
}
