<?php
/**
 * Global helper functions available to every controller and view.
 */

use App\Core\PathHandler;
use App\Core\Url;
use App\Models\StoreSetting;

function url(string $path = '/'): string
{
    return Url::to($path);
}

function asset(string $path): string
{
    return Url::asset($path);
}

/**
 * Product/category/logo images: seed CDN URLs stay as-is; uploaded files and
 * leftover localhost paths are rewritten to the current site URL in config/paths.php.
 */
function imageUrl(?string $path): string
{
    return PathHandler::image($path);
}

function formatPrice(float $amountInUsd, string $currency): string
{
    if ($currency === 'KSH') {
        return 'Ksh ' . number_format($amountInUsd * 100);
    }
    $rate = $currency === 'EUR' ? 0.92 : ($currency === 'GBP' ? 0.79 : 1.0);
    $symbol = $currency === 'EUR' ? '€' : ($currency === 'GBP' ? '£' : '$');
    return $symbol . number_format(round($amountInUsd * $rate));
}

function pentagonLogoSvg(string $class = 'w-4 h-4 text-white'): string
{
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="' . $class . '"><polygon points="12,2 22,9 18,21 6,21 2,9" /></svg>';
}

function storeLogoPath(): ?string
{
    try {
        return StoreSetting::get('store_logo');
    } catch (Throwable $e) {
        return null;
    }
}

function storeLogoHtml(string $imageClass, string $fallbackSvgClass = 'w-4 h-4 text-white'): string
{
    $logo = storeLogoPath();
    if ($logo) {
        return '<img src="' . e(imageUrl($logo)) . '" alt="Store logo" class="' . e($imageClass) . '" />';
    }
    return pentagonLogoSvg($fallbackSvgClass);
}

function e($str): string
{
    return htmlspecialchars((string) ($str ?? ''), ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

function csrfVerify(?string $token): bool
{
    return !empty($_SESSION['csrf_token']) && $token !== null && hash_equals($_SESSION['csrf_token'], $token);
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flashSuccess(string $message): void
{
    $_SESSION['flash_success'] = $message;
}

function flashError(string $message): void
{
    $_SESSION['flash_error'] = $message;
}
