<?php
/**
 * Shared download link form fields
 * Variables: $fd (form data), $gameOptions, $linkTypes, $statusOptions
 */
$fd = $fd ?? [];
$isEditMode = isset($editLink) && $editLink !== null;

$fv = function(string $key, string $fallback = '') use ($fd): string {
    return htmlspecialchars((string)($fd[$key] ?? $fallback), ENT_QUOTES | ENT_HTML5, 'UTF-8');
};
?>
<div class="admin-form-card">
  <h3 class="admin-section-title">🔗 Link Details</h3>
  <div class="admin-form-grid admin-form-grid-2">

    <div class="admin-form-group">
      <label class="admin-form-label" for="dl_game">
        Game <span class="admin-form-required">*</span>
      </label>
      <select id="dl_game" name="game_id" class="admin-form-control" required>
        <option value="">— Select a game —</option>
        <?php foreach ($gameOptions as $go): ?>
          <option value="<?= (int)$go['id'] ?>"
            <?= (int)($fd['game_id'] ?? 0) === (int)$go['id'] ? 'selected' : '' ?>>
            <?= e(truncateText($go['title'] ?? '', 50)) ?>
            (<?= e(ucfirst($go['status'] ?? '')) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <span class="admin-form-help">Draft and active games are listed.</span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="dl_type">
        Link Type <span class="admin-form-required">*</span>
      </label>
      <select id="dl_type" name="link_type" class="admin-form-control" required>
        <option value="">— Select type —</option>
        <?php foreach ($linkTypes as $v => $l): ?>
          <option value="<?= e($v) ?>"
            <?= $fv('link_type') === $v ? 'selected' : '' ?>>
            <?= e($l) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="admin-form-help">
        Torrent type is for official/legal/open-source torrents only.
      </span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="dl_title">
        Link Title <span class="admin-form-required">*</span>
      </label>
      <input type="text" id="dl_title" name="link_title"
             class="admin-form-control"
             placeholder="e.g. Official Download, Developer Mirror, Demo Build"
             value="<?= $fv('link_title') ?>"
             maxlength="180" required>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="dl_provider">
        Provider Name <span class="admin-form-required">*</span>
      </label>
      <input type="text" id="dl_provider" name="provider_name"
             class="admin-form-control"
             placeholder="e.g. Official Website, itch.io, GitHub Releases"
             value="<?= $fv('provider_name') ?>"
             maxlength="120" required>
    </div>

    <div class="admin-form-group admin-form-span-2">
      <label class="admin-form-label" for="dl_url">
        Download URL <span class="admin-form-required">*</span>
      </label>
      <input type="url" id="dl_url" name="download_url"
             class="admin-form-control admin-link-url-field"
             placeholder="https://example.com/download/game-file.zip"
             value="<?= $fv('download_url') ?>"
             maxlength="2000"
             data-url-scheme-check
             required>
      <span class="admin-form-help" id="urlSchemeHelp">
        ✅ Use https:// or http:// URLs. Magnet links allowed for Torrent type.
        Unsafe schemes (javascript:, ftp:, data:) are rejected.
      </span>
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="dl_size">File Size</label>
      <input type="text" id="dl_size" name="file_size"
             class="admin-form-control"
             placeholder="e.g. 500 MB, 2.5 GB"
             value="<?= $fv('file_size') ?>"
             maxlength="80">
    </div>

    <div class="admin-form-group">
      <label class="admin-form-label" for="dl_status">
        Status <span class="admin-form-required">*</span>
      </label>
      <select id="dl_status" name="status" class="admin-form-control" required>
        <?php foreach ($statusOptions as $v => $l): ?>
          <option value="<?= e($v) ?>"
            <?= $fv('status','active') === $v ? 'selected' : '' ?>>
            <?= e($l) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <span class="admin-form-help">
        Active = visible publicly. Under Review = hidden until verified.
      </span>
    </div>

  </div>
</div>

<!-- Legal Confirmation -->
<div class="admin-form-card admin-legal-confirm-box">
  <label class="admin-toggle-row" style="gap:.75rem;font-size:.9rem;cursor:pointer;">
    <input type="checkbox" name="legal_confirm" value="1"
           required style="width:16px;height:16px;accent-color:var(--success);">
    <span>
      <strong>⚖️ I confirm</strong> this download link is legal, authorized,
      official, freeware, open-source, demo, indie-permission, store-based, or
      permission-based. I am not adding unauthorized game copies,
      license-bypassing files, or unsafe download links.
    </span>
  </label>
</div>
