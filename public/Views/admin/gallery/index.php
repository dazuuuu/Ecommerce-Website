<?php
/** Requires $items, $errors, $form in scope. */
require __DIR__ . '/../layout-header.php';
$isNewArrival = !empty($form['is_new']);
?>

<form method="post" action="<?= url('/admin/gallery') ?>" enctype="multipart/form-data" class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-5 mb-6 max-w-4xl">
  <?= csrfField() ?>

  <div class="flex items-start justify-between gap-4 border-b border-neutral-100 pb-4">
    <div>
      <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Upload Product Gallery</h2>
      <p class="text-xs text-neutral-500 mt-1">Upload multiple gallery images at once. No price, category, or description is required.</p>
    </div>
  </div>

  <?php foreach ($errors as $err): ?>
    <div class="bg-rose-50 border border-rose-300 text-rose-800 text-sm font-semibold rounded-lg p-3"><?= e($err) ?></div>
  <?php endforeach; ?>

  <div>
    <label class="text-[11px] font-bold text-neutral-600 uppercase">Gallery Images</label>
    <input type="file" name="gallery_images[]" accept="image/*" multiple required class="w-full mt-1 text-sm" />
    <p class="text-[11px] text-neutral-400 mt-1">Each selected image becomes a separate gallery item.</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="text-[11px] font-bold text-neutral-600 uppercase">Title <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
      <input type="text" name="title" value="<?= e($form['title'] ?? '') ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
    </div>
    <div>
      <label class="text-[11px] font-bold text-neutral-600 uppercase">Description <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
      <input type="text" name="description" value="<?= e($form['description'] ?? '') ?>" class="w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
    </div>
  </div>

  <div class="border border-neutral-200 rounded-lg p-4 space-y-3">
    <label class="flex items-center gap-2 text-sm font-bold text-neutral-800">
      <input type="checkbox" name="is_new" value="1" <?= $isNewArrival ? 'checked' : '' ?> class="w-4 h-4 accent-black" data-gallery-new-toggle />
      <span>Mark uploaded images as New Arrival</span>
    </label>
    <div class="<?= $isNewArrival ? '' : 'hidden' ?>" data-gallery-new-fields>
      <label class="text-[11px] font-bold text-neutral-600 uppercase">Show as New Until <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
      <input type="datetime-local" name="new_arrival_until" value="<?= e($form['new_arrival_until'] ?? '') ?>" class="w-full max-w-xs mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black" />
      <p class="text-[11px] text-neutral-400 mt-1">Leave blank to keep them new until you manually turn it off.</p>
    </div>
  </div>

  <button type="submit" class="bg-[#0a0a0a] hover:bg-black text-white text-xs font-bold px-6 py-3 rounded-lg uppercase tracking-widest transition-colors cursor-pointer border border-neutral-300">
    Upload Gallery
  </button>
</form>

<div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Gallery Items</h2>
    <span class="text-xs text-neutral-400 font-bold"><?= count($items) ?> images</span>
  </div>

  <?php if (!$items): ?>
    <div class="p-8 text-center text-sm text-neutral-400">No gallery images yet.</div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-5">
      <?php foreach ($items as $item): ?>
        <div class="border border-neutral-200 rounded-xl overflow-hidden bg-white">
          <img src="<?= e(imageUrl($item['image'])) ?>" alt="<?= e($item['title'] ?? 'Gallery image') ?>" class="w-full aspect-[4/5] object-cover bg-neutral-100" />
          <form method="post" action="<?= url('/admin/gallery/' . (int) $item['id']) ?>" class="p-4 space-y-3">
            <?= csrfField() ?>
            <div class="grid grid-cols-2 gap-3">
              <div class="col-span-2">
                <label class="text-[10px] font-bold text-neutral-500 uppercase">Title</label>
                <input type="text" name="title" value="<?= e($item['title'] ?? '') ?>" class="w-full mt-1 border border-neutral-300 rounded-lg p-2 text-xs focus:outline-none focus:border-black" />
              </div>
              <div class="col-span-2">
                <label class="text-[10px] font-bold text-neutral-500 uppercase">Description</label>
                <input type="text" name="description" value="<?= e($item['description'] ?? '') ?>" class="w-full mt-1 border border-neutral-300 rounded-lg p-2 text-xs focus:outline-none focus:border-black" />
              </div>
              <div>
                <label class="text-[10px] font-bold text-neutral-500 uppercase">Sort</label>
                <input type="number" name="sort_order" value="<?= e($item['sort_order'] ?? 0) ?>" class="w-full mt-1 border border-neutral-300 rounded-lg p-2 text-xs focus:outline-none focus:border-black" />
              </div>
              <div>
                <label class="text-[10px] font-bold text-neutral-500 uppercase">New Until</label>
                <input type="datetime-local" name="new_arrival_until" value="<?= !empty($item['new_arrival_until']) ? e(date('Y-m-d\TH:i', strtotime($item['new_arrival_until']))) : '' ?>" class="w-full mt-1 border border-neutral-300 rounded-lg p-2 text-xs focus:outline-none focus:border-black" />
              </div>
            </div>
            <label class="flex items-center gap-2 text-xs font-bold text-neutral-800">
              <input type="checkbox" name="is_new" value="1" <?= !empty($item['is_new']) ? 'checked' : '' ?> class="w-4 h-4 accent-black" />
              <span>New Arrival</span>
            </label>
            <div class="flex items-center gap-3">
              <button type="submit" class="bg-black text-white text-[11px] font-bold px-4 py-2 rounded-lg uppercase tracking-widest">Save</button>
              <button form="delete-gallery-<?= (int) $item['id'] ?>" type="submit" class="text-[11px] font-bold text-rose-600 hover:underline" onclick="return confirm('Delete this gallery image?');">Delete</button>
            </div>
          </form>
          <form id="delete-gallery-<?= (int) $item['id'] ?>" method="post" action="<?= url('/admin/gallery/' . (int) $item['id'] . '/delete') ?>">
            <?= csrfField() ?>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
  (function () {
    var toggle = document.querySelector('[data-gallery-new-toggle]');
    var fields = document.querySelector('[data-gallery-new-fields]');
    if (toggle && fields) {
      toggle.addEventListener('change', function () {
        fields.classList.toggle('hidden', !toggle.checked);
      });
    }
  })();
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
