<?php
/**
 * QMGames Store — Games Listing Page
 * Step: 7 — Games Listing Page Development
 *
 * Database-powered, paginated games browser with search,
 * category, license, platform, low-end-PC, and sort filters.
 * All queries use prepared statements. No raw errors shown.
 */

require_once __DIR__ . '/includes/init.php';

/* ================================================================
   1. READ & SANITISE GET PARAMETERS
   ================================================================ */
$f_q        = isset($_GET['q'])        ? trim(mb_substr((string)$_GET['q'], 0, 100)) : '';
$f_category = isset($_GET['category']) ? trim((string)$_GET['category'])  : '';
$f_license  = isset($_GET['license'])  ? trim((string)$_GET['license'])   : '';
$f_platform = isset($_GET['platform']) ? trim((string)$_GET['platform'])  : '';
$f_low_end  = (isset($_GET['low_end']) && $_GET['low_end'] === '1') ? '1' : '';
$f_sort     = isset($_GET['sort'])     ? trim((string)$_GET['sort'])      : 'latest';
$f_page     = max(1, (int)($_GET['page'] ?? 1));

/* ================================================================
   2. DETERMINE IF ANY FILTER IS ACTIVE
   ================================================================ */
$hasFilters = ($f_q !== '' || $f_category !== '' || $f_license !== ''
            || $f_platform !== '' || $f_low_end === '1');

/* ================================================================
   3. COLLECT FILTER ARRAY FOR HELPERS
   ================================================================ */
$filters = [
    'q'        => $f_q,
    'category' => $f_category,
    'license'  => $f_license,
    'platform' => $f_platform,
    'low_end'  => $f_low_end,
    'sort'     => $f_sort,
];

/* ================================================================
   4. FETCH DATA
   ================================================================ */
$perPage    = (int) GAMES_PER_PAGE;   // defined in config.php = 24; overriding to 12 here
$perPage    = 12;
$result     = getGamesList($filters, $f_page, $perPage);
$games      = $result['games'];
$totalGames = $result['total'];
$totalPages = $result['total_pages'];
$currentPage= $result['page'];

/* Sidebar / filter data */
$allCategories = getActiveCategories();
$allPlatforms  = getActivePlatforms();

/* ================================================================
   5. VALID LICENSE OPTIONS (display list)
   ================================================================ */
$licenseOptions = [
    ''                  => 'All License Types',
    'freeware'          => 'Freeware',
    'open_source'       => 'Open Source',
    'demo'              => 'Demo',
    'official_mirror'   => 'Official Mirror',
    'indie_permission'  => 'Indie Permission',
    'other_authorized'  => 'Other Authorized',
];

/* Static platform fallback if DB returns nothing */
$staticPlatforms = ['Windows PC', 'Linux', 'Mac', 'Browser', 'Multi-platform'];
if (empty($allPlatforms)) {
    $allPlatforms = $staticPlatforms;
}

/* Sort options */
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
   6. PAGE META
   ================================================================ */
$pageTitle       = 'All Games';
$pageDescription = 'Browse legal, authorized, freeware, open-source, demo, and '
                 . 'permission-based games on QMGames Store.';
$activePage      = 'games';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="games-page">

<!-- ================================================================
     SECTION 1: PAGE HERO
     ================================================================ -->
<section class="games-hero page-hero" aria-labelledby="games-page-title">
  <div class="container">
    <span class="section-label">Browse</span>
    <h1 class="page-title" id="games-page-title">All Games</h1>
    <p class="page-subtitle">
      Browse the QMGames Store collection of legal, safe, and high-quality game downloads.
      Use search, filters, and sorting to find the right game for your PC.
    </p>
    <!-- Hero stat badges -->
    <div class="games-hero-badges">
      <span class="hero-trust-badge">
        🎮 <?= $totalGames > 0 ? e((string)$totalGames) : '—' ?> Games
      </span>
      <span class="hero-trust-badge">
        📁 <?= count($allCategories) ?> Categories
      </span>
      <span class="hero-trust-badge">✅ Legal Downloads Only</span>
    </div>
  </div>
</section>


<!-- ================================================================
     SECTION 2: FILTER PANEL + RESULTS
     ================================================================ -->
<section class="section-sm games-section">
  <div class="container">

    <!-- ── Legal notice strip ── -->
    <div class="games-legal-strip">
      🛡️ QMGames Store lists legal, authorized, official, freeware, open-source,
      demo, or permission-based games only.
    </div>

    <div class="games-layout">

      <!-- ════════════ FILTER PANEL ════════════ -->
      <aside class="games-filter-panel" aria-label="Filter games">

        <div class="filter-panel-header">
          <h2 class="filter-panel-title">🔧 Filters</h2>
          <?php if ($hasFilters): ?>
            <a href="<?= e(siteUrl('games.php')) ?>"
               class="filter-reset-link" aria-label="Clear all filters">
              Clear All ✕
            </a>
          <?php endif; ?>
        </div>

        <form method="GET"
              action="<?= e(siteUrl('games.php')) ?>"
              class="filter-form"
              id="filterForm"
              aria-label="Game filters">

          <!-- Search -->
          <div class="filter-group">
            <label class="filter-label" for="f_q">🔍 Search</label>
            <input type="search"
                   id="f_q"
                   name="q"
                   class="form-control filter-input"
                   placeholder="Search by title, developer, platform..."
                   value="<?= e($f_q) ?>"
                   maxlength="100"
                   autocomplete="off">
          </div>

          <!-- Category -->
          <div class="filter-group">
            <label class="filter-label" for="f_cat">📁 Category</label>
            <select id="f_cat" name="category" class="form-control filter-select">
              <option value="">All Categories</option>
              <?php foreach ($allCategories as $cat): ?>
                <option value="<?= e($cat['slug']) ?>"
                  <?= $f_category === $cat['slug'] ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- License Type -->
          <div class="filter-group">
            <label class="filter-label" for="f_license">⚖️ License</label>
            <select id="f_license" name="license" class="form-control filter-select">
              <?php foreach ($licenseOptions as $val => $label): ?>
                <option value="<?= e($val) ?>"
                  <?= $f_license === $val ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Platform -->
          <div class="filter-group">
            <label class="filter-label" for="f_platform">💻 Platform</label>
            <select id="f_platform" name="platform" class="form-control filter-select">
              <option value="">All Platforms</option>
              <?php foreach ($allPlatforms as $plat): ?>
                <option value="<?= e($plat) ?>"
                  <?= $f_platform === $plat ? 'selected' : '' ?>>
                  <?= e($plat) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Low-end PC -->
          <div class="filter-group">
            <label class="filter-label" for="f_lowend">🖥️ System</label>
            <select id="f_lowend" name="low_end" class="form-control filter-select">
              <option value="" <?= $f_low_end === '' ? 'selected' : '' ?>>All Games</option>
              <option value="1"  <?= $f_low_end === '1' ? 'selected' : '' ?>>Low-End PC Friendly</option>
            </select>
          </div>

          <!-- Sort -->
          <div class="filter-group">
            <label class="filter-label" for="f_sort">↕️ Sort By</label>
            <select id="f_sort" name="sort" class="form-control filter-select">
              <?php foreach ($sortOptions as $val => $label): ?>
                <option value="<?= e($val) ?>"
                  <?= $f_sort === $val ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Buttons -->
          <div class="filter-actions">
            <button type="submit" class="btn btn-primary btn-block">
              Apply Filters
            </button>
            <a href="<?= e(siteUrl('games.php')) ?>"
               class="btn btn-secondary btn-block">
              Reset
            </a>
          </div>

        </form>

      </aside><!-- /.games-filter-panel -->


      <!-- ════════════ RESULTS AREA ════════════ -->
      <div class="games-results">

        <!-- Results toolbar -->
        <div class="results-toolbar">
          <div class="results-summary">
            <?php if ($f_q !== '' || $hasFilters): ?>
              <?php if ($totalGames === 0): ?>
                <span>No games found</span>
              <?php else: ?>
                <span>
                  Showing <strong><?= e((string)count($games)) ?></strong>
                  of <strong><?= e((string)$totalGames) ?></strong> game<?= $totalGames !== 1 ? 's' : '' ?>
                </span>
              <?php endif; ?>
            <?php else: ?>
              <span>
                Showing <strong><?= e((string)count($games)) ?></strong>
                of <strong><?= e((string)$totalGames) ?></strong> game<?= $totalGames !== 1 ? 's' : '' ?>
              </span>
            <?php endif; ?>
          </div>

          <!-- Active filter badges -->
          <?php if ($hasFilters): ?>
          <div class="active-filters" aria-label="Active filters">
            <?php if ($f_q !== ''): ?>
              <span class="filter-badge">
                Search: <strong><?= e($f_q) ?></strong>
                <a href="<?= e(buildFilterUrl($filters, ['q' => '', 'page' => ''])) ?>"
                   class="filter-badge-remove" aria-label="Remove search filter">✕</a>
              </span>
            <?php endif; ?>
            <?php if ($f_category !== ''): ?>
              <?php
              $catLabel = '';
              foreach ($allCategories as $c) {
                  if ($c['slug'] === $f_category) { $catLabel = $c['name']; break; }
              }
              ?>
              <span class="filter-badge">
                Category: <strong><?= e($catLabel ?: $f_category) ?></strong>
                <a href="<?= e(buildFilterUrl($filters, ['category' => '', 'page' => ''])) ?>"
                   class="filter-badge-remove" aria-label="Remove category filter">✕</a>
              </span>
            <?php endif; ?>
            <?php if ($f_license !== ''): ?>
              <span class="filter-badge">
                License: <strong><?= e($licenseOptions[$f_license] ?? $f_license) ?></strong>
                <a href="<?= e(buildFilterUrl($filters, ['license' => '', 'page' => ''])) ?>"
                   class="filter-badge-remove" aria-label="Remove license filter">✕</a>
              </span>
            <?php endif; ?>
            <?php if ($f_platform !== ''): ?>
              <span class="filter-badge">
                Platform: <strong><?= e($f_platform) ?></strong>
                <a href="<?= e(buildFilterUrl($filters, ['platform' => '', 'page' => ''])) ?>"
                   class="filter-badge-remove" aria-label="Remove platform filter">✕</a>
              </span>
            <?php endif; ?>
            <?php if ($f_low_end === '1'): ?>
              <span class="filter-badge">
                Low-End PC
                <a href="<?= e(buildFilterUrl($filters, ['low_end' => '', 'page' => ''])) ?>"
                   class="filter-badge-remove" aria-label="Remove low-end filter">✕</a>
              </span>
            <?php endif; ?>
          </div>
          <?php endif; ?>

        </div><!-- /.results-toolbar -->


        <!-- ── Games Grid ── -->
        <?php if (!empty($games)): ?>

          <div class="games-grid">
            <?php foreach ($games as $game): ?>
              <?php
              $gSlug   = e($game['slug'] ?? '');
              $gTitle  = e($game['title'] ?? 'Untitled Game');
              $gDesc   = e(truncateText($game['short_description'] ?? '', 85));
              $gPlat   = e($game['platform'] ?? 'Windows PC');
              $gSize   = !empty($game['game_size']) ? e($game['game_size']) : 'TBA';
              $gLic    = $game['license_type'] ?? 'freeware';
              $gBadge  = getLicenseBadgeClass($gLic);
              $gLabel  = getLicenseLabel($gLic);
              $gCover  = !empty($game['cover_image'])
                           ? e(siteUrl($game['cover_image']))
                           : getPlaceholderImage('cover');
              $gHref   = $gSlug ? e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug']))) : '#';
              $gViews  = (int)($game['views_count'] ?? 0);
              $gDls    = (int)($game['downloads_count'] ?? 0);
              $isFeat  = !empty($game['is_featured']);
              $isTrend = !empty($game['is_trending']);
              $isLow   = !empty($game['is_low_end_pc']);
              ?>
              <a href="<?= $gHref ?>"
                 class="game-card card"
                 aria-label="View details for <?= $gTitle ?>">

                <!-- Cover -->
                <div class="game-card-cover">
                  <img src="<?= $gCover ?>"
                       alt="<?= $gTitle ?> cover"
                       loading="lazy">
                  <!-- Overlay badges -->
                  <div class="game-card-overlay-badges">
                    <?php if ($isFeat): ?>
                      <span class="card-overlay-badge card-overlay-featured">⭐ Featured</span>
                    <?php endif; ?>
                    <?php if ($isTrend): ?>
                      <span class="card-overlay-badge card-overlay-trending">📈 Trending</span>
                    <?php endif; ?>
                    <?php if ($isLow): ?>
                      <span class="card-overlay-badge card-overlay-lowend">🖥️ Low-End</span>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Body -->
                <div class="game-card-body">
                  <p class="game-card-meta"><?= $gPlat ?> · <?= $gSize ?></p>
                  <h3 class="game-card-title"><?= $gTitle ?></h3>
                  <p class="game-card-description"><?= $gDesc ?></p>

                  <!-- Stats row -->
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

                  <!-- Actions -->
                  <div class="game-card-actions-row">
                    <span class="badge <?= $gBadge ?>"><?= $gLabel ?></span>
                    <span class="btn btn-sm btn-outline">View Details →</span>
                  </div>
                </div>

              </a><!-- /.game-card -->
            <?php endforeach; ?>
          </div><!-- /.games-grid -->

        <?php else: ?>

          <!-- ── Empty State ── -->
          <div class="card games-empty-state">
            <div class="empty-state">
              <span class="empty-state-icon">🎮</span>
              <h3>No Games Found</h3>
              <p>
                No active games match your current search or filters.<br>
                Try changing your filters or browse all available games.
              </p>
              <div class="empty-state-actions">
                <a href="<?= e(siteUrl('games.php')) ?>"
                   class="btn btn-primary">Reset Filters</a>
                <a href="<?= e(siteUrl('index.php')) ?>"
                   class="btn btn-secondary">Back to Home</a>
              </div>
            </div>
          </div>

        <?php endif; ?>


        <!-- ── Pagination ── -->
        <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Page navigation">

          <?php
          /* Build base params without 'page' for pagination links */
          $pageFilters = $filters;

          /* Previous */
          $prevDisabled = $currentPage <= 1;
          if ($prevDisabled): ?>
            <span class="pagination-link pagination-prev disabled" aria-disabled="true">
              ← Prev
            </span>
          <?php else: ?>
            <a href="<?= e(buildFilterUrl($pageFilters, ['page' => (string)($currentPage - 1)])) ?>"
               class="pagination-link pagination-prev" aria-label="Previous page">
              ← Prev
            </a>
          <?php endif; ?>

          <?php
          /* Page numbers — show at most 7, with ellipsis */
          $range   = 2;
          $start   = max(1, $currentPage - $range);
          $end     = min($totalPages, $currentPage + $range);

          /* Always show first page */
          if ($start > 1): ?>
            <a href="<?= e(buildFilterUrl($pageFilters, ['page' => '1'])) ?>"
               class="pagination-link">1</a>
            <?php if ($start > 2): ?>
              <span class="pagination-ellipsis">…</span>
            <?php endif; ?>
          <?php endif; ?>

          <?php for ($p = $start; $p <= $end; $p++): ?>
            <?php if ($p === $currentPage): ?>
              <span class="pagination-link active" aria-current="page"><?= $p ?></span>
            <?php else: ?>
              <a href="<?= e(buildFilterUrl($pageFilters, ['page' => (string)$p])) ?>"
                 class="pagination-link" aria-label="Page <?= $p ?>"><?= $p ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php
          /* Always show last page */
          if ($end < $totalPages): ?>
            <?php if ($end < $totalPages - 1): ?>
              <span class="pagination-ellipsis">…</span>
            <?php endif; ?>
            <a href="<?= e(buildFilterUrl($pageFilters, ['page' => (string)$totalPages])) ?>"
               class="pagination-link"><?= $totalPages ?></a>
          <?php endif; ?>

          <?php /* Next */
          $nextDisabled = $currentPage >= $totalPages;
          if ($nextDisabled): ?>
            <span class="pagination-link pagination-next disabled" aria-disabled="true">
              Next →
            </span>
          <?php else: ?>
            <a href="<?= e(buildFilterUrl($pageFilters, ['page' => (string)($currentPage + 1)])) ?>"
               class="pagination-link pagination-next" aria-label="Next page">
              Next →
            </a>
          <?php endif; ?>

        </nav><!-- /.pagination -->
        <?php endif; ?>

      </div><!-- /.games-results -->

    </div><!-- /.games-layout -->

  </div><!-- /.container -->
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
