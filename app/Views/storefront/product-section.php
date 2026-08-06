<?php
/**
 * Mirrors the product catalog section of src/App.tsx (category filter, sort,
 * and product grid). Initial render = category 'all', sort 'featured'.
 * assets/js/app.js re-renders #product-grid + #products-heading on filter/sort/currency changes.
 * Requires $products, $currency in scope.
 */
require_once __DIR__ . '/partials/product-card.php';
?>
<section id="products-section" class="py-10 px-3 sm:px-6 lg:px-8 max-w-7xl mx-auto">
  <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 mb-6 border-b border-neutral-200 gap-4">
    <div>
      <span class="text-xs font-bold text-amber-700 uppercase tracking-widest flex items-center gap-1.5 mb-1">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
        Pentagon Collections
      </span>
      <h2 id="products-heading" class="font-serif-heading text-2xl sm:text-3xl font-bold tracking-tight text-[#0a0a0a] uppercase">All Luxury Goods</h2>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <div class="hidden sm:flex items-center space-x-1.5 bg-[#0d2a18] p-1 rounded-lg text-xs font-medium uppercase tracking-wider text-white" id="category-pills">
        <button data-select-category="all" class="cat-pill px-3 py-1.5 rounded-md transition-colors cursor-pointer bg-amber-400 text-black font-bold" data-cat="all">All</button>
        <button data-select-category="accessories" class="cat-pill px-3 py-1.5 rounded-md transition-colors cursor-pointer text-amber-100/80 hover:text-white" data-cat="accessories">Furniture &amp; Dining</button>
        <button data-select-category="outerwear" class="cat-pill px-3 py-1.5 rounded-md transition-colors cursor-pointer text-amber-100/80 hover:text-white" data-cat="outerwear">Outerwear</button>
        <button data-select-category="dresses" class="cat-pill px-3 py-1.5 rounded-md transition-colors cursor-pointer text-amber-100/80 hover:text-white" data-cat="dresses">Dresses</button>
      </div>

      <div class="flex items-center space-x-2 bg-white border border-neutral-300 px-3 py-2 rounded-lg text-xs shadow-xs">
        <svg class="w-3.5 h-3.5 text-neutral-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>
        <span class="font-semibold text-neutral-500 hidden sm:inline uppercase tracking-wider">Sort:</span>
        <select id="sort-select" class="bg-transparent font-bold text-neutral-900 focus:outline-none cursor-pointer uppercase tracking-wider">
          <option value="featured">Featured First</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="rating">Highest Rated</option>
        </select>
      </div>
    </div>
  </div>

  <div id="product-grid" class="grid grid-cols-2 gap-3 sm:gap-6 lg:gap-8">
    <?php foreach ($products as $product): ?>
      <?= renderProductCard($product, $currency, false) ?>
    <?php endforeach; ?>
  </div>
</section>
