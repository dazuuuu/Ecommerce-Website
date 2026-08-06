<?php
/** Mirrors src/components/Footer.tsx. Requires $currency in scope. */
$currencyCodes = ['KSH', 'USD', 'EUR', 'GBP'];
?>
<footer class="bg-[#0a0a0a] text-white border-t border-amber-500/30">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

      <div class="lg:col-span-2 space-y-4">
        <div class="flex items-center space-x-2.5">
          <div class="w-8 h-8 bg-[#0d2a18] text-amber-300 flex items-center justify-center rounded-lg border border-amber-400/40">
            <?= pentagonLogoSvg('w-5 h-5 text-amber-300') ?>
          </div>
          <div class="flex flex-col">
            <span class="font-serif-heading text-xl font-bold tracking-[0.2em] text-white leading-none uppercase">PENTAGON</span>
            <span class="text-[9px] tracking-[0.3em] text-amber-300/80 font-sans uppercase">COLLECTIONS</span>
          </div>
        </div>

        <p class="text-xs text-amber-100/70 font-light leading-relaxed max-w-sm">
          Authentic luxury collections featuring 6-seater &amp; 4-seater dining tables, transparent Sakura umbrellas, Italian wool trenches, silk dresses, and cashmere.
        </p>

        <div class="pt-2 text-xs text-amber-200/80 space-y-1">
          <p class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg> Pentagon Headquarters &amp; Showroom</p>
          <p class="flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-amber-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg> Client Care: concierge@pentagoncollections.com</p>
        </div>
      </div>

      <div class="space-y-3">
        <h4 class="font-serif-heading text-sm font-bold tracking-widest uppercase text-amber-400">COLLECTIONS</h4>
        <ul class="space-y-2 text-xs text-amber-100/80 font-medium">
          <li><button data-select-category="accessories" class="nav-select hover:text-amber-300 transition-colors cursor-pointer">Dining &amp; Furniture</button></li>
          <li><button data-select-category="accessories" class="nav-select hover:text-amber-300 transition-colors cursor-pointer">Transparent Umbrellas</button></li>
          <li><button data-select-category="outerwear" class="nav-select hover:text-amber-300 transition-colors cursor-pointer">Tailored Outerwear</button></li>
          <li><button data-select-category="knitwear" class="nav-select hover:text-amber-300 transition-colors cursor-pointer">Cashmere &amp; Knit</button></li>
        </ul>
      </div>

      <div class="space-y-3">
        <h4 class="font-serif-heading text-sm font-bold tracking-widest uppercase text-amber-400">CLIENT SERVICES</h4>
        <ul class="space-y-2 text-xs text-amber-100/80 font-medium">
          <li><button id="footer-open-size-guide" class="hover:text-amber-300 transition-colors cursor-pointer">Specifications &amp; Sizing</button></li>
          <li><span class="hover:text-amber-300 transition-colors cursor-pointer">Express Delivery</span></li>
          <li><span class="hover:text-amber-300 transition-colors cursor-pointer">30-Day Returns &amp; Exchanges</span></li>
        </ul>
      </div>

      <div class="space-y-3">
        <h4 class="font-serif-heading text-sm font-bold tracking-widest uppercase text-amber-400">CURRENCY SELECTOR</h4>
        <p class="text-xs text-amber-100/70 font-light">Select your active currency for real-time price formatting.</p>
        <div class="flex flex-wrap gap-1.5 pt-1" id="footer-currency-options">
          <?php foreach ($currencyCodes as $c): ?>
            <button data-select-currency="<?= e($c) ?>" class="currency-option-footer px-3 py-1.5 text-xs font-bold rounded-md border transition-colors cursor-pointer <?= $c === $currency ? 'bg-amber-400 text-black border-amber-400' : 'bg-[#0d2a18] text-amber-200 border-amber-500/20 hover:border-amber-400' ?>"><?= e($c) ?></button>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <div class="mt-12 pt-6 border-t border-amber-500/20 flex flex-col sm:flex-row items-center justify-between text-[11px] text-amber-200/60 space-y-4 sm:space-y-0">
      <p>© <?= date('Y') ?> Pentagon Collections. Authentic Luxury. All rights reserved.</p>
      <div class="flex space-x-4 text-amber-300/80 font-mono text-xs">
        <span>M-Pesa</span>
        <span>Visa</span>
        <span>Mastercard</span>
        <span>Apple Pay</span>
      </div>
    </div>
  </div>
</footer>
