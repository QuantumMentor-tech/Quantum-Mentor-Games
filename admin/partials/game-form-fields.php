<?php
/**
 * Shared game form fields — included by add-game.php and edit-game.php
 * Variables available:
 *   $submittedData  array   — posted values for re-display on error (or existing game)
 *   $allCategories  array   — all categories for checkboxes
 *   $selectedCatIds array   — category IDs currently linked (edit mode)
 *   $selectedTags   string  — comma-separated tags (edit mode)
 *   $isEdit         bool    — true when in edit mode
 *   $game           array   — current game row (edit mode)
 */

$isEdit         = $isEdit         ?? false;
$game           = $game           ?? [];
$selectedCatIds = $selectedCatIds ?? [];
$selectedTags   = $selectedTags   ?? ($submittedData['tags_raw'] ?? '');

/* Pre-fill helpers */
$v = function(string $key, string $fallback = ''): string {
    global $submittedData, $game;
    $val = $submittedData[$key] ?? $game[$key] ?? $fallback;
    return htmlspecialchars((string)$val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
};

$licenseOptions = [
    'freeware'         => 'Freeware',
    'open_source'      => 'Open Source',
    'demo'             => 'Demo',
    'official_mirror'  => 'Official Mirror',
    'indie_permission' => 'Indie Permission',
    'paid_future'      => 'Paid Future',
    'other_authorized' => 'Other Authorized',
];

$platformOptions = ['Windows PC','Linux','Mac','Browser','Multi-platform','Other'];
$statusOptions   = ['draft'=>'Draft','active'=>'Active','inactive'=>'Inactive','archived'=>'Archived'];
?>

<!-- ── Basic Information ── -->
<div class="admin-form-card">
  <h3 class="admin-section-title">📋 Basic Information</h3>

  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_title">
        Title <span class="admin-form-required">*</span>
      </label>
      <input type="text" id="f_title" name="title"
             class="admin-form-control"
             placeholder="Game title"
             value="<?= $v('title') ?>"
             maxlength="180"
             data-slug-source
             required>
      <span class="admin-form-help">Max 180 characters</span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_slug">Slug</label>
      <input type="text" id="f_slug" name="slug"
             class="admin-form-control"
             placeholder="auto-generated-from-title"
             value="<?= $v('slug') ?>"
             maxlength="220"
             data-slug-target>
      <span class="admin-form-help">Auto-generated if empty. Lowercase, hyphens only.</span>
    </div>

    <div class="admin-form-group admin-form-span-2">
      <label class="admin-form-label" for="f_short">Short Description</label>
      <textarea id="f_short" name="short_description"
                class="admin-form-control"
                rows="2"
                placeholder="Brief one-liner description for listing cards..."
                maxlength="350"
                data-character-counter
                data-max-length="350"><?= $v('short_description') ?></textarea>
      <span class="admin-form-help">
        <span id="shortDescCount">0</span> / 350 characters
      </span>
    </div>

    <div class="admin-form-group admin-form-span-2">
      <label class="admin-form-label" for="f_full">Full Description</label>
      <textarea id="f_full" name="full_description"
                class="admin-form-control"
                rows="6"
                placeholder="Full game description..."><?= $v('full_description') ?></textarea>
      <span class="admin-form-help">Plain text or basic HTML (p, strong, em, ul, ol, li). No scripts.</span>
    </div>

  </div>
</div>

<!-- ── Media ── -->
<div class="admin-form-card">
  <h3 class="admin-section-title">🖼️ Media</h3>

  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_cover">Cover Image</label>
      <?php
      $coverVal = $submittedData['cover_image'] ?? $game['cover_image'] ?? '';
      if ($coverVal !== ''):
      ?>
        <div class="admin-image-preview">
          <img src="<?= e(siteUrl($coverVal)) ?>"
               alt="Current cover" class="admin-current-image">
          <span class="admin-form-help">Current cover. Upload new to replace.</span>
        </div>
      <?php endif; ?>
      <input type="file" id="f_cover" name="cover_image"
             class="admin-form-control" accept="image/jpeg,image/png,image/webp"
             data-image-preview="coverPreview">
      <img id="coverPreview" class="admin-image-preview-img" src="" alt=""
           style="display:none;margin-top:.5rem;max-width:160px;border-radius:6px;">
      <span class="admin-form-help">JPG, PNG, or WebP. Max 2 MB.</span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_banner">Banner Image</label>
      <?php
      $bannerVal = $submittedData['banner_image'] ?? $game['banner_image'] ?? '';
      if ($bannerVal !== ''):
      ?>
        <div class="admin-image-preview">
          <img src="<?= e(siteUrl($bannerVal)) ?>"
               alt="Current banner" class="admin-current-image admin-current-banner">
          <span class="admin-form-help">Current banner. Upload new to replace.</span>
        </div>
      <?php endif; ?>
      <input type="file" id="f_banner" name="banner_image"
             class="admin-form-control" accept="image/jpeg,image/png,image/webp"
             data-image-preview="bannerPreview">
      <img id="bannerPreview" class="admin-image-preview-img" src="" alt=""
           style="display:none;margin-top:.5rem;max-width:260px;border-radius:6px;">
      <span class="admin-form-help">JPG, PNG, or WebP. Max 4 MB. Wide format preferred.</span>
    </div>

    <div class="admin-form-group admin-form-span-2">
      <label class="admin-form-label" for="f_trailer">Trailer URL</label>
      <input type="url" id="f_trailer" name="trailer_url"
             class="admin-form-control"
             placeholder="https://www.youtube.com/watch?v=..."
             value="<?= $v('trailer_url') ?>"
             maxlength="500">
      <span class="admin-form-help">YouTube URLs only. Leave empty if not available.</span>
    </div>

  </div>
</div>

<!-- ── Game Details ── -->
<div class="admin-form-card">
  <h3 class="admin-section-title">🎮 Game Details</h3>

  <div class="admin-form-grid admin-form-grid-3">

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_dev">Developer</label>
      <input type="text" id="f_dev" name="developer"
             class="admin-form-control"
             placeholder="Developer name"
             value="<?= $v('developer') ?>"
             maxlength="150">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_pub">Publisher</label>
      <input type="text" id="f_pub" name="publisher"
             class="admin-form-control"
             placeholder="Publisher name"
             value="<?= $v('publisher') ?>"
             maxlength="150">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_ver">Version</label>
      <input type="text" id="f_ver" name="version"
             class="admin-form-control"
             placeholder="e.g. 1.4.2"
             value="<?= $v('version') ?>"
             maxlength="80">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_date">Release Date</label>
      <input type="date" id="f_date" name="release_date"
             class="admin-form-control"
             value="<?= $v('release_date') ?>">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_size">Game Size</label>
      <input type="text" id="f_size" name="game_size"
             class="admin-form-control"
             placeholder="e.g. 500 MB or 2.5 GB"
             value="<?= $v('game_size') ?>"
             maxlength="80">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_plat">Platform</label>
      <select id="f_plat" name="platform" class="admin-form-control">
        <?php foreach ($platformOptions as $p): ?>
          <option value="<?= e($p) ?>"
            <?= ($v('platform','Windows PC') === $p) ? 'selected' : '' ?>>
            <?= e($p) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

  </div>
</div>

<!-- ── Legal and Status ── -->
<div class="admin-form-card">
  <h3 class="admin-section-title">⚖️ Legal and Status</h3>

  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_license">
        License Type <span class="admin-form-required">*</span>
      </label>
      <select id="f_license" name="license_type" class="admin-form-control" required>
        <?php foreach ($licenseOptions as $val => $label): ?>
          <option value="<?= e($val) ?>"
            <?= ($v('license_type','freeware') === $val) ? 'selected' : '' ?>>
            <?= e($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="admin-form-help">
        Only legal distribution license types. No unauthorized options.
      </span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_status">
        Status <span class="admin-form-required">*</span>
      </label>
      <select id="status_field" name="status" class="admin-form-control" required>
        <?php foreach ($statusOptions as $val => $label): ?>
          <option value="<?= e($val) ?>"
            <?= ($v('status','draft') === $val) ? 'selected' : '' ?>>
            <?= e($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="admin-form-help">Draft = not public. Active = visible on public site.</span>
    </div>

    <div class="admin-form-group admin-form-span-2">
      <label class="admin-form-label">Flags</label>
      <div class="admin-checkbox-row">
        <label class="admin-toggle-row">
          <input type="checkbox" name="is_featured" value="1"
            <?= (!empty($submittedData['is_featured']) || !empty($game['is_featured'])) ? 'checked' : '' ?>>
          <span>⭐ Featured</span>
        </label>
        <label class="admin-toggle-row">
          <input type="checkbox" name="is_trending" value="1"
            <?= (!empty($submittedData['is_trending']) || !empty($game['is_trending'])) ? 'checked' : '' ?>>
          <span>📈 Trending</span>
        </label>
        <label class="admin-toggle-row">
          <input type="checkbox" name="is_low_end_pc" value="1"
            <?= (!empty($submittedData['is_low_end_pc']) || !empty($game['is_low_end_pc'])) ? 'checked' : '' ?>>
          <span>🖥️ Low-End PC Friendly</span>
        </label>
      </div>
    </div>

  </div>
</div>

<!-- ── Categories and Tags ── -->
<div class="admin-form-card">
  <h3 class="admin-section-title">📁 Categories and Tags</h3>

  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label">Categories</label>
      <div class="admin-checkbox-grid">
        <?php foreach ($allCategories as $cat): ?>
          <label class="admin-toggle-row">
            <input type="checkbox"
                   name="categories[]"
                   value="<?= (int)$cat['id'] ?>"
              <?= in_array((int)$cat['id'], array_map('intval', $selectedCatIds), true) ? 'checked' : '' ?>>
            <span><?= e($cat['name']) ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_tags">Tags</label>
      <input type="text" id="f_tags" name="tags"
             class="admin-form-control"
             placeholder="offline, low-end pc, demo, windows..."
             value="<?= e($selectedTags) ?>"
             maxlength="500">
      <span class="admin-form-help">
        Comma-separated. Tags will be created if they don't exist.
        Max 100 characters per tag.
      </span>
    </div>

  </div>
</div>

<!-- ── SEO ── -->
<div class="admin-form-card">
  <h3 class="admin-section-title">🔍 SEO (Optional)</h3>

  <div class="admin-form-grid admin-form-grid-1">

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_meta_title">Meta Title</label>
      <input type="text" id="f_meta_title" name="meta_title"
             class="admin-form-control"
             placeholder="Custom page title (overrides game title)"
             value="<?= $v('meta_title') ?>"
             maxlength="255">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="f_meta_desc">Meta Description</label>
      <textarea id="f_meta_desc" name="meta_description"
                class="admin-form-control"
                rows="2"
                placeholder="Custom meta description for search engines..."
                maxlength="350"
                data-character-counter
                data-max-length="350"><?= $v('meta_description') ?></textarea>
      <span class="admin-form-help">
        <span id="metaDescCount">0</span> / 350 characters
      </span>
    </div>

  </div>
</div>
