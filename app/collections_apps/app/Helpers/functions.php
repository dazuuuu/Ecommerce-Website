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

function availableCurrencies(): array
{
    return [
        'USD' => ['label' => '$ (USD)', 'symbol' => '$', 'rate' => 1, 'decimals' => 2],
        'KSH' => ['label' => 'Ksh (KSH)', 'symbol' => 'Ksh ', 'rate' => 129.53, 'decimals' => 0],
        'TZS' => ['label' => 'TSh (TZS)', 'symbol' => 'TSh ', 'rate' => 2636.01, 'decimals' => 0],
        'UGX' => ['label' => 'USh (UGX)', 'symbol' => 'USh ', 'rate' => 3772.02, 'decimals' => 0],
        'EUR' => ['label' => '€ (EUR)', 'symbol' => '€', 'rate' => 0.88, 'decimals' => 2],
        'GBP' => ['label' => '£ (GBP)', 'symbol' => '£', 'rate' => 0.75, 'decimals' => 2],
        'JPY' => ['label' => '¥ (JPY)', 'symbol' => '¥', 'rate' => 163.86, 'decimals' => 0],
        'ZAR' => ['label' => 'R (ZAR)', 'symbol' => 'R', 'rate' => 16.81, 'decimals' => 2],
        'CAD' => ['label' => 'C$ (CAD)', 'symbol' => 'C$', 'rate' => 1.41, 'decimals' => 2],
        'AUD' => ['label' => 'A$ (AUD)', 'symbol' => 'A$', 'rate' => 1.43, 'decimals' => 2],
    ];
}

function formatPrice(float $amountInUsd, string $currency): string
{
    $currencies = availableCurrencies();
    $config = $currencies[$currency] ?? $currencies['USD'];
    $converted = $amountInUsd * $config['rate'];

    return $config['symbol'] . number_format($converted, $config['decimals']);
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

function storeSettingValue(string $key, string $default = ''): string
{
    try {
        return StoreSetting::get($key, $default) ?? $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function storeName(): string
{
    return storeSettingValue('store_name', 'Pentagon Collections');
}

function storeDisplayName(): string
{
    $name = trim(storeName());
    return $name !== '' ? $name : 'Pentagon Collections';
}

function storeContactPhone(): string
{
    return storeSettingValue('contact_phone', '+254 747 900 900');
}

function storeContactEmail(): string
{
    return storeSettingValue('contact_email', 'concierge@pentagoncollections.com');
}

function storeContactLocation(): string
{
    return storeSettingValue('contact_location', 'Nairobi, Kenya');
}

function storeLogoHtml(string $imageClass, string $fallbackSvgClass = 'w-4 h-4 text-white'): string
{
    $logo = storeLogoPath();
    if ($logo) {
        return '<img src="' . e(imageUrl($logo)) . '" alt="Store logo" class="' . e(trim($imageClass . ' store-logo-image')) . '" />';
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
