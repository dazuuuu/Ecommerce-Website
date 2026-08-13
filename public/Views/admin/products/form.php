<?php
/** Requires $product (nullable), $errors, $form, $existingImages, $existingColors, $categories in scope. */
require __DIR__ . '/../layout-header.php';
$actionUrl = $product ? url('/admin/products/' . (int) $product['id']) : url('/admin/products');
?>

<form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" class="max-w-4xl space-y-6" id="product-form">
  <?= csrfField() ?>

  <?php foreach ($errors as $err): ?>
    <div class="bg-rose-50 border border-rose-300 text-rose-800 text-sm font-semibold rounded-lg p-3"><?= e($err) ?></div>
  <?php endforeach; ?>

  <?php if ($product): ?>
    <div class="text-xs text-neutral-400 font-mono">Product code: <?= e($product['product_code']) ?></div>
  <?php endif; ?>

  <!-- Basic details -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a] border-b border-neutral-100 pb-3">Basic Details</h2>
    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Product Name</label>
        <input type="text" name="name" required value="<?= e($form['name']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Subtitle</label>
        <input type="text" name="subtitle" value="<?= e($form['subtitle']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Price (USD base unit)</label>
        <input type="number" step="0.01" min="0.01" name="price" required value="<?= e($form['price']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Original Price <span class="text-neutral-400 font-normal normal-case">(for offers — optional)</span></label>
        <input type="number" step="0.01" min="0" name="original_price" value="<?= e($form['original_price']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Category</label>
        <select name="category_key" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800">
          <?php foreach ($categories as $cat): ?>
            <option value="<?= e($cat['category_key']) ?>" <?= $form['category_key'] === $cat['category_key'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Sub-category label</label>
        <input type="text" name="sub_category" placeholder="e.g. Dining & Furniture" value="<?= e($form['sub_category']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Description</label>
        <textarea name="description" rows="3" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800"><?= e($form['description']) ?></textarea>
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Fabric / Materials</label>
        <input type="text" name="fabric" value="<?= e($form['fabric']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Fit / Specification</label>
        <input type="text" name="fit" value="<?= e($form['fit']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-emerald-800" />
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Sizes <span class="text-neutral-400 font-normal normal-case">(one per line)</span></label>
        <textarea name="sizes" rows="3" placeholder="S&#10;M&#10;L" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm font-mono focus:outline-none focus:border-emerald-800"><?= e($form['sizes']) ?></textarea>
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Detail bullet points <span class="text-neutral-400 font-normal normal-case">(one per line)</span></label>
        <textarea name="details" rows="3" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm font-mono focus:outline-none focus:border-emerald-800"><?= e($form['details']) ?></textarea>
      </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
      <?php
      $flags = [
          'is_new' => 'New',
          'is_best_seller' => 'Best Seller',
          'is_sale' => 'On Offer',
          'in_stock' => 'In Stock',
          'featured_in_lookbook' => 'In Lookbook',
      ];
      foreach ($flags as $key => $label): ?>
        <label class="flex items-center gap-2 text-xs font-semibold text-neutral-700 cursor-pointer">
          <input type="checkbox" name="<?= $key ?>" <?= $form[$key] ? 'checked' : '' ?> class="w-4 h-4 accent-emerald-800" />
          <?= e($label) ?>
        </label>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Photos -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a] border-b border-neutral-100 pb-3">Product Photos</h2>
    <?php if ($existingImages): ?>
      <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
        <?php foreach ($existingImages as $img): ?>
          <label class="relative block">
            <img src="<?= e(imageUrl($img)) ?>" alt="" class="w-full aspect-square object-cover rounded-lg border border-neutral-200" />
            <span class="absolute top-1 right-1 bg-white/90 rounded p-1 shadow">
              <input type="checkbox" name="images_remove[]" value="<?= e($img) ?>" class="w-3.5 h-3.5 accent-rose-600" title="Remove this photo" />
            </span>
          </label>
        <?php endforeach; ?>
      </div>
      <p class="text-[11px] text-neutral-400">Tick a photo's checkbox to remove it when you save.</p>
    <?php endif; ?>
    <div>
      <label class="text-[11px] font-bold text-neutral-600 uppercase">Upload photos from your device</label>
      <input type="file" name="images[]" accept="image/*" multiple class="w-full mt-1 text-sm" />
      <p class="text-[11px] text-neutral-400 mt-1">The first photo (existing + new, in order) is used as the primary thumbnail. JPG, PNG, WEBP or GIF, up to 8MB each.</p>
    </div>
  </div>

  <!-- Colors -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
      <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Color Options</h2>
      <button type="button" id="add-color-row" class="text-xs font-bold text-emerald-800 hover:underline cursor-pointer">+ Add Color</button>
    </div>
    <p class="text-[11px] text-neutral-400 -mt-2">Each color shows as a swatch bubble on the storefront. Attaching a photo per color is optional — if skipped, the general product photos above are used.</p>

    <div id="colors-repeater" class="space-y-3">
      <?php foreach ($existingColors as $i => $c): ?>
        <div class="color-row flex items-start gap-3 bg-neutral-50 border border-neutral-200 rounded-lg p-3">
          <input type="color" name="colors[<?= $i ?>][hex]" value="<?= e($c['hex'] ?: '#0f2e1b') ?>" class="w-10 h-10 rounded border border-neutral-300 cursor-pointer shrink-0" title="Swatch color" />
          <div class="flex-1 grid grid-cols-2 gap-2">
            <input type="text" name="colors[<?= $i ?>][name]" value="<?= e($c['name'] ?? '') ?>" placeholder="Color name e.g. Emerald Green" class="bg-white border border-neutral-300 rounded-lg p-2 text-sm focus:outline-none focus:border-emerald-800" />
            <div class="flex items-center gap-2">
              <input type="file" name="colors[<?= $i ?>][image_file]" accept="image/*" class="text-xs flex-1" />
              <?php if (!empty($c['image'])): ?>
                <img src="<?= e(imageUrl($c['image'])) ?>" alt="" class="w-9 h-9 object-cover rounded border border-neutral-200 shrink-0" />
                <input type="hidden" name="colors[<?= $i ?>][existing_image]" value="<?= e($c['image']) ?>" />
                <label class="text-[10px] text-rose-600 font-semibold flex items-center gap-1 whitespace-nowrap"><input type="checkbox" name="colors[<?= $i ?>][remove_image]" value="1" class="w-3 h-3 accent-rose-600" />Remove</label>
              <?php endif; ?>
            </div>
          </div>
          <button type="button" class="remove-color-row text-neutral-400 hover:text-rose-600 shrink-0 p-1" title="Remove this color">&times;</button>
        </div>
      <?php endforeach; ?>
    </div>

    <template id="color-row-template">
      <div class="color-row flex items-start gap-3 bg-neutral-50 border border-neutral-200 rounded-lg p-3">
        <input type="color" name="colors[__INDEX__][hex]" value="#0f2e1b" class="w-10 h-10 rounded border border-neutral-300 cursor-pointer shrink-0" title="Swatch color" />
        <div class="flex-1 grid grid-cols-2 gap-2">
          <input type="text" name="colors[__INDEX__][name]" placeholder="Color name e.g. Emerald Green" class="bg-white border border-neutral-300 rounded-lg p-2 text-sm focus:outline-none focus:border-emerald-800" />
          <input type="file" name="colors[__INDEX__][image_file]" accept="image/*" class="text-xs" />
        </div>
        <button type="button" class="remove-color-row text-neutral-400 hover:text-rose-600 shrink-0 p-1" title="Remove this color">&times;</button>
      </div>
    </template>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-[#0a0a0a] hover:bg-[#0d2a18] text-amber-300 text-xs font-bold px-6 py-3 rounded-lg uppercase tracking-widest transition-colors cursor-pointer border border-amber-400/30">
      <?= $product ? 'Save Changes' : 'Create Product' ?>
    </button>
    <a href="<?= url('/admin/products') ?>" class="text-xs font-bold text-neutral-500 hover:text-neutral-800">Cancel</a>
  </div>
</form>

<script>
(function () {
  var repeater = document.getElementById('colors-repeater');
  var template = document.getElementById('color-row-template');
  var nextIndex = repeater.querySelectorAll('.color-row').length;

  document.getElementById('add-color-row').addEventListener('click', function () {
    var html = template.innerHTML.replace(/__INDEX__/g, nextIndex);
    var wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();
    repeater.appendChild(wrapper.firstChild);
    nextIndex++;
  });

  repeater.addEventListener('click', function (e) {
    var btn = e.target.closest('.remove-color-row');
    if (btn) {
      var rows = repeater.querySelectorAll('.color-row');
      if (rows.length > 1) {
        btn.closest('.color-row').remove();
      } else {
        alert('A product needs at least one color option.');
      }
    }
  });
})();
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
