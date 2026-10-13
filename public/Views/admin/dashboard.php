<?php
/** Requires $range, $kpis, $totals, $recentOrders in scope. */
require __DIR__ . '/layout-header.php';

$rangeLabel = $range === 'month' ? 'last month' : 'last week';

$statusPill = [
    'pending' => ['bg-amber-100 text-amber-800 border-amber-200', 'bg-amber-500'],
    'processing' => ['bg-sky-100 text-sky-800 border-sky-200', 'bg-sky-500'],
    'shipped' => ['bg-indigo-100 text-indigo-800 border-indigo-200', 'bg-indigo-500'],
    'delivered' => ['bg-emerald-100 text-emerald-800 border-emerald-200', 'bg-emerald-500'],
    'cancelled' => ['bg-rose-100 text-rose-800 border-rose-200', 'bg-rose-500'],
];

/** Small inline SVG area sparkline for a KPI card. */
$sparkline = function (array $points, string $color): string {
    $w = 64; $h = 24;
    $max = max($points ?: [0]);
    $n = max(count($points) - 1, 1);
    $coords = [];
    foreach ($points as $i => $v) {
        $x = round($i / $n * $w, 1);
        $y = round($h - ($max > 0 ? $v / $max : 0) * ($h - 3) - 1.5, 1);
        $coords[] = "{$x},{$y}";
    }
    $line = implode(' ', $coords);
    return '<svg viewBox="0 0 ' . $w . ' ' . $h . '" class="w-16 h-6" preserveAspectRatio="none" aria-hidden="true">'
        . '<polygon points="0,' . $h . ' ' . $line . ' ' . $w . ',' . $h . '" fill="' . $color . '" fill-opacity="0.15" />'
        . '<polyline points="' . $line . '" fill="none" stroke="' . $color . '" stroke-width="1.5" stroke-linejoin="round" /></svg>';
};

$cards = [
    ['label' => 'Total Orders', 'value' => number_format($totals['orders']), 'kpi' => $kpis['orders'], 'href' => url('/admin/orders'), 'tile' => 'bg-blue-100 text-blue-700', 'color' => '#3b82f6', 'goodWhenUp' => true,
     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />'],
    ['label' => 'Customers', 'value' => number_format($totals['customers']), 'kpi' => $kpis['customers'], 'href' => url('/admin/orders'), 'tile' => 'bg-emerald-100 text-emerald-700', 'color' => '#10b981', 'goodWhenUp' => true,
     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />'],
    ['label' => 'Pending Orders', 'value' => number_format($totals['pending']), 'kpi' => $kpis['pending'], 'href' => url('/admin/orders?status=pending'), 'tile' => 'bg-amber-100 text-amber-700', 'color' => '#f59e0b', 'goodWhenUp' => false,
     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'],
    ['label' => 'Total Revenue', 'value' => formatPrice($totals['revenue'], 'KSH'), 'kpi' => $kpis['revenue'], 'href' => url('/admin/sales'), 'tile' => 'bg-violet-100 text-violet-700', 'color' => '#8b5cf6', 'goodWhenUp' => true,
     'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'],
];
?>

<div class="flex flex-wrap items-start justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-semibold text-slate-900">Dashboard</h1>
    <p class="text-slate-500 mt-1">Overview of your store operations</p>
  </div>
  <form method="get" action="<?= url('/admin') ?>">
    <label class="flex items-center gap-2 bg-white border border-slate-300 rounded-lg pl-3 pr-2 py-2 shadow-sm">
      <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
      <select name="range" onchange="this.form.submit()" class="bg-transparent text-sm font-medium text-slate-800 focus:outline-none cursor-pointer pr-1">
        <option value="week" <?= $range === 'week' ? 'selected' : '' ?>>This Week</option>
        <option value="month" <?= $range === 'month' ? 'selected' : '' ?>>This Month</option>
      </select>
    </label>
  </form>
</div>

<!-- KPI cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
  <?php foreach ($cards as $card):
    $change = $card['kpi']['change'];
    $up = $change >= 0;
    $tone = $change === 0 ? 'text-slate-500' : (($up === $card['goodWhenUp']) ? 'text-emerald-600' : 'text-rose-600');
  ?>
    <a href="<?= e($card['href']) ?>" class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition-shadow">
      <div class="flex items-start gap-3">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center shrink-0 <?= $card['tile'] ?>">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $card['icon'] ?></svg>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-medium text-slate-700"><?= e($card['label']) ?></p>
          <p class="text-2xl font-semibold text-slate-900 truncate"><?= e($card['value']) ?></p>
        </div>
      </div>
      <div class="flex items-end justify-between mt-4">
        <p class="text-xs text-slate-500 flex items-center gap-1">
          <span class="font-semibold flex items-center <?= $tone ?>">
            <svg class="w-4 h-4 <?= $up ? '' : 'rotate-180' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m-6 6l6-6 6 6" /></svg>
            <?= abs($change) ?>%
          </span>
          vs <?= e($rangeLabel) ?>
        </p>
        <?= $sparkline($card['kpi']['series'], $card['color']) ?>
      </div>
    </a>
  <?php endforeach; ?>
</div>

<!-- Recent orders -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm" id="recent-orders">
  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
    <h2 class="flex items-center gap-2 text-lg font-semibold text-slate-900">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
      Recent Orders
    </h2>
    <div class="flex items-center gap-2">
      <label class="flex items-center gap-1.5 border border-slate-300 rounded-md px-2 py-1.5 text-sm text-slate-700">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18l-7 8v6l-4 2v-8L3 4z" /></svg>
        <select data-ro-filter class="bg-transparent focus:outline-none cursor-pointer">
          <option value="">Filter</option>
          <?php foreach (array_keys($statusPill) as $s): ?><option value="<?= e($s) ?>"><?= ucfirst($s) ?></option><?php endforeach; ?>
        </select>
      </label>
      <button type="button" data-ro-export class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md px-3 py-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" /></svg>
        Export
      </button>
    </div>
  </div>

  <div class="overflow-x-auto px-5">
    <table class="w-full text-left text-sm border-collapse min-w-[720px]">
      <thead>
        <tr class="bg-blue-50 text-slate-800">
          <th class="py-3 px-4 font-medium rounded-l-lg">Order ID</th>
          <th class="py-3 px-4 font-medium">Customer Name</th>
          <th class="py-3 px-4 font-medium">Contact</th>
          <th class="py-3 px-4 font-medium">Total</th>
          <th class="py-3 px-4 font-medium">Status</th>
          <th class="py-3 px-4 font-medium">Order Date</th>
          <th class="py-3 px-4 font-medium text-center rounded-r-lg">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (!$recentOrders): ?>
          <tr><td colspan="7" class="py-10 text-center text-slate-500">No orders placed yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($recentOrders as $o):
          $customer = trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')) ?: ($o['email'] ?: $o['phone']);
          [$pill, $dot] = $statusPill[$o['status']] ?? ['bg-slate-100 text-slate-700 border-slate-200', 'bg-slate-500'];
          $orderUrl = url('/admin/orders/' . urlencode($o['order_ref']));
        ?>
          <tr data-ro-row data-status="<?= e($o['status']) ?>" class="hover:bg-slate-50">
            <td class="py-3.5 px-4"><a href="<?= e($orderUrl) ?>" class="font-medium text-blue-600 hover:underline"><?= e($o['order_ref']) ?></a></td>
            <td class="py-3.5 px-4 font-medium text-slate-900"><?= e($customer) ?></td>
            <td class="py-3.5 px-4 text-slate-600 text-xs">
              <span class="inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-blue-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 00-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"/></svg>
                <?= e($o['phone'] ?: $o['email']) ?>
              </span>
            </td>
            <td class="py-3.5 px-4 font-medium text-slate-900"><?= e(formatPrice((float) $o['total'], $o['currency'])) ?></td>
            <td class="py-3.5 px-4">
              <span class="inline-flex items-center gap-1.5 rounded border px-2 py-1 text-[10px] font-bold uppercase tracking-wide <?= $pill ?>">
                <span class="w-2 h-2 rounded-full <?= $dot ?>"></span><?= e($o['status'] === 'shipped' ? 'In transit' : $o['status']) ?>
              </span>
            </td>
            <td class="py-3.5 px-4 text-slate-800 leading-tight">
              <?= e(date('M j, Y', strtotime($o['created_at']))) ?><br>
              <span class="text-xs text-slate-500"><?= e(date('g:i A', strtotime($o['created_at']))) ?></span>
            </td>
            <td class="py-3.5 px-4 text-center">
              <a href="<?= e($orderUrl) ?>" class="inline-flex p-1.5 rounded-md text-slate-700 hover:bg-slate-100" title="View order">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 text-sm text-slate-600">
    <p data-ro-summary></p>
    <div class="flex items-center gap-1" data-ro-pages></div>
    <label class="flex items-center gap-2">
      Rows per page
      <select data-ro-size class="border border-slate-300 rounded-md px-2 py-1 focus:outline-none">
        <option>5</option><option>10</option><option>20</option>
      </select>
    </label>
  </div>
</div>

<script>
  (function () {
    var root = document.getElementById('recent-orders');
    var rows = Array.prototype.slice.call(root.querySelectorAll('[data-ro-row]'));
    var filterEl = root.querySelector('[data-ro-filter]');
    var sizeEl = root.querySelector('[data-ro-size]');
    var pagesEl = root.querySelector('[data-ro-pages]');
    var summaryEl = root.querySelector('[data-ro-summary]');
    var page = 1;

    function visibleRows() {
      var status = filterEl.value;
      return rows.filter(function (r) { return !status || r.getAttribute('data-status') === status; });
    }

    function pageButton(label, target, active, disabled) {
      var b = document.createElement('button');
      b.type = 'button';
      b.innerHTML = label;
      b.disabled = disabled;
      b.className = 'min-w-[32px] h-8 px-2 rounded-md font-medium text-base ' +
        (active ? 'bg-blue-600 text-white' : 'text-slate-700 hover:bg-slate-100') + (disabled ? ' opacity-40 cursor-default' : '');
      b.addEventListener('click', function () { page = target; render(); });
      return b;
    }

    function render() {
      var size = parseInt(sizeEl.value, 10);
      var list = visibleRows();
      var pages = Math.max(1, Math.ceil(list.length / size));
      page = Math.min(Math.max(page, 1), pages);
      rows.forEach(function (r) { r.style.display = 'none'; });
      list.slice((page - 1) * size, page * size).forEach(function (r) { r.style.display = ''; });

      var from = list.length ? (page - 1) * size + 1 : 0;
      summaryEl.textContent = 'Showing ' + from + ' to ' + Math.min(page * size, list.length) + ' of ' + list.length + ' entries';

      pagesEl.innerHTML = '';
      pagesEl.appendChild(pageButton('&#10094;', page - 1, false, page === 1));
      for (var i = 1; i <= pages; i++) pagesEl.appendChild(pageButton(String(i), i, i === page, false));
      pagesEl.appendChild(pageButton('&#10095;', page + 1, false, page === pages));
    }

    filterEl.addEventListener('change', function () { page = 1; render(); });
    sizeEl.addEventListener('change', function () { page = 1; render(); });

    root.querySelector('[data-ro-export]').addEventListener('click', function () {
      var csv = [['Order ID', 'Customer', 'Contact', 'Total', 'Status', 'Date']].concat(visibleRows().map(function (r) {
        return Array.prototype.slice.call(r.cells, 0, 6).map(function (c) { return c.innerText.replace(/\s+/g, ' ').trim(); });
      })).map(function (cols) {
        return cols.map(function (v) { return '"' + v.replace(/"/g, '""') + '"'; }).join(',');
      }).join('\n');
      var a = document.createElement('a');
      a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
      a.download = 'recent-orders.csv';
      a.click();
    });

    render();
  })();
</script>

<?php require __DIR__ . '/layout-footer.php'; ?>
