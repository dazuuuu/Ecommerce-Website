    </main>
  </div>

  <?php
  // Mobile app tab bar. Tabs map to nav ids; anything else lives under "More".
  $tabNav = [
      'dashboard' => ['Home', url('/admin'), '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />'],
      'products' => ['Products', url('/admin/products'), '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'],
      'sales' => null,
      'orders' => ['Orders', url('/admin/orders'), '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />'],
  ];
  $currentNav = $activeNav ?? '';
  $moreActive = !array_key_exists($currentNav, $tabNav);
  ?>
  <nav class="admin-tabbar lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur border-t border-slate-200 shadow-[0_-4px_20px_rgba(15,23,42,0.06)]">
    <div class="grid grid-cols-5 items-end max-w-lg mx-auto">
      <?php foreach ($tabNav as $id => $tab): ?>
        <?php if ($tab === null): ?>
          <a href="<?= url('/admin/sales') ?>" class="flex flex-col items-center -mt-6 pb-2" aria-label="Record sale">
            <span class="w-14 h-14 rounded-full flex items-center justify-center text-white shadow-lg shadow-blue-600/40 border-4 border-white <?= $currentNav === 'sales' ? 'bg-blue-800' : 'bg-blue-600' ?>">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            </span>
            <span class="text-[11px] font-medium mt-0.5 <?= $currentNav === 'sales' ? 'text-blue-600' : 'text-slate-500' ?>">Sale</span>
          </a>
        <?php else: [$label, $href, $icon] = $tab; $on = $currentNav === $id; ?>
          <a href="<?= e($href) ?>" class="admin-tab flex flex-col items-center gap-0.5 pt-2.5 pb-2 <?= $on ? 'text-blue-600' : 'text-slate-500' ?>">
            <span class="px-4 py-1 rounded-full <?= $on ? 'bg-blue-50' : '' ?>"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $icon ?></svg></span>
            <span class="text-[11px] font-medium"><?= e($label) ?></span>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
      <button type="button" id="admin-more-btn" class="admin-tab flex flex-col items-center gap-0.5 pt-2.5 pb-2 <?= $moreActive ? 'text-blue-600' : 'text-slate-500' ?>">
        <span class="px-4 py-1 rounded-full <?= $moreActive ? 'bg-blue-50' : '' ?>"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><circle cx="5" cy="12" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="19" cy="12" r="2"/></svg></span>
        <span class="text-[11px] font-medium">More</span>
      </button>
    </div>
  </nav>

  <!-- "More" bottom sheet -->
  <div id="admin-more-sheet" class="lg:hidden fixed inset-0 z-40 hidden">
    <div class="absolute inset-0 bg-slate-900/40" data-close-more></div>
    <div class="admin-sheet-panel absolute bottom-0 inset-x-0 bg-white rounded-t-3xl px-5 pt-3 shadow-2xl">
      <div class="w-10 h-1.5 bg-slate-300 rounded-full mx-auto mb-4"></div>
      <div class="grid grid-cols-4 gap-3">
        <?php foreach ($navItems as $item): if (array_key_exists($item['id'], $tabNav)) continue; $on = $currentNav === $item['id']; ?>
          <a href="<?= e($item['href']) ?>" class="flex flex-col items-center gap-1.5 py-2 rounded-2xl <?= $on ? 'bg-blue-50 text-blue-600' : 'text-slate-700 active:bg-slate-100' ?>">
            <span class="w-12 h-12 rounded-2xl flex items-center justify-center <?= $on ? 'bg-blue-600 text-white' : 'bg-slate-100' ?>">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><?= $item['icon'] ?></svg>
            </span>
            <span class="text-xs font-medium text-center leading-tight"><?= e($item['label']) ?></span>
          </a>
        <?php endforeach; ?>
        <a href="<?= url('/') ?>" target="_blank" class="flex flex-col items-center gap-1.5 py-2 rounded-2xl text-slate-700 active:bg-slate-100">
          <span class="w-12 h-12 rounded-2xl flex items-center justify-center bg-slate-100"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg></span>
          <span class="text-xs font-medium text-center leading-tight">Storefront</span>
        </a>
        <a href="<?= url('/admin/logout') ?>" class="flex flex-col items-center gap-1.5 py-2 rounded-2xl text-rose-600 active:bg-rose-50">
          <span class="w-12 h-12 rounded-2xl flex items-center justify-center bg-rose-50"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg></span>
          <span class="text-xs font-medium text-center leading-tight">Logout</span>
        </a>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var sheet = document.getElementById('admin-more-sheet');
      var moreBtn = document.getElementById('admin-more-btn');
      function setSheet(open) {
        sheet.classList.toggle('hidden', !open);
        document.body.style.overflow = open ? 'hidden' : '';
      }
      if (moreBtn) moreBtn.addEventListener('click', function () { setSheet(true); });
      sheet.querySelectorAll('[data-close-more]').forEach(function (el) { el.addEventListener('click', function () { setSheet(false); }); });
      document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setSheet(false); });
      document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
          var search = document.getElementById('admin-search');
          if (search) { e.preventDefault(); search.focus(); }
        }
      });
    })();
  </script>
</body>
</html>
