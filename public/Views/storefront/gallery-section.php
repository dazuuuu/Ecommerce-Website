<?php
/** Requires $galleryItems in scope. */
if (empty($galleryItems)) {
    return;
}
?>
<section id="gallery-section" class="store-products-section">
  <div class="store-products-heading-row">
    <div>
      <h2 class="store-products-heading">Gallery</h2>
      <p class="store-products-count"><?= count($galleryItems) ?> looks uploaded</p>
    </div>
  </div>

  <div class="store-product-grid">
    <?php foreach ($galleryItems as $item): ?>
      <article class="product-card group">
        <div>
          <div class="product-card-image-wrap">
            <img src="<?= e(imageUrl($item['image'])) ?>" alt="<?= e($item['title'] ?? 'Gallery image') ?>" class="product-card-img w-full h-full object-cover object-center transition-transform duration-200 ease-out pointer-events-none scale-100" />
            <?php if (!empty($item['is_new'])): ?>
              <div class="product-card-badges">
                <span>NEW</span>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!empty($item['title']) || !empty($item['description'])): ?>
            <div class="product-card-body">
              <?php if (!empty($item['title'])): ?>
                <div class="product-card-title-row">
                  <h3><?= e($item['title']) ?></h3>
                </div>
              <?php endif; ?>
              <?php if (!empty($item['description'])): ?>
                <p class="text-xs text-neutral-500 leading-relaxed mt-1"><?= e($item['description']) ?></p>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
