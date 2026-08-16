<?php
/** Requires $products, $categories, $occasions, $lookbook, $reviews, $currency, $focus in scope. */
$galleryItems = $galleryItems ?? [];
?>
    <!-- Dynamic modals/drawers (QuickView, Cart, Wishlist, Search, Checkout) are
         injected here by assets/js/app.js, mirroring conditional rendering —
         nothing renders until opened. -->
    <div id="modal-root"></div>
  </div>

  <script id="pentagon-data" type="application/json">
    <?= json_encode([
        'products' => $products,
        'categories' => $categories,
        'occasions' => $occasions ?? [],
        'lookbook' => $lookbook,
        'reviews' => $reviews,
        'galleryItems' => $galleryItems,
        'currency' => $currency,
        'currencies' => availableCurrencies(),
        'baseUrl' => rtrim(\App\Core\PathHandler::appUrl(), '/') ?: url('/'),
        'focusProductCode' => $focus['product'] ?? null,
        'focusCategoryKey' => $focus['category'] ?? null,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>
  </script>
  <script src="<?= asset('assets/js/app.js') ?>"></script>
</body>
</html>
