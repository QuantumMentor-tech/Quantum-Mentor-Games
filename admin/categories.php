<?php
/**
 * QMGames Store — Admin: Category Management
 * Step: 23 — Admin Category Management
 * Modes: list | add (action=add) | edit (action=edit&id=N)
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$admin           = getCurrentAdmin();
$pageTitle       = 'Categories';
$adminPageTitle  = 'Categories';
$activeAdminPage = 'categories';

const CAT_CSRF_KEY = 'admin_categories';

$action = trim((string)($_GET['action'] ?? ''));
$catId  = (int)($_GET['id'] ?? 0);

/* ── Flash messages ── */
$flashMsg  = '';
$flashType = 'info';

/* ── Handle POST: status update ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_status'])) {
    $tok = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($tok, CAT_CSRF_KEY)) {
        $flashMsg = 'Session expired. Please try again.'; $flashType = 'danger';
    } else {
        $sid = (int)($_POST['category_id'] ?? 0);
        $ns  = trim((string)($_POST['new_status'] ?? ''));
        if ($sid > 0 && updateCategoryStatus($sid, $ns)) {
            redirect(siteUrl('admin/categories.php?status_updated=1'));
        } else {
            $flashMsg = 'Status update failed.'; $flashType = 'danger';
        }
    }
}

/* ── Flash from query string ── */
if ($flashMsg === '') {
    if (isset($_GET['status_updated'])) { $flashMsg = '✅ Category status updated.'; $flashType = 'success'; }
    if (isset($_GET['created']))        { $flashMsg = '✅ Category created successfully.'; $flashType = 'success'; }
    if (isset($_GET['updated']))        { $flashMsg = '✅ Category updated successfully.'; $flashType = 'success'; }
    if (isset($_GET['not_found']))      { $flashMsg = 'Category not found.'; $flashType = 'danger'; }
    if (isset($_GET['error']))          { $flashMsg = 'Something went wrong. Please try again.'; $flashType = 'danger'; }
}

$statusOptions = ['active'=>'Active','inactive'=>'Inactive','archived'=>'Archived'];
$sortOptions   = [
    'sort_order'      => 'Sort Order',
    'latest'          => 'Latest',
    'oldest'          => 'Oldest',
    'name_az'         => 'Name A–Z',
    'name_za'         => 'Name Z–A',
    'most_games'      => 'Most Games',
    'recently_updated'=> 'Recently Updated',
];

/* ================================================================
   ADD MODE — handle POST
   ================================================================ */
$addErrors = [];
$addData   = [];
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $tok = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($tok, CAT_CSRF_KEY)) {
        $addErrors[] = 'Session expired. Please refresh.';
    } else {
        $v = validateCategoryForm($_POST);
        if (!$v['valid']) {
            $addErrors = $v['errors'];
            $addData   = [
                'name'        => trim((string)($_POST['name']        ?? '')),
                'slug'        => trim((string)($_POST['slug']        ?? '')),
                'description' => trim((string)($_POST['description'] ?? '')),
                'icon'        => trim((string)($_POST['icon']        ?? '')),
                'status'      => trim((string)($_POST['status']      ?? 'active')),
                'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            ];
        } else {
            $newId = createCategory($v['data']);
            if ($newId !== false) {
                redirect(siteUrl('admin/categories.php?action=edit&id=' . $newId . '&created=1'));
            } else {
                $addErrors[] = 'Failed to create category. Please try again.';
                $addData = $v['data'];
            }
        }
    }
}

/* ================================================================
   EDIT MODE — load category + handle POST
   ================================================================ */
$editCat    = null;
$editErrors = [];
$editData   = [];
if ($action === 'edit') {
    $editCat = getAdminCategoryById($catId);
    if ($editCat === null) {
        redirect(siteUrl('admin/categories.php?not_found=1'));
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tok = trim((string)($_POST['csrf_token'] ?? ''));
        if (!validateCsrfToken($tok, CAT_CSRF_KEY)) {
            $editErrors[] = 'Session expired. Please refresh.';
        } else {
            $v = validateCategoryForm($_POST, $catId);
            if (!$v['valid']) {
                $editErrors = $v['errors'];
                $editData   = [
                    'name'        => trim((string)($_POST['name']        ?? '')),
                    'slug'        => trim((string)($_POST['slug']        ?? '')),
                    'description' => trim((string)($_POST['description'] ?? '')),
                    'icon'        => trim((string)($_POST['icon']        ?? '')),
                    'status'      => trim((string)($_POST['status']      ?? 'active')),
                    'sort_order'  => (int)($_POST['sort_order'] ?? 0),
                ];
            } else {
                if (updateCategory($catId, $v['data'])) {
                    redirect(siteUrl('admin/categories.php?action=edit&id=' . $catId . '&updated=1'));
                } else {
                    $editErrors[] = 'Failed to update category. Please try again.';
                    $editData = $v['data'];
                }
            }
        }
    }
    /* Re-display flash on edit page */
    if ($flashMsg === '' && isset($_GET['created'])) { $flashMsg = '✅ Category created.'; $flashType = 'success'; }
    if ($flashMsg === '' && isset($_GET['updated']))  { $flashMsg = '✅ Category updated.'; $flashType = 'success'; }
}

/* ================================================================
   LIST MODE
   ================================================================ */
$listFilters = [];
$listPage    = 1;
$listResult  = ['categories'=>[],'total'=>0,'page'=>1,'per_page'=>15,'total_pages'=>0];
if ($action === '' || $action === 'list') {
    $listFilters = [
        'q'      => trim(mb_substr((string)($_GET['q']      ?? ''), 0, 100)),
        'status' => trim((string)($_GET['status'] ?? '')),
        'sort'   => trim((string)($_GET['sort']   ?? 'sort_order')),
    ];
    $listPage   = max(1, (int)($_GET['page'] ?? 1));
    $listResult = getAdminCategoriesList($listFilters, $listPage, 15);
}

/* CSRF for all forms */
$pageToken = generateCsrfToken(CAT_CSRF_KEY);

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
          <?= $action === 'add' ? 'Add Category' : ($action === 'edit' ? 'Edit Category' : 'Categories') ?>
        </h2>
      </div>
      <div class="admin-topbar-actions">
        <?php if ($action !== 'add'): ?>
          <a href="<?= e(siteUrl('admin/categories.php?action=add')) ?>"
             class="admin-btn admin-btn-primary admin-btn-sm">➕ Add Category</a>
        <?php endif; ?>
        <a href="<?= e(siteUrl('category.php')) ?>" target="_blank" rel="noopener"
           class="admin-btn admin-btn-ghost admin-btn-sm">🌐 Public</a>
        <a href="<?= e(siteUrl('admin/games.php')) ?>"
           class="admin-btn admin-btn-ghost admin-btn-sm">🎮 Games</a>
      </div>
    </div>

    <div class="admin-content admin-categories-page">

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
        <h1 style="font-size:1.3rem;font-weight:800;margin-bottom:.3rem;">Add Category</h1>
        <p class="text-muted text-sm">Create a new category to organise game listings.</p>
      </div>

      <?php if (!empty($addErrors)): ?>
        <div class="admin-alert admin-alert-danger" style="margin-bottom:1.25rem;">
          <?php foreach ($addErrors as $er): echo '<div>' . e($er) . '</div>'; endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="admin-legal-note admin-category-legal-note">
        <strong>📋 Category Safety Reminder:</strong>
        Use categories to organise legal, authorized, official, freeware, open-source,
        demo, indie-permission, store-based, or permission-based game listings.
        Do not create category names that promote unauthorized content.
      </div>

      <form method="POST"
            action="<?= e(siteUrl('admin/categories.php?action=add')) ?>"
            id="catForm" class="admin-category-form-page" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($pageToken) ?>">
        <?php $fd = $addData; include __DIR__ . '/partials/cat-form-fields.php'; ?>
        <div class="admin-form-card">
          <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary"
                    id="catFormSubmit" data-disable-on-submit>
              ✅ Create Category
            </button>
            <a href="<?= e(siteUrl('admin/categories.php')) ?>"
               class="admin-btn admin-btn-ghost">← Back to List</a>
          </div>
        </div>
      </form>

<?php /* ========================================================
           EDIT MODE
           ======================================================== */
elseif ($action === 'edit' && $editCat !== null): ?>

      <div class="admin-page-header">
        <h1 style="font-size:1.3rem;font-weight:800;margin-bottom:.3rem;">
          Edit: <?= e(truncateText($editCat['name'] ?? '', 40)) ?>
        </h1>
        <p class="text-muted text-sm">
          ID #<?= (int)$editCat['id'] ?> — Slug: <code><?= e($editCat['slug'] ?? '') ?></code>
        </p>
      </div>

      <?php if (!empty($editErrors)): ?>
        <div class="admin-alert admin-alert-danger" style="margin-bottom:1.25rem;">
          <?php foreach ($editErrors as $er): echo '<div>' . e($er) . '</div>'; endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="admin-legal-note admin-category-legal-note">
        <strong>📋 Category Safety Reminder:</strong>
        Only organise legal, authorized, or permission-based game listings in this category.
      </div>

      <form method="POST"
            action="<?= e(siteUrl('admin/categories.php?action=edit&id=' . (int)$editCat['id'])) ?>"
            id="catForm" class="admin-category-form-page" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e($pageToken) ?>">
        <?php
        $fd = !empty($editData) ? $editData : $editCat;
        include __DIR__ . '/partials/cat-form-fields.php';
        ?>
        <div class="admin-form-card">
          <div class="admin-form-actions">
            <button type="submit" class="admin-btn admin-btn-primary"
                    id="catFormSubmit" data-disable-on-submit>
              💾 Save Changes
            </button>
            <?php if (($editCat['status'] ?? '') === 'active'): ?>
              <a href="<?= e(siteUrl('category.php?slug=' . rawurlencode($editCat['slug'] ?? ''))) ?>"
                 target="_blank" rel="noopener"
                 class="admin-btn admin-btn-ghost">🌐 View Public</a>
            <?php endif; ?>
            <a href="<?= e(siteUrl('admin/categories.php')) ?>"
               class="admin-btn admin-btn-ghost">← Back to List</a>
          </div>
        </div>
      </form>

<?php /* ========================================================
           LIST MODE
           ======================================================== */
else:
    $categories = $listResult['categories'];
    $totalCats  = $listResult['total'];
    $totalPages = $listResult['total_pages'];
?>

      <div class="admin-page-header" style="margin-bottom:1rem;">
        <h1 style="font-size:1.2rem;font-weight:800;margin-bottom:.2rem;">All Categories</h1>
        <p class="text-muted text-sm">
          <?= e((string)$totalCats) ?> categor<?= $totalCats !== 1 ? 'ies' : 'y' ?>
          — organise legal game listings.
        </p>
      </div>

      <!-- Filters -->
      <div class="admin-filter-panel" style="margin-bottom:1.25rem;">
        <form method="GET" action="<?= e(siteUrl('admin/categories.php')) ?>"
              class="admin-filter-form" id="catFilterForm">
          <div class="admin-filter-grid admin-cat-filter-grid">
            <div class="admin-form-group">
              <label class="admin-form-label">Search</label>
              <input type="search" name="q" class="admin-form-control"
                     placeholder="Name, slug, description..."
                     value="<?= e($listFilters['q']) ?>" maxlength="100">
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label">Status</label>
              <select name="status" class="admin-form-control">
                <option value="">All Statuses</option>
                <?php foreach ($statusOptions as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $listFilters['status'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="admin-form-group">
              <label class="admin-form-label">Sort</label>
              <select name="sort" class="admin-form-control">
                <?php foreach ($sortOptions as $v => $l): ?>
                  <option value="<?= e($v) ?>"
                    <?= $listFilters['sort'] === $v ? 'selected' : '' ?>>
                    <?= e($l) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="admin-filter-actions">
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm">Apply</button>
            <a href="<?= e(siteUrl('admin/categories.php')) ?>"
               class="admin-btn admin-btn-ghost admin-btn-sm">Reset</a>
          </div>
        </form>
      </div>

      <!-- Table -->
      <?php if (!empty($categories)): ?>
      <div class="admin-table-card">
        <div class="admin-table-wrap">
          <table class="admin-table admin-category-table" role="grid">
            <thead>
              <tr>
                <th style="width:44px;">Icon</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Status</th>
                <th>Games</th>
                <th>Order</th>
                <th>Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $cat):
                $cId     = (int)$cat['id'];
                $cStatus = $cat['status'] ?? 'inactive';
                $cSlug   = $cat['slug']   ?? '';
                $cCount  = (int)($cat['game_count'] ?? 0);
              ?>
              <tr>
                <td class="admin-category-icon">
                  <?= e($cat['icon'] ?? '🏷️') ?>
                </td>
                <td class="admin-category-name-cell">
                  <strong><?= e($cat['name'] ?? '') ?></strong>
                  <span class="admin-category-meta">ID: <?= $cId ?></span>
                </td>
                <td>
                  <code class="admin-category-slug"><?= e(truncateText($cSlug, 30)) ?></code>
                </td>
                <td class="admin-category-description">
                  <?= e(truncateText($cat['description'] ?? '', 70)) ?>
                </td>
                <td>
                  <span class="admin-status-badge <?= e(getCategoryStatusBadgeClass($cStatus)) ?>">
                    <?= e(getReadableCategoryStatus($cStatus)) ?>
                  </span>
                </td>
                <td class="admin-category-count">
                  <?php if ($cCount > 0 && $cSlug !== ''): ?>
                    <a href="<?= e(siteUrl('admin/games.php?category=' . rawurlencode($cSlug))) ?>"
                       class="admin-mini-link"><?= $cCount ?></a>
                  <?php else: ?>
                    <span class="text-muted"><?= $cCount ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="admin-sort-order-pill"><?= (int)$cat['sort_order'] ?></span>
                </td>
                <td class="text-xs text-muted">
                  <?= e(date('d M y', strtotime($cat['updated_at'] ?? $cat['created_at'] ?? 'now'))) ?>
                </td>
                <td>
                  <div class="admin-action-group">
                    <a href="<?= e(siteUrl('admin/categories.php?action=edit&id=' . $cId)) ?>"
                       class="admin-action-btn admin-btn-ghost admin-btn-sm"
                       title="Edit">✏️</a>
                    <?php if ($cStatus === 'active' && $cSlug !== ''): ?>
                      <a href="<?= e(siteUrl('category.php?slug=' . rawurlencode($cSlug))) ?>"
                         target="_blank" rel="noopener"
                         class="admin-action-btn admin-btn-ghost admin-btn-sm"
                         title="View public">🌐</a>
                    <?php endif; ?>
                    <!-- Status form -->
                    <form method="POST"
                          action="<?= e(siteUrl('admin/categories.php')) ?>"
                          style="display:inline;">
                      <input type="hidden" name="csrf_token" value="<?= e($pageToken) ?>">
                      <input type="hidden" name="action_status" value="1">
                      <input type="hidden" name="category_id" value="<?= $cId ?>">
                      <?php if ($cStatus !== 'active'): ?>
                        <input type="hidden" name="new_status" value="active">
                        <button type="submit"
                                class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                title="Set active">✅</button>
                      <?php endif; ?>
                      <?php if ($cStatus !== 'inactive'): ?>
                        <input type="hidden" name="new_status" value="inactive">
                        <button type="submit"
                                class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                title="Set inactive"
                                data-confirm-action="Set this category to inactive?">⏸️</button>
                      <?php endif; ?>
                      <?php if ($cStatus !== 'archived'): ?>
                        <input type="hidden" name="new_status" value="archived">
                        <button type="submit"
                                class="admin-action-btn admin-btn-ghost admin-btn-sm"
                                title="Archive"
                                data-confirm-action="Archive this category? It will be hidden publicly.">🗄️</button>
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
        $pCur = $listPage;
        $pS   = max(1, $pCur - 2);
        $pE   = min($totalPages, $pCur + 2);
      ?>
      <nav class="admin-pagination">
        <?php if ($pCur > 1): ?>
          <a href="<?= e(buildAdminCatsUrl($listFilters, ['page'=>(string)($pCur-1)])) ?>"
             class="admin-page-btn">← Prev</a>
        <?php else: ?>
          <span class="admin-page-btn disabled">← Prev</span>
        <?php endif; ?>
        <?php if ($pS > 1): ?>
          <a href="<?= e(buildAdminCatsUrl($listFilters, ['page'=>'1'])) ?>"
             class="admin-page-btn">1</a>
          <?php if ($pS > 2): ?><span class="admin-page-ellipsis">…</span><?php endif; ?>
        <?php endif; ?>
        <?php for ($pp = $pS; $pp <= $pE; $pp++): ?>
          <?php if ($pp === $pCur): ?>
            <span class="admin-page-btn active"><?= $pp ?></span>
          <?php else: ?>
            <a href="<?= e(buildAdminCatsUrl($listFilters, ['page'=>(string)$pp])) ?>"
               class="admin-page-btn"><?= $pp ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($pE < $totalPages): ?>
          <?php if ($pE < $totalPages - 1): ?><span class="admin-page-ellipsis">…</span><?php endif; ?>
          <a href="<?= e(buildAdminCatsUrl($listFilters, ['page'=>(string)$totalPages])) ?>"
             class="admin-page-btn"><?= $totalPages ?></a>
        <?php endif; ?>
        <?php if ($pCur < $totalPages): ?>
          <a href="<?= e(buildAdminCatsUrl($listFilters, ['page'=>(string)($pCur+1)])) ?>"
             class="admin-page-btn">Next →</a>
        <?php else: ?>
          <span class="admin-page-btn disabled">Next →</span>
        <?php endif; ?>
      </nav>
      <?php endif; ?>

      <?php else: ?>
      <div class="admin-empty-state">
        <span>📁</span>
        <p>No categories found.</p>
        <a href="<?= e(siteUrl('admin/categories.php?action=add')) ?>"
           class="admin-btn admin-btn-primary admin-btn-sm">➕ Add Category</a>
      </div>
      <?php endif; ?>

<?php endif; /* end mode switch */ ?>

    </div>
  </main>
</div>

<?php require_once INCLUDES_PATH . '/admin-footer.php'; ?>
