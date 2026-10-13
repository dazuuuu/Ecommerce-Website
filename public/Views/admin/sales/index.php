<?php
/** Requires $products, $recentSales in scope. Spreadsheet-style (client design). */
require __DIR__ . '/../layout-header.php';

$entryCols = ['Product Code', 'Photo', 'Product Name', 'Unit Price (KSH)', 'In Stock', 'Qty Sold', 'Line Total (KSH)'];
$historyCols = ['Date', 'Time', 'Customer', 'Payment', 'M-Pesa Code', 'Cash (KSH)', 'M-Pesa (KSH)', 'Total (KSH)'];
$kshRate = availableCurrencies()['KSH']['rate'];
?>

<?php require __DIR__ . '/../partials/sheet-style.php'; ?>

<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-semibold text-slate-900">Record Sale</h1>
    <p class="text-slate-500 mt-1">Type quantities into the yellow <strong>Qty Sold</strong> cells — totals calculate automatically, like Excel.</p>
  </div>
</div>

<!-- Sheet 1: sale entry -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-8" id="sale-sheet">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M9 4v16M15 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" /></svg>
      Sale Entry
    </h2>
    <div class="flex flex-wrap items-center gap-2">
      <div class="relative">
        <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" id="productSearch" placeholder="Search name or code..." class="border border-slate-300 rounded-md py-1.5 pl-8 pr-3 text-sm w-56" />
      </div>
      <label class="flex items-center gap-2 text-sm text-slate-700 border border-slate-300 rounded-md px-2.5 py-1.5 cursor-pointer">
        <input type="checkbox" id="onlySelected" class="w-4 h-4" /> Show only items in this sale
      </label>
      <button type="button" id="clearSale" class="text-sm font-medium text-slate-700 border border-slate-300 rounded-md px-3 py-1.5 hover:bg-slate-50">Clear</button>
    </div>
  </div>

  <div class="xl-formula">
    <span>fx</span>
    <output id="formulaBar">=SUM(G:G)</output>
  </div>

  <div class="overflow-auto max-h-[60vh] border-t border-slate-200">
    <table class="xl-sheet w-full">
      <thead>
        <tr class="xl-letters">
          <th class="xl-rownum"></th>
          <?php foreach ($entryCols as $i => $_): ?><th><?= chr(65 + $i) ?></th><?php endforeach; ?>
        </tr>
        <tr class="xl-heads">
          <th class="xl-rownum" style="cursor:default">#</th>
          <?php foreach ($entryCols as $label): ?><th style="cursor:default"><?= e($label) ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody id="saleRows">
        <?php if (!$products): ?>
          <tr><td class="xl-rownum">1</td><td colspan="<?= count($entryCols) ?>" class="text-center text-slate-500">No products with available stock.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $n => $p): $images = $p['images'] ?? []; ?>
          <tr data-sale-row
              data-id="<?= (int) $p['dbId'] ?>"
              data-price="<?= e(convertPrice((float) $p['price'], 'KSH')) ?>"
              data-stock="<?= (int) $p['quantity'] ?>"
              data-search="<?= e(strtolower($p['name'] . ' ' . $p['id'])) ?>">
            <td class="xl-rownum"><?= $n + 1 ?></td>
            <td class="font-mono text-blue-600"><?= e($p['id']) ?></td>
            <td class="text-center">
              <?php if ($images): ?><img src="<?= e(imageUrl($images[0])) ?>" alt="" class="w-8 h-8 object-cover rounded inline-block" /><?php else: ?><span class="text-slate-300">—</span><?php endif; ?>
            </td>
            <td class="font-medium max-w-[260px] truncate" title="<?= e($p['name']) ?>"><?= e($p['name']) ?></td>
            <td class="xl-num"><?= number_format(convertPrice((float) $p['price'], 'KSH')) ?></td>
            <td class="xl-num"><?= (int) $p['quantity'] ?></td>
            <td class="xl-input xl-editable"><input type="number" min="0" max="<?= (int) $p['quantity'] ?>" step="1" placeholder="0" data-qty aria-label="Quantity sold of <?= e($p['name']) ?>" /></td>
            <td class="xl-num font-medium" data-line-total></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td class="xl-rownum">Σ</td>
          <td colspan="5" class="text-right uppercase tracking-wide text-slate-600">Total</td>
          <td class="xl-num" id="totalQty">0</td>
          <td class="xl-num text-blue-700" id="cartTotal">0</td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<!-- Sheet 2: payment details -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-8 max-w-2xl">
  <div class="px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
      Payment
    </h2>
  </div>
  <table class="xl-sheet w-full border-t border-slate-200" id="paymentSheet">
    <thead>
      <tr class="xl-letters"><th class="xl-rownum"></th><th>A</th><th>B</th></tr>
    </thead>
    <tbody>
      <tr><td class="xl-rownum">1</td><td class="font-medium w-48 bg-blue-50/60">Customer Name</td><td class="xl-input xl-editable"><input type="text" id="customerName" placeholder="Optional — e.g. Jane Doe" /></td></tr>
      <tr><td class="xl-rownum">2</td><td class="font-medium bg-blue-50/60">Payment Method</td>
        <td class="xl-input xl-editable">
          <select id="paymentMethod">
            <option value="cash">Cash</option>
            <option value="mpesa">M-Pesa</option>
            <option value="mixed">Mixed (Cash + M-Pesa)</option>
          </select>
        </td>
      </tr>
      <tr id="mpesaFields" class="hidden"><td class="xl-rownum">3</td><td class="font-medium bg-blue-50/60">M-Pesa Code</td><td class="xl-input xl-editable"><input type="text" id="mpesaCode" placeholder="e.g. QFT7XXXXX" class="uppercase font-mono" /></td></tr>
      <tr data-mixed class="hidden"><td class="xl-rownum">4</td><td class="font-medium bg-blue-50/60">Cash Amount (KSH)</td><td class="xl-input xl-editable"><input type="number" min="0" step="1" id="cashAmount" /></td></tr>
      <tr data-mixed class="hidden"><td class="xl-rownum">5</td><td class="font-medium bg-blue-50/60">M-Pesa Amount (KSH)</td><td class="xl-input xl-editable"><input type="number" min="0" step="1" id="mpesaAmount" /></td></tr>
      <tr><td class="xl-rownum">6</td><td class="font-bold bg-blue-50/60">Amount Due (KSH)</td><td class="xl-num font-bold text-blue-700 text-base" id="amountDue">0</td></tr>
    </tbody>
  </table>
  <div class="flex flex-wrap items-center gap-4 px-5 py-4">
    <button type="button" id="recordSaleBtn" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
      Record Sale
    </button>
    <p id="saleFeedback" class="text-sm font-semibold hidden"></p>
  </div>
</div>

<!-- Sheet 3: sales history -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
      Recent Sales
    </h2>
    <button type="button" id="exportSales" class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-3 py-1.5">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
      Export
    </button>
  </div>
  <div class="overflow-auto max-h-[60vh] border-t border-slate-200">
    <table class="xl-sheet w-full" id="historySheet">
      <thead>
        <tr class="xl-letters">
          <th class="xl-rownum"></th>
          <?php foreach ($historyCols as $i => $_): ?><th><?= chr(65 + $i) ?></th><?php endforeach; ?>
        </tr>
        <tr class="xl-heads">
          <th class="xl-rownum" style="cursor:default">#</th>
          <?php foreach ($historyCols as $label): ?><th style="cursor:default"><?= e($label) ?></th><?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (!$recentSales): ?>
          <tr><td class="xl-rownum">1</td><td colspan="<?= count($historyCols) ?>" class="text-center text-slate-500">No sales recorded yet.</td></tr>
        <?php endif; ?>
        <?php $historyTotal = 0; foreach ($recentSales as $n => $s): $historyTotal += (float) $s['total']; ?>
          <tr data-history-row>
            <td class="xl-rownum"><?= $n + 1 ?></td>
            <td><?= e(date('Y-m-d', strtotime($s['created_at']))) ?></td>
            <td><?= e(date('H:i', strtotime($s['created_at']))) ?></td>
            <td class="font-medium"><?= e($s['customer_name'] ?: '—') ?></td>
            <td class="capitalize"><?= e($s['payment_method']) ?></td>
            <td class="font-mono"><?= e($s['mpesa_code'] ?: '') ?></td>
            <td class="xl-num"><?= number_format(convertPrice((float) $s['cash_amount'], 'KSH')) ?></td>
            <td class="xl-num"><?= number_format(convertPrice((float) $s['mpesa_amount'], 'KSH')) ?></td>
            <td class="xl-num font-semibold"><?= number_format(convertPrice((float) $s['total'], 'KSH')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <?php if ($recentSales): ?>
        <tfoot>
          <tr>
            <td class="xl-rownum">Σ</td>
            <td colspan="7" class="text-right uppercase tracking-wide text-slate-600">Total</td>
            <td class="xl-num text-blue-700"><?= number_format(convertPrice($historyTotal, 'KSH')) ?></td>
          </tr>
        </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>

<script>
(function () {
  var csrfToken = <?= json_encode(csrfToken()) ?>;
  var kshRate = <?= json_encode($kshRate) ?>;
  var rows = Array.prototype.slice.call(document.querySelectorAll('[data-sale-row]'));
  var searchEl = document.getElementById('productSearch');
  var onlySelectedEl = document.getElementById('onlySelected');
  var paymentMethodEl = document.getElementById('paymentMethod');
  var feedbackEl = document.getElementById('saleFeedback');
  var recordBtn = document.getElementById('recordSaleBtn');

  function money(v) { return Math.round(v).toLocaleString('en-KE'); }

  function qtyOf(row) {
    var input = row.querySelector('[data-qty]');
    var stock = parseInt(row.dataset.stock, 10);
    var q = Math.max(0, parseInt(input.value, 10) || 0);
    if (q > stock) { q = stock; input.value = stock; }
    return q;
  }

  function recalc() {
    var total = 0, qtyTotal = 0, refs = [];
    rows.forEach(function (row, i) {
      var q = qtyOf(row);
      var line = q * parseFloat(row.dataset.price);
      row.querySelector('[data-line-total]').textContent = q ? money(line) : '';
      if (q) { total += line; qtyTotal += q; refs.push('G' + (i + 1)); }
    });
    document.getElementById('cartTotal').textContent = money(total);
    document.getElementById('totalQty').textContent = qtyTotal;
    document.getElementById('amountDue').textContent = money(total);
    document.getElementById('formulaBar').textContent = refs.length ? '=SUM(' + refs.join(',') + ')  →  ' + money(total) : '=SUM(G:G)';
    filterRows();
  }

  function filterRows() {
    var term = searchEl.value.trim().toLowerCase();
    var only = onlySelectedEl.checked;
    rows.forEach(function (row) {
      var show = row.dataset.search.indexOf(term) !== -1 && (!only || qtyOf(row) > 0);
      row.style.display = show ? '' : 'none';
    });
  }

  rows.forEach(function (row, i) {
    var input = row.querySelector('[data-qty]');
    input.addEventListener('input', recalc);
    // Enter moves down a row, like Excel.
    input.addEventListener('keydown', function (e) {
      if (e.key !== 'Enter') return;
      e.preventDefault();
      for (var j = i + 1; j < rows.length; j++) {
        if (rows[j].style.display !== 'none') { rows[j].querySelector('[data-qty]').focus(); break; }
      }
    });
  });
  searchEl.addEventListener('input', filterRows);
  onlySelectedEl.addEventListener('change', filterRows);
  document.getElementById('clearSale').addEventListener('click', function () {
    rows.forEach(function (row) { row.querySelector('[data-qty]').value = ''; });
    recalc();
  });

  paymentMethodEl.addEventListener('change', function () {
    var method = paymentMethodEl.value;
    document.getElementById('mpesaFields').classList.toggle('hidden', method === 'cash');
    document.querySelectorAll('[data-mixed]').forEach(function (r) { r.classList.toggle('hidden', method !== 'mixed'); });
    var n = 0;
    document.querySelectorAll('#paymentSheet tbody tr').forEach(function (r) {
      if (!r.classList.contains('hidden')) r.cells[0].textContent = ++n;
    });
  });

  function showFeedback(message, ok) {
    feedbackEl.textContent = message;
    feedbackEl.classList.remove('hidden', 'text-emerald-600', 'text-rose-600');
    feedbackEl.classList.add(ok ? 'text-emerald-600' : 'text-rose-600');
  }

  recordBtn.addEventListener('click', function () {
    var items = rows.filter(function (r) { return qtyOf(r) > 0; }).map(function (r) {
      return { productId: parseInt(r.dataset.id, 10), quantity: qtyOf(r) };
    });
    if (!items.length) {
      showFeedback('Enter a quantity for at least one product.', false);
      return;
    }
    var paymentMethod = paymentMethodEl.value;
    // Amounts are stored in the base currency (USD); the sheet works in KSH.
    var toBase = function (id) { return (parseFloat(document.getElementById(id).value) || 0) / kshRate; };
    var payload = {
      csrfToken: csrfToken,
      customerName: document.getElementById('customerName').value.trim(),
      paymentMethod: paymentMethod,
      mpesaCode: document.getElementById('mpesaCode').value.trim(),
      cashAmount: paymentMethod === 'mixed' ? toBase('cashAmount') : 0,
      mpesaAmount: paymentMethod === 'mixed' ? toBase('mpesaAmount') : 0,
      items: items,
    };

    recordBtn.disabled = true;
    recordBtn.classList.add('opacity-60');

    fetch('<?= url('/admin/sales') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(payload),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        recordBtn.disabled = false;
        recordBtn.classList.remove('opacity-60');
        if (data.success) {
          showFeedback(data.message || 'Sale recorded successfully.', true);
          setTimeout(function () { window.location.reload(); }, 900);
        } else {
          showFeedback(data.message || 'Could not record the sale.', false);
        }
      })
      .catch(function () {
        recordBtn.disabled = false;
        recordBtn.classList.remove('opacity-60');
        showFeedback('Network error. Please try again.', false);
      });
  });

  document.getElementById('exportSales').addEventListener('click', function () {
    var lines = [<?= json_encode($historyCols) ?>];
    document.querySelectorAll('[data-history-row]').forEach(function (r) {
      lines.push(Array.prototype.slice.call(r.cells, 1).map(function (c) { return c.innerText.trim(); }));
    });
    var csv = lines.map(function (l) { return l.map(function (v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(','); }).join('\n');
    var a = document.createElement('a');
    a.href = URL.createObjectURL(new Blob(['﻿' + csv], { type: 'text/csv' }));
    a.download = 'sales.csv';
    a.click();
  });

  paymentMethodEl.dispatchEvent(new Event('change'));
  recalc();
})();
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
