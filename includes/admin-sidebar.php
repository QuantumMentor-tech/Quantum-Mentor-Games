<?php
/**
 * QMGames Store - Admin Sidebar
 * Step: 20 — Admin Dashboard
 * Uses $activeAdminPage for active link highlighting.
 */

if (!defined('QMGAMES_INIT')) {
    die('Direct access not permitted. Load init.php first.');
}

$adminUser = getCurrentAdmin();

/* $activeAdminPage is set in each admin page before including header */
$activePage = $activeAdminPage ?? '';

$sidebarLinks = [
    ['key'=>'dashboard',     'url'=>'admin/dashboard.php',      'icon'=>'📊', 'label'=>'Dashboard'],
    ['key'=>'games',         'url'=>'admin/games.php',          'icon'=>'🎮', 'label'=>'Games'],
    ['key'=>'add-game',      'url'=>'admin/add-game.php',       'icon'=>'➕', 'label'=>'Add Game'],
    ['key'=>'categories',    'url'=>'admin/categories.php',     'icon'=>'📁', 'label'=>'Categories'],
    ['key'=>'download-links','url'=>'admin/download-links.php', 'icon'=>'🔗', 'label'=>'Download Links'],
    ['key'=>'reports',       'url'=>'admin/reports.php',        'icon'=>'🔔', 'label'=>'Reports'],
    ['key'=>'messages',      'url'=>'admin/messages.php',       'icon'=>'💬', 'label'=>'Messages'],
    ['key'=>'settings',      'url'=>'admin/settings.php',       'icon'=>'⚙️', 'label'=>'Settings'],
];
?>
<aside class="admin-sidebar"
       id="adminSidebar"
       data-admin-sidebar
       role="navigation"
       aria-label="Admin Navigation">

  <div class="admin-sidebar-brand">
    <span class="qm">QM</span>Games Admin
  </div>

  <?php if ($adminUser): ?>
    <div class="admin-sidebar-user">
      <span class="admin-sidebar-user-avatar" aria-hidden="true">👤</span>
      <div>
        <p class="admin-sidebar-user-name">
          <?= e($adminUser['name']) ?>
        </p>
        <p class="admin-sidebar-user-role">
          <?= e(ucfirst(str_replace('_', ' ', $adminUser['role']))) ?>
        </p>
      </div>
    </div>
  <?php endif; ?>

  <nav class="admin-sidebar-nav" aria-label="Admin menu">
    <?php foreach ($sidebarLinks as $link):
      $isActive = ($activePage === $link['key']);
    ?>
    <a href="<?= e(siteUrl($link['url'])) ?>"
       class="admin-sidebar-link<?= $isActive ? ' active' : '' ?>"
       <?= $isActive ? 'aria-current="page"' : '' ?>>
      <span class="admin-sidebar-link-icon" aria-hidden="true">
        <?= $link['icon'] ?>
      </span>
      <span><?= e($link['label']) ?></span>
    </a>
    <?php endforeach; ?>

    <hr class="admin-sidebar-divider">

    <a href="<?= e(siteUrl('index.php')) ?>"
       class="admin-sidebar-link"
       target="_blank" rel="noopener">
      <span class="admin-sidebar-link-icon" aria-hidden="true">🌐</span>
      <span>View Website</span>
    </a>

    <a href="<?= e(siteUrl('admin/logout.php')) ?>"
       class="admin-sidebar-link admin-sidebar-logout">
      <span class="admin-sidebar-link-icon" aria-hidden="true">🚪</span>
      <span>Logout</span>
    </a>
  </nav>

</aside>
