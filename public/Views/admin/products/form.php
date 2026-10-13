<?php
/** Requires $product (nullable), $errors, $form, $existingImages, $categories in scope. */
require __DIR__ . '/../layout-header.php';
$actionUrl = $product ? url('/admin/products/' . (int) $product['id']) : url('/admin/products');
$selectedCategories = $form['category_keys'] ?? [];
$colors = is_array($form['colors'] ?? null) ? $form['colors'] : [];
$hasOffer = !empty($form['has_offer']);
$isNewArrival = !empty($form['is_new']);
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
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a] border-b border-neutral-100 pb-3">Product Details</h2>

    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Product Name</label>
        <input type="text" name="name" required value="<?= e($form['name']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
      </div>
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Description <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
        <textarea name="description" rows="3" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black"><?= e($form['description']) ?></textarea>
      </div>
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">More Details <span class="text-neutral-400 font-normal normal-case">(optional, one per line)</span></label>
        <textarea name="details" rows="3" placeholder="Care instructions&#10;Included accessories&#10;Warranty or delivery notes" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm font-mono focus:outline-none focus:border-black"><?= e($form['details'] ?? '') ?></textarea>
      </div>
      <!-- Advanced Category Search & Multi-select -->
      <div class="col-span-2 relative" id="category-search-widget">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Categories (Search & Auto-Suggest)</label>
        
        <!-- Selected Category Badges -->
        <div id="category-badge-container" class="flex flex-wrap gap-2 my-2 min-h-[42px] p-2 bg-neutral-50 border border-neutral-200 rounded-xl items-center">
          <!-- Populated by JS -->
        </div>

        <!-- Hidden Inputs for Form Submission -->
        <div id="category-hidden-inputs"></div>

        <!-- Search Input -->
        <div class="relative">
          <input type="text" id="category-search-input" placeholder="Type category name to search & add..." class="w-full bg-white border border-neutral-300 rounded-xl p-2.5 pl-9 text-sm focus:outline-none focus:border-black transition-all" autocomplete="off" />
          <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <!-- Auto-Suggest Dropdown List -->
        <div id="category-suggest-dropdown" class="absolute z-30 left-0 right-0 mt-1 bg-white border border-neutral-200 rounded-xl shadow-xl max-h-56 overflow-y-auto hidden divide-y divide-neutral-100">
        </div>

        <p class="text-[11px] text-neutral-400 mt-1.5">Type to filter and select multiple categories. The first selected category will be primary.</p>
      </div>

      <!-- Price & Quantity -->
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Base Price (KSH / Currency)</label>
        <input type="number" step="0.01" min="0.01" name="base_price" required value="<?= e($form['base_price'] ?? $form['price']) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-xl p-2.5 text-sm focus:outline-none focus:border-black" />
      </div>

      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Stock Quantity</label>
        <input type="number" step="1" min="0" name="quantity" required value="<?= e($form['quantity'] ?? 10) ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-xl p-2.5 text-sm font-bold focus:outline-none focus:border-black text-gray-900" placeholder="10" />
        <p class="text-[11px] text-neutral-400 mt-1">Total items in stock. When quantity reaches 0, it automatically displays 'Out of Stock'.</p>
      </div>

      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Sizes <span class="text-neutral-400 font-normal normal-case">(optional, one per line)</span></label>
        <textarea name="sizes" rows="3" placeholder="S&#10;M&#10;L" class="w-full mt-1 bg-white border border-neutral-300 rounded-xl p-2.5 text-sm font-mono focus:outline-none focus:border-black"><?= e($form['sizes']) ?></textarea>
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Materials <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
        <input type="text" name="fabric" value="<?= e($form['fabric'] ?? '') ?>" placeholder="Cotton, leather, stainless steel..." class="w-full mt-1 bg-white border border-neutral-300 rounded-xl p-2.5 text-sm focus:outline-none focus:border-black" />
      </div>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Specification <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
        <input type="text" name="fit" value="<?= e($form['fit'] ?? '') ?>" placeholder="Fit, dimensions, capacity, finish..." class="w-full mt-1 bg-white border border-neutral-300 rounded-xl p-2.5 text-sm focus:outline-none focus:border-black" />
      </div>
    </div>
  </div>

  <!-- Storefront status -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a] border-b border-neutral-100 pb-3">Storefront Status</h2>
    <label class="flex items-center gap-2 text-sm font-bold text-neutral-800">
      <input type="checkbox" name="is_new" value="1" <?= $isNewArrival ? 'checked' : '' ?> class="w-4 h-4 accent-black" data-new-arrival-toggle />
      <span>Show as New Arrival</span>
    </label>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 <?= $isNewArrival ? '' : 'hidden' ?>" data-new-arrival-fields>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Show as New Until <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
        <input type="datetime-local" name="new_arrival_until" value="<?= e($form['new_arrival_until'] ?? '') ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
        <p class="text-[11px] text-neutral-400 mt-1">Leave blank to keep it new until you manually turn it off.</p>
      </div>
    </div>
  </div>

  <!-- Offer -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <label class="flex items-center gap-2 text-sm font-bold text-neutral-800">
      <input type="checkbox" name="has_offer" value="1" <?= $hasOffer ? 'checked' : '' ?> class="w-4 h-4 accent-black" data-offer-toggle />
      <span>Has an offer <span class="text-neutral-400 font-normal">(optional)</span></span>
    </label>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 <?= $hasOffer ? '' : 'hidden' ?>" data-offer-fields>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Offer Type</label>
        <select name="offer_type" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black">
          <option value="price" <?= ($form['offer_type'] ?? 'price') === 'price' ? 'selected' : '' ?>>Offer price</option>
          <option value="percentage" <?= ($form['offer_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage off</option>
        </select>
      </div>
      <div class="col-span-2">
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Offer Price / Percentage</label>
        <input type="number" step="0.01" min="0" name="offer_value" value="<?= e($form['offer_value'] ?? '') ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
      </div>
    </div>
  </div>

  <!-- Photos -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a] border-b border-neutral-100 pb-3">Product Photos</h2>
    <?php if ($existingImages): ?>
      <div>
        <label class="text-[11px] font-bold text-neutral-600 uppercase">Current Cover</label>
        <img src="<?= e(imageUrl($existingImages[0])) ?>" alt="" class="mt-2 w-32 aspect-square object-cover rounded-lg border border-neutral-200" />
      </div>
      <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
        <?php foreach ($existingImages as $idx => $img): ?>
          <label class="relative block">
            <img src="<?= e(imageUrl($img)) ?>" alt="" class="w-full aspect-square object-cover rounded-lg border border-neutral-200" />
            <?php if ($idx === 0): ?>
              <span class="absolute bottom-1 left-1 bg-black/80 text-white text-[9px] font-bold rounded px-1.5 py-0.5 uppercase">Cover</span>
            <?php endif; ?>
            <span class="absolute top-1 right-1 bg-white/90 rounded p-1 shadow">
              <input type="checkbox" name="images_remove[]" value="<?= e($img) ?>" class="w-3.5 h-3.5 accent-rose-600" title="Remove this photo" />
            </span>
          </label>
        <?php endforeach; ?>
      </div>
      <p class="text-[11px] text-neutral-400">Tick a photo's checkbox to remove it when you save.</p>
    <?php endif; ?>
    <div>
      <label class="text-[11px] font-bold text-neutral-600 uppercase">Cover Image</label>
      <input type="file" name="cover_image" accept="image/*" <?= $existingImages ? '' : 'required' ?> class="w-full mt-1 text-sm" />
      <p class="text-[11px] text-neutral-400 mt-1">This is the main thumbnail shown on the frontpage. JPG, PNG, WEBP or GIF, up to 8MB.</p>
    </div>
    <div>
      <label class="text-[11px] font-bold text-neutral-600 uppercase">Product Gallery / Views <span class="text-neutral-400 font-normal normal-case">(optional, upload more than one)</span></label>
      <input type="file" name="gallery_images[]" accept="image/*" multiple class="w-full mt-1 text-sm" />
      <p class="text-[11px] text-neutral-400 mt-1">Add alternate angles or close-up views. These appear in the product details gallery.</p>
    </div>
  </div>

  <!-- Colors -->
  <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-4">
    <div class="flex items-center justify-between gap-3 border-b border-neutral-100 pb-3">
      <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Colors <span class="text-neutral-400 font-normal text-sm">(optional)</span></h2>
      <button type="button" data-add-color class="bg-white hover:bg-neutral-50 border border-neutral-300 text-neutral-900 text-xs font-bold px-3 py-2 rounded-lg uppercase tracking-widest">Add Color</button>
    </div>
    <div class="space-y-3" data-color-list>
      <?php foreach ($colors as $color): ?>
        <div class="grid grid-cols-[auto_1fr_auto] gap-3 items-center" data-color-row>
          <input type="color" name="color_hexes[]" value="<?= e($color['hex'] ?? '#000000') ?>" class="w-11 h-10 p-1 bg-white border border-neutral-300 rounded-lg" />
          <input type="text" name="color_names[]" value="<?= e($color['name'] ?? '') ?>" placeholder="Color name" class="w-full bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
          <button type="button" data-remove-color class="text-xs font-bold text-rose-600 hover:text-rose-700">Remove</button>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="text-[11px] text-neutral-400">Add rows only when the product comes in multiple colors.</p>
  </div>

  <div class="flex items-center gap-3">
    <button type="submit" class="bg-[#0a0a0a] hover:bg-black text-white text-xs font-bold px-6 py-3 rounded-lg uppercase tracking-widest transition-colors cursor-pointer border border-neutral-300">
      <?= $product ? 'Save Changes' : 'Create Product' ?>
    </button>
    <a href="<?= url('/admin/products') ?>" class="text-xs font-bold text-neutral-500 hover:text-neutral-800">Cancel</a>
  </div>
</form>

<script>
  (function () {
    var offerToggle = document.querySelector('[data-offer-toggle]');
    var offerFields = document.querySelector('[data-offer-fields]');
    var newArrivalToggle = document.querySelector('[data-new-arrival-toggle]');
    var newArrivalFields = document.querySelector('[data-new-arrival-fields]');
    if (offerToggle && offerFields) {
      offerToggle.addEventListener('change', function () {
        offerFields.classList.toggle('hidden', !offerToggle.checked);
      });
    }
    if (newArrivalToggle && newArrivalFields) {
      newArrivalToggle.addEventListener('change', function () {
        newArrivalFields.classList.toggle('hidden', !newArrivalToggle.checked);
      });
    }

    var allCategories = <?= json_encode(array_map(fn($c) => ['key' => $c['category_key'], 'name' => $c['name']], $categories), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    var selectedKeys = <?= json_encode(array_values($selectedCategories), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

    var badgeContainer = document.getElementById('category-badge-container');
    var hiddenInputs = document.getElementById('category-hidden-inputs');
    var searchInput = document.getElementById('category-search-input');
    var suggestDropdown = document.getElementById('category-suggest-dropdown');

    function categoryName(key) {
      var found = allCategories.find(function (c) { return c.key === key; });
      return found ? found.name : key;
    }

    function renderCategories() {
      badgeContainer.innerHTML = '';
      hiddenInputs.innerHTML = '';
      if (!selectedKeys.length) {
        var empty = document.createElement('span');
        empty.className = 'text-xs text-neutral-400';
        empty.textContent = 'No categories selected yet.';
        badgeContainer.appendChild(empty);
      }
      selectedKeys.forEach(function (key, idx) {
        var badge = document.createElement('span');
        badge.className = 'inline-flex items-center gap-1.5 bg-black text-white text-xs font-bold pl-3 pr-2 py-1.5 rounded-full';
        badge.innerHTML = (idx === 0 ? '<span class="text-[9px] uppercase tracking-wide text-neutral-300 mr-1">Primary</span>' : '') +
          '<span>' + categoryName(key).replace(/</g, '&lt;') + '</span>';
        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'ml-1 text-neutral-300 hover:text-white';
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', function () {
          selectedKeys.splice(idx, 1);
          renderCategories();
        });
        badge.appendChild(removeBtn);
        badgeContainer.appendChild(badge);

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'category_keys[]';
        hidden.value = key;
        hiddenInputs.appendChild(hidden);
      });
    }

    function renderSuggestions(term) {
      var matches = allCategories.filter(function (c) {
        return selectedKeys.indexOf(c.key) === -1 && c.name.toLowerCase().indexOf(term.toLowerCase()) !== -1;
      });
      suggestDropdown.innerHTML = '';
      if (!term || !matches.length) {
        suggestDropdown.classList.add('hidden');
        return;
      }
      matches.slice(0, 8).forEach(function (c) {
        var item = document.createElement('button');
        item.type = 'button';
        item.className = 'w-full text-left px-3 py-2 text-sm hover:bg-neutral-50 flex items-center justify-between';
        item.innerHTML = '<span>' + c.name.replace(/</g, '&lt;') + '</span>';
        item.addEventListener('click', function () {
          selectedKeys.push(c.key);
          renderCategories();
          searchInput.value = '';
          suggestDropdown.classList.add('hidden');
          searchInput.focus();
        });
        suggestDropdown.appendChild(item);
      });
      suggestDropdown.classList.remove('hidden');
    }

    if (searchInput) {
      searchInput.addEventListener('input', function () {
        renderSuggestions(searchInput.value.trim());
      });
      searchInput.addEventListener('focus', function () {
        if (searchInput.value.trim()) renderSuggestions(searchInput.value.trim());
      });
      searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          var first = suggestDropdown.querySelector('button');
          if (first) first.click();
        }
      });
      document.addEventListener('click', function (e) {
        if (!document.getElementById('category-search-widget').contains(e.target)) {
          suggestDropdown.classList.add('hidden');
        }
      });
    }

    renderCategories();

    var list = document.querySelector('[data-color-list]');
    var add = document.querySelector('[data-add-color]');
    function bindRemove(row) {
      var button = row.querySelector('[data-remove-color]');
      if (button) {
        button.addEventListener('click', function () {
          row.remove();
        });
      }
    }
    if (list) {
      list.querySelectorAll('[data-color-row]').forEach(bindRemove);
    }
    if (add && list) {
      add.addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'grid grid-cols-[auto_1fr_auto] gap-3 items-center';
        row.setAttribute('data-color-row', '');
        row.innerHTML = '<input type="color" name="color_hexes[]" value="#000000" class="w-11 h-10 p-1 bg-white border border-neutral-300 rounded-lg" />' +
          '<input type="text" name="color_names[]" placeholder="Color name" class="w-full bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />' +
          '<button type="button" data-remove-color class="text-xs font-bold text-rose-600 hover:text-rose-700">Remove</button>';
        list.appendChild(row);
        bindRemove(row);
      });
    }
  })();
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
