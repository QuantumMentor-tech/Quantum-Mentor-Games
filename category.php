<?php
/**
 * QMGames Store — Category System
 * Step: 8 — Category System and Category Pages
 *
 * Dual-mode page:
 *   - No ?slug  → Category directory (all categories)
 *   - ?slug=xxx → Single category detail with filtered games
 */

require_once __DIR__ . '/includes/init.php';

/* ================================================================
   1. DETECT MODE — directory vs. single category
   ================================================================ */
$rawSlug    = isset($_GET['slug']) ? trim((string)$_GET['slug']) : '';
$isDetail   = ($rawSlug !== '');
$category   = null;
$notFound   = false;

if ($isDetail) {
    $category = getCategoryBySlug($rawSlug);
    if ($category === null) {
        $notFound = true;
    }
}

/* ================================================================
   2. READ FILTER PARAMS (detail mode only)
   ================================================================ */
$f_q       = $isDetail ? trim(mb_substr((string)($_GET['q'] ?? ''), 0, 100)) : '';
$f_license = $isDetail ? trim((string)($_GET['license'] ?? '')) : '';
$f_low_end = ($isDetail && ($_GET['low_end'] ?? '') === '1') ? '1' : '';
$f_sort    = $isDetail ? trim((string)($_GET['sort'] ?? 'latest')) : 'latest';
$f_page    = max(1, (int)($_GET['page'] ?? 1));
$hasFilters = ($f_q !== '' || $f_license !== '' || $f_low_end === '1');

$catFilters = [
    'q'       => $f_q,
    'license' => $f_license,
    'low_end' => $f_low_end,
    'sort'    => $f_sort,
];

/* ================================================================
   3. FETCH DATA
   ================================================================ */
if ($isDetail && !$notFound) {
    $perPage    = 12;
    $catResult  = getCategoryGames((int)$category['id'], $catFilters, $f_page, $perPage);
    $catGames   = $catResult['games'];
    $catTotal   = $catResult['total'];
    $catPages   = $catResult['total_pages'];
    $catPage    = $catResult['page'];
} else {
    $allCats    = getAllActiveCategoriesWithCounts();
    $highlights = getCategoryHighlights();
}

/* ================================================================
   4. LICENSE OPTIONS
   ================================================================ */
$licenseOptions = [
    ''                 => 'All License Types',
    'freeware'         => 'Freeware',
    'open_source'      => 'Open Source',
    'demo'             => 'Demo',
    'official_mirror'  => 'Official Mirror',
    'indie_permission' => 'Indie Permission',
    'other_authorized' => 'Other Authorized',
];

$sortOptions = [
    'latest'          => 'Latest',
    'oldest'          => 'Oldest',
    'most_viewed'     => 'Most Viewed',
    'most_downloaded' => 'Most Downloaded',
    'az'              => 'A to Z',
    'za'              => 'Z to A',
    'featured'        => 'Featured First',
    'trending'        => 'Trending First',
];

/* ================================================================
   5. PAGE META
   ================================================================ */
$activePage = 'categories';

if (!$isDetail) {
    $pageTitle       = 'Categories';
    $pageDescription = 'Browse game categories on QMGames Store and discover legal, '
                     . 'authorized, freeware, open-source, demo, and permission-based games.';
} elseif ($notFound) {
    $pageTitle       = 'Category Not Found';
    $pageDescription = 'The requested category could not be found on QMGames Store.';
} else {
    $pageTitle       = $category['name'];
    $pageDescription = !empty($category['description'])
        ? $category['description']
        : 'Browse legal and authorized ' . $category['name'] . ' games on QMGames Store.';
}

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="category-page">

<?php /* ============================================================
          A) CATEGORY NOT FOUND
          ============================================================ */
if ($notFound): ?>

<section class="page-hero" aria-labelledby="notfound-title">
  <div class="container">
    <h1 class="page-title" id="notfound-title">Category Not Found</h1>
    <p class="page-subtitle">
      The category you are looking for does not exist or is no longer active.
    </p>
  </div>
</section>

<section class="section-sm">
  <div class="container-sm">
    <div class="card">
      <div class="empty-state">
        <span class="empty-state-icon">📁</span>
        <h3>Category Not Found</h3>
        <p>
          The category <strong>&ldquo;<?= e($rawSlug) ?>&rdquo;</strong>
          does not exist or is currently inactive.
        </p>
        <div class="empty-state-actions">
          <a href="<?= e(getCategoryDirectoryUrl()) ?>" class="btn btn-primary">
            View All Categories
          </a>
          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
            Browse All Games
          </a>
          <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-ghost">
            Back to Home
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php /* ============================================================
          B) SINGLE CATEGORY DETAIL PAGE
          ============================================================ */
elseif ($isDetail): ?>

<!-- ── Category Hero ── -->
<section class="category-hero" aria-labelledby="cat-detail-title">
  <div class="container">
    <div class="category-hero-grid">

      <div class="category-hero-content">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="<?= e(siteUrl('index.php')) ?>">Home</a>
          <span class="breadcrumb-sep">›</span>
          <a href="<?= e(getCategoryDirectoryUrl()) ?>">Categories</a>
          <span class="breadcrumb-sep">›</span>
          <span><?= e($category['name']) ?></span>
        </nav>

        <?php if (!empty($category['icon'])): ?>
          <div class="category-icon-large" aria-hidden="true">
            <?= e($category['icon']) ?>
          </div>
        <?php endif; ?>

        <h1 class="page-title" id="cat-detail-title">
          <?= e($category['name']) ?>
        </h1>

        <?php if (!empty($category['description'])): ?>
          <p class="page-subtitle"><?= e($category['description']) ?></p>
        <?php endif; ?>

        <!-- Stats -->
        <div class="category-stats">
          <div class="category-stat">
            <span class="category-stat-value">
              <?= !$hasFilters ? e((string)($catTotal)) : '—' ?>
            </span>
            <span class="category-stat-label">Games</span>
          </div>
          <div class="category-stat">
            <span class="category-stat-value badge badge-success">✅</span>
            <span class="category-stat-label">Legal Only</span>
          </div>
        </div>

        <!-- CTA buttons -->
        <div class="category-hero-actions">
          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-outline btn-sm">
            Browse All Games
          </a>
          <a href="<?= e(getCategoryDirectoryUrl()) ?>" class="btn btn-ghost btn-sm">
            ← All Categories
          </a>
        </div>
      </div><!-- /.category-hero-content -->

    </div><!-- /.category-hero-grid -->
  </div>
</section>


<!-- ── Filter + Results ── -->
<section class="section-sm">
  <div class="container">

    <!-- Legal strip -->
    <div class="games-legal-strip">
      🛡️ QMGames Store categories are designed for legal, authorized, official,
      freeware, open-source, demo, or permission-based games only.
    </div>

    <div class="games-layout">

      <!-- ── Filter Panel ── -->
      <aside class="games-filter-panel" aria-label="Filter games in category">

        <div class="filter-panel-header">
          <h2 class="filter-panel-title">🔧 Filters</h2>
          <?php if ($hasFilters): ?>
            <a href="<?= e(getCategoryUrl($category['slug'])) ?>"
               class="filter-reset-link" aria-label="Clear all filters">
              Clear All ✕
            </a>
          <?php endif; ?>
        </div>

        <form method="GET"
              action="<?= e(siteUrl('category.php')) ?>"
              class="filter-form"
              id="catFilterForm">

          <!-- Preserve slug -->
          <input type="hidden" name="slug" value="<?= e($category['slug']) ?>">

          <!-- Search -->
          <div class="filter-group">
            <label class="filter-label" for="cf_q">🔍 Search</label>
            <input type="search"
                   id="cf_q" name="q"
                   class="form-control filter-input"
                   placeholder="Search games in this category..."
                   value="<?= e($f_q) ?>"
                   maxlength="100"
                   autocomplete="off">
          </div>

          <!-- License -->
          <div class="filter-group">
            <label class="filter-label" for="cf_license">⚖️ License</label>
            <select id="cf_license" name="license" class="form-control filter-select">
              <?php foreach ($licenseOptions as $val => $label): ?>
                <option value="<?= e($val) ?>"
                  <?= $f_license === $val ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Low-end -->
          <div class="filter-group">
            <label class="filter-label" for="cf_lowend">🖥️ System</label>
            <select id="cf_lowend" name="low_end" class="form-control filter-select">
              <option value=""  <?= $f_low_end === '' ? 'selected' : '' ?>>All Games</option>
              <option value="1" <?= $f_low_end === '1' ? 'selected' : '' ?>>Low-End PC Friendly</option>
            </select>
          </div>

          <!-- Sort -->
          <div class="filter-group">
            <label class="filter-label" for="cf_sort">↕️ Sort By</label>
            <select id="cf_sort" name="sort" class="form-control filter-select">
              <?php foreach ($sortOptions as $val => $label): ?>
                <option value="<?= e($val) ?>"
                  <?= $f_sort === $val ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-actions">
            <button type="submit" class="btn btn-primary btn-block">
              Apply Filters
            </button>
            <a href="<?= e(getCategoryUrl($category['slug'])) ?>"
               class="btn btn-secondary btn-block">Reset</a>
          </div>

        </form>

      </aside><!-- /.games-filter-panel -->

      <!-- ── Results Area ── -->
      <div class="games-results">

        <!-- Results toolbar -->
        <div class="results-toolbar">
          <div class="results-summary">
            <?php if ($catTotal === 0): ?>
              <span>No games found in <strong><?= e($category['name']) ?></strong></span>
            <?php else: ?>
              <span>
                Showing <strong><?= e((string)count($catGames)) ?></strong>
                of <strong><?= e((string)$catTotal) ?></strong>
                game<?= $catTotal !== 1 ? 's' : '' ?>
                in <strong><?= e($category['name']) ?></strong>
              </span>
            <?php endif; ?>
          </div>

          <!-- Active filter badges -->
          <?php if ($hasFilters): ?>
          <div class="active-filters">
            <?php if ($f_q !== ''): ?>
              <span class="filter-badge">
                Search: <strong><?= e($f_q) ?></strong>
                <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['q'=>'','page'=>''])) ?>"
                   class="filter-badge-remove">✕</a>
              </span>
            <?php endif; ?>
            <?php if ($f_license !== ''): ?>
              <span class="filter-badge">
                License: <strong><?= e($licenseOptions[$f_license] ?? $f_license) ?></strong>
                <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['license'=>'','page'=>''])) ?>"
                   class="filter-badge-remove">✕</a>
              </span>
            <?php endif; ?>
            <?php if ($f_low_end === '1'): ?>
              <span class="filter-badge">
                Low-End PC
                <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['low_end'=>'','page'=>''])) ?>"
                   class="filter-badge-remove">✕</a>
              </span>
            <?php endif; ?>
          </div>
          <?php endif; ?>
        </div><!-- /.results-toolbar -->

        <!-- Game Grid -->
        <?php if (!empty($catGames)): ?>
          <div class="games-grid category-game-grid">
            <?php foreach ($catGames as $game):
              $gSlug  = e($game['slug'] ?? '');
              $gTitle = e($game['title'] ?? 'Untitled');
              $gDesc  = e(truncateText($game['short_description'] ?? '', 85));
              $gPlat  = e($game['platform'] ?? 'Windows PC');
              $gSize  = !empty($game['game_size']) ? e($game['game_size']) : 'TBA';
              $gLic   = $game['license_type'] ?? 'freeware';
              $gBadge = getLicenseBadgeClass($gLic);
              $gLabel = getLicenseLabel($gLic);
              $gCover = !empty($game['cover_image'])
                          ? e(siteUrl($game['cover_image']))
                          : getPlaceholderImage('cover');
              $gHref  = $gSlug
                          ? e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug'])))
                          : '#';
              $gViews = (int)($game['views_count'] ?? 0);
              $gDls   = (int)($game['downloads_count'] ?? 0);
            ?>
            <a href="<?= $gHref ?>"
               class="game-card card"
               aria-label="View details for <?= $gTitle ?>">
              <div class="game-card-cover">
                <img src="<?= $gCover ?>" alt="<?= $gTitle ?> cover" loading="lazy">
                <div class="game-card-overlay-badges">
                  <?php if (!empty($game['is_featured'])): ?>
                    <span class="card-overlay-badge card-overlay-featured">⭐ Featured</span>
                  <?php endif; ?>
                  <?php if (!empty($game['is_trending'])): ?>
                    <span class="card-overlay-badge card-overlay-trending">📈 Trending</span>
                  <?php endif; ?>
                  <?php if (!empty($game['is_low_end_pc'])): ?>
                    <span class="card-overlay-badge card-overlay-lowend">🖥️ Low-End</span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="game-card-body">
                <p class="game-card-meta"><?= $gPlat ?> · <?= $gSize ?></p>
                <h3 class="game-card-title"><?= $gTitle ?></h3>
                <p class="game-card-description"><?= $gDesc ?></p>
                <?php if ($gViews > 0 || $gDls > 0): ?>
                <div class="game-card-stats">
                  <?php if ($gViews > 0): ?>
                    <span class="game-stat">👁 <?= e(formatNumberShort($gViews)) ?></span>
                  <?php endif; ?>
                  <?php if ($gDls > 0): ?>
                    <span class="game-stat">⬇ <?= e(formatNumberShort($gDls)) ?></span>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="game-card-actions-row">
                  <span class="badge <?= $gBadge ?>"><?= $gLabel ?></span>
                  <span class="btn btn-sm btn-outline">View Details →</span>
                </div>
              </div>
            </a>
            <?php endforeach; ?>
          </div><!-- /.games-grid -->

        <?php else: ?>
          <div class="card">
            <div class="empty-state">
              <span class="empty-state-icon">🎮</span>
              <h3>No Games Found in This Category</h3>
              <p>
                <?php if ($hasFilters): ?>
                  No active games match your filters in
                  <strong><?= e($category['name']) ?></strong>.
                  Try adjusting or clearing your filters.
                <?php else: ?>
                  <strong><?= e($category['name']) ?></strong> does not have active
                  games yet. Check back later or browse all games.
                <?php endif; ?>
              </p>
              <div class="empty-state-actions">
                <?php if ($hasFilters): ?>
                  <a href="<?= e(getCategoryUrl($category['slug'])) ?>"
                     class="btn btn-primary">Clear Filters</a>
                <?php endif; ?>
                <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
                  Browse All Games
                </a>
                <a href="<?= e(getCategoryDirectoryUrl()) ?>" class="btn btn-ghost">
                  All Categories
                </a>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if (isset($catPages) && $catPages > 1): ?>
        <nav class="pagination" aria-label="Page navigation">

          <?php $prevDis = $catPage <= 1; ?>
          <?php if ($prevDis): ?>
            <span class="pagination-link pagination-prev disabled">← Prev</span>
          <?php else: ?>
            <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['page'=>(string)($catPage-1)])) ?>"
               class="pagination-link pagination-prev">← Prev</a>
          <?php endif; ?>

          <?php
          $range = 2;
          $pStart = max(1, $catPage - $range);
          $pEnd   = min($catPages, $catPage + $range);
          if ($pStart > 1): ?>
            <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['page'=>'1'])) ?>"
               class="pagination-link">1</a>
            <?php if ($pStart > 2): ?>
              <span class="pagination-ellipsis">…</span>
            <?php endif; ?>
          <?php endif; ?>

          <?php for ($pp = $pStart; $pp <= $pEnd; $pp++): ?>
            <?php if ($pp === $catPage): ?>
              <span class="pagination-link active" aria-current="page"><?= $pp ?></span>
            <?php else: ?>
              <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['page'=>(string)$pp])) ?>"
                 class="pagination-link"><?= $pp ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($pEnd < $catPages): ?>
            <?php if ($pEnd < $catPages - 1): ?>
              <span class="pagination-ellipsis">…</span>
            <?php endif; ?>
            <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['page'=>(string)$catPages])) ?>"
               class="pagination-link"><?= $catPages ?></a>
          <?php endif; ?>

          <?php $nextDis = $catPage >= $catPages; ?>
          <?php if ($nextDis): ?>
            <span class="pagination-link pagination-next disabled">Next →</span>
          <?php else: ?>
            <a href="<?= e(buildCategoryFilterUrl($category['slug'], $catFilters, ['page'=>(string)($catPage+1)])) ?>"
               class="pagination-link pagination-next">Next →</a>
          <?php endif; ?>

        </nav>
        <?php endif; ?>

      </div><!-- /.games-results -->
    </div><!-- /.games-layout -->
  </div><!-- /.container -->
</section>


<?php /* ============================================================
          C) CATEGORY DIRECTORY PAGE
          ============================================================ */
else: ?>

<!-- ── Directory Hero ── -->
<section class="category-directory-hero page-hero" aria-labelledby="cat-dir-title">
  <div class="container">
    <span class="section-label">Explore</span>
    <h1 class="page-title" id="cat-dir-title">Game Categories</h1>
    <p class="page-subtitle">
      Browse legal games by genre, style, platform, and system type.
    </p>
    <p class="page-subtitle" style="font-size:.88rem;margin-top:.5rem;">
      Find action games, racing games, RPGs, low-end PC games, open-source
      games, demos, and more from one clean gaming hub.
    </p>
  </div>
</section>


<section class="section-sm">
  <div class="container">

    <!-- Legal strip -->
    <div class="games-legal-strip">
      🛡️ QMGames Store categories are designed for legal, authorized, official,
      freeware, open-source, demo, or permission-based games only.
    </div>

    <?php if (!empty($allCats)): ?>

      <!-- Category Grid -->
      <div class="category-grid category-directory-grid" id="categoryGrid">
        <?php foreach ($allCats as $cat):
          $catIcon  = !empty($cat['icon']) ? $cat['icon'] : '🎮';
          $catSlug  = e($cat['slug']);
          $catName  = e($cat['name']);
          $catDesc  = !empty($cat['description'])
                          ? e(truncateText($cat['description'], 70))
                          : '';
          $catCount = (int)($cat['game_count'] ?? 0);
        ?>
        <a href="<?= e(getCategoryUrl($cat['slug'])) ?>"
           class="category-card card"
           aria-label="Browse <?= $catName ?> — <?= $catCount ?> games">
          <div class="category-card-inner">
            <span class="category-card-icon" aria-hidden="true"><?= $catIcon ?></span>
            <h3 class="category-card-title"><?= $catName ?></h3>
            <?php if ($catDesc !== ''): ?>
              <p class="category-card-description"><?= $catDesc ?></p>
            <?php endif; ?>
            <div class="category-card-footer">
              <span class="category-card-count">
                <?= $catCount ?> game<?= $catCount !== 1 ? 's' : '' ?>
              </span>
              <span class="btn btn-xs btn-outline">Browse →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

    <?php else: ?>

      <!-- Empty state -->
      <div class="card">
        <div class="empty-state">
          <span class="empty-state-icon">📁</span>
          <h3>No Categories Found</h3>
          <p>
            Categories will appear here after they are added to the database.
            Ensure XAMPP MySQL is running and seed data is imported.
          </p>
          <div class="empty-state-actions">
            <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-primary">Back to Home</a>
            <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">Browse Games</a>
          </div>
        </div>
      </div>

    <?php endif; ?>

    <!-- View All Games CTA -->
    <div class="section-cta" style="margin-top:2rem;">
      <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
        🎮 Browse All Games →
      </a>
    </div>

  </div>
</section>


<!-- ── Highlighted Categories Section ── -->
<?php if (!empty($highlights)): ?>
<section class="section-xs alt-section category-highlight-section"
         aria-labelledby="highlights-heading">
  <div class="container">

    <div class="section-header" style="margin-bottom:1.5rem;">
      <h2 id="highlights-heading" style="font-size:1.3rem;">
        Popular Category Shortcuts
      </h2>
      <p>Jump directly into your favourite game type.</p>
    </div>

    <div class="category-highlight-grid">
      <?php foreach ($highlights as $h):
        $hIcon = !empty($h['icon']) ? $h['icon'] : '🎮';
        $hName = e($h['name']);
        $hSlug = e($h['slug']);
      ?>
      <a href="<?= e(getCategoryUrl($h['slug'])) ?>"
         class="highlight-card card"
         aria-label="Browse <?= $hName ?>">
        <div class="highlight-card-inner">
          <span class="highlight-icon" aria-hidden="true"><?= $hIcon ?></span>
          <span class="highlight-name"><?= $hName ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

  </div>
</section>
<?php endif; ?>

<?php endif; // end directory mode ?>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
