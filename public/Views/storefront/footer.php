<?php
$storeName = storeDisplayName();
$contactPhone = storeContactPhone();
$contactEmail = storeContactEmail();
$contactLocation = storeContactLocation();
?>
<footer class="store-footer">
  <div class="store-footer-inner">
    <div class="store-footer-grid">
      <div>
        <h4>Products</h4>
        <button data-select-category="all" class="nav-select">Catalog</button>
        <button data-select-category="new" class="nav-select">New Arrivals</button>
        <button id="footer-open-size-guide">Size Guide</button>
        <button id="footer-open-wishlist">Wishlist</button>
      </div>
      <div>
        <h4>The Brand</h4>
        <span>Our Story</span>
        <span>Get in Touch</span>
        <span>FAQ</span>
        <span>Help</span>
      </div>
      <div>
        <h4>Occasions</h4>
        <?php if (!empty($occasions)): ?>
          <?php foreach (array_slice($occasions, 0, 5) as $occasion): ?>
            <button data-select-category="<?= e($occasion['id']) ?>" class="nav-select"><?= e($occasion['label']) ?></button>
          <?php endforeach; ?>
        <?php else: ?>
          <span>Add product occasions in admin</span>
        <?php endif; ?>
      </div>
      <div>
        <h4>Connect</h4>
        <?php if ($contactPhone): ?><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $contactPhone)) ?>"><?= e($contactPhone) ?></a><?php endif; ?>
        <?php if ($contactEmail): ?><a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a><?php endif; ?>
        <?php if ($contactLocation): ?><span><?= e($contactLocation) ?></span><?php endif; ?>
      </div>
    </div>
    <div class="store-footer-bottom">
      <p>© <?= date('Y') ?> <?= e($storeName) ?>. All rights reserved.</p>
    </div>
  </div>
</footer>
