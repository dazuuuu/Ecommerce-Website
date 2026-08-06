<?php
/** Requires $stats, $recentOrders in scope. */
require __DIR__ . '/layout-header.php';
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <?php
  $cards = [
      ['label' => 'Products', 'value' => $stats['products'], 'href' => url('/admin/products')],
      ['label' => 'On Offer', 'value' => $stats['onOffer'], 'href' => url('/admin/offers')],
      ['label' => 'Categories', 'value' => $stats['categories'], 'href' => url('/admin/categories')],
      ['label' => 'Orders', 'value' => $stats['orders'], 'href' => url('/admin/orders')],
      ['label' => 'Pending Orders', 'value' => $stats['pendingOrders'], 'href' => url('/admin/orders?status=pending')],
      ['label' => 'Customers', 'value' => $stats['customers'], 'href' => null],
  ];
  foreach ($cards as $s): ?>
    <a href="<?= $s['href'] ? e($s['href']) : '#' ?>" class="bg-white border border-neutral-200 rounded-xl p-5 shadow-sm hover:border-amber-400 transition-colors <?= $s['href'] ? '' : 'pointer-events-none' ?>">
      <p class="text-[11px] font-bold text-neutral-500 uppercase tracking-widest"><?= e($s['label']) ?></p>
      <p class="font-serif-heading text-3xl font-bold text-[#0a0a0a] mt-1"><?= (int) $s['value'] ?></p>
    </a>
  <?php endforeach; ?>
</div>

<div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
  <div class="px-5 py-4 border-b border-neutral-200 flex items-center justify-between">
    <h2 class="font-serif-heading text-lg font-bold text-[#0a0a0a]">Recent Orders</h2>
    <a href="<?= url('/admin/orders') ?>" class="text-xs font-bold text-emerald-800 hover:underline">View all &rarr;</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-left text-xs">
      <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider text-[10px]">
        <tr>
          <th class="px-5 py-3">Order Ref</th>
          <th class="px-5 py-3">Customer</th>
          <th class="px-5 py-3">Total</th>
          <th class="px-5 py-3">Status</th>
          <th class="px-5 py-3">Date</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-neutral-100">
        <?php if (!$recentOrders): ?>
          <tr><td colspan="5" class="px-5 py-8 text-center text-neutral-400">No orders placed yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($recentOrders as $o): ?>
          <tr class="hover:bg-neutral-50">
            <td class="px-5 py-3 font-mono font-bold"><a href="<?= url('/admin/orders/' . urlencode($o['order_ref'])) ?>" class="hover:text-emerald-800"><?= e($o['order_ref']) ?></a></td>
            <td class="px-5 py-3"><?= e(trim($o['first_name'] . ' ' . $o['last_name'])) ?: e($o['email'] ?: $o['phone']) ?></td>
            <td class="px-5 py-3 font-bold text-[#8b1c1c]"><?= e(formatPrice((float) $o['total'], $o['currency'])) ?></td>
            <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-neutral-100 text-neutral-700"><?= e($o['status']) ?></span></td>
            <td class="px-5 py-3 text-neutral-500"><?= e(date('M j, Y', strtotime($o['created_at']))) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/layout-footer.php'; ?>
