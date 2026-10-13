<?php
/** Requires $ad (nullable), $errors, $form in scope. */
require __DIR__ . '/../layout-header.php';
$actionUrl = $ad ? url('/admin/ads/' . (int) $ad['id']) : url('/admin/ads');
$input = 'w-full mt-1 bg-white border border-neutral-300 rounded-lg p-2.5 text-sm focus:outline-none focus:border-black';
$label = 'text-[11px] font-bold text-neutral-600 uppercase';
?>

<form method="post" action="<?= $actionUrl ?>" enctype="multipart/form-data" class="max-w-2xl bg-white border border-neutral-200 rounded-xl shadow-sm p-6 space-y-5">
  <?= csrfField() ?>

  <?php foreach ($errors as $err): ?>
    <div class="bg-rose-50 border border-rose-300 text-rose-800 text-sm font-semibold rounded-lg p-3"><?= e($err) ?></div>
  <?php endforeach; ?>

  <div>
    <label class="<?= $label ?>">Title</label>
    <input type="text" name="title" required maxlength="191" value="<?= e($form['title']) ?>" placeholder="e.g. Weekend Flash Sale" class="<?= $input ?>" />
  </div>

  <div>
    <label class="<?= $label ?>">Short description</label>
    <textarea name="description" rows="3" placeholder="e.g. 30% off all outerwear until Sunday." class="<?= $input ?>"><?= e($form['description']) ?></textarea>
  </div>

  <div>
    <label class="<?= $label ?>">Badge <span class="text-neutral-400 font-normal normal-case">(optional, e.g. "30% OFF" or "NEW LAUNCH")</span></label>
    <input type="text" name="badge" maxlength="60" value="<?= e($form['badge']) ?>" class="<?= $input ?>" />
  </div>

  <div>
    <label class="<?= $label ?>">Image or video <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
    <?php if (!empty($ad['media_path'])): ?>
      <?php if ($ad['media_type'] === 'video'): ?>
        <video src="<?= e(imageUrl($ad['media_path'])) ?>" class="w-56 rounded-lg border border-neutral-200 mt-2" muted controls></video>
      <?php else: ?>
        <img src="<?= e(imageUrl($ad['media_path'])) ?>" alt="" class="w-56 rounded-lg border border-neutral-200 mt-2" />
      <?php endif; ?>
      <label class="flex items-center gap-2 mt-2 text-xs text-neutral-600"><input type="checkbox" name="remove_media" value="1" /> Remove current media</label>
    <?php endif; ?>
    <input type="file" name="media" accept="image/*,video/mp4,video/webm" class="w-full mt-2 text-sm" />
    <p class="text-[11px] text-neutral-400 mt-1">JPG, PNG, WEBP, GIF (up to 8MB) or MP4/WebM video (up to 25MB). Leave empty to keep the current file.</p>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="<?= $label ?>">Button label <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
      <input type="text" name="cta_label" maxlength="60" value="<?= e($form['cta_label']) ?>" placeholder="Shop now" class="<?= $input ?>" />
    </div>
    <div>
      <label class="<?= $label ?>">Button link <span class="text-neutral-400 font-normal normal-case">(optional)</span></label>
      <input type="text" name="link_url" maxlength="500" value="<?= e($form['link_url']) ?>" placeholder="/category/outerwear" class="<?= $input ?>" />
    </div>
  </div>

  <div class="border border-neutral-200 rounded-lg p-4 space-y-3">
    <label class="flex items-center gap-2 text-sm font-semibold text-neutral-800">
      <input type="checkbox" name="show_countdown" value="1" id="show_countdown" <?= $form['show_countdown'] === '1' ? 'checked' : '' ?> />
      Show a countdown timer
    </label>
    <div id="countdown-fields" class="grid grid-cols-2 gap-4 <?= $form['show_countdown'] === '1' ? '' : 'hidden' ?>">
      <div>
        <label class="<?= $label ?>">Countdown text</label>
        <input type="text" name="countdown_label" maxlength="80" value="<?= e($form['countdown_label']) ?>" placeholder="Offer ends in / Launching in" class="<?= $input ?>" />
      </div>
      <div>
        <label class="<?= $label ?>">Counts down to <span class="text-neutral-400 font-normal normal-case">(blank = when the ad ends)</span></label>
        <input type="datetime-local" name="countdown_to" value="<?= e($form['countdown_to']) ?>" class="<?= $input ?>" />
      </div>
    </div>
  </div>

  <div class="border border-neutral-200 rounded-lg p-4 space-y-3">
    <p class="<?= $label ?>">How long should the ad run?</p>
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="text-[11px] text-neutral-500">Starts <span class="text-neutral-400">(blank = now)</span></label>
        <input type="datetime-local" name="starts_at" value="<?= e($form['starts_at']) ?>" class="<?= $input ?>" />
      </div>
      <div>
        <label class="text-[11px] text-neutral-500">Duration</label>
        <input type="number" name="duration_value" min="1" required value="<?= (int) $form['duration_value'] ?>" class="<?= $input ?>" />
      </div>
      <div>
        <label class="text-[11px] text-neutral-500">Unit</label>
        <select name="duration_unit" class="<?= $input ?>">
          <option value="hours" <?= $form['duration_unit'] === 'hours' ? 'selected' : '' ?>>Hours</option>
          <option value="days" <?= $form['duration_unit'] === 'days' ? 'selected' : '' ?>>Days</option>
        </select>
      </div>
    </div>
    <p class="text-[11px] text-neutral-400">After this time the ad stops showing completely, even to visitors who already have the page open.</p>
  </div>

  <label class="flex items-center gap-2 text-sm font-semibold text-neutral-800">
    <input type="checkbox" name="is_active" value="1" <?= $form['is_active'] === '1' ? 'checked' : '' ?> />
    Active (untick to keep it saved but hidden)
  </label>

  <div class="flex items-center gap-3 pt-3 border-t border-neutral-100">
    <button type="submit" class="bg-[#0a0a0a] hover:bg-black text-white text-xs font-bold px-6 py-3 rounded-lg uppercase tracking-widest transition-colors cursor-pointer border border-neutral-300">
      <?= $ad ? 'Save Changes' : 'Create Ad' ?>
    </button>
    <a href="<?= url('/admin/ads') ?>" class="text-xs font-bold text-neutral-500 hover:text-neutral-800">Cancel</a>
  </div>
</form>

<script>
document.getElementById('show_countdown').addEventListener('change', function () {
  document.getElementById('countdown-fields').classList.toggle('hidden', !this.checked);
});
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
