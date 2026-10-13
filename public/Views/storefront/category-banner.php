<?php
/** Requires $category (id, name, tagline, image) and $productCount in scope. */
$bannerImage = !empty($category['image']) ? imageUrl($category['image']) : null;
?>
<section class="category-banner" id="category-banner">
  <?php if ($bannerImage): ?>
    <div class="category-banner-media"><img src="<?= e($bannerImage) ?>" alt="<?= e($category['name']) ?>" /></div>
  <?php endif; ?>
  <div class="store-nav-inner category-banner-inner">
    <div>
      <a href="<?= url('/') ?>" class="category-back-link">&larr; Back to all products</a>
      <p class="category-banner-eyebrow"><?= e(storeDisplayName()) ?></p>
      <h1 class="category-banner-title"><?= e($category['name']) ?></h1>
      <?php if (!empty($category['tagline'])): ?>
        <p class="category-banner-tagline"><?= e($category['tagline']) ?></p>
      <?php endif; ?>
      <p class="category-banner-count"><?= (int) $productCount ?> <?= $productCount === 1 ? 'product' : 'products' ?></p>
    </div>
    <button type="button" class="category-share-btn" id="category-share-btn" data-share-title="<?= e($category['name'] . ' | ' . storeDisplayName()) ?>">Share</button>
  </div>
</section>
