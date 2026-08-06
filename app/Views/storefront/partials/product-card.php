<?php
/**
 * Mirrors src/components/ProductCard.tsx
 * Hover zoom / image swap / wishlist toggle / add-to-cart / buy-now handled
 * client-side in assets/js/app.js (delegated listeners keyed off data-* attrs).
 */

if (!function_exists('renderProductCard')) {
function renderProductCard(array $product, string $currency, bool $isWishlisted): string
{
    $formattedPrice = formatPrice($product['price'], $currency);
    $formattedOriginal = isset($product['originalPrice']) ? formatPrice($product['originalPrice'], $currency) : null;
    $img0 = e(imageUrl($product['images'][0]));
    $img1 = e(imageUrl($product['images'][1] ?? $product['images'][0]));
    $wishClasses = $isWishlisted ? 'text-rose-600' : 'text-neutral-400 hover:text-neutral-700';
    $heartClasses = $isWishlisted ? 'fill-rose-600 text-rose-600' : '';

    ob_start();
    ?>
    <div class="product-card group relative bg-white border border-[#e2e8f0] rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between p-2.5 sm:p-3.5" data-product-id="<?= e($product['id']) ?>">
      <div>
        <div class="product-card-image-wrap relative w-full aspect-[4/3] bg-neutral-100 rounded-lg overflow-hidden cursor-crosshair group select-none" data-quickview-trigger data-product-id="<?= e($product['id']) ?>">
          <img src="<?= $img0 ?>" data-primary="<?= $img0 ?>" data-secondary="<?= $img1 ?>" alt="<?= e($product['name']) ?>" class="product-card-img w-full h-full object-cover object-center transition-transform duration-200 ease-out pointer-events-none scale-100" />

          <div class="absolute top-2 left-2 flex flex-col gap-1 z-10 pointer-events-none">
            <?php if (!empty($product['isNew'])): ?>
              <span class="bg-[#0f2e1b] text-amber-300 text-[9px] font-bold tracking-widest uppercase px-2 py-0.5 rounded shadow-sm border border-amber-400/30">NEW</span>
            <?php endif; ?>
            <?php if (!empty($product['isBestSeller'])): ?>
              <span class="bg-amber-600 text-white text-[9px] font-bold tracking-widest uppercase px-2 py-0.5 rounded shadow-sm">BEST SELLER</span>
            <?php endif; ?>
          </div>

          <div class="absolute bottom-2 right-2 flex items-center gap-1.5 z-10">
            <span class="product-card-zoom-label hidden bg-black/75 text-amber-300 text-[9px] font-bold uppercase tracking-wider px-2 py-1 rounded-md shadow-md backdrop-blur-xs animate-fade-in pointer-events-none">Inspect Texture</span>
            <button data-quickview-trigger data-product-id="<?= e($product['id']) ?>" class="p-2 rounded-full bg-black/80 text-amber-300 hover:bg-black hover:scale-110 transition-all opacity-0 group-hover:opacity-100 cursor-pointer shadow-md" title="Quick View Details">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="pt-3 pb-2 flex flex-col">
          <h3 data-quickview-trigger data-product-id="<?= e($product['id']) ?>" class="font-medium text-sm sm:text-base text-neutral-900 hover:text-[#0f2e1b] transition-colors cursor-pointer leading-snug line-clamp-2 min-h-[2.5rem]"><?= e($product['name']) ?></h3>

          <div class="py-1.5 flex items-center">
            <button data-wishlist-toggle data-product-id="<?= e($product['id']) ?>" class="wishlist-btn p-1 -ml-1 transition-colors cursor-pointer rounded-full <?= $wishClasses ?>" aria-label="Wishlist">
              <svg class="w-5 h-5 <?= $heartClasses ?>" viewBox="0 0 24 24" fill="<?= $isWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </button>
          </div>

          <div class="flex items-baseline gap-2 mb-2">
            <span class="text-base sm:text-xl font-bold text-[#8b1c1c] tracking-tight"><?= e($formattedPrice) ?></span>
            <?php if ($formattedOriginal): ?>
              <span class="text-xs text-neutral-400 line-through font-normal"><?= e($formattedOriginal) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="flex flex-col gap-2 pt-1 mt-auto">
        <button data-buy-now data-product-id="<?= e($product['id']) ?>" class="w-full bg-[#0a0a0a] hover:bg-[#0f2e1b] active:bg-[#143d23] text-white font-bold py-2.5 px-3 rounded-lg sm:rounded-xl flex items-center justify-center gap-2 text-xs sm:text-sm tracking-wider uppercase transition-all duration-200 cursor-pointer shadow-md hover:shadow-lg border border-[#0a0a0a]">
          <svg class="w-4 h-4 text-amber-300 fill-amber-300" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/></svg>
          <span>BUY NOW</span>
        </button>
        <button data-quick-add data-product-id="<?= e($product['id']) ?>" class="w-full bg-white hover:bg-emerald-50/50 active:bg-neutral-100 text-[#0a0a0a] font-bold py-2.5 px-3 rounded-lg sm:rounded-xl flex items-center justify-center gap-2 text-xs sm:text-sm tracking-wider uppercase transition-all duration-200 cursor-pointer border-2 border-[#0a0a0a]">
          <svg class="w-4 h-4 text-[#0a0a0a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          <span>ADD TO CART</span>
        </button>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
}
