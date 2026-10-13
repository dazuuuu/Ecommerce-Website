<?php

namespace App\Core;

/**
 * Minimal PHP-include view renderer. Controllers call View::render() once per
 * "chunk" (layout-header, page body, layout-footer) matching the pattern
 * already used throughout the templates — no compiled templating language.
 */
class View
{
    public static function render(string $view, array $data = []): void
    {
        $relative = str_replace('.', '/', $view) . '.php';
        $file = PathHandler::viewsPath() . '/' . $relative;
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
