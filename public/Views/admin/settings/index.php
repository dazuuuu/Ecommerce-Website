<?php
/** Requires $settings in scope. */
require __DIR__ . '/../layout-header.php';
$logo = $settings['store_logo'] ?? null;
$storeName = $settings['store_name'] ?? storeDisplayName();
$contactPhone = $settings['contact_phone'] ?? storeContactPhone();
$contactEmail = $settings['contact_email'] ?? storeContactEmail();
$contactLocation = $settings['contact_location'] ?? storeContactLocation();
?>

<div class="max-w-3xl space-y-6">
  <form method="post" action="<?= url('/admin/settings') ?>" enctype="multipart/form-data" class="rounded-xl border border-neutral-300 bg-white p-6 shadow-sm space-y-6">
    <?= csrfField() ?>

    <div class="flex flex-col gap-4 border-b border-neutral-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <p class="text-xs font-black uppercase tracking-widest text-neutral-600">Store Settings</p>
        <h2 class="mt-2 text-2xl font-black text-black">Brand & Contact Details</h2>
        <p class="mt-1 text-sm font-medium text-neutral-700">Manage the logo, website name, and contact details shown on the storefront.</p>
      </div>
      <button type="submit" class="shrink-0 rounded-lg bg-black px-6 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-neutral-900">Save Settings</button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div class="sm:col-span-2">
        <label class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Website Name</label>
        <input type="text" name="store_name" required value="<?= e($storeName) ?>" class="mt-2 w-full rounded-lg border border-neutral-400 bg-white px-4 py-3 text-sm font-semibold text-black focus:border-black focus:outline-none" />
      </div>
      <div>
        <label class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Phone Number</label>
        <input type="text" name="contact_phone" value="<?= e($contactPhone) ?>" class="mt-2 w-full rounded-lg border border-neutral-400 bg-white px-4 py-3 text-sm font-semibold text-black focus:border-black focus:outline-none" />
      </div>
      <div>
        <label class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Email Address</label>
        <input type="email" name="contact_email" value="<?= e($contactEmail) ?>" class="mt-2 w-full rounded-lg border border-neutral-400 bg-white px-4 py-3 text-sm font-semibold text-black focus:border-black focus:outline-none" />
      </div>
      <div class="sm:col-span-2">
        <label class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Location</label>
        <input type="text" name="contact_location" value="<?= e($contactLocation) ?>" class="mt-2 w-full rounded-lg border border-neutral-400 bg-white px-4 py-3 text-sm font-semibold text-black focus:border-black focus:outline-none" />
      </div>
    </div>

    <div class="grid gap-5 sm:grid-cols-[180px_minmax(0,1fr)] sm:items-start">
      <div class="rounded-xl border border-neutral-300 bg-neutral-50 p-4">
        <p class="mb-3 text-[11px] font-black uppercase tracking-widest text-neutral-700">Current Logo</p>
        <div class="flex h-28 items-center justify-center rounded-lg border border-neutral-300 bg-white p-4">
          <?php if ($logo): ?>
            <img src="<?= e(imageUrl($logo)) ?>" alt="Current store logo" class="max-h-full max-w-full object-contain" />
          <?php else: ?>
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-black text-white">
              <?= pentagonLogoSvg('w-7 h-7 text-white') ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="space-y-4">
        <div>
          <label class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Upload Logo</label>
          <div class="mt-2 flex flex-col gap-2 sm:flex-row">
            <input type="file" name="store_logo" accept="image/*" class="block w-full rounded-lg border border-neutral-400 bg-white p-3 text-sm font-semibold text-black" />
          </div>
          <p class="mt-2 text-xs font-medium text-neutral-600">Use PNG, JPG, WEBP, or GIF up to 8MB. A transparent PNG works best on both white and black surfaces.</p>
        </div>

        <?php if ($logo): ?>
          <label class="flex items-center gap-2 text-sm font-bold text-neutral-800">
            <input type="checkbox" name="remove_logo" value="1" class="h-4 w-4 accent-black" />
            Remove current logo and use the default mark
          </label>
        <?php endif; ?>

        <div class="rounded-lg border border-neutral-200 bg-neutral-50 p-4">
          <p class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Where it appears</p>
          <div class="mt-3 grid gap-2 text-sm font-semibold text-black sm:grid-cols-3">
            <span class="rounded-md bg-white px-3 py-2 border border-neutral-200">Store navbar</span>
            <span class="rounded-md bg-white px-3 py-2 border border-neutral-200">Store footer</span>
            <span class="rounded-md bg-white px-3 py-2 border border-neutral-200">Admin pages</span>
          </div>
        </div>
      </div>
    </div>

    <div class="flex items-center gap-3 border-t border-neutral-200 pt-5">
      <button type="submit" class="rounded-lg bg-black px-6 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-neutral-900">Save Settings</button>
      <a href="<?= url('/admin') ?>" class="text-xs font-black text-neutral-700 hover:text-black">Back to Dashboard</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
