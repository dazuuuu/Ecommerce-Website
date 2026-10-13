<?php
/**
 * Offers section without background colors, featuring a slider if multiple products.
 * Requires $offers, $currency in scope.
 */
$activeOffers = array_values($offers ?? []);
?>
<section aria-label="Featured offers" class="store-products-section" style="padding-bottom: 0;">
  <?php if (!empty($activeOffers)): ?>
    <div class="relative overflow-hidden w-full bg-neutral-50 rounded-2xl p-4 md:p-6" id="offers-slider" data-offers-count="<?= count($activeOffers) ?>">
      <div class="flex transition-transform duration-500 ease-in-out" id="offers-track">
        <?php foreach ($activeOffers as $idx => $offer):
          $image = imageUrl($offer['offerImage'] ?? ($offer['images'][0] ?? ''));
          $original = $offer['originalPrice'] ?? null;
          $discount = $original && $original > $offer['price'] ? max(1, (int) round((1 - ($offer['price'] / $original)) * 100)) : null;
          $offerText = $discount ? $discount . '% OFF' : 'Special Offer';
          
          $waNumber = whatsappNumberDigits();
          $message = "Hello, I am interested in this product on offer:\n";
          $message .= "Product: " . ($offer['name'] ?? '') . "\n";
          $message .= "Offer: " . $offerText . "\n";
          $message .= "Price: " . formatPrice((float) ($offer['price'] ?? 0), $currency) . "\n";
          $message .= "Link: " . productUrl($offer);
          $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode($message) : '#';
        ?>
          <div class="w-full flex-shrink-0 flex flex-col md:flex-row items-center gap-6 md:gap-12" data-offer-slide="<?= $idx ?>">
            <div class="w-full md:w-1/2 relative flex justify-center">
              <?php if ($image): ?>
                <img src="<?= e($image) ?>" alt="<?= e($offer['name']) ?>" class="w-full max-h-[40vh] md:max-h-[400px] object-contain rounded-xl" />
              <?php endif; ?>
              <div class="absolute top-4 right-4 bg-red-600 text-white text-xs md:text-sm font-bold px-2 py-1 md:px-3 md:py-1 rounded-full shadow-lg">
                <?= e($offerText) ?>
              </div>
            </div>
            <div class="w-full md:w-1/2 space-y-4 text-center md:text-left">
              <h1 class="text-3xl font-serif font-bold text-gray-900"><?= e($offer['name']) ?></h1>
              <p class="text-sm md:text-base text-gray-600 leading-relaxed"><?= e($offer['subtitle'] ?: ($offer['description'] ?? 'Unlock exclusive discounts and special offers with our amazing deals.')) ?></p>
              
              <div class="flex items-baseline justify-center md:justify-start gap-3">
                <span class="text-xl md:text-2xl font-semibold text-gray-900" data-price-usd="<?= e((string) (float) $offer['price']) ?>">
                  <?= e(formatPrice((float) $offer['price'], $currency)) ?>
                </span>
                <?php if ($original): ?>
                  <del class="text-base md:text-lg text-gray-400" data-price-usd="<?= e((string) (float) $original) ?>">
                    <?= e(formatPrice((float) $original, $currency)) ?>
                  </del>
                <?php endif; ?>
              </div>

              <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-6 py-3 bg-[#1a1a1a] text-white font-medium rounded-lg hover:bg-black transition-colors">
                Get Started
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (count($activeOffers) > 1): ?>
        <div class="flex justify-center mt-8 gap-2" id="offers-dots">
          <?php foreach ($activeOffers as $idx => $_): ?>
            <button type="button" aria-label="Go to offer <?= $idx + 1 ?>" class="w-3 h-3 rounded-full bg-gray-300 transition-colors duration-200" data-dot-idx="<?= $idx ?>"></button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        const track = document.getElementById('offers-track');
        const dots = document.querySelectorAll('#offers-dots button');
        const count = parseInt(document.getElementById('offers-slider')?.dataset.offersCount || 0, 10);
        
        if (count > 1 && track && dots.length) {
          let currentIdx = 0;
          
          const updateSlider = (idx) => {
            currentIdx = idx;
            track.style.transform = `translateX(-${currentIdx * 100}%)`;
            dots.forEach((dot, i) => {
              if (i === currentIdx) {
                dot.classList.remove('bg-gray-300');
                dot.classList.add('bg-gray-900');
              } else {
                dot.classList.remove('bg-gray-900');
                dot.classList.add('bg-gray-300');
              }
            });
          };

          dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => updateSlider(idx));
          });
          
          updateSlider(0);
          
          // Auto-slide every 5 seconds
          setInterval(() => {
            let nextIdx = (currentIdx + 1) % count;
            updateSlider(nextIdx);
          }, 5000);
        }
      });
    </script>
  <?php endif; ?>
</section>
