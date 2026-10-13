<?php
/**
 * Shared admin shell (sidebar + topbar). Include after setting:
 *   $pageTitle  — shown in <title> and the topbar
 *   $activeNav  — one of: dashboard, products, product-form, gallery, categories, offers, orders, seo, settings, updates
 * Requires App\Core\AdminSession::require() to have already run.
 */

use App\Core\AdminSession;
use App\Models\Order;
use App\Services\MigrationService;

$navItems = [
    ['id' => 'dashboard', 'href' => url('/admin'), 'label' => 'Dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />'],
    ['id' => 'products', 'href' => url('/admin/products'), 'label' => 'Products', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'],
    ['id' => 'product-form', 'href' => url('/admin/products/create'), 'label' => 'Add Product', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />'],
    ['id' => 'gallery', 'href' => url('/admin/gallery'), 'label' => 'Gallery', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />'],
    ['id' => 'offers', 'href' => url('/admin/offers'), 'label' => 'Offers', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />'],
    ['id' => 'categories', 'href' => url('/admin/categories'), 'label' => 'Categories', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />'],
    ['id' => 'orders', 'href' => url('/admin/orders'), 'label' => 'Orders', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />'],
    ['id' => 'sales', 'href' => url('/admin/sales'), 'label' => 'Record Sale', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3v-6m-3 6v-9m-2 9h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />'],
    ['id' => 'ads', 'href' => url('/admin/ads'), 'label' => 'Ads', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />'],
    ['id' => 'seo', 'href' => url('/admin/seo'), 'label' => 'SEO', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />'],
    ['id' => 'settings', 'href' => url('/admin/settings'), 'label' => 'Settings', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />'],
];
$navItems[] = ['id' => 'updates', 'href' => url('/admin/updates'), 'label' => 'Updates', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />'];
$admin = AdminSession::current();
$storeName = storeDisplayName();
$hasUpdate = false;
try {
    $hasUpdate = (bool) MigrationService::pending();
} catch (Throwable $e) {
    $hasUpdate = false;
}
$pendingOrderCount = 0;
try {
    $pendingOrderCount = Order::counts()['pending'];
} catch (Throwable $e) {
    $pendingOrderCount = 0;
}
$logoPath = storeLogoPath();
$logoUrl = $logoPath ? imageUrl($logoPath) : null;
$adminName = ucfirst(explode('@', $admin['email'] ?? 'admin')[0]);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="theme-color" content="#1e3a8a" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
  <meta name="apple-mobile-web-app-title" content="<?= e($storeName) ?> Admin" />
  <title><?= e($pageTitle ?? 'Admin') ?> | <?= e($storeName) ?> Admin</title>
  <?= faviconTags(url('/admin.webmanifest')) ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= asset('assets/css/app.css') ?>?v=<?= (int) @filemtime(dirname(__DIR__, 2) . '/assets/css/app.css') ?>">
  <style>
    body { font-family: 'Poppins', sans-serif; background-color: #f5f7fb; }
    .admin-sidebar { background: linear-gradient(180deg, #1e40af 0%, #1e3a8a 55%, #172e6e 100%); }
  </style>
</head>
<body class="admin-panel text-slate-800 antialiased min-h-screen flex">

  <!-- Mobile sidebar backdrop -->
  <div id="admin-sidebar-backdrop" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

  <!-- Sidebar -->
  <aside id="admin-sidebar" class="admin-sidebar w-64 shrink-0 text-white h-screen flex flex-col fixed lg:sticky top-0 z-40 -translate-x-full lg:translate-x-0 transition-transform duration-200 overflow-y-auto">
    <a href="<?= url('/admin') ?>" class="flex items-center gap-3 px-6 pt-7 pb-8">
      <?php if ($logoUrl): ?>
        <img src="<?= e($logoUrl) ?>" alt="<?= e($storeName) ?>" class="w-10 h-10 rounded-lg object-contain bg-white/10 p-1" />
      <?php else: ?>
        <div class="w-10 h-10 bg-white/15 flex items-center justify-center rounded-lg">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-5 h-5"><polygon points="12,2 22,9 18,21 6,21 2,9" /></svg>
        </div>
      <?php endif; ?>
      <div class="leading-tight">
        <span class="block text-lg font-bold tracking-tight"><?= e($storeName) ?></span>
        <span class="block text-[11px] text-blue-200">Admin Portal</span>
      </div>
    </a>

    <nav class="flex-1 px-4 space-y-1">
      <?php foreach ($navItems as $item): $active = ($activeNav ?? '') === $item['id']; ?>
        <a href="<?= e($item['href']) ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors text-[15px] font-medium <?= $active ? 'bg-blue-500 text-white shadow-md shadow-blue-900/30' : 'text-blue-50/90 hover:bg-white/10 hover:text-white' ?>">
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
          <?= e($item['label']) ?>
          <?php if ($item['id'] === 'updates' && $hasUpdate): ?>
            <span class="ml-auto bg-rose-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5">v<?= e(APP_VERSION) ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="p-4 space-y-3">
      <div class="rounded-xl bg-blue-500/40 border border-white/10 p-4">
        <p class="text-sm font-semibold leading-snug">See your store the way customers do</p>
        <div class="flex items-center gap-3 my-3 text-blue-100">
          <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
        </div>
        <a href="<?= url('/') ?>" target="_blank" class="flex items-center justify-between bg-white text-slate-900 text-sm font-semibold rounded-md px-3 py-1.5 hover:bg-blue-50">
          View Storefront
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </a>
      </div>
      <a href="<?= url('/admin/logout') ?>" class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-blue-100 hover:bg-white/10 hover:text-white text-sm font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
        Logout
      </a>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-1 min-w-0 flex flex-col min-h-screen">
    <header class="admin-appbar bg-white border-b border-slate-200 shadow-sm px-4 sm:px-8 py-3 sticky top-0 z-20 flex items-center gap-4">
      <!-- Mobile app bar: logo + page title -->
      <div class="flex items-center gap-2.5 min-w-0 lg:hidden">
        <?php if ($logoUrl): ?>
          <img src="<?= e($logoUrl) ?>" alt="" class="w-8 h-8 rounded-lg object-contain" />
        <?php endif; ?>
        <span class="text-lg font-semibold text-slate-900 truncate"><?= e($pageTitle ?? 'Admin') ?></span>
      </div>

      <form method="get" action="<?= url('/admin/orders') ?>" class="flex-1 max-w-md relative hidden sm:block">
        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        <input type="text" name="q" id="admin-search" placeholder="Search orders, products, customers..." class="w-full bg-white border border-slate-200 rounded-lg py-2 pl-10 pr-16 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 border border-slate-200 rounded px-1.5 py-0.5">Ctrl + K</kbd>
      </form>

      <div class="ml-auto flex items-center gap-3 sm:gap-5">
        <div class="hidden md:flex items-center gap-2 text-sm font-medium text-slate-700">
          <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
          <?= e(date('F j, Y')) ?>
        </div>

        <?php if ($hasUpdate): ?>
          <a href="<?= url('/admin/updates') ?>" class="bg-blue-600 text-white text-xs font-bold px-3 py-2 rounded-lg shadow-sm hover:bg-blue-700">
            Update to v<?= e(APP_VERSION) ?>
          </a>
        <?php endif; ?>

        <a href="<?= url('/admin/orders?status=pending') ?>" class="relative p-1 text-slate-700 hover:text-blue-600" title="<?= (int) $pendingOrderCount ?> pending orders">
          <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 22zm7-6V11a7 7 0 00-5.5-6.84V3.5a1.5 1.5 0 00-3 0v.66A7 7 0 005 11v5l-2 2v1h18v-1l-2-2z"/></svg>
          <?php if ($pendingOrderCount > 0): ?><span class="absolute top-0.5 right-0.5 w-2.5 h-2.5 bg-rose-500 rounded-full border-2 border-white"></span><?php endif; ?>
        </a>

        <div class="flex items-center gap-3">
          <div class="relative">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
              <?= e(strtoupper(substr($adminName, 0, 1))) ?>
            </div>
            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
          </div>
          <div class="hidden sm:flex flex-col leading-tight">
            <span class="text-sm font-semibold text-slate-900"><?= e($adminName) ?></span>
            <span class="text-xs text-slate-500">Admin</span>
          </div>
        </div>
      </div>
    </header>

    <main class="admin-main px-4 sm:px-8 py-5 sm:py-8 flex-1">
      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold rounded-xl p-4 shadow-sm flex items-center gap-3">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
          <?= e($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>

      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold rounded-xl p-4 shadow-sm flex items-center gap-3">
          <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          <?= e($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>
