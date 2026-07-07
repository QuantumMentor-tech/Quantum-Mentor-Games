<?php
/**
 * Shared category form fields
 * Variables: $fd (form data / existing category row), $statusOptions
 */
$fd = $fd ?? [];
$fv = function(string $key, string $fb = '') use ($fd): string {
    return htmlspecialchars((string)($fd[$key] ?? $fb), ENT_QUOTES | ENT_HTML5, 'UTF-8');
};
?>

<!-- Basic Information -->
<div class="admin-form-card admin-category-form-card">
  <h3 class="admin-section-title">📋 Basic Information</h3>
  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label" for="cat_name">
        Category Name <span class="admin-form-required">*</span>
      </label>
      <input type="text" id="cat_name" name="name"
             class="admin-form-control"
             placeholder="e.g. Action, Racing, Open Source Games, Demo Games"
             value="<?= $fv('name') ?>"
             maxlength="120"
             data-slug-source
             required>
      <span class="admin-form-help">Min 2, max 120 characters.</span>
    </div>

    <div class="admin-form-group admin-slug-row">
      <label class="admin-form-label" for="cat_slug">Slug</label>
      <input type="text" id="cat_slug" name="slug"
             class="admin-form-control"
             placeholder="auto-generated-from-name"
             value="<?= $fv('slug') ?>"
             maxlength="150"
             data-slug-target>
      <span class="admin-form-help">
        Auto-generated if empty. Lowercase, hyphens only. Must be unique.
      </span>
    </div>

    <div class="admin-form-group admin-form-span-2">
      <label class="admin-form-label" for="cat_desc">Description</label>
      <textarea id="cat_desc" name="description"
                class="admin-form-control"
                rows="3"
                placeholder="Describe what this category contains..."
                maxlength="600"
                data-character-counter
                data-max-length="600"><?= $fv('description') ?></textarea>
      <span class="admin-form-help">
        <span id="catDescCount">0</span> / 600 characters. Optional but recommended.
      </span>
    </div>

  </div>
</div>

<!-- Display Options -->
<div class="admin-form-card admin-category-form-card">
  <h3 class="admin-section-title">🎨 Display Options</h3>
  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label" for="cat_icon">Icon / Symbol</label>
      <div style="display:flex;gap:.65rem;align-items:center;">
        <input type="text" id="cat_icon" name="icon"
               class="admin-form-control"
               placeholder="e.g. 🎮 ⚔️ 🏎️ 💻"
               value="<?= $fv('icon') ?>"
               maxlength="80"
               data-icon-source
               style="flex:1;">
        <span id="catIconPreview" class="admin-category-icon admin-icon-preview"
              data-icon-preview style="font-size:1.8rem;min-width:2rem;text-align:center;">
          <?= $fv('icon') !== '' ? $fv('icon') : '🏷️' ?>
        </span>
      </div>
      <span class="admin-form-help">
        Emoji, text symbol, or short icon name. Max 80 characters.
      </span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="cat_sort">Sort Order</label>
      <input type="number" id="cat_sort" name="sort_order"
             class="admin-form-control"
             min="0" max="9999" step="1"
             value="<?= $fv('sort_order', '0') ?>">
      <span class="admin-form-help">
        Lower number = displayed first. Default: 0.
      </span>
    </div>

  </div>
</div>

<!-- Status -->
<div class="admin-form-card admin-category-form-card">
  <h3 class="admin-section-title">⚙️ Status</h3>
  <div class="admin-form-group">
    <label class="admin-form-label" for="cat_status">
      Status <span class="admin-form-required">*</span>
    </label>
    <select id="cat_status" name="status" class="admin-form-control" required>
      <?php foreach ($statusOptions as $v => $l): ?>
        <option value="<?= e($v) ?>"
          <?= $fv('status', 'active') === $v ? 'selected' : '' ?>>
          <?= e($l) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <span class="admin-form-help">
      Active = visible on public category pages.
      Inactive = hidden from public.
      Archived = soft-deleted (games not removed).
    </span>
  </div>
</div>
