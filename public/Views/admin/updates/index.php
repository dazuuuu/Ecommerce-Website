<?php
/** Requires $hasUpdate, $version in scope. Individual migrations are intentionally never listed. */
require __DIR__ . '/../layout-header.php';
?>

<div class="max-w-2xl bg-white border border-slate-200 rounded-2xl shadow-sm p-8 space-y-6">
  <div class="flex items-center gap-4">
    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
      <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
    </div>
    <div>
      <h2 class="text-xl font-bold text-slate-900">System Updates</h2>
      <p class="text-sm text-slate-500">Keep your store running on the latest version.</p>
    </div>
  </div>

  <?php if ($hasUpdate): ?>
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
      <p class="text-xs font-bold uppercase tracking-widest text-blue-600">New version available</p>
      <p class="text-3xl font-extrabold text-slate-900 mt-1">Version <?= e($version) ?></p>
    </div>

    <form method="post" action="<?= url('/admin/updates/run') ?>" onsubmit="return confirm('Update to version <?= e($version) ?> now?');">
      <?= csrfField() ?>
      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-6 py-3 rounded-xl shadow-sm cursor-pointer">
        Update to Version <?= e($version) ?>
      </button>
    </form>
  <?php else: ?>
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
      <p class="text-xs font-bold uppercase tracking-widest text-emerald-600">Up to date</p>
      <p class="text-3xl font-extrabold text-slate-900 mt-1">Version <?= e($version) ?></p>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
