<?php
/**
 * QMGames Store — Admin: Edit Game
 * Step: 21 — Admin Game Management
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin           = getCurrentAdmin();
$pageTitle       = 'Edit Game';
$adminPageTitle  = 'Edit Game';
$activeAdminPage = 'games';

const EDIT_CSRF_KEY = 'admin_edit_game';

/* ── Load game ── */
$gameId = (int)($_GET['id'] ?? 0);
$game   = ($gameId > 0) ? getAdminGameById($gameId) : null;

if ($game === null) {
    require_once INCLUDES_PATH . '/admin-header.php';
    echo '<div class="admin-wrapper">';
    require_once INCLUDES_PATH . '/admin-sidebar.php';
    echo '<main class="admin-main"><div class="admin-content">
          <div class="admin-empty-state"><span>🎮</span>
          <p>Game not found.</p>
          <a href="' . e(siteUrl('admin/games.php')) . '"
             class="admin-btn admin-btn-primary admin-btn-sm">Back to Games</a>
          </div></div></main></div>';
    require_once INCLUDES_PATH . '/admin-footer.php';
    exit;
}

$formErrors    = [];
$submittedData = [];
$selectedCatIds= getAdminGameCategories($gameId);
$selectedTags  = implode(', ', getAdminGameTags($gameId));
$isEdit        = true;

/* Flash messages */
$flashMsg  = '';
$flashType = 'info';
if (isset($_GET['created'])) { $flashMsg = '✅ Game created successfully!'; $flashType = 'success'; }
if (isset($_GET['updated'])) { $flashMsg = '✅ Game updated successfully!'; $flashType = 'success'; }

/* ================================================================
   POST HANDLING
   ================================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrfToken = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($csrfToken, EDIT_CSRF_KEY)) {
        $formErrors[] = 'Session expired. Please refresh and try again.';
    }

    if (empty($formErrors)) {
        $validation = validateGameForm($_POST, $_FILES, $gameId);
        if (!$validation['valid']) {
            $formErrors    = $validation['errors'];
            $submittedData = $validation['data'];
            $submittedData['title']             = trim((string)($_POST['title']             ?? ''));
            $submittedData['slug']              = trim((string)($_POST['slug']              ?? ''));
            $submittedData['short_description'] = trim((string)($_POST['short_description']?? ''));
            $submittedData['full_description']  = trim((string)($_POST['full_description'] ?? ''));
            $submittedData['tags_raw']          = trim((string)($_POST['tags']             ?? ''));
        } else {
            $data = $validation['data'];

            /* Handle image uploads — keep existing if no new file */
            $newCover  = handleGameImageUpload($_FILES['cover_image']  ?? [], 'cover');
            $newBanner = handleGameImageUpload($_FILES['banner_image'] ?? [], 'banner');

            $data['cover_image']  = $newCover  ?? $game['cover_image'];
            $data['banner_image'] = $newBanner ?? $game['banner_image'];

            if (updateGame($gameId, $data)) {
                updateGameCategories($gameId, $data['category_ids']);
                updateGameTags($gameId, $data['tags']);
                redirect(siteUrl('admin/edit-game.php?id=' . $gameId . '&updated=1'));
            } else {
                $formErrors[] = 'Failed to update game. Please try again.';
                $submittedData = $data;
            }
        }
    }

    /* Refresh selected cats/tags from POST on error */
    if (!empty($formErrors)) {
        $selectedCatIds = array_map('intval', (array)($_POST['categories'] ?? []));
        $selectedTags   = trim((string)($_POST['tags'] ?? ''));
    }
}

$csrfToken     = generateCsrfToken(EDIT_CSRF_KEY);
$allCategories = getAllAdminCategories();

/* Re-load game after successful redirect won't reach here, but after error we use $game */
require_once INCLUDES_PATH . '/admin-header.php';
?>

<div class="admin-wrapper">
  <?php require_once INCLUDES_PATH . '/admin-sidebar.php'; ?>

  <main class="admin-main" id="adminMain">

    <div class="admin-topbar">
      <div class="admin-topbar-left">
        <button class="admin-sidebar-toggle-btn" type="button"
                data-admin-sidebar-toggle aria-label="Toggle sidebar">☰</button>
        <h2 class="admin-topbar-title">
          Edit: <?= e(truncateText($game['title'] ?? '', 35)) ?>
        </h2>
      </div>
      <div class="admin-topbar-actions">
        <?php if (($game['status'] ?? '') === 'active' && !empty($game['slug'])): ?>
          <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug']))) ?>"
             target="_blank" rel="noopener"
             class="admin-btn admin-btn-ghost admin-btn-sm">
            🌐 View Public
          </a>
        <?php endif; ?>
        <a href="<?= e(siteUrl('admin/games.php')) ?>"
           class="admin-btn admin-btn-ghost admin-btn-sm">← Back to Games</a>
      </div>
    </div>

    <div class="admin-content admin-form-page">

      <?php if ($flashMsg !== ''): ?>
        <div class="admin-alert admin-alert-<?= e($flashType) ?>"
             style="margin-bottom:1.25rem;">
          <?= e($flashMsg) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($formErrors)): ?>
        <div class="admin-alert admin-alert-danger" style="margin-bottom:1.25rem;">
          <?php foreach ($formErrors as $err): ?>
            <div><?= e($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Legal notice -->
      <div class="admin-legal-note">
        <strong>⚖️ Legal Listing Reminder:</strong>
        Only edit games that are legal, authorized, official, freeware, open-source,
        demo, indie-permission, official mirror, or permission-based.
      </div>

      <form method="POST"
            action="<?= e(siteUrl('admin/edit-game.php?id=' . $gameId)) ?>"
            enctype="multipart/form-data"
            id="gameForm"
            novalidate>

        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <?php include __DIR__ . '/partials/game-form-fields.php'; ?>

        <div class="admin-form-card">
          <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary"
                    id="gameFormSubmit">
              💾 Save Changes
            </button>
            <?php if (($game['status'] ?? '') === 'active' && !empty($game['slug'])): ?>
              <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug']))) ?>"
                 target="_blank" rel="noopener"
                 class="admin-btn admin-btn-ghost">
                🌐 View Public Game
              </a>
            <?php endif; ?>
            <a href="<?= e(siteUrl('admin/games.php')) ?>"
               class="admin-btn admin-btn-ghost">Cancel</a>
          </div>
        </div>

      </form>

    </div>
  </main>
</div>

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
