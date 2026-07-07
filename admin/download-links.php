<?php
/**
 * QMGames Store — Admin: Download Link Management
 * Step: 22 — Admin Download Link Management
 * Modes: list | add (action=add) | edit (action=edit&id=N)
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin           = getCurrentAdmin();
$pageTitle       = 'Download Links';
$adminPageTitle  = 'Download Links';
$activeAdminPage = 'download-links';

const DL_CSRF_KEY = 'admin_dl_links';

$action  = trim((string)($_GET['action'] ?? ''));
$linkId  = (int)($_GET['id'] ?? 0);
$flashMsg  = '';
$flashType = 'info';

/* ================================================================
   HANDLE POST: STATUS UPDATE or CREATE/EDIT dispatch
   ================================================================ */

/* ── Status update (POST) ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_status'])) {
    $tok = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($tok, DL_CSRF_KEY)) {
        $flashMsg = 'Session expired. Please try again.'; $flashType = 'danger';
    } else {
        $sid = (int)($_POST['link_id']    ?? 0);
        $ns  = trim((string)($_POST['new_status'] ?? ''));
        if ($sid > 0 && updateDownloadLinkStatus($sid, $ns)) {
            redirect(siteUrl('admin/download-links.php?status_updated=1'));
        } else {
            $flashMsg = 'Status update failed.'; $flashType = 'danger';
        }
    }
}

/* ── Flash from query string ── */
if ($flashMsg === '') {
    if (isset($_GET['status_updated'])) { $flashMsg = '✅ Link status updated.'; $flashType = 'success'; }
    if (isset($_GET['created']))        { $flashMsg = '✅ Download link created.'; $flashType = 'success'; }
    if (isset($_GET['updated']))        { $flashMsg = '✅ Download link updated.'; $flashType = 'success'; }
    if (isset($_GET['not_found']))      { $flashMsg = 'Download link not found.'; $flashType = 'danger'; }
    if (isset($_GET['error']))          { $flashMsg = 'Something went wrong. Please try again.'; $flashType = 'danger'; }
}

/* ================================================================
   SHARED DATA
   ================================================================ */
$gameOptions  = getAdminGameOptionsForLinks();
$linkTypes    = ['cloud'=>'Cloud','torrent'=>'Torrent','official'=>'Official',
                 'mirror'=>'Mirror','developer_site'=>'Developer Site','store_link'=>'Store Link'];
$statusOptions= ['active'=>'Active','inactive'=>'Inactive',
                 'broken'=>'Broken','under_review'=>'Under Review'];

/* ================================================================
   ADD MODE — handle POST
   ================================================================ */
$addErrors    = [];
$addData      = [];
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tok = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($tok, DL_CSRF_KEY)) {
        $addErrors[] = 'Session expired. Please refresh.';
    } else {
        $v = validateDownloadLinkForm($_POST);
        if (!$v['valid']) {
            $addErrors = $v['errors'];
            $addData   = $v['data'] + ['game_id'=>(int)($_POST['game_id']??0),
                'link_title'=>$_POST['link_title']??'',
                'provider_name'=>$_POST['provider_name']??'',
                'download_url'=>$_POST['download_url']??'',
                'file_size'=>$_POST['file_size']??'',
                'link_type'=>$_POST['link_type']??'',
                'status'=>$_POST['status']??'active'];
        } else {
            $newId = createDownloadLink($v['data']);
            if ($newId !== false) {
                redirect(siteUrl('admin/download-links.php?action=edit&id='.$newId.'&created=1'));
            } else {
                $addErrors[] = 'Failed to create download link. Please try again.';
                $addData = $v['data'];
            }
        }
    }
}

/* ================================================================
   EDIT MODE — load link + handle POST
   ================================================================ */
$editLink   = null;
$editErrors = [];
$editData   = [];
if ($action === 'edit') {
    $editLink = getAdminDownloadLinkById($linkId);
    if ($editLink === null) { redirect(siteUrl('admin/download-links.php?not_found=1')); }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tok = trim((string)($_POST['csrf_token'] ?? ''));
        if (!validateCsrfToken($tok, DL_CSRF_KEY)) {
            $editErrors[] = 'Session expired. Please refresh.';
        } else {
            $v = validateDownloadLinkForm($_POST);
            if (!$v['valid']) {
                $editErrors = $v['errors'];
                $editData = $v['data'] + ['game_id'=>(int)($_POST['game_id']??0),
                    'link_title'=>$_POST['link_title']??'',
                    'provider_name'=>$_POST['provider_name']??'',
                    'download_url'=>$_POST['download_url']??'',
                    'file_size'=>$_POST['file_size']??'',
                    'link_type'=>$_POST['link_type']??'',
                    'status'=>$_POST['status']??'active'];
            } else {
                if (updateDownloadLink($linkId, $v['data'])) {
                    redirect(siteUrl('admin/download-links.php?action=edit&id='.$linkId.'&updated=1'));
                } else {
                    $editErrors[] = 'Failed to update. Please try again.';
                    $editData = $v['data'];
                }
            }
        }
    }
}

/* ================================================================
   LIST MODE
   ================================================================ */
$filters = [];
$curPage = 1;
$listResult = ['links'=>[], 'total'=>0, 'page'=>1, 'per_page'=>15, 'total_pages'=>0];
if ($action === '' || $action === 'list') {
    $filters = [
        'q'         => trim(mb_substr((string)($_GET['q']         ?? ''), 0, 100)),
        'game_id'   => (int)($_GET['game_id']   ?? 0),
        'link_type' => trim((string)($_GET['link_type'] ?? '')),
        'status'    => trim((string)($_GET['status']    ?? '')),
        'sort'      => trim((string)($_GET['sort']      ?? 'latest')),
    ];
    $curPage    = max(1, (int)($_GET['page'] ?? 1));
    $listResult = getAdminDownloadLinksList($filters, $curPage, 15);
}

/* CSRF token for all forms on this page */
$pageToken = generateCsrfToken(DL_CSRF_KEY);

/* ================================================================
   RENDER
   ================================================================ */
require_once INCLUDES_PATH . '/admin-header.php';
?>

<div class="admin-wrapper">
  <?php require_once INCLUDES_PATH . '/admin-sidebar.php'; ?>
  <main class="admin-main" id="adminMain">

    <div class="admin-topbar">
      <div class="admin-topbar-left">
        <button class="admin-sidebar-toggle-btn" type="button"
                data-admin-sidebar-toggle aria-label="Toggle sidebar">☰</button>
        <h2 class="admin-topbar-title">Download Links</h2>
      </div>
      <div class="admin-topbar-actions">
        <?php if ($action !== 'add' && $action !== 'edit'): ?>
          <a href="<?= e(siteUrl('admin/download-links.php?action=add')) ?>"
             class="admin-btn admin-btn-primary admin-btn-sm">➕ Add Link</a>
        <?php endif; ?>
        <a href="<?= e(siteUrl('admin/games.php')) ?>"
           class="admin-btn admin-btn-ghost admin-btn-sm">🎮 Games</a>
      </div>
    </div>

    <div class="admin-content admin-download-links-page">

      <?php if ($flashMsg !== ''): ?>
        <div class="admin-alert admin-alert-<?= e($flashType) ?>"
             style="margin-bottom:1.25rem;">
          <?= e($flashMsg) ?>
        </div>
      <?php endif; ?>

<?php /* ========================================================
           ADD MODE
           ======================================================== */
if ($action === 'add'): ?>

      <div class="admin-page-header">
        <h1 style="font-size:1.3rem;font-weight:800;margin-bottom:.3rem;">
          Add Download Link
        </h1>
        <p class="text-muted text-sm">
          Connect an authorized download source to a game listing.
        </p>
      </div>

      <?php if (!empty($addErrors)): ?>
        <div class="admin-alert admin-alert-danger" style="margin-bottom:1.25rem;">
          <?php foreach ($addErrors as $e2): echo '<div>' . e($e2) . '</div>'; endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="admin-legal-note">
        <strong>⚖️ Legal Source Reminder:</strong>
        Only add download links that are legal, authorized, official, freeware,
        open-source, demo, indie-permission, store-based, or permission-based.
        Do not add unauthorized game copies, license-bypassing files, or unsafe links.
      </div>

      <form method="POST"
            action="<?= e(siteUrl('admin/download-links.php?action=add')) ?>"
            class="admin-link-form-page" id="dlLinkForm">
        <input type="hidden" name="csrf_token" value="<?= e($pageToken) ?>">

        <?php
        $fd = $addData; // form data for re-fill
        include __DIR__ . '/partials/dl-link-form-fields.php';
        ?>

        <div class="admin-form-card">
          <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary"
                    id="dlFormSubmit" data-disable-on-submit>
              ✅ Create Download Link
            </button>
            <a href="<?= e(siteUrl('admin/download-links.php')) ?>"
               class="admin-btn admin-btn-ghost">← Back to List</a>
          </div>
        </div>
      </form>

<?php /* ========================================================
           EDIT MODE
           ======================================================== */
elseif ($action === 'edit' && $editLink !== null): ?>

      <div class="admin-page-header">
        <h1 style="font-size:1.3rem;font-weight:800;margin-bottom:.3rem;">
          Edit Download Link
        </h1>
        <p class="text-muted text-sm">
          Link ID #<?= (int)$editLink['id'] ?> —
          Game: <strong><?= e(truncateText($editLink['game_title'] ?? '—', 40)) ?></strong>
        </p>
      </div>

      <?php if (!empty($editErrors)): ?>
        <div class="admin-alert admin-alert-danger" style="margin-bottom:1.25rem;">
          <?php foreach ($editErrors as $e2): echo '<div>' . e($e2) . '</div>'; endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="admin-legal-note">
        <strong>⚖️ Legal Source Reminder:</strong>
        Only authorized, official, freeware, open-source, demo, indie-permission,
        store-based, or permission-based sources should be listed.
      </div>

      <form method="POST"
            action="<?= e(siteUrl('admin/download-links.php?action=edit&id=' . (int)$editLink['id'])) ?>"
            class="admin-link-form-page" id="dlLinkForm">
        <input type="hidden" name="csrf_token" value="<?= e($pageToken) ?>">

        <?php
        $fd = !empty($editData) ? $editData : $editLink;
        include __DIR__ . '/partials/dl-link-form-fields.php';
        ?>

        <div class="admin-form-card">
          <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary"
                    id="dlFormSubmit" data-disable-on-submit>
              💾 Save Changes
            </button>
            <?php if (($editLink['game_status'] ?? '') === 'active' && !empty($editLink['game_slug'])): ?>
              <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($editLink['game_slug']))) ?>"
                 target="_blank" rel="noopener"
                 class="admin-btn admin-btn-ghost">🌐 View Game</a>
            <?php endif; ?>
            <a href="<?= e(siteUrl('admin/download-links.php')) ?>"
               class="admin-btn admin-btn-ghost">← Back to List</a>
          </div>
        </div>
      </form>

<?php /* ========================================================
           LIST MODE
           ======================================================== */
else:
    $links      = $listResult['links'];
    $totalLinks = $listResult['total'];
    $totalPages = $listResult['total_pages'];
?>

      <div class="admin-page-header" style="margin-bottom:1rem;">
        <h1 style="font-size:1.2rem;font-weight:800;margin-bottom:.2rem;">
          All Download Links
        </h1>
        <p class="text-muted text-sm">
          <?= e((string)$totalLinks) ?> link<?= $totalLinks !== 1 ? 's' : '' ?>
          — authorized download sources for game listings.
        </p>
      </div>

      <!-- Filters -->
      <div class="admin-filter-panel" style="margin-bottom:1.25rem;">
        <form method="GET" action="<?= e(siteUrl('admin/download-links.php')) ?>"
              class="admin-filter-form" id="dlFilterForm">

          <div class="admin-filter-grid admin-dl-filter-grid">
            <div class="admin-form-group">
              <label class="admin-form-label" for="dlf_q">Search</label>
              <input type="search" id="dlf_q" name="q"
                     class="admin-form-control"
                     placeholder="Game, provider, link title..."
                     value="<?= e($filters['q']) ?>" maxlength="100">
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label" for="dlf_game">Game</label>
              <select id="dlf_game" name="game_id" class="admin-form-control">
                <option value="">All Games</option>
                <?php foreach ($gameOptions as $g): ?>
                  <option value="<?= (int)$g['id'] ?>"
                    <?= $filters['game_id'] === (int)$g['id'] ? 'selected' : '' ?>>
                    <?= e(truncateText($g['title'] ?? '', 35)) ?>
                    (<?= e(ucfirst($g['status']??'')) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label" for="dlf_type">Link Type</label>
              <select id="dlf_type" name="link_type" class="admin-form-control">
                <option value="">All Types</option>
                <?php foreach ($linkTypes as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $filters['link_type'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label" for="dlf_status">Status</label>
              <select id="dlf_status" name="status" class="admin-form-control">
                <option value="">All Statuses</option>
                <?php foreach ($statusOptions as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $filters['status'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label" for="dlf_sort">Sort</label>
              <select id="dlf_sort" name="sort" class="admin-form-control">
                <?php foreach (['latest'=>'Latest','oldest'=>'Oldest',
                    'most_clicked'=>'Most Clicked','provider_az'=>'Provider A–Z',
                    'game_az'=>'Game A–Z','recently_updated'=>'Recently Updated'] as $v=>$l): ?>
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
              Apply
            </button>
            <a href="<?= e(siteUrl('admin/download-links.php')) ?>"
               class="admin-btn admin-btn-ghost admin-btn-sm">Reset</a>
          </div>
        </form>
      </div>

      <!-- Table -->
      <?php if (!empty($links)): ?>
      <div class="admin-table-card">
        <div class="admin-table-wrap">
          <table class="admin-table admin-download-table" role="grid">
            <thead>
              <tr>
                <th>Game</th>
                <th>Link Title / Provider</th>
                <th>Type</th>
                <th>Size</th>
                <th>Status</th>
                <th>Clicks</th>
                <th>Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($links as $lk):
                $lkId     = (int)$lk['id'];
                $lkStatus = $lk['status'] ?? 'inactive';
                $gameSlug = $lk['game_slug'] ?? '';
                $gameStat = $lk['game_status'] ?? '';
              ?>
              <tr>
                <td class="admin-link-game-cell">
                  <span><?= e(truncateText($lk['game_title'] ?? '—', 28)) ?></span>
                  <span class="admin-status-badge admin-status-<?= e($gameStat) ?>"
                        style="font-size:.62rem;">
                    <?= e(ucfirst($gameStat)) ?>
                  </span>
                </td>
                <td class="admin-link-title-cell">
                  <strong><?= e(truncateText($lk['link_title'] ?? '', 30)) ?></strong>
                  <span class="admin-link-provider">
                    <?= e(truncateText($lk['provider_name'] ?? '', 25)) ?>
                  </span>
                </td>
                <td>
                  <span class="admin-link-type-badge">
                    <?= e(getReadableLinkType($lk['link_type'] ?? '')) ?>
                  </span>
                </td>
                <td class="text-xs text-muted">
                  <?= e($lk['file_size'] ?? '—') ?>
                </td>
                <td>
                  <span class="admin-status-badge <?= e(getLinkStatusBadgeClass($lkStatus)) ?>">
                    <?= e(getReadableLinkStatus($lkStatus)) ?>
                  </span>
                </td>
                <td class="text-xs text-muted">
                  <?= e(formatNumberShort((int)($lk['clicks_count'] ?? 0))) ?>
                </td>
                <td class="text-xs text-muted">
                  <?= e(date('d M y', strtotime($lk['updated_at'] ?? $lk['created_at'] ?? 'now'))) ?>
                </td>
                <td>
                  <div class="admin-action-group">
                    <a href="<?= e(siteUrl('admin/download-links.php?action=edit&id=' . $lkId)) ?>"
                       class="admin-action-btn admin-btn-ghost admin-btn-sm"
                       title="Edit">✏️</a>
                    <?php if (!empty($lk['download_url'])): ?>
                      <a href="<?= e($lk['download_url']) ?>"
                         target="_blank" rel="noopener noreferrer"
                         class="admin-action-btn admin-btn-ghost admin-btn-sm"
                         title="Test link (admin only)"
                         data-confirm-action="Open this download URL in a new tab?">
                        🔗
                      </a>
                    <?php endif; ?>
                    <!-- Quick status form -->
                    <form method="POST"
                          action="<?= e(siteUrl('admin/download-links.php')) ?>"
                          style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= e($pageToken) ?>">
                      <input type="hidden" name="action_status" value="1">
                      <input type="hidden" name="link_id" value="<?= $lkId ?>">
                      <?php if ($lkStatus !== 'active'): ?>
                        <input type="hidden" name="new_status" value="active">
                        <button type="submit" class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                title="Set active">✅</button>
                      <?php endif; ?>
                      <?php if ($lkStatus !== 'inactive'): ?>
                        <input type="hidden" name="new_status" value="inactive">
                        <button type="submit" class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                title="Set inactive"
                                data-confirm-action="Mark this link as inactive?">⏸️</button>
                      <?php endif; ?>
                      <?php if ($lkStatus !== 'broken'): ?>
                        <input type="hidden" name="new_status" value="broken">
                        <button type="submit" class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                title="Mark broken"
                                data-confirm-action="Mark this link as broken?">🔴</button>
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
      <?php if ($totalPages > 1):
        $pCur = $curPage;
        $pStart = max(1, $pCur - 2);
        $pEnd   = min($totalPages, $pCur + 2);
      ?>
      <nav class="admin-pagination">
        <?php if ($pCur > 1): ?>
          <a href="<?= e(buildAdminLinksUrl($filters, ['page'=>(string)($pCur-1)])) ?>"
             class="admin-page-btn">← Prev</a>
        <?php else: ?>
          <span class="admin-page-btn disabled">← Prev</span>
        <?php endif; ?>
        <?php if ($pStart > 1): ?>
          <a href="<?= e(buildAdminLinksUrl($filters, ['page'=>'1'])) ?>"
             class="admin-page-btn">1</a>
          <?php if ($pStart > 2): ?><span class="admin-page-ellipsis">…</span><?php endif; ?>
        <?php endif; ?>
        <?php for ($pp = $pStart; $pp <= $pEnd; $pp++): ?>
          <?php if ($pp === $pCur): ?>
            <span class="admin-page-btn active"><?= $pp ?></span>
          <?php else: ?>
            <a href="<?= e(buildAdminLinksUrl($filters, ['page'=>(string)$pp])) ?>"
               class="admin-page-btn"><?= $pp ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($pEnd < $totalPages): ?>
          <?php if ($pEnd < $totalPages - 1): ?><span class="admin-page-ellipsis">…</span><?php endif; ?>
          <a href="<?= e(buildAdminLinksUrl($filters, ['page'=>(string)$totalPages])) ?>"
             class="admin-page-btn"><?= $totalPages ?></a>
        <?php endif; ?>
        <?php if ($pCur < $totalPages): ?>
          <a href="<?= e(buildAdminLinksUrl($filters, ['page'=>(string)($pCur+1)])) ?>"
             class="admin-page-btn">Next →</a>
        <?php else: ?>
          <span class="admin-page-btn disabled">Next →</span>
        <?php endif; ?>
      </nav>
      <?php endif; ?>

      <?php else: ?>
      <div class="admin-empty-state">
        <span>🔗</span>
        <p>No download links found.</p>
        <a href="<?= e(siteUrl('admin/download-links.php?action=add')) ?>"
           class="admin-btn admin-btn-primary admin-btn-sm">➕ Add Download Link</a>
      </div>
      <?php endif; ?>

<?php endif; /* end mode switch */ ?>

    </div><!-- /.admin-content -->
  </main>
</div>

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
