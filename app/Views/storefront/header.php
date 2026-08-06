<?php
/**
 * Mirrors src/components/Header.tsx
 * Interactivity (scroll shadow, mobile drawer, currency dropdown, active nav state,
 * cart/wishlist counts) is handled client-side in assets/js/app.js.
 */
$navLinks = [
    ['id' => 'all', 'label' => 'All Goods'],
    ['id' => 'new', 'label' => 'New Arrivals'],
    ['id' => 'accessories', 'label' => 'Dining & Furniture'],
    ['id' => 'outerwear', 'label' => 'Outerwear'],
    ['id' => 'dresses', 'label' => 'Dresses & Sets'],
    ['id' => 'knitwear', 'label' => 'Cashmere & Knit'],
];
$currencyLabels = [
    'KSH' => 'Ksh (KSH)',
    'USD' => '$ (USD)',
    'EUR' => '€ (EUR)',
    'GBP' => '£ (GBP)',
];
?>
<header class="sticky top-0 z-40 w-full" id="site-header">
  <div id="header-bar" class="w-full transition-all duration-200 border-b bg-[#0c2e1b] py-3 sm:py-4 border-[#1b432a] text-white">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 flex items-center justify-between">

      <!-- Mobile Left: Menu Hamburger -->
      <div class="flex items-center lg:hidden">
        <button id="mobile-menu-toggle" class="p-1.5 text-amber-200 hover:text-white focus:outline-none cursor-pointer rounded-md active:bg-[#184229]" aria-label="Toggle menu">
          <svg id="icon-menu" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
          <svg id="icon-x" class="w-6 h-6 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
        </button>
      </div>

      <!-- Center Brand Logo -->
      <div class="flex-1 lg:flex-none text-center lg:text-left pl-2 sm:pl-0">
        <button data-select-category="all" class="nav-select inline-flex items-center gap-2 group cursor-pointer">
          <div class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center bg-[#0a0a0a] text-amber-300 rounded-md border border-amber-400/40 shrink-0">
            <?= pentagonLogoSvg('w-4 h-4 text-amber-300') ?>
          </div>
          <div class="flex flex-col text-left">
            <span class="font-serif-heading text-lg sm:text-2xl font-extrabold tracking-[0.18em] text-white leading-none uppercase">PENTAGON</span>
            <span class="text-[8px] sm:text-[9px] tracking-[0.3em] text-amber-300/90 font-sans font-semibold uppercase mt-0.5">COLLECTIONS</span>
          </div>
        </button>
      </div>

      <!-- Desktop Links -->
      <nav class="hidden lg:flex items-center space-x-6 text-xs tracking-widest font-semibold uppercase text-amber-100" id="desktop-nav-links">
        <?php foreach ($navLinks as $link): ?>
          <button data-select-category="<?= e($link['id']) ?>" class="nav-select relative py-1 transition-colors hover:text-amber-300 cursor-pointer text-amber-100/90" data-nav-id="<?= e($link['id']) ?>">
            <?= e($link['label']) ?>
            <span class="nav-active-underline absolute bottom-0 left-0 w-full h-[2px] bg-amber-400 rounded-full hidden"></span>
          </button>
        <?php endforeach; ?>
      </nav>

      <!-- Right Action Utilities -->
      <div class="flex items-center space-x-1.5 sm:space-x-3 text-white">

        <button id="open-search" class="p-1.5 sm:p-2 text-amber-200 hover:text-amber-300 transition-colors cursor-pointer rounded-md" title="Search collection" aria-label="Search">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>

        <!-- Currency Selector (Desktop / Tablet) -->
        <div class="relative hidden sm:block">
          <button id="currency-dropdown-toggle" class="flex items-center space-x-1 text-xs font-bold tracking-wider text-amber-300 border border-amber-400/30 bg-[#0a0a0a] px-2.5 py-1.5 rounded-md hover:border-amber-400 transition-all cursor-pointer">
            <span id="currency-label-header">KSH</span>
            <svg class="w-3.5 h-3.5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div id="currency-dropdown-menu" class="hidden absolute right-0 mt-2 w-32 bg-[#0a0a0a] border border-amber-500/40 shadow-2xl rounded-md py-1 z-50 text-xs font-medium">
            <?php foreach ($currencyLabels as $code => $label): ?>
              <button data-select-currency="<?= e($code) ?>" class="currency-option w-full text-left px-3 py-2 hover:bg-[#184229] transition-colors text-amber-100/80"><?= e($label) ?></button>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Wishlist Button -->
        <button id="open-wishlist" class="relative p-1.5 sm:p-2 text-amber-200 hover:text-amber-300 transition-colors cursor-pointer rounded-md" title="View Wishlist" aria-label="Wishlist">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          <span id="wishlist-count-badge" class="hidden absolute -top-0.5 -right-0.5 w-4 h-4 bg-rose-600 text-white rounded-full text-[10px] font-bold flex items-center justify-center">0</span>
        </button>

        <!-- Shopping Cart Button -->
        <button id="open-cart" class="relative bg-[#0a0a0a] hover:bg-black text-amber-300 border border-amber-400/50 hover:border-amber-400 transition-all rounded-md flex items-center space-x-1.5 px-2.5 sm:px-3 py-1.5 cursor-pointer shadow-sm" title="View Cart" aria-label="Cart">
          <svg class="w-4 h-4 text-amber-300 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="text-xs font-bold tracking-wider hidden sm:inline">CART</span>
          <span id="cart-count-badge" class="hidden bg-amber-400 text-[#0a0a0a] text-[10px] font-bold px-1.5 py-0.2 rounded-full min-w-[18px] text-center">0</span>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Drawer Navigation -->
  <div id="mobile-drawer" class="fixed inset-0 z-50 lg:hidden hidden">
    <div id="mobile-drawer-backdrop" class="fixed inset-0 bg-black/70 transition-opacity"></div>
    <div class="relative w-4/5 max-w-xs bg-[#0c2e1b] text-white h-full shadow-2xl flex flex-col justify-between z-10 p-5 overflow-y-auto border-r border-[#1b432a]">
      <div>
        <div class="flex items-center justify-between pb-4 border-b border-[#1b432a]">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 bg-[#0a0a0a] text-amber-300 flex items-center justify-center rounded-md border border-amber-400/30">
              <?= pentagonLogoSvg('w-4 h-4 text-amber-300') ?>
            </div>
            <span class="font-serif-heading font-extrabold text-base tracking-widest text-white uppercase">PENTAGON</span>
          </div>
          <button id="mobile-drawer-close" class="p-1.5 text-amber-300 hover:text-white cursor-pointer rounded-md">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>

        <div class="mt-4">
          <button id="mobile-open-search" class="w-full flex items-center justify-between bg-[#0a0a0a] border border-[#1b432a] text-amber-200/80 px-3 py-2 rounded-md text-xs font-medium cursor-pointer">
            <span class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              Search goods...
            </span>
            <span class="text-[10px] bg-[#184229] text-amber-300 px-1.5 py-0.5 rounded">Find</span>
          </button>
        </div>

        <div class="py-5 space-y-1">
          <p class="text-[10px] uppercase tracking-widest text-amber-400 font-bold mb-2">Browse Categories</p>
          <?php foreach ($navLinks as $link): ?>
            <button data-select-category="<?= e($link['id']) ?>" class="nav-select-mobile block w-full text-left py-2.5 px-3 rounded-md text-xs tracking-wider uppercase font-semibold transition-all text-amber-100 hover:bg-[#184229]" data-nav-id="<?= e($link['id']) ?>">
              <?= e($link['label']) ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="pt-4 border-t border-[#1b432a] space-y-3 text-xs text-amber-100">
        <div class="flex items-center justify-between">
          <span class="text-amber-200/80 text-xs font-medium">Currency:</span>
          <div class="flex space-x-1" id="mobile-currency-options">
            <?php foreach (array_keys($currencyLabels) as $code): ?>
              <button data-select-currency="<?= e($code) ?>" class="currency-option-mobile px-2 py-1 rounded text-[11px] font-bold cursor-pointer bg-[#0a0a0a] text-amber-300 border border-[#1b432a]"><?= e($code) ?></button>
            <?php endforeach; ?>
          </div>
        </div>

        <button id="mobile-open-size-guide" class="w-full text-left py-1 text-xs text-amber-300 hover:underline cursor-pointer">
          Size Guide &amp; Product Specifications
        </button>

        <p class="text-[10px] text-amber-200/50 pt-2 border-t border-[#1b432a]/50">
          © Pentagon Collections. Nairobi, Kenya.
        </p>
      </div>
    </div>
  </div>
</header>
