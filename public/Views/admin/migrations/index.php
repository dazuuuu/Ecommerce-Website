<?php
/** Requires $pendingMigrations and $appliedMigrations in scope. */
require __DIR__ . '/../layout-header.php';
$pendingCount = count($pendingMigrations);
$appliedMigrations = $appliedMigrations ?? [];
?>

<div class="max-w-3xl space-y-6">
  <div class="rounded-xl border border-neutral-300 bg-white p-6 shadow-sm space-y-6">
    <div class="flex flex-col gap-4 border-b border-neutral-200 pb-5 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <p class="text-xs font-black uppercase tracking-widest text-neutral-600">System Settings</p>
        <h2 class="mt-2 text-2xl font-black text-black">Database Migrations</h2>
        <p class="mt-1 text-sm font-medium text-neutral-700">
          Schema updates ship as PHP files. After a new deploy, run them here with one click instead of importing SQL.
        </p>
      </div>
      <?php if ($pendingCount): ?>
        <form method="post" action="<?= url('/admin/migrations/run') ?>" class="shrink-0" onsubmit="return confirm('Run <?= (int) $pendingCount ?> pending migration(s) now?');">
          <?= csrfField() ?>
          <button type="submit" class="rounded-lg bg-black px-6 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-neutral-900">
            Migrate
          </button>
        </form>
      <?php endif; ?>
    </div>

    <?php if ($pendingCount): ?>
      <div class="rounded-xl border border-neutral-300 bg-neutral-50 p-4">
        <div class="mb-3 flex items-center justify-between gap-3">
          <p class="text-[11px] font-black uppercase tracking-widest text-neutral-700">Pending migrations</p>
          <span class="rounded-full bg-black px-2 py-0.5 text-[10px] font-black uppercase tracking-widest text-white"><?= (int) $pendingCount ?> ready</span>
        </div>
        <ul class="space-y-2">
          <?php foreach ($pendingMigrations as $migration): ?>
            <li class="rounded-lg border border-neutral-200 bg-white px-3 py-2 font-mono text-sm font-semibold text-black"><?= e($migration) ?></li>
          <?php endforeach; ?>
        </ul>
        <p class="mt-3 text-xs font-medium text-neutral-600">This button runs each pending PHP migration once, then hides until the next update is pushed.</p>
      </div>
    <?php else: ?>
      <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-4">
        <p class="text-sm font-bold text-black">Everything is up to date.</p>
        <p class="mt-1 text-sm font-medium text-neutral-700">When a new update includes PHP migrations, this page and the admin header will show a one-click Migrate button.</p>
      </div>
    <?php endif; ?>

    <div class="flex items-center gap-3 border-t border-neutral-200 pt-5">
      <?php if ($pendingCount): ?>
        <form method="post" action="<?= url('/admin/migrations/run') ?>" onsubmit="return confirm('Run <?= (int) $pendingCount ?> pending migration(s) now?');">
          <?= csrfField() ?>
          <button type="submit" class="rounded-lg bg-black px-6 py-3 text-xs font-black uppercase tracking-widest text-white hover:bg-neutral-900">Migrate</button>
        </form>
      <?php endif; ?>
      <a href="<?= url('/admin/settings') ?>" class="text-xs font-black text-neutral-700 hover:text-black">Back to Settings</a>
    </div>
  </div>

  <div class="rounded-xl border border-neutral-300 bg-white shadow-sm">
    <div class="border-b border-neutral-200 px-6 py-4">
      <p class="text-[11px] font-black uppercase tracking-widest text-neutral-600">Applied history</p>
      <h3 class="mt-1 text-lg font-black text-black">Already migrated</h3>
    </div>
    <?php if (!$appliedMigrations): ?>
      <p class="p-6 text-sm font-bold text-neutral-700">No migrations have been recorded yet.</p>
    <?php else: ?>
      <ul class="divide-y divide-neutral-100">
        <?php foreach ($appliedMigrations as $row): ?>
          <li class="flex flex-col gap-1 px-6 py-3 sm:flex-row sm:items-center sm:justify-between">
            <span class="font-mono text-sm font-semibold text-black"><?= e($row['migration']) ?></span>
            <span class="text-xs font-bold text-neutral-600"><?= e(date('M j, Y g:ia', strtotime((string) $row['applied_at']))) ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
