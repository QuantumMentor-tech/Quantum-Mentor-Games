<?php
/**
 * QMGames Store — Admin: Add New Game
 * Step: 21 — Admin Game Management
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin           = getCurrentAdmin();
$pageTitle       = 'Add Game';
$adminPageTitle  = 'Add New Game';
$activeAdminPage = 'games';

const ADD_CSRF_KEY = 'admin_add_game';

$formErrors    = [];
$formSuccess   = false;
$submittedData = [];

/* ================================================================
   POST HANDLING
   ================================================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrfToken = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($csrfToken, ADD_CSRF_KEY)) {
        $formErrors[] = 'Session expired. Please refresh and try again.';
    }

    if (empty($formErrors)) {
        $validation = validateGameForm($_POST, $_FILES);
        if (!$validation['valid']) {
            $formErrors = $validation['errors'];
            $submittedData = $validation['data'];
            $submittedData['title']             = trim((string)($_POST['title']             ?? ''));
            $submittedData['slug']              = trim((string)($_POST['slug']              ?? ''));
            $submittedData['short_description'] = trim((string)($_POST['short_description']?? ''));
            $submittedData['full_description']  = trim((string)($_POST['full_description'] ?? ''));
            $submittedData['tags_raw']          = trim((string)($_POST['tags']             ?? ''));
        } else {
            $data = $validation['data'];

            /* Handle image uploads */
            $coverPath  = handleGameImageUpload($_FILES['cover_image']  ?? [], 'cover');
            $bannerPath = handleGameImageUpload($_FILES['banner_image'] ?? [], 'banner');

            $data['cover_image']  = $coverPath;
            $data['banner_image'] = $bannerPath;

            $newId = createGame($data);
            if ($newId !== false) {
                updateGameCategories($newId, $data['category_ids']);
                updateGameTags($newId, $data['tags']);
                redirect(siteUrl('admin/edit-game.php?id=' . $newId . '&created=1'));
            } else {
                $formErrors[] = 'Failed to create game. Please try again.';
                $submittedData = $data;
            }
        }
    }
}

$csrfToken     = generateCsrfToken(ADD_CSRF_KEY);
$allCategories = getAllAdminCategories();

require_once INCLUDES_PATH . '/admin-header.php';
?>

<div class="admin-wrapper">
  <?php require_once INCLUDES_PATH . '/admin-sidebar.php'; ?>

  <main class="admin-main" id="adminMain">

    <div class="admin-topbar">
      <div class="admin-topbar-left">
        <button class="admin-sidebar-toggle-btn" type="button"
                data-admin-sidebar-toggle aria-label="Toggle sidebar">☰</button>
        <h2 class="admin-topbar-title">Add New Game</h2>
      </div>
      <div class="admin-topbar-actions">
        <a href="<?= e(siteUrl('admin/games.php')) ?>"
           class="admin-btn admin-btn-ghost admin-btn-sm">← Back to Games</a>
      </div>
    </div>

    <div class="admin-content admin-form-page">

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
        Only add games that are legal, authorized, official, freeware, open-source,
        demo, indie-permission, official mirror, or permission-based. Do not add
        unauthorized copies or content that violates developer or publisher rights.
      </div>

      <form method="POST"
            action="<?= e(siteUrl('admin/add-game.php')) ?>"
            enctype="multipart/form-data"
            id="gameForm"
            novalidate>

        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <?php include __DIR__ . '/partials/game-form-fields.php'; ?>

        <div class="admin-form-card">
          <div class="admin-form-actions">
            <button type="submit" name="status_action" value="publish"
                    class="admin-btn admin-btn-primary"
                    id="gameFormSubmit">
              ✅ Create Game
            </button>
            <button type="submit" name="status_draft" value="1"
                    class="admin-btn admin-btn-ghost"
                    onclick="document.getElementById('status_field').value='draft';">
              📝 Save as Draft
            </button>
            <a href="<?= e(siteUrl('admin/games.php')) ?>"
               class="admin-btn admin-btn-ghost">Cancel</a>
          </div>
        </div>

      </form>

    </div>
  </main>
</div>

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
