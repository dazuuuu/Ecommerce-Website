<?php
/** Requires $onOffer, $notOnOffer in scope. */
require __DIR__ . '/../layout-header.php';
?>

<p class="text-sm text-neutral-500 mb-5">Products marked "On Offer" show a strikethrough original price on the storefront. Set the discounted price and the original price here.</p>

<div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden mb-8">
  <div class="px-5 py-4 border-b border-neutral-200">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Currently On Offer (<?= count($onOffer) ?>)</h2>
  </div>
  <table class="w-full text-left text-xs">
    <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider text-[10px]">
      <tr>
        <th class="px-5 py-3">Product</th>
        <th class="px-5 py-3">Offer Price</th>
        <th class="px-5 py-3">Original Price</th>
        <th class="px-5 py-3">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-neutral-100">
      <?php if (!$onOffer): ?>
        <tr><td colspan="4" class="px-5 py-8 text-center text-neutral-400">No products on offer right now.</td></tr>
      <?php endif; ?>
      <?php foreach ($onOffer as $p):
        $images = json_decode($p['images'] ?? '[]', true) ?: [];
        $formId = 'offer-form-' . (int) $p['id'];
      ?>
        <tr class="hover:bg-neutral-50">
          <td class="px-5 py-3 flex items-center gap-3">
            <?php if ($images): ?><img src="<?= e(imageUrl($images[0])) ?>" class="w-10 h-12 object-cover rounded-md border border-neutral-200" alt="" /><?php endif; ?>
            <div>
              <p class="font-bold text-neutral-900"><?= e($p['name']) ?></p>
              <p class="text-[10px] text-neutral-400 font-mono"><?= e($p['product_code']) ?></p>
            </div>
          </td>
          <td class="px-5 py-3"><input form="<?= $formId ?>" type="number" step="0.01" name="price" value="<?= e($p['price']) ?>" class="w-24 border border-neutral-300 rounded-lg p-1.5 text-xs" /></td>
          <td class="px-5 py-3"><input form="<?= $formId ?>" type="number" step="0.01" name="original_price" value="<?= e($p['original_price']) ?>" class="w-24 border border-neutral-300 rounded-lg p-1.5 text-xs" /></td>
          <td class="px-5 py-3 flex items-center gap-3">
            <form id="<?= $formId ?>" method="post" action="<?= url('/admin/offers/' . (int) $p['id'] . '/update') ?>" class="contents">
              <?= csrfField() ?>
              <button type="submit" class="font-bold text-emerald-800 hover:underline cursor-pointer">Save</button>
            </form>
            <form method="post" action="<?= url('/admin/offers/' . (int) $p['id'] . '/remove') ?>" onsubmit="return confirm('Remove this product from offers?');" class="contents">
              <?= csrfField() ?>
              <button type="submit" class="font-bold text-rose-600 hover:underline cursor-pointer">Remove</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-neutral-200">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Add a Product to Offers</h2>
  </div>
  <table class="w-full text-left text-xs">
    <tbody class="divide-y divide-neutral-100">
      <?php if (!$notOnOffer): ?>
        <tr><td class="px-5 py-8 text-center text-neutral-400">Every product is already on offer.</td></tr>
      <?php endif; ?>
      <?php foreach ($notOnOffer as $p): ?>
        <tr class="hover:bg-neutral-50">
          <td class="px-5 py-3">
            <p class="font-bold text-neutral-900"><?= e($p['name']) ?></p>
            <p class="text-[10px] text-neutral-400 font-mono"><?= e($p['product_code']) ?></p>
          </td>
          <td class="px-5 py-3 text-neutral-500"><?= e(formatPrice((float) $p['price'], 'KSH')) ?></td>
          <td class="px-5 py-3 text-right">
            <form method="post" action="<?= url('/admin/offers/' . (int) $p['id'] . '/add') ?>">
              <?= csrfField() ?>
              <button type="submit" class="font-bold text-emerald-800 hover:underline cursor-pointer">+ Add to Offers</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
