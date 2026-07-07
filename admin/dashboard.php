<?php
/**
 * QMGames Store — Admin Dashboard
 * Step: 20 — Admin Dashboard
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin          = getCurrentAdmin();
$adminPageTitle = 'Admin Dashboard';
$activeAdminPage= 'dashboard';

/* ── Load dashboard data ── */
$stats        = getAdminDashboardStats();
$recentGames  = getRecentAdminGames(5);
$recentReports= getRecentDownloadReports(5);
$recentMsgs   = getRecentContactMessages(5);
$sysStatus    = getAdminSystemStatus();

require_once INCLUDES_PATH . '/admin-header.php';
?>

<div class="admin-wrapper">
  <?php require_once INCLUDES_PATH . '/admin-sidebar.php'; ?>

  <main class="admin-main" id="adminMain">

    <!-- ── Top bar ── -->
    <div class="admin-topbar">
      <div class="admin-topbar-left">
        <!-- Mobile sidebar toggle -->
        <button class="admin-sidebar-toggle-btn"
                type="button"
                data-admin-sidebar-toggle
                aria-label="Toggle sidebar"
                title="Toggle sidebar">
          ☰
        </button>
        <h2 class="admin-topbar-title">Dashboard</h2>
      </div>
      <div class="admin-topbar-actions">
        <a href="<?= e(siteUrl('index.php')) ?>"
           target="_blank" rel="noopener"
           class="admin-btn admin-btn-ghost admin-btn-sm">
          🌐 View Site
        </a>
        <button class="theme-toggle admin-btn admin-btn-ghost admin-btn-sm"
                type="button"
                data-theme-toggle
                aria-label="Toggle theme">
          <span data-theme-icon aria-hidden="true">☀️</span>
        </button>
        <a href="<?= e(siteUrl('admin/logout.php')) ?>"
           class="admin-btn admin-btn-ghost admin-btn-sm">
          🚪 Logout
        </a>
      </div>
    </div>

    <div class="admin-content">

      <!-- ── Welcome header ── -->
      <div class="admin-welcome-card">
        <div class="admin-welcome-info">
          <h1 class="admin-welcome-title">
            Welcome back, <?= e($admin['name'] ?? 'Admin') ?>!
          </h1>
          <p class="admin-welcome-sub">
            Here is the latest overview of <?= e(SITE_SHORT_NAME) ?>.
          </p>
          <ul class="admin-meta-list">
            <li>
              <span class="admin-meta-icon" aria-hidden="true">📅</span>
              <?= e(date('l, d F Y')) ?>
            </li>
            <li>
              <span class="admin-meta-icon" aria-hidden="true">🔑</span>
              Role: <?= e(ucfirst(str_replace('_', ' ', $admin['role'] ?? 'admin'))) ?>
            </li>
          </ul>
        </div>
        <div class="admin-welcome-actions">
          <a href="<?= e(siteUrl('admin/add-game.php')) ?>"
             class="admin-btn admin-btn-primary">
            ➕ Add New Game
          </a>
          <a href="<?= e(siteUrl('admin/reports.php')) ?>"
             class="admin-btn admin-btn-ghost">
            🔔 View Reports
            <?php if ($stats['pending_reports'] > 0): ?>
              <span class="admin-badge-count">
                <?= e(formatNumberShort($stats['pending_reports'])) ?>
              </span>
            <?php endif; ?>
          </a>
        </div>
      </div>


      <!-- ══════════════════════════════════════════
           STAT CARDS
           ══════════════════════════════════════════ -->
      <div class="admin-stats-grid">

        <?php
        $statCards = [
          ['icon'=>'🎮','label'=>'Total Games',      'value'=>$stats['total_games'],         'help'=>'All game records',              'link'=>'admin/games.php',         'color'=>'primary'],
          ['icon'=>'✅','label'=>'Active Games',      'value'=>$stats['active_games'],        'help'=>'Publicly visible',              'link'=>'admin/games.php',         'color'=>'success'],
          ['icon'=>'📝','label'=>'Draft Games',       'value'=>$stats['draft_games'],         'help'=>'Not published yet',             'link'=>'admin/games.php',         'color'=>'warning'],
          ['icon'=>'📁','label'=>'Categories',        'value'=>$stats['total_categories'],    'help'=>'Genre groups',                  'link'=>'admin/categories.php',    'color'=>'info'],
          ['icon'=>'🔗','label'=>'Download Links',    'value'=>$stats['total_download_links'],'help'=>'All link records',              'link'=>'admin/download-links.php','color'=>'info'],
          ['icon'=>'⬇','label'=>'Total Downloads',   'value'=>$stats['total_downloads'],     'help'=>'Across all games',              'link'=>'admin/games.php',         'color'=>'primary'],
          ['icon'=>'👁','label'=>'Total Views',       'value'=>$stats['total_views'],         'help'=>'Across all games',              'link'=>'admin/games.php',         'color'=>'primary'],
          ['icon'=>'🔔','label'=>'Pending Reports',  'value'=>$stats['pending_reports'],     'help'=>'Awaiting review',               'link'=>'admin/reports.php',       'color'=>'danger'],
          ['icon'=>'💬','label'=>'New Messages',      'value'=>$stats['new_messages'],        'help'=>'Unread contact messages',       'link'=>'admin/messages.php',      'color'=>'warning'],
          ['icon'=>'⭐','label'=>'Featured Games',   'value'=>$stats['featured_games'],      'help'=>'Shown in featured sections',    'link'=>'admin/games.php',         'color'=>'warning'],
          ['icon'=>'📈','label'=>'Trending Games',   'value'=>$stats['trending_games'],      'help'=>'Trending flag enabled',         'link'=>'admin/games.php',         'color'=>'success'],
          ['icon'=>'🖥️','label'=>'Low-End PC Games', 'value'=>$stats['low_end_games'],       'help'=>'Low-end PC tag enabled',        'link'=>'admin/games.php',         'color'=>'info'],
        ];
        foreach ($statCards as $sc):
        ?>
        <a href="<?= e(siteUrl($sc['link'])) ?>"
           class="admin-stat-card admin-stat-<?= $sc['color'] ?>">
          <div class="admin-stat-icon" aria-hidden="true"><?= $sc['icon'] ?></div>
          <div class="admin-stat-content">
            <div class="admin-stat-value">
              <?= e(formatNumberShort((int)$sc['value'])) ?>
            </div>
            <div class="admin-stat-label"><?= e($sc['label']) ?></div>
            <div class="admin-stat-help"><?= e($sc['help']) ?></div>
          </div>
        </a>
        <?php endforeach; ?>

      </div><!-- /.admin-stats-grid -->


      <!-- ══════════════════════════════════════════
           TWO-COLUMN PANELS
           ══════════════════════════════════════════ -->
      <div class="admin-dashboard-grid">

        <!-- ── Recent Games ── -->
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h2 class="admin-panel-title">🎮 Recent Games</h2>
            <a href="<?= e(siteUrl('admin/games.php')) ?>"
               class="admin-mini-link">View all →</a>
          </div>

          <?php if (!empty($recentGames)): ?>
            <div class="admin-table-wrap">
              <table class="admin-table" role="grid">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>License</th>
                    <th>Views</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentGames as $g): ?>
                  <tr>
                    <td class="admin-table-title">
                      <?= e(truncateText($g['title'] ?? '', 28)) ?>
                    </td>
                    <td>
                      <span class="admin-status-badge admin-status-<?= e($g['status'] ?? 'draft') ?>">
                        <?= e(ucfirst($g['status'] ?? 'draft')) ?>
                      </span>
                    </td>
                    <td class="text-muted text-xs">
                      <?= e(getLicenseLabel($g['license_type'] ?? 'freeware')) ?>
                    </td>
                    <td class="text-muted text-xs">
                      <?= e(formatNumberShort((int)($g['views_count'] ?? 0))) ?>
                    </td>
                    <td>
                      <?php if (!empty($g['slug'])): ?>
                        <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($g['slug']))) ?>"
                           target="_blank" rel="noopener"
                           class="admin-mini-link" aria-label="View <?= e($g['title'] ?? '') ?>">
                          View
                        </a>
                      <?php else: ?>
                        <span class="text-muted text-xs">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="admin-empty-state">
              <span aria-hidden="true">🎮</span>
              <p>No games have been added yet.</p>
              <a href="<?= e(siteUrl('admin/add-game.php')) ?>"
                 class="admin-btn admin-btn-primary admin-btn-sm">
                ➕ Add New Game
              </a>
            </div>
          <?php endif; ?>
        </div><!-- /.admin-panel (recent games) -->


        <!-- ── Recent Reports ── -->
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h2 class="admin-panel-title">🔔 Recent Reports</h2>
            <a href="<?= e(siteUrl('admin/reports.php')) ?>"
               class="admin-mini-link">View all →</a>
          </div>

          <?php if (!empty($recentReports)): ?>
            <div class="admin-table-wrap">
              <table class="admin-table" role="grid">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Game</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentReports as $r): ?>
                  <tr>
                    <td class="text-xs">
                      <?= e(str_replace('_', ' ', ucfirst($r['report_type'] ?? 'other'))) ?>
                    </td>
                    <td class="admin-table-title text-xs">
                      <?= e(truncateText($r['game_title'] ?? '—', 22)) ?>
                    </td>
                    <td>
                      <span class="admin-status-badge admin-status-<?= e($r['report_status'] ?? 'pending') ?>">
                        <?= e(ucfirst($r['report_status'] ?? 'pending')) ?>
                      </span>
                    </td>
                    <td class="text-muted text-xs">
                      <?= e(date('d M', strtotime($r['created_at'] ?? 'now'))) ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="admin-empty-state">
              <span aria-hidden="true">🔔</span>
              <p>No download reports yet.</p>
            </div>
          <?php endif; ?>
        </div><!-- /.admin-panel (reports) -->


        <!-- ── Recent Messages ── -->
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h2 class="admin-panel-title">💬 Recent Contact Messages</h2>
            <a href="<?= e(siteUrl('admin/messages.php')) ?>"
               class="admin-mini-link">View all →</a>
          </div>

          <?php if (!empty($recentMsgs)): ?>
            <div class="admin-table-wrap">
              <table class="admin-table" role="grid">
                <thead>
                  <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentMsgs as $m): ?>
                  <tr>
                    <td class="text-xs"><?= e(truncateText($m['name'] ?? '—', 16)) ?></td>
                    <td class="admin-table-title text-xs">
                      <?= e(truncateText($m['subject'] ?? '—', 24)) ?>
                    </td>
                    <td>
                      <span class="admin-status-badge admin-status-<?= e($m['status'] ?? 'new') ?>">
                        <?= e(ucfirst($m['status'] ?? 'new')) ?>
                      </span>
                    </td>
                    <td class="text-muted text-xs">
                      <?= e(date('d M', strtotime($m['created_at'] ?? 'now'))) ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="admin-empty-state">
              <span aria-hidden="true">💬</span>
              <p>No contact messages yet.</p>
            </div>
          <?php endif; ?>
        </div><!-- /.admin-panel (messages) -->


        <!-- ── Website Health ── -->
        <div class="admin-panel">
          <div class="admin-panel-header">
            <h2 class="admin-panel-title">🏥 Website Health</h2>
          </div>

          <div class="admin-health-grid">
            <?php
            $healthItems = [
              ['label'=>'Database',       'value'=>$sysStatus['database'],
               'ok'=> ($sysStatus['database'] === 'Connected')],
              ['label'=>'PHP Version',    'value'=>$sysStatus['php_version'],
               'ok'=> true],
              ['label'=>'Uploads Folder', 'value'=>$sysStatus['uploads_writable'] ? 'Writable' : 'Not Writable',
               'ok'=> $sysStatus['uploads_writable']],
              ['label'=>'Logs Folder',    'value'=>$sysStatus['logs_writable'] ? 'Writable' : 'Not Writable',
               'ok'=> $sysStatus['logs_writable']],
              ['label'=>'Environment',    'value'=>ucfirst($sysStatus['environment'] ?? 'unknown'),
               'ok'=> true],
              ['label'=>'Admin Session',  'value'=>$sysStatus['admin_session'],
               'ok'=> true],
            ];
            foreach ($healthItems as $h):
            ?>
            <div class="admin-health-item">
              <span class="admin-health-label"><?= e($h['label']) ?></span>
              <span class="admin-status-badge <?= $h['ok'] ? 'admin-status-success' : 'admin-status-danger' ?>">
                <?= e($h['value']) ?>
              </span>
            </div>
            <?php endforeach; ?>
          </div>
        </div><!-- /.admin-panel (health) -->

      </div><!-- /.admin-dashboard-grid -->


      <!-- ══════════════════════════════════════════
           QUICK ACTIONS
           ══════════════════════════════════════════ -->
      <div class="admin-panel" style="margin-top:2rem;">
        <div class="admin-panel-header">
          <h2 class="admin-panel-title">⚡ Quick Actions</h2>
        </div>
        <div class="admin-quick-actions-grid">
          <?php
          $quickActions = [
            ['icon'=>'➕','label'=>'Add New Game',    'desc'=>'Create a new legal game listing.',                'url'=>'admin/add-game.php'],
            ['icon'=>'🎮','label'=>'Manage Games',    'desc'=>'Review, edit, or organize game listings.',       'url'=>'admin/games.php'],
            ['icon'=>'📁','label'=>'Categories',       'desc'=>'Organize game genres and collections.',          'url'=>'admin/categories.php'],
            ['icon'=>'🔗','label'=>'Download Links',   'desc'=>'Manage authorized download sources.',           'url'=>'admin/download-links.php'],
            ['icon'=>'🔔','label'=>'Review Reports',  'desc'=>'Check broken link and safety reports.',          'url'=>'admin/reports.php'],
            ['icon'=>'💬','label'=>'Messages',         'desc'=>'Review user contact submissions.',              'url'=>'admin/messages.php'],
            ['icon'=>'⚙️','label'=>'Settings',         'desc'=>'Update website settings and contact info.',     'url'=>'admin/settings.php'],
            ['icon'=>'🌐','label'=>'View Public Site', 'desc'=>'Open the public-facing website.',               'url'=>'index.php', 'external'=>true],
          ];
          foreach ($quickActions as $qa):
          ?>
          <a href="<?= e(siteUrl($qa['url'])) ?>"
             class="admin-quick-action-card"
             <?= !empty($qa['external']) ? 'target="_blank" rel="noopener"' : '' ?>>
            <span class="admin-qa-icon" aria-hidden="true"><?= $qa['icon'] ?></span>
            <div>
              <p class="admin-qa-label"><?= e($qa['label']) ?></p>
              <p class="admin-qa-desc"><?= e($qa['desc']) ?></p>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

    </div><!-- /.admin-content -->
  </main><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
