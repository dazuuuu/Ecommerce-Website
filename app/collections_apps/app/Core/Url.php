<?php

namespace App\Core;

/**
 * Public URL helpers. PathHandler owns local-vs-hosted site address handling.
 */
class Url
{
    public static function init(): void
    {
        PathHandler::init();
    }

    public static function basePath(): string
    {
        return PathHandler::basePath();
    }

    /** App URL for a route, e.g. Url::to('/admin/products') */
    public static function to(string $path = '/'): string
    {
        return PathHandler::to($path);
    }

    /** App URL for a static file under public/, e.g. Url::asset('assets/css/app.css') */
    public static function asset(string $path): string
    {
        return PathHandler::asset($path);
    }

    /**
     * The current request path relative to the app base (e.g. "/admin/products"),
     * with the query string stripped and a leading slash guaranteed.
     */
    public static function currentPath(): string
    {
        return PathHandler::currentPath();
    }
}
