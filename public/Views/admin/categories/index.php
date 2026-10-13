<?php
/** Requires $categories in scope. Spreadsheet-style (client design). */
require __DIR__ . '/../layout-header.php';

$columns = ['Category Key', 'Cover', 'Category Name', 'Tagline', 'Products', 'Actions'];
$totalProducts = array_sum(array_map(fn($c) => (int) $c['product_count'], $categories));
?>

<?php require __DIR__ . '/../partials/sheet-style.php'; ?>

<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-semibold text-slate-900">Categories</h1>
    <p class="text-slate-500 mt-1">The categories products can be assigned to, and their cover images</p>
  </div>
  <a href="<?= url('/admin/categories/create') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    Add Category
  </a>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
      All Categories
      <span class="text-sm font-medium text-slate-500">(<?= count($categories) ?>)</span>
    </h2>
    <div class="relative">
      <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
      <input type="text" id="cat-search" placeholder="Search categories..." class="border border-slate-300 rounded-md py-1.5 pl-8 pr-3 text-sm w-56" />
    </div>
  </div>

  <div class="overflow-auto max-h-[70vh] border-t border-slate-200">
    <table class="xl-sheet w-full" id="cat-sheet">
      <thead>
        <tr class="xl-letters">
          <th class="xl-rownum"></th>
          <?php foreach ($columns as $i => $_): ?><th><?= chr(65 + $i) ?></th><?php endforeach; ?>
        </tr>
        <tr class="xl-heads">
          <th class="xl-rownum" style="cursor:default">#</th>
          <?php foreach ($columns as $label): ?><th style="cursor:default"><?= e($label) ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!$categories): ?>
          <tr><td class="xl-rownum">1</td><td colspan="<?= count($columns) ?>" class="text-center text-slate-500">No categories yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($categories as $n => $cat): ?>
          <tr data-search="<?= e(strtolower($cat['name'] . ' ' . $cat['category_key'] . ' ' . $cat['tagline'])) ?>">
            <td class="xl-rownum"><?= $n + 1 ?></td>
            <td class="font-mono text-blue-600"><a href="<?= url('/admin/categories/' . (int) $cat['id'] . '/edit') ?>" class="hover:underline"><?= e($cat['category_key']) ?></a></td>
            <td class="text-center">
              <?php if ($cat['image']): ?><img src="<?= e(imageUrl($cat['image'])) ?>" alt="" class="w-10 h-8 object-cover rounded inline-block" /><?php else: ?><span class="text-slate-300">—</span><?php endif; ?>
            </td>
            <td class="font-medium"><?= e($cat['name']) ?></td>
            <td class="text-slate-600 max-w-[340px] truncate" title="<?= e($cat['tagline']) ?>"><?= e($cat['tagline']) ?></td>
            <td class="xl-num"><?= (int) $cat['product_count'] ?></td>
            <td>
              <div class="flex items-center gap-1">
                <a href="<?= url('/admin/categories/' . (int) $cat['id'] . '/edit') ?>" class="px-2 py-1 rounded text-xs font-medium text-slate-700 hover:bg-slate-100">Edit</a>
                <?php if ($cat['image']): ?>
                  <form method="post" action="<?= url('/admin/categories/' . (int) $cat['id'] . '/delete-image') ?>" onsubmit="return confirm('Remove the cover image for this category?');" class="m-0">
                    <?= csrfField() ?>
                    <button type="submit" class="px-2 py-1 rounded text-xs font-medium text-rose-700 hover:bg-rose-50">Remove Image</button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <?php if ($categories): ?>
        <tfoot>
          <tr>
            <td class="xl-rownum">Σ</td>
            <td colspan="4" class="text-right uppercase tracking-wide text-slate-600">Total</td>
            <td class="xl-num text-blue-700"><?= $totalProducts ?></td>
            <td></td>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<script>
  (function () {
    var search = document.getElementById('cat-search');
    search.addEventListener('input', function () {
      var term = search.value.trim().toLowerCase(), n = 0;
      document.querySelectorAll('#cat-sheet tbody tr[data-search]').forEach(function (r) {
        var show = r.getAttribute('data-search').indexOf(term) !== -1;
        r.style.display = show ? '' : 'none';
        if (show) r.cells[0].textContent = ++n;
      });
    });
  })();
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
