<?php
/** Requires $onOffer, $notOnOffer in scope. Spreadsheet-style (client design). */
require __DIR__ . '/../layout-header.php';

$offerCols = ['Product Code', 'Photo', 'Product Name', 'Offer Price (USD)', 'Original Price (USD)', 'Offer Price (KSH)', 'Discount', 'Offer Image', 'Ends At', 'Actions'];
$availCols = ['Product Code', 'Product Name', 'Price (KSH)', 'Qty', 'Action'];
?>

<?php require __DIR__ . '/../partials/sheet-style.php'; ?>

<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-semibold text-slate-900">Offers</h1>
    <p class="text-slate-500 mt-1">Yellow cells are editable — type like in Excel, then press Save on that row. Matured offers are removed automatically.</p>
  </div>
</div>

<!-- Sheet 1: currently on offer -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-8">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
      Currently On Offer
      <span class="text-sm font-medium text-slate-500">(<?= count($onOffer) ?>)</span>
    </h2>
  </div>

  <div class="overflow-auto max-h-[65vh] border-t border-slate-200">
    <table class="xl-sheet w-full">
      <thead>
        <tr class="xl-letters">
          <th class="xl-rownum"></th>
          <?php foreach ($offerCols as $i => $_): ?><th><?= chr(65 + $i) ?></th><?php endforeach; ?>
        </tr>
        <tr class="xl-heads">
          <th class="xl-rownum" style="cursor:default">#</th>
          <?php foreach ($offerCols as $label): ?><th style="cursor:default"><?= e($label) ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!$onOffer): ?>
          <tr><td class="xl-rownum">1</td><td colspan="<?= count($offerCols) ?>" class="text-center text-slate-500">No products on offer right now — add one from the sheet below.</td></tr>
        <?php endif; ?>
        <?php foreach ($onOffer as $n => $p):
          $images = json_decode($p['images'] ?? '[]', true) ?: [];
          $offerImage = $p['offer_image'] ?? null;
          $endsValue = !empty($p['offer_ends_at']) ? date('Y-m-d\TH:i', strtotime($p['offer_ends_at'])) : '';
          $formId = 'offer-form-' . (int) $p['id'];
          $discount = ($p['original_price'] ?? 0) > 0 ? round((1 - $p['price'] / $p['original_price']) * 100) : null;
        ?>
          <tr data-offer-row>
            <td class="xl-rownum"><?= $n + 1 ?></td>
            <td class="font-mono text-blue-600"><?= e($p['product_code']) ?></td>
            <td class="text-center">
              <?php if ($offerImage || $images): ?><img src="<?= e(imageUrl($offerImage ?: $images[0])) ?>" alt="" class="w-8 h-8 object-cover rounded inline-block" /><?php else: ?><span class="text-slate-300">—</span><?php endif; ?>
            </td>
            <td class="font-medium max-w-[240px] truncate" title="<?= e($p['name']) ?>"><?= e($p['name']) ?></td>
            <td class="xl-input xl-editable"><input form="<?= $formId ?>" type="number" step="0.01" name="price" value="<?= e($p['price']) ?>" data-offer-price /></td>
            <td class="xl-input xl-editable"><input form="<?= $formId ?>" type="number" step="0.01" name="original_price" value="<?= e($p['original_price']) ?>" data-offer-original /></td>
            <td class="xl-num text-slate-700" data-offer-ksh><?= number_format(convertPrice((float) $p['price'], 'KSH')) ?></td>
            <td class="xl-num font-semibold text-emerald-700" data-offer-discount><?= $discount !== null ? '-' . $discount . '%' : '' ?></td>
            <td class="xl-editable">
              <input form="<?= $formId ?>" type="file" name="offer_image" accept="image/*" class="block w-44 text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700" />
              <?php if ($offerImage): ?>
                <label class="mt-1 flex items-center gap-1.5 text-[11px] text-slate-600 cursor-pointer">
                  <input form="<?= $formId ?>" type="checkbox" name="remove_offer_image" value="1" class="w-3.5 h-3.5" /> Remove image
                </label>
              <?php endif; ?>
            </td>
            <td class="xl-input xl-editable"><input form="<?= $formId ?>" type="datetime-local" name="offer_ends_at" value="<?= e($endsValue) ?>" /></td>
            <td>
              <div class="flex items-center gap-1">
                <form id="<?= $formId ?>" method="post" enctype="multipart/form-data" action="<?= url('/admin/offers/' . (int) $p['id'] . '/update') ?>" class="m-0">
                  <?= csrfField() ?>
                  <button type="submit" class="px-2.5 py-1 rounded text-xs font-medium bg-blue-600 text-white hover:bg-blue-700">Save</button>
                </form>
                <form method="post" action="<?= url('/admin/offers/' . (int) $p['id'] . '/remove') ?>" onsubmit="return confirm('Remove this product from offers?');" class="m-0">
                  <?= csrfField() ?>
                  <button type="submit" class="px-2 py-1 rounded text-xs font-medium text-rose-700 hover:bg-rose-50">Remove</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Sheet 2: products not on offer -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
      Available Products
      <span class="text-sm font-medium text-slate-500">(<?= count($notOnOffer) ?>)</span>
    </h2>
    <div class="relative">
      <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      <input type="text" id="avail-search" placeholder="Search name or code..." class="border border-slate-300 rounded-md py-1.5 pl-8 pr-3 text-sm w-56" />
    </div>
  </div>

  <div class="overflow-auto max-h-[60vh] border-t border-slate-200">
    <table class="xl-sheet w-full" id="avail-sheet">
      <thead>
        <tr class="xl-letters">
          <th class="xl-rownum"></th>
          <?php foreach ($availCols as $i => $_): ?><th><?= chr(65 + $i) ?></th><?php endforeach; ?>
        </tr>
        <tr class="xl-heads">
          <th class="xl-rownum" style="cursor:default">#</th>
          <?php foreach ($availCols as $label): ?><th style="cursor:default"><?= e($label) ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!$notOnOffer): ?>
          <tr><td class="xl-rownum">1</td><td colspan="<?= count($availCols) ?>" class="text-center text-slate-500">Every product is already on offer.</td></tr>
        <?php endif; ?>
        <?php foreach ($notOnOffer as $n => $p): ?>
          <tr data-search="<?= e(strtolower($p['name'] . ' ' . $p['product_code'])) ?>">
            <td class="xl-rownum"><?= $n + 1 ?></td>
            <td class="font-mono text-blue-600"><?= e($p['product_code']) ?></td>
            <td class="font-medium"><?= e($p['name']) ?></td>
            <td class="xl-num"><?= number_format(convertPrice((float) $p['price'], 'KSH')) ?></td>
            <td class="xl-num"><?= (int) ($p['quantity'] ?? 0) ?></td>
            <td>
              <form method="post" action="<?= url('/admin/offers/' . (int) $p['id'] . '/add') ?>" class="m-0">
                <?= csrfField() ?>
                <button type="submit" class="px-2.5 py-1 rounded text-xs font-medium text-blue-700 border border-blue-200 hover:bg-blue-50">+ Add to Offers</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  (function () {
    var rate = <?= json_encode(availableCurrencies()['KSH']['rate']) ?>;
    // Live "formula" columns: KSH price and discount recalc as you type, like Excel.
    document.querySelectorAll('[data-offer-row]').forEach(function (row) {
      var price = row.querySelector('[data-offer-price]');
      var original = row.querySelector('[data-offer-original]');
      function recalc() {
        var p = parseFloat(price.value) || 0, o = parseFloat(original.value) || 0;
        row.querySelector('[data-offer-ksh]').textContent = Math.round(p * rate).toLocaleString('en-KE');
        row.querySelector('[data-offer-discount]').textContent = o > 0 && p < o ? '-' + Math.round((1 - p / o) * 100) + '%' : '';
      }
      price.addEventListener('input', recalc);
      original.addEventListener('input', recalc);
    });

    var search = document.getElementById('avail-search');
    search.addEventListener('input', function () {
      var term = search.value.trim().toLowerCase(), n = 0;
      document.querySelectorAll('#avail-sheet tbody tr[data-search]').forEach(function (r) {
        var show = r.getAttribute('data-search').indexOf(term) !== -1;
        r.style.display = show ? '' : 'none';
        if (show) r.cells[0].textContent = ++n;
      });
    });
  })();
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
