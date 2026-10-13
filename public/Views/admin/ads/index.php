<?php
/** Requires $ads, $now in scope. */
require __DIR__ . '/../layout-header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
  <div>
    <h2 class="text-2xl font-bold text-gray-900">Ads</h2>
    <p class="text-sm text-gray-500 mt-1">Pop-up ads shown to visitors each time they open or refresh the site. Each ad disappears automatically when its duration ends.</p>
  </div>
  <a href="<?= url('/admin/ads/create') ?>" class="inline-flex items-center gap-2 bg-indigo-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    New Ad
  </a>
</div>

<div class="bg-white rounded-3xl shadow-[0_2px_12px_rgba(0,0,0,0.03)] overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="bg-gray-50/50">
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">Ad</th>
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">Runs</th>
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">Status</th>
          <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        <?php if (!$ads): ?>
          <tr><td colspan="4" class="px-6 py-12 text-center text-sm font-medium text-gray-400">No ads yet. Create one to show an offer, a countdown or a new launch.</td></tr>
        <?php endif; ?>
        <?php foreach ($ads as $ad):
          $expired = $ad['ends_at'] <= $now;
          $upcoming = $ad['starts_at'] > $now;
          if (!$ad['is_active']) { $label = 'Paused'; $cls = 'bg-gray-100 text-gray-600'; }
          elseif ($expired) { $label = 'Ended'; $cls = 'bg-rose-100 text-rose-700'; }
          elseif ($upcoming) { $label = 'Scheduled'; $cls = 'bg-amber-100 text-amber-800'; }
          else { $label = 'Live'; $cls = 'bg-emerald-100 text-emerald-800'; }
        ?>
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-4">
                <?php if ($ad['media_type'] === 'image' && $ad['media_path']): ?>
                  <img src="<?= e(imageUrl($ad['media_path'])) ?>" alt="" class="w-16 h-12 object-cover rounded-lg border border-gray-100" />
                <?php elseif ($ad['media_type'] === 'video'): ?>
                  <div class="w-16 h-12 rounded-lg bg-gray-900 text-white text-[10px] font-bold flex items-center justify-center">VIDEO</div>
                <?php else: ?>
                  <div class="w-16 h-12 rounded-lg bg-gray-100"></div>
                <?php endif; ?>
                <div>
                  <p class="font-bold text-gray-900 text-sm"><?= e($ad['title']) ?></p>
                  <?php if ($ad['badge']): ?><p class="text-xs text-indigo-600 font-semibold"><?= e($ad['badge']) ?></p><?php endif; ?>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-xs text-gray-600">
              <?= e(date('M j, Y g:i A', strtotime($ad['starts_at']))) ?><br>
              to <?= e(date('M j, Y g:i A', strtotime($ad['ends_at']))) ?>
            </td>
            <td class="px-6 py-4"><span class="inline-block px-3 py-1 rounded-full text-xs font-bold <?= $cls ?>"><?= $label ?></span></td>
            <td class="px-6 py-4">
              <div class="flex items-center justify-end gap-2">
                <?php if (!$expired): ?>
                  <form method="post" action="<?= url('/admin/ads/' . (int) $ad['id'] . '/toggle') ?>" class="m-0">
                    <?= csrfField() ?>
                    <button type="submit" class="bg-gray-50 text-gray-700 hover:bg-gray-200 px-3 py-2 rounded-lg text-xs font-bold transition-colors"><?= $ad['is_active'] ? 'Pause' : 'Resume' ?></button>
                  </form>
                <?php endif; ?>
                <a href="<?= url('/admin/ads/' . (int) $ad['id'] . '/edit') ?>" class="bg-gray-50 text-gray-700 hover:bg-gray-200 px-3 py-2 rounded-lg text-xs font-bold transition-colors"><?= $expired ? 'Edit / Rerun' : 'Edit' ?></a>
                <form method="post" action="<?= url('/admin/ads/' . (int) $ad['id'] . '/delete') ?>" onsubmit="return confirm('Delete this ad?');" class="m-0">
                  <?= csrfField() ?>
                  <button type="submit" class="bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition-colors">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
