<?php
/**
 * QMGames Store — Admin: Site Settings
 * Step: 19 — Admin Login System (protected placeholder)
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin         = getCurrentAdmin();
$adminPageTitle = 'Site Settings';

require_once INCLUDES_PATH . '/admin-header.php';
?>

<div class="admin-wrapper">
  <?php require_once INCLUDES_PATH . '/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-topbar">
      <h2 style="font-size:1.05rem;font-weight:700;color:var(--text-heading);">
        <?= e($adminPageTitle) ?>
      </h2>
      <span style="font-size:.82rem;color:var(--text-muted);">
        <?= e($admin['name'] ?? 'Admin') ?>
      </span>
    </div>

    <div class="admin-content">
      <div class="admin-page-header">
        <h1 style="font-size:1.3rem;font-weight:800;margin-bottom:.35rem;">
          <?= e($adminPageTitle) ?>
        </h1>
        <p class="text-muted text-sm">Settings panel will be built in a later step.</p>
      </div>

      <div class="admin-placeholder-card" style="text-align:center;padding:3rem 2rem;">
        <p class="text-muted">&#128679; This section is under construction.</p>
        <a href="<?= e(siteUrl('admin/dashboard.php')) ?>"
           class="admin-btn admin-btn-primary" style="margin-top:1rem;display:inline-flex;">
          ← Back to Dashboard
        </a>
      </div>
    </div>
  </main>
</div>

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
