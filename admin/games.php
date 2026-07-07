<?php
/**
 * QMGames Store — Admin: Manage Games
 * Step: 21 — Admin Game Management
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin           = getCurrentAdmin();
$pageTitle       = 'Manage Games';
$adminPageTitle  = 'Manage Games';
$activeAdminPage = 'games';

const GAMES_CSRF_KEY = 'admin_games';

/* ================================================================
   HANDLE STATUS CHANGE (POST)
   ================================================================ */
$flashMsg  = '';
$flashType = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_status'])) {
    $postToken = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($postToken, GAMES_CSRF_KEY)) {
        $flashMsg  = 'Session expired. Please try again.';
        $flashType = 'danger';
    } else {
        $actionGameId = (int)($_POST['game_id'] ?? 0);
        $newStatus    = trim((string)($_POST['new_status'] ?? ''));
        if ($actionGameId > 0 && updateGameStatus($actionGameId, $newStatus)) {
            redirect(siteUrl('admin/games.php?status_updated=1'));
        } else {
            $flashMsg  = 'Status update failed. Please try again.';
            $flashType = 'danger';
        }
    }
}

/* Flash messages from query string (whitelisted) */
if ($flashMsg === '') {
    if (isset($_GET['status_updated']))    { $flashMsg = '✅ Game status updated.'; $flashType = 'success'; }
    elseif (isset($_GET['created']))       { $flashMsg = '✅ Game created successfully.'; $flashType = 'success'; }
    elseif (isset($_GET['deleted']))       { $flashMsg = '✅ Game archived.'; $flashType = 'success'; }
}

/* ================================================================
   FILTERS
   ================================================================ */
$filters = [
    'q'        => trim(mb_substr((string)($_GET['q']        ?? ''), 0, 100)),
    'status'   => trim((string)($_GET['status']   ?? '')),
    'license'  => trim((string)($_GET['license']  ?? '')),
    'category' => trim((string)($_GET['category'] ?? '')),
    'featured' => trim((string)($_GET['featured'] ?? '')),
    'trending' => trim((string)($_GET['trending'] ?? '')),
    'low_end'  => trim((string)($_GET['low_end']  ?? '')),
    'sort'     => trim((string)($_GET['sort']     ?? 'latest')),
];
$currentPage = max(1, (int)($_GET['page'] ?? 1));

$result    = getAdminGamesList($filters, $currentPage, ADMIN_GAMES_PER_PAGE);
$games     = $result['games'];
$total     = $result['total'];
$totalPages= $result['total_pages'];
$curPage   = $result['page'];

$allCategories = getAllAdminCategories();

$statusOptions = [
    ''         => 'All Statuses',
    'draft'    => 'Draft',
    'active'   => 'Active',
    'inactive' => 'Inactive',
    'archived' => 'Archived',
];
$licenseOptions = [
    ''                 => 'All Licenses',
    'freeware'         => 'Freeware',
    'open_source'      => 'Open Source',
    'demo'             => 'Demo',
    'official_mirror'  => 'Official Mirror',
    'indie_permission' => 'Indie Permission',
    'paid_future'      => 'Paid Future',
    'other_authorized' => 'Other Authorized',
];
$sortOptions = [
    'latest'          => 'Latest',
    'oldest'          => 'Oldest',
    'title_az'        => 'Title A–Z',
    'title_za'        => 'Title Z–A',
    'most_viewed'     => 'Most Viewed',
    'most_downloaded' => 'Most Downloaded',
    'updated'         => 'Recently Updated',
];

/* CSRF for status forms on this page */
$pageToken = generateCsrfToken(GAMES_CSRF_KEY);

require_once INCLUDES_PATH . '/admin-header.php';
?>

<div class="admin-wrapper">
  <?php require_once INCLUDES_PATH . '/admin-sidebar.php'; ?>

  <main class="admin-main" id="adminMain">

    <div class="admin-topbar">
      <div class="admin-topbar-left">
        <button class="admin-sidebar-toggle-btn" type="button"
                data-admin-sidebar-toggle aria-label="Toggle sidebar">☰</button>
        <h2 class="admin-topbar-title">Manage Games</h2>
      </div>
      <div class="admin-topbar-actions">
        <a href="<?= e(siteUrl('admin/add-game.php')) ?>"
           class="admin-btn admin-btn-primary admin-btn-sm">
          ➕ Add Game
        </a>
        <a href="<?= e(siteUrl('games.php')) ?>" target="_blank" rel="noopener"
           class="admin-btn admin-btn-ghost admin-btn-sm">
          🌐 Public Games
        </a>
      </div>
    </div>

    <div class="admin-content admin-games-page">

      <?php if ($flashMsg !== ''): ?>
        <div class="admin-alert admin-alert-<?= e($flashType) ?>" style="margin-bottom:1.25rem;">
          <?= e($flashMsg) ?>
        </div>
      <?php endif; ?>

      <!-- ── Filter panel ── -->
      <div class="admin-filter-panel">
        <form method="GET" action="<?= e(siteUrl('admin/games.php')) ?>"
              class="admin-filter-form" id="gamesFilterForm">

          <div class="admin-filter-grid">

            <div class="admin-form-group">
              <label class="admin-form-label" for="gf_q">Search</label>
              <input type="search" id="gf_q" name="q"
                     class="admin-form-control"
                     placeholder="Title, developer, slug..."
                     value="<?= e($filters['q']) ?>"
                     maxlength="100">
            </div>

            <div class="admin-form-group">
              <label class="admin-form-label" for="gf_status">Status</label>
              <select id="gf_status" name="status" class="admin-form-control">
                <?php foreach ($statusOptions as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $filters['status'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="admin-form-group">
              <label class="admin-form-label" for="gf_license">License</label>
              <select id="gf_license" name="license" class="admin-form-control">
                <?php foreach ($licenseOptions as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $filters['license'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="admin-form-group">
              <label class="admin-form-label" for="gf_cat">Category</label>
              <select id="gf_cat" name="category" class="admin-form-control">
                <option value="">All Categories</option>
                <?php foreach ($allCategories as $cat): ?>
                  <option value="<?= e($cat['slug']) ?>"
                    <?= $filters['category'] === $cat['slug'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="admin-form-group">
              <label class="admin-form-label" for="gf_sort">Sort</label>
              <select id="gf_sort" name="sort" class="admin-form-control">
                <?php foreach ($sortOptions as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $filters['sort'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <div class="admin-filter-actions">
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">
              Apply Filters
            </button>
            <a href="<?= e(siteUrl('admin/games.php')) ?>"
               class="admin-btn admin-btn-ghost admin-btn-sm">Reset</a>
            <span class="admin-filter-count text-muted text-xs">
              <?= e((string)$total) ?> game<?= $total !== 1 ? 's' : '' ?>
            </span>
          </div>

        </form>
      </div>

      <!-- ── Games table ── -->
      <?php if (!empty($games)): ?>

        <div class="admin-table-card">
          <div class="admin-table-wrap">
            <table class="admin-table admin-games-table" role="grid">
              <thead>
                <tr>
                  <th style="width:52px;">Cover</th>
                  <th>Title</th>
                  <th>Status</th>
                  <th>License</th>
                  <th>Platform</th>
                  <th>Views</th>
                  <th>DLs</th>
                  <th>Updated</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($games as $g):
                  $gId     = (int)$g['id'];
                  $gTitle  = e($g['title'] ?? '');
                  $gSlug   = e($g['slug']  ?? '');
                  $gStatus = $g['status']       ?? 'draft';
                  $gLic    = $g['license_type'] ?? 'freeware';
                  $gCover  = !empty($g['cover_image'])
                               ? e(siteUrl($g['cover_image']))
                               : getPlaceholderImage('cover');
                ?>
                <tr>
                  <td>
                    <img src="<?= $gCover ?>"
                         alt="<?= $gTitle ?> cover"
                         class="admin-cover-thumb"
                         loading="lazy">
                  </td>
                  <td class="admin-game-title-cell">
                    <strong><?= $gTitle ?></strong>
                    <span class="admin-game-slug"><?= $gSlug ?></span>
                  </td>
                  <td>
                    <span class="admin-status-badge admin-status-<?= e($gStatus) ?>">
                      <?= e(ucfirst($gStatus)) ?>
                    </span>
                  </td>
                  <td class="text-xs text-muted">
                    <?= e(getLicenseLabel($gLic)) ?>
                  </td>
                  <td class="text-xs text-muted">
                    <?= e($g['platform'] ?? '—') ?>
                  </td>
                  <td class="text-xs text-muted">
                    <?= e(formatNumberShort((int)($g['views_count'] ?? 0))) ?>
                  </td>
                  <td class="text-xs text-muted">
                    <?= e(formatNumberShort((int)($g['downloads_count'] ?? 0))) ?>
                  </td>
                  <td class="text-xs text-muted">
                    <?= e(date('d M y', strtotime($g['updated_at'] ?? $g['created_at'] ?? 'now'))) ?>
                  </td>
                  <td>
                    <div class="admin-action-group">
                      <a href="<?= e(siteUrl('admin/edit-game.php?id=' . $gId)) ?>"
                         class="admin-action-btn admin-btn-ghost admin-btn-sm"
                         title="Edit">✏️</a>
                      <?php if ($gStatus === 'active' && $gSlug !== ''): ?>
                        <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($g['slug'] ?? ''))) ?>"
                           target="_blank" rel="noopener"
                           class="admin-action-btn admin-btn-ghost admin-btn-sm"
                           title="View public page">🌐</a>
                      <?php endif; ?>
                      <!-- Quick status form -->
                      <form method="POST"
                            action="<?= e(siteUrl('admin/games.php')) ?>"
                            style="display:inline;">
                        <input type="hidden" name="csrf_token"
                               value="<?= e($pageToken) ?>">
                        <input type="hidden" name="action_status" value="1">
                        <input type="hidden" name="game_id" value="<?= $gId ?>">
                        <?php if ($gStatus !== 'active'): ?>
                          <input type="hidden" name="new_status" value="active">
                          <button type="submit"
                                  class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                  title="Set active">✅</button>
                        <?php endif; ?>
                        <?php if ($gStatus !== 'draft'): ?>
                          <input type="hidden" name="new_status" value="draft">
                          <button type="submit"
                                  class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                  title="Set draft">📝</button>
                        <?php endif; ?>
                        <?php if ($gStatus !== 'archived'): ?>
                          <input type="hidden" name="new_status" value="archived">
                          <button type="submit"
                                  class="admin-action-btn admin-btn-ghost admin-btn-sm admin-btn-archive"
                                  title="Archive"
                                  data-confirm-action="Archive this game?">🗄️</button>
                        <?php endif; ?>
                      </form>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="admin-pagination" aria-label="Games pagination">
          <?php $prevDis = $curPage <= 1; ?>
          <?php if ($prevDis): ?>
            <span class="admin-page-btn disabled">← Prev</span>
          <?php else: ?>
            <a href="<?= e(buildAdminGamesUrl($filters, ['page'=>(string)($curPage-1)])) ?>"
               class="admin-page-btn">← Prev</a>
          <?php endif; ?>

          <?php
          $start = max(1, $curPage - 2);
          $end   = min($totalPages, $curPage + 2);
          if ($start > 1): ?>
            <a href="<?= e(buildAdminGamesUrl($filters, ['page'=>'1'])) ?>"
               class="admin-page-btn">1</a>
            <?php if ($start > 2): ?><span class="admin-page-ellipsis">…</span><?php endif; ?>
          <?php endif; ?>

          <?php for ($pp = $start; $pp <= $end; $pp++): ?>
            <?php if ($pp === $curPage): ?>
              <span class="admin-page-btn active"><?= $pp ?></span>
            <?php else: ?>
              <a href="<?= e(buildAdminGamesUrl($filters, ['page'=>(string)$pp])) ?>"
                 class="admin-page-btn"><?= $pp ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?><span class="admin-page-ellipsis">…</span><?php endif; ?>
            <a href="<?= e(buildAdminGamesUrl($filters, ['page'=>(string)$totalPages])) ?>"
               class="admin-page-btn"><?= $totalPages ?></a>
          <?php endif; ?>

          <?php $nextDis = $curPage >= $totalPages; ?>
          <?php if ($nextDis): ?>
            <span class="admin-page-btn disabled">Next →</span>
          <?php else: ?>
            <a href="<?= e(buildAdminGamesUrl($filters, ['page'=>(string)($curPage+1)])) ?>"
               class="admin-page-btn">Next →</a>
          <?php endif; ?>
        </nav>
        <?php endif; ?>

      <?php else: ?>
        <div class="admin-empty-state">
          <span>🎮</span>
          <p>No games found matching your filters.</p>
          <a href="<?= e(siteUrl('admin/add-game.php')) ?>"
             class="admin-btn admin-btn-primary admin-btn-sm">
            ➕ Add New Game
          </a>
        </div>
      <?php endif; ?>

    </div>
  </main>
</div>

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
