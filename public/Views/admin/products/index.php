<?php
/** Requires $products, $search in scope. Spreadsheet-style grid (client design). */
require __DIR__ . '/../layout-header.php';

$columns = ['Product Code', 'Photo', 'Product Name', 'Category', 'Price (KSH)', 'Was (KSH)', 'Qty', 'Status', 'Flags', 'Actions'];
?>

<?php require __DIR__ . '/../partials/sheet-style.php'; ?>

<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-semibold text-slate-900">Products</h1>
    <p class="text-slate-500 mt-1">Your full inventory as a spreadsheet — click a column header to sort</p>
  </div>
  <a href="<?= url('/admin/products/create') ?>" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    Add Product
  </a>
</div>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm" id="product-sheet">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
      All Products
    </h2>
    <div class="flex flex-wrap items-center gap-2">
      <form method="get" action="<?= url('/admin/products') ?>" class="relative">
        <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search name or code..." class="border border-slate-300 rounded-md py-1.5 pl-8 pr-3 text-sm w-56" />
      </form>
      <label class="flex items-center gap-1.5 border border-slate-300 rounded-md px-2 py-1.5 text-sm text-slate-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z" /></svg>
        <select data-sheet-filter class="bg-transparent focus:outline-none cursor-pointer border-0 p-0">
          <option value="">Filter</option>
          <option value="in">In stock</option>
          <option value="out">Out of stock</option>
          <option value="sale">On sale</option>
          <option value="new">New</option>
        </select>
      </label>
      <button type="button" data-sheet-export class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-3 py-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
        Export
      </button>
    </div>
  </div>

  <div class="overflow-auto max-h-[70vh] border-y border-slate-200">
    <table class="xl-sheet w-full" data-sheet>
      <thead>
        <tr class="xl-letters">
          <th class="xl-rownum"></th>
          <?php foreach ($columns as $i => $_): ?><th><?= chr(65 + $i) ?></th><?php endforeach; ?>
        </tr>
        <tr class="xl-heads">
          <th class="xl-rownum" style="cursor:default">#</th>
          <?php foreach ($columns as $i => $label): ?>
            <th <?= in_array($label, ['Photo', 'Actions'], true) ? 'style="cursor:default"' : 'data-sort-col="' . $i . '"' ?>><?= e($label) ?></th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!$products): ?>
          <tr><td class="xl-rownum">1</td><td colspan="<?= count($columns) ?>" class="text-center text-slate-500">No products found.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $p):
          $images = json_decode($p['images'] ?? '[]', true) ?: [];
          $categoryKeys = json_decode($p['category_keys'] ?? '[]', true) ?: [$p['category_key']];
          $qty = (int) ($p['quantity'] ?? 0);
          $isOutOfStock = $qty <= 0 || empty($p['in_stock']);
          $flags = array_filter([$p['is_new'] ? 'NEW' : null, $p['is_best_seller'] ? 'BEST' : null, $p['is_sale'] ? 'SALE' : null]);
          $price = convertPrice((float) $p['price'], 'KSH');
          $was = $p['original_price'] ? convertPrice((float) $p['original_price'], 'KSH') : null;
        ?>
          <tr data-sheet-row data-stock="<?= $isOutOfStock ? 'out' : 'in' ?>" data-sale="<?= $p['is_sale'] ? 1 : 0 ?>" data-new="<?= $p['is_new'] ? 1 : 0 ?>">
            <td class="xl-rownum"></td>
            <td data-v="<?= e($p['product_code']) ?>" class="font-mono text-blue-600"><a href="<?= url('/admin/products/' . (int) $p['id'] . '/edit') ?>" class="hover:underline"><?= e($p['product_code']) ?></a></td>
            <td class="text-center">
              <?php if ($images): ?><img src="<?= e(imageUrl($images[0])) ?>" alt="" class="w-8 h-8 object-cover rounded inline-block" /><?php else: ?><span class="text-slate-300">—</span><?php endif; ?>
            </td>
            <td data-v="<?= e($p['name']) ?>" class="font-medium max-w-[260px] truncate" title="<?= e($p['name']) ?>"><?= e($p['name']) ?></td>
            <td data-v="<?= e(implode(', ', $categoryKeys)) ?>"><?= e(implode(', ', $categoryKeys)) ?></td>
            <td data-v="<?= $price ?>" class="xl-num font-medium"><?= number_format($price) ?></td>
            <td data-v="<?= $was ?? 0 ?>" class="xl-num text-slate-500"><?= $was ? number_format($was) : '' ?></td>
            <td data-v="<?= $qty ?>" class="xl-num <?= $isOutOfStock ? 'text-rose-600 font-semibold' : '' ?>"><?= $qty ?></td>
            <td data-v="<?= $isOutOfStock ? 'Out of stock' : 'In stock' ?>">
              <span class="inline-flex items-center gap-1.5 rounded border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide <?= $isOutOfStock ? 'bg-rose-100 text-rose-800 border-rose-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200' ?>">
                <span class="w-2 h-2 rounded-full <?= $isOutOfStock ? 'bg-rose-500' : 'bg-emerald-500' ?>"></span><?= $isOutOfStock ? 'Out of stock' : 'In stock' ?>
              </span>
            </td>
            <td data-v="<?= e(implode(' ', $flags)) ?>" class="text-xs font-semibold text-slate-600"><?= e(implode(' · ', $flags)) ?></td>
            <td>
              <div class="flex items-center gap-1">
                <button type="button" onclick="openSaleModal(<?= (int) $p['id'] ?>, <?= e(json_encode($p['name'])) ?>, <?= $qty ?>)" class="px-2 py-1 rounded text-xs font-medium text-emerald-700 hover:bg-emerald-50">Sale</button>
                <button type="button" onclick="openRestockModal(<?= (int) $p['id'] ?>, <?= e(json_encode($p['name'])) ?>, <?= $qty ?>)" class="px-2 py-1 rounded text-xs font-medium text-blue-700 hover:bg-blue-50">Restock</button>
                <a href="<?= url('/admin/products/' . (int) $p['id'] . '/edit') ?>" class="px-2 py-1 rounded text-xs font-medium text-slate-700 hover:bg-slate-100">Edit</a>
                <form method="post" action="<?= url('/admin/products/' . (int) $p['id'] . '/delete') ?>" onsubmit="return confirm('Delete this product? This cannot be undone.');" class="m-0">
                  <?= csrfField() ?>
                  <button type="submit" class="px-2 py-1 rounded text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 text-sm text-slate-600">
    <p data-sheet-summary></p>
    <div class="flex items-center gap-1" data-sheet-pages></div>
    <label class="flex items-center gap-2">
      Rows per page
      <select data-sheet-size class="border border-slate-300 rounded-md px-2 py-1">
        <option>10</option><option selected>25</option><option>50</option><option value="100000">All</option>
      </select>
    </label>
  </div>
</div>

<script>
  (function () {
    var root = document.getElementById('product-sheet');
    var tbody = root.querySelector('[data-sheet] tbody');
    var rows = Array.prototype.slice.call(root.querySelectorAll('[data-sheet-row]'));
    var filterEl = root.querySelector('[data-sheet-filter]');
    var sizeEl = root.querySelector('[data-sheet-size]');
    var page = 1;

    function matches(r) {
      switch (filterEl.value) {
        case 'in': return r.dataset.stock === 'in';
        case 'out': return r.dataset.stock === 'out';
        case 'sale': return r.dataset.sale === '1';
        case 'new': return r.dataset.new === '1';
        default: return true;
      }
    }
    function cellValue(r, col) { return r.cells[col + 1].getAttribute('data-v') || ''; }

    function render() {
      var size = parseInt(sizeEl.value, 10);
      var list = rows.filter(matches);
      var pages = Math.max(1, Math.ceil(list.length / size));
      page = Math.min(Math.max(page, 1), pages);
      rows.forEach(function (r) { r.style.display = 'none'; });
      list.slice((page - 1) * size, page * size).forEach(function (r, i) {
        r.style.display = '';
        r.cells[0].textContent = (page - 1) * size + i + 1;
      });
      var from = list.length ? (page - 1) * size + 1 : 0;
      root.querySelector('[data-sheet-summary]').textContent = 'Showing ' + from + ' to ' + Math.min(page * size, list.length) + ' of ' + list.length + ' entries';
      var pagesEl = root.querySelector('[data-sheet-pages]');
      pagesEl.innerHTML = '';
      function btn(label, target, active, disabled) {
        var b = document.createElement('button');
        b.type = 'button'; b.innerHTML = label; b.disabled = disabled;
        b.className = 'min-w-[32px] h-8 px-2 rounded-md font-medium text-base ' + (active ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100') + (disabled ? ' opacity-40 cursor-default' : '');
        b.addEventListener('click', function () { page = target; render(); });
        pagesEl.appendChild(b);
      }
      btn('&#10094;', page - 1, false, page === 1);
      for (var i = 1; i <= pages; i++) btn(String(i), i, i === page, false);
      btn('&#10095;', page + 1, false, page === pages);
    }

    root.querySelectorAll('[data-sort-col]').forEach(function (th) {
      th.addEventListener('click', function () {
        var col = parseInt(th.getAttribute('data-sort-col'), 10);
        var dir = th.getAttribute('data-sort-dir') === 'asc' ? 'desc' : 'asc';
        root.querySelectorAll('[data-sort-col]').forEach(function (o) { o.removeAttribute('data-sort-dir'); });
        th.setAttribute('data-sort-dir', dir);
        rows.sort(function (a, b) {
          var x = cellValue(a, col), y = cellValue(b, col);
          var nx = parseFloat(x), ny = parseFloat(y);
          var cmp = (!isNaN(nx) && !isNaN(ny) && /^-?[\d.]+$/.test(x) && /^-?[\d.]+$/.test(y)) ? nx - ny : x.localeCompare(y, undefined, { numeric: true });
          return dir === 'asc' ? cmp : -cmp;
        });
        rows.forEach(function (r) { tbody.appendChild(r); });
        page = 1; render();
      });
    });

    // Excel-like cell selection.
    tbody.addEventListener('click', function (e) {
      var td = e.target.closest('td');
      if (!td || td.classList.contains('xl-rownum') || e.target.closest('a,button,form')) return;
      root.querySelectorAll('.xl-selected').forEach(function (c) { c.classList.remove('xl-selected'); });
      td.classList.add('xl-selected');
    });

    filterEl.addEventListener('change', function () { page = 1; render(); });
    sizeEl.addEventListener('change', function () { page = 1; render(); });

    root.querySelector('[data-sheet-export]').addEventListener('click', function () {
      var heads = ['Product Code', 'Product Name', 'Category', 'Price (KSH)', 'Was (KSH)', 'Qty', 'Status', 'Flags'];
      var cols = [0, 2, 3, 4, 5, 6, 7, 8];
      var csv = [heads].concat(rows.filter(matches).map(function (r) {
        return cols.map(function (c) { return cellValue(r, c); });
      })).map(function (line) {
        return line.map(function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(',');
      }).join('\n');
      var a = document.createElement('a');
      a.href = URL.createObjectURL(new Blob(['﻿' + csv], { type: 'text/csv' }));
      a.download = 'products.csv';
      a.click();
    });

    render();
  })();
</script>

<!-- Record Sale Modal -->
<div id="recordSaleModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
  <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 relative border border-gray-100 animate-in fade-in zoom-in duration-200">
    <button type="button" onclick="closeSaleModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
      <div>
        <h3 class="text-lg font-bold text-gray-900">Record Today's Sale</h3>
        <p class="text-xs text-gray-500">Deduct sold quantity from available stock</p>
      </div>
    </div>
    <form id="saleForm" method="post" action="">
      <?= csrfField() ?>
      <div class="mb-4 bg-gray-50 p-3.5 rounded-2xl border border-gray-100">
        <p class="text-xs text-gray-500">Product</p>
        <p id="saleProductName" class="font-bold text-gray-900 text-sm mt-0.5"></p>
        <p class="text-xs text-gray-500 mt-1">Current Stock: <span id="saleCurrentStock" class="font-semibold text-emerald-700"></span></p>
      </div>
      <div class="mb-5">
        <label for="qty_sold" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Quantity Sold</label>
        <div class="flex items-center gap-2">
          <button type="button" onclick="adjustSaleQty(-1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-lg flex items-center justify-center transition-colors">-</button>
          <input type="number" id="qty_sold" name="qty_sold" value="1" min="1" class="w-full text-center bg-gray-50 border border-gray-200 rounded-xl py-2.5 font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all text-base" required />
          <button type="button" onclick="adjustSaleQty(1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-lg flex items-center justify-center transition-colors">+</button>
        </div>
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeSaleModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors">Cancel</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-200 transition-colors flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          Record Sale & Deduct
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Restock Modal -->
<div id="restockModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4">
  <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 relative border border-gray-100 animate-in fade-in zoom-in duration-200">
    <button type="button" onclick="closeRestockModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
    <div class="flex items-center gap-3 mb-4">
      <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
      </div>
      <div>
        <h3 class="text-lg font-bold text-gray-900">Restock Product</h3>
        <p class="text-xs text-gray-500">Add received/new inventory to available stock</p>
      </div>
    </div>
    <form id="restockForm" method="post" action="">
      <?= csrfField() ?>
      <div class="mb-4 bg-gray-50 p-3.5 rounded-2xl border border-gray-100">
        <p class="text-xs text-gray-500">Product</p>
        <p id="restockProductName" class="font-bold text-gray-900 text-sm mt-0.5"></p>
        <p class="text-xs text-gray-500 mt-1">Current Stock: <span id="restockCurrentStock" class="font-semibold text-indigo-700"></span></p>
      </div>
      <div class="mb-5">
        <label for="qty_added" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Quantity Received</label>
        <div class="flex items-center gap-2">
          <button type="button" onclick="adjustRestockQty(-1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-lg flex items-center justify-center transition-colors">-</button>
          <input type="number" id="qty_added" name="qty_added" value="1" min="1" class="w-full text-center bg-gray-50 border border-gray-200 rounded-xl py-2.5 font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all text-base" required />
          <button type="button" onclick="adjustRestockQty(1)" class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-lg flex items-center justify-center transition-colors">+</button>
        </div>
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeRestockModal()" class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors">Cancel</button>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm shadow-indigo-200 transition-colors flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          Restock
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openRestockModal(productId, productName, currentStock) {
  const modal = document.getElementById('restockModal');
  const form = document.getElementById('restockForm');
  document.getElementById('restockProductName').textContent = productName;
  document.getElementById('restockCurrentStock').textContent = currentStock + ' units';
  document.getElementById('qty_added').value = 1;
  form.action = '<?= url('/admin/products/') ?>' + productId + '/restock';
  modal.classList.remove('hidden');
}

function closeRestockModal() {
  document.getElementById('restockModal').classList.add('hidden');
}

function adjustRestockQty(delta) {
  const input = document.getElementById('qty_added');
  let val = parseInt(input.value || 1) + delta;
  if (val < 1) val = 1;
  input.value = val;
}

function openSaleModal(productId, productName, currentStock) {
  const modal = document.getElementById('recordSaleModal');
  const form = document.getElementById('saleForm');
  document.getElementById('saleProductName').textContent = productName;
  document.getElementById('saleCurrentStock').textContent = currentStock + ' units';
  document.getElementById('qty_sold').value = 1;
  form.action = '<?= url('/admin/products/') ?>' + productId + '/record-sale';
  modal.classList.remove('hidden');
}

function closeSaleModal() {
  document.getElementById('recordSaleModal').classList.add('hidden');
}

function adjustSaleQty(delta) {
  const input = document.getElementById('qty_sold');
  let val = parseInt(input.value || 1) + delta;
  if (val < 1) val = 1;
  input.value = val;
}
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
