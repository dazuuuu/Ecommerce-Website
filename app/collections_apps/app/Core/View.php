<?php

namespace App\Core;

/**
 * Minimal PHP-include view renderer. Controllers call View::render() once per
 * "chunk" (layout-header, page body, layout-footer) matching the pattern
 * already used throughout the templates — no compiled templating language.
 */
class View
{
    private static string $basePath;

    public static function render(string $view, array $data = []): void
    {
        self::$basePath = self::$basePath ?? dirname(__DIR__, 4) . '/public/Views/';
        $file = self::$basePath . str_replace('.', '/', $view) . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException("View not found: {$view} ({$file})");
        }
        extract($data, EXTR_SKIP);
        require $file;
    }

    public static function capture(string $view, array $data = []): string
    {
        ob_start();
        self::render($view, $data);
        return ob_get_clean();
    }
}
