<?php
/**
 * QMGames Store — Search System
 * Step: 13 — Search System
 *
 * Global search with keyword, category, license, platform,
 * low-end PC, sort, and pagination filters.
 * Only active games are shown. No direct download buttons.
 */

require_once __DIR__ . '/includes/init.php';

/* ================================================================
   1. COLLECT FILTERS
   ================================================================ */
$filters   = getSearchFiltersFromRequest();
$q         = $filters['q'];
$hasSearch = ($q !== '' || $filters['category'] !== '' || $filters['license'] !== ''
           || $filters['platform'] !== '' || $filters['lowEnd'] === '1');

/* ================================================================
   2. FETCH RESULTS (only when something is being searched)
   ================================================================ */
$perPage = 12;
if ($hasSearch) {
    $result     = searchGames($filters, $filters['page'], $perPage);
    $games      = $result['games'];
    $totalGames = $result['total'];
    $totalPages = $result['total_pages'];
    $curPage    = $result['page'];
} else {
    $games = []; $totalGames = 0; $totalPages = 0; $curPage = 1;
}

/* ================================================================
   3. SIDEBAR DATA
   ================================================================ */
$allCategories = getActiveCategories();
$allPlatforms  = getActivePlatforms();
$staticPlatforms = ['Windows PC', 'Linux', 'Mac', 'Browser', 'Multi-platform'];
if (empty($allPlatforms)) $allPlatforms = $staticPlatforms;

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
    'relevance'       => 'Relevance',
    'latest'          => 'Latest',
    'oldest'          => 'Oldest',
    'most_viewed'     => 'Most Viewed',
    'most_downloaded' => 'Most Downloaded',
    'az'              => 'A to Z',
    'za'              => 'Z to A',
    'featured'        => 'Featured First',
    'trending'        => 'Trending First',
];

$suggestions = getSearchSuggestions();

/* ================================================================
   4. PAGE META
   ================================================================ */
$pageTitle = $q !== '' ? 'Search: ' . $q : 'Search Games';
$pageDescription = 'Search legal, authorized, freeware, open-source, demo, and '
                 . 'permission-based games on ' . SITE_SHORT_NAME . '.';
$activePage = 'search';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="search-page">

<!-- ================================================================
     SECTION 1: SEARCH HERO
     ================================================================ -->
<section class="search-hero" aria-label="Search games">
  <div class="container">
    <span class="section-label">Search</span>
    <h1 class="page-title">Search Games</h1>
    <p class="page-subtitle">
      Find legal, safe, and high-quality game downloads across the
      <?= e(SITE_SHORT_NAME) ?> collection.
    </p>
    <p class="text-muted text-sm" style="margin-top:.35rem;">
      Search by title, genre, platform, developer, tags, or description.
    </p>

    <!-- Main search bar -->
    <form class="search-form-main"
          method="GET"
          action="<?= e(siteUrl('search.php')) ?>"
          role="search"
          id="searchHeroForm">
      <div class="search-input-group">
        <input type="search"
               name="q"
               class="form-control search-input-large"
               placeholder="Search games, demos, indie titles, platforms..."
               value="<?= e($q) ?>"
               aria-label="Search games"
               autocomplete="off"
               maxlength="100"
               id="searchHeroInput">
        <button type="submit" class="btn btn-primary search-submit-btn">
          🔍 Search
        </button>
      </div>
      <div class="search-hero-secondary">
        <a href="<?= e(siteUrl('games.php')) ?>"
           class="btn btn-ghost btn-sm">
          Browse All Games →
        </a>
        <a href="<?= e(siteUrl('category.php')) ?>"
           class="btn btn-ghost btn-sm">
          Browse Categories →
        </a>
      </div>
    </form>

  </div>
</section>


<!-- ================================================================
     SECTION 2: FILTERS + RESULTS
     ================================================================ -->
<section class="section-sm">
  <div class="container">
    <div class="games-layout">

      <!-- ════════ FILTER PANEL ════════ -->
      <aside class="games-filter-panel search-filter-panel"
             aria-label="Search filters">

        <div class="filter-panel-header">
          <h2 class="filter-panel-title">🔧 Filters</h2>
          <?php if ($hasSearch): ?>
            <a href="<?= e(siteUrl('search.php')) ?>"
               class="filter-reset-link" aria-label="Clear all filters">
              Clear ✕
            </a>
          <?php endif; ?>
        </div>

        <form method="GET"
              action="<?= e(siteUrl('search.php')) ?>"
              class="filter-form"
              id="searchFilterForm">

          <!-- Keyword -->
          <div class="filter-group">
            <label class="filter-label" for="sf_q">🔍 Keyword</label>
            <input type="search"
                   id="sf_q" name="q"
                   class="form-control filter-input"
                   placeholder="Game title, tag, genre..."
                   value="<?= e($q) ?>"
                   maxlength="100"
                   autocomplete="off">
          </div>

          <!-- Category -->
          <div class="filter-group">
            <label class="filter-label" for="sf_cat">📁 Category</label>
            <select id="sf_cat" name="category" class="form-control filter-select">
              <option value="">All Categories</option>
              <?php foreach ($allCategories as $cat): ?>
                <option value="<?= e($cat['slug']) ?>"
                  <?= $filters['category'] === $cat['slug'] ? 'selected' : '' ?>>
                  <?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- License -->
          <div class="filter-group">
            <label class="filter-label" for="sf_lic">⚖️ License</label>
            <select id="sf_lic" name="license" class="form-control filter-select">
              <?php foreach ($licenseOptions as $val => $label): ?>
                <option value="<?= e($val) ?>"
                  <?= $filters['license'] === $val ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Platform -->
          <div class="filter-group">
            <label class="filter-label" for="sf_plat">💻 Platform</label>
            <select id="sf_plat" name="platform" class="form-control filter-select">
              <option value="">All Platforms</option>
              <?php foreach ($allPlatforms as $plat): ?>
                <option value="<?= e($plat) ?>"
                  <?= $filters['platform'] === $plat ? 'selected' : '' ?>>
                  <?= e($plat) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Low-end -->
          <div class="filter-group">
            <label class="filter-label" for="sf_low">🖥️ System</label>
            <select id="sf_low" name="low_end" class="form-control filter-select">
              <option value=""  <?= $filters['lowEnd'] !== '1' ? 'selected' : '' ?>>All Games</option>
              <option value="1" <?= $filters['lowEnd'] === '1' ? 'selected' : '' ?>>Low-End PC Friendly</option>
            </select>
          </div>

          <!-- Sort -->
          <div class="filter-group">
            <label class="filter-label" for="sf_sort">↕️ Sort By</label>
            <select id="sf_sort" name="sort" class="form-control filter-select">
              <?php foreach ($sortOptions as $val => $label): ?>
                <option value="<?= e($val) ?>"
                  <?= $filters['sort'] === $val ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="filter-actions">
            <button type="submit" class="btn btn-primary btn-block">
              Search
            </button>
            <a href="<?= e(siteUrl('search.php')) ?>"
               class="btn btn-secondary btn-block">Reset</a>
          </div>

        </form>
      </aside><!-- /.search-filter-panel -->


      <!-- ════════ RESULTS ════════ -->
      <div class="games-results">

        <?php if (!$hasSearch): ?>
          <!-- ── EMPTY STARTER STATE ── -->
          <div class="search-empty-state">
            <div class="empty-state">
              <span class="empty-state-icon">🔍</span>
              <h2 style="font-size:1.3rem;margin-bottom:.65rem;">
                Start Searching
              </h2>
              <p class="text-muted">
                Enter a game title, category, platform, or tag to discover
                legal games in <?= e(SITE_SHORT_NAME) ?>.
              </p>
            </div>

            <!-- Quick suggestion chips -->
            <div class="search-suggestions" aria-label="Search suggestions">
              <p class="filter-label" style="margin-bottom:.65rem;">Quick Searches</p>
              <div class="search-chips">
                <?php foreach ($suggestions as $s): ?>
                  <a href="<?= e($s['url']) ?>"
                     class="search-chip">
                    <?= $s['label'] ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

        <?php elseif ($hasSearch && empty($games)): ?>
          <!-- ── NO RESULTS STATE ── -->
          <div class="card">
            <div class="empty-state">
              <span class="empty-state-icon">😕</span>
              <h2 style="font-size:1.3rem;margin-bottom:.65rem;">
                No Games Found
              </h2>
              <p class="text-muted" style="max-width:440px;margin:0 auto .75rem;">
                No active games matched your search.
                Try a different keyword or reset your filters.
              </p>
              <ul class="search-help-list text-muted text-sm"
                  style="list-style:disc;text-align:left;display:inline-block;margin-bottom:1.5rem;">
                <li>Check your spelling</li>
                <li>Try a broader or shorter keyword</li>
                <li>Remove some filters</li>
                <li>Browse by <a href="<?= e(siteUrl('category.php')) ?>">category</a></li>
              </ul>
              <div class="empty-state-actions">
                <a href="<?= e(siteUrl('search.php')) ?>" class="btn btn-primary">
                  Reset Search
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

        <?php else: ?>
          <!-- ── RESULTS TOOLBAR ── -->
          <div class="results-toolbar">
            <div class="results-summary">
              <?php
              $summaryParts = [];
              if ($totalGames > 0) {
                  $summaryParts[] = 'Showing <strong>'
                      . e((string)count($games))
                      . '</strong> of <strong>'
                      . e((string)$totalGames)
                      . '</strong> game'
                      . ($totalGames !== 1 ? 's' : '');
              }
              if ($q !== '') {
                  $summaryParts[] = 'for <strong>&ldquo;' . e($q) . '&rdquo;</strong>';
              }
              echo implode(' ', $summaryParts);
              ?>
            </div>

            <!-- Active filter badges -->
            <?php
            $activeBadges = [];
            if ($q !== '')                 $activeBadges[] = ['Search',   $q];
            if ($filters['category'] !== '') {
                $catLabel = '';
                foreach ($allCategories as $c) {
                    if ($c['slug'] === $filters['category']) { $catLabel = $c['name']; break; }
                }
                $activeBadges[] = ['Category', $catLabel ?: $filters['category']];
            }
            if ($filters['license'] !== '')  $activeBadges[] = ['License',  $licenseOptions[$filters['license']] ?? $filters['license']];
            if ($filters['platform'] !== '')  $activeBadges[] = ['Platform', $filters['platform']];
            if ($filters['lowEnd'] === '1')   $activeBadges[] = ['System',   'Low-End PC'];
            if ($filters['sort'] !== 'relevance') $activeBadges[] = ['Sort', $sortOptions[$filters['sort']] ?? $filters['sort']];
            ?>
            <?php if (!empty($activeBadges)): ?>
              <div class="active-filters search-active-filters">
                <?php foreach ($activeBadges as [$lbl, $val]): ?>
                  <span class="filter-badge search-filter-badge">
                    <?= e($lbl) ?>: <strong><?= e($val) ?></strong>
                  </span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div><!-- /.results-toolbar -->

          <!-- ── RESULTS GRID ── -->
          <div class="games-grid search-results-grid">
            <?php foreach ($games as $game):
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
               aria-label="View <?= $gTitle ?>">
              <div class="game-card-cover">
                <img src="<?= $gCover ?>"
                     alt="<?= $gTitle ?> cover"
                     loading="lazy">
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
          </div><!-- /.search-results-grid -->

          <!-- ── PAGINATION ── -->
          <?php if ($totalPages > 1): ?>
          <nav class="pagination" aria-label="Search results pagination">

            <?php $prevDis = $curPage <= 1; ?>
            <?php if ($prevDis): ?>
              <span class="pagination-link pagination-prev disabled">← Prev</span>
            <?php else: ?>
              <a href="<?= e(buildSearchUrl($filters, ['page' => (string)($curPage-1)])) ?>"
                 class="pagination-link pagination-prev">← Prev</a>
            <?php endif; ?>

            <?php
            $range  = 2;
            $pStart = max(1, $curPage - $range);
            $pEnd   = min($totalPages, $curPage + $range);
            if ($pStart > 1): ?>
              <a href="<?= e(buildSearchUrl($filters, ['page'=>'1'])) ?>"
                 class="pagination-link">1</a>
              <?php if ($pStart > 2): ?>
                <span class="pagination-ellipsis">…</span>
              <?php endif; ?>
            <?php endif; ?>

            <?php for ($pp = $pStart; $pp <= $pEnd; $pp++): ?>
              <?php if ($pp === $curPage): ?>
                <span class="pagination-link active" aria-current="page"><?= $pp ?></span>
              <?php else: ?>
                <a href="<?= e(buildSearchUrl($filters, ['page'=>(string)$pp])) ?>"
                   class="pagination-link"><?= $pp ?></a>
              <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pEnd < $totalPages): ?>
              <?php if ($pEnd < $totalPages - 1): ?>
                <span class="pagination-ellipsis">…</span>
              <?php endif; ?>
              <a href="<?= e(buildSearchUrl($filters, ['page'=>(string)$totalPages])) ?>"
                 class="pagination-link"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php $nextDis = $curPage >= $totalPages; ?>
            <?php if ($nextDis): ?>
              <span class="pagination-link pagination-next disabled">Next →</span>
            <?php else: ?>
              <a href="<?= e(buildSearchUrl($filters, ['page'=>(string)($curPage+1)])) ?>"
                 class="pagination-link pagination-next">Next →</a>
            <?php endif; ?>

          </nav>
          <?php endif; ?>

        <?php endif; /* results state */ ?>

      </div><!-- /.games-results -->

    </div><!-- /.games-layout -->
  </div><!-- /.container -->
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
