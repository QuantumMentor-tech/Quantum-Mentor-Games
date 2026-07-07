<?php
/**
 * QMGames Store — Homepage
 * Step: 6 — Professional Homepage Development
 */

require_once __DIR__ . '/includes/init.php';

$pageTitle       = 'Home';
$pageDescription = 'Quantum Mentor Games Store is a legal game discovery and download platform '
                 . 'for authorized, freeware, open-source, demo, and permission-based games.';
$activePage      = 'home';

/* ── Fetch homepage data (safe — returns [] if DB unavailable) ── */
$featuredGames  = getFeaturedGames(4);
$trendingGames  = getTrendingGames(4);
$latestGames    = getLatestGames(4);
$lowEndGames    = getLowEndGames(3);
$homeCategories = getActiveCategories(12);

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';

/* ── Reusable game-card renderer ── */
function renderGameCard(array $game, bool $showNew = false): void {
    $slug    = e($game['slug'] ?? '');
    $title   = e($game['title'] ?? 'Game Coming Soon');
    $desc    = e(truncateText($game['short_description'] ?? 'Legal game listing coming soon.', 80));
    $cover   = !empty($game['cover_image'])
                   ? e(siteUrl($game['cover_image']))
                   : getPlaceholderImage('cover');
    $license = $game['license_type'] ?? 'freeware';
    $badge   = getLicenseBadgeClass($license);
    $label   = getLicenseLabel($license);
    $plat    = e($game['platform'] ?? 'Windows PC');
    $size    = !empty($game['game_size']) ? e($game['game_size']) : 'TBA';
    $href    = $slug ? e(siteUrl('game-details.php?slug=' . $slug)) : '#';
    $views   = (int)($game['views_count'] ?? 0);
    $dls     = (int)($game['downloads_count'] ?? 0);
    ?>
    <a href="<?= $href ?>" class="game-card card" aria-label="View <?= $title ?>">
      <div class="game-card-image">
        <img src="<?= $cover ?>" alt="<?= $title ?> cover" loading="lazy">
        <?php if ($showNew): ?>
          <span class="game-card-new-badge">New</span>
        <?php endif; ?>
        <?php if (!empty($game['is_trending'])): ?>
          <span class="card-overlay-badge card-overlay-trending" style="position:absolute;top:.5rem;right:.5rem;">📈</span>
        <?php endif; ?>
      </div>
      <div class="game-card-content">
        <p class="game-card-meta"><?= $plat ?> &bull; <?= $size ?></p>
        <h3 class="game-card-title"><?= $title ?></h3>
        <p class="game-card-desc"><?= $desc ?></p>
        <?php if ($views > 0 || $dls > 0): ?>
        <div class="game-card-stats">
          <?php if ($views > 0): ?>
            <span class="game-stat">👁 <?= e(formatNumberShort($views)) ?></span>
          <?php endif; ?>
          <?php if ($dls > 0): ?>
            <span class="game-stat">⬇ <?= e(formatNumberShort($dls)) ?></span>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="game-card-actions">
          <span class="badge <?= $badge ?>"><?= $label ?></span>
          <span class="btn btn-sm btn-outline">View Details</span>
        </div>
      </div>
    </a>
    <?php
}
?>

<main>

<!-- ================================================================
     SECTION 1: HERO
     ================================================================ -->
<section class="home-hero" aria-labelledby="hero-title">
  <div class="container">
    <div class="home-hero-grid">

      <!-- Left: Content -->
      <div class="hero-content">

        <div class="hero-badge-row">
          <span class="hero-badge">⚡ Legal &amp; Safe Downloads Only</span>
        </div>

        <h1 class="hero-title" id="hero-title">
          Quantum Mentor<br><span class="hero-title-accent">Games Store</span>
        </h1>

        <p class="hero-subtitle">
          Discover legal, safe, and high-quality game downloads
          for every kind of gamer.
        </p>

        <p class="hero-supporting">
          Browse authorized freeware, open-source, demo, official, and
          permission-based games from one clean gaming hub.
        </p>

        <div class="hero-actions">
          <a href="<?= e(siteUrl('games.php')) ?>"
             class="btn btn-primary btn-lg">
            🎮&nbsp; Browse Games
          </a>
          <a href="<?= e(siteUrl('category.php')) ?>"
             class="btn btn-outline btn-lg">
            📁&nbsp; Explore Categories
          </a>
        </div>

        <!-- Trust badges -->
        <div class="hero-badges">
          <span class="hero-trust-badge">✅ Legal Downloads</span>
          <span class="hero-trust-badge">🔍 Fast Discovery</span>
          <span class="hero-trust-badge">💻 PC Games</span>
          <span class="hero-trust-badge">📚 Future Library</span>
        </div>

      </div><!-- /.hero-content -->

      <!-- Right: Visual Panel -->
      <div class="hero-visual" aria-hidden="true">
        <div class="hero-panel">

          <div class="hero-panel-header">
            <div class="hero-panel-dots">
              <span></span><span></span><span></span>
            </div>
            <span class="hero-panel-title">QMGames Store</span>
          </div>

          <div class="hero-panel-preview">
            <div class="hero-panel-cover">
              <span class="hero-panel-icon">🎮</span>
              <span class="hero-panel-cover-label">Featured Game Preview</span>
            </div>
            <div class="hero-panel-info">
              <div class="hero-panel-info-row">
                <span class="hero-panel-tag">✔ Safe Links</span>
                <span class="hero-panel-tag">✔ Legal Source</span>
              </div>
              <div class="hero-panel-info-row">
                <span class="hero-panel-tag hero-panel-tag-primary">
                  ⬇ Download Ready
                </span>
              </div>
            </div>
          </div>

          <!-- Mini stats -->
          <div class="hero-stat-grid">
            <div class="hero-stat-card">
              <span class="hero-stat-value">—</span>
              <span class="hero-stat-label">Total Games</span>
            </div>
            <div class="hero-stat-card">
              <span class="hero-stat-value">14</span>
              <span class="hero-stat-label">Categories</span>
            </div>
            <div class="hero-stat-card">
              <span class="hero-stat-value">—</span>
              <span class="hero-stat-label">Downloads</span>
            </div>
          </div>

        </div><!-- /.hero-panel -->
      </div><!-- /.hero-visual -->

    </div><!-- /.home-hero-grid -->
  </div><!-- /.container -->
</section>


<!-- ================================================================
     SECTION 2: HOMEPAGE SEARCH
     ================================================================ -->
<section class="home-search-section" aria-label="Search games">
  <div class="container">
    <div class="home-search-inner">
      <p class="home-search-label">🔍 What are you looking for?</p>
      <form class="home-search-form"
            action="<?= e(siteUrl('search.php')) ?>"
            method="GET"
            role="search"
            id="homeSearchForm">
        <input
          type="search"
          name="q"
          class="form-control home-search-input"
          placeholder="Search legal games, demos, indie titles..."
          aria-label="Search games"
          autocomplete="off"
          maxlength="100"
          id="homeSearchInput"
        >
        <button type="submit" class="btn btn-primary home-search-btn">
          Search
        </button>
      </form>
      <p class="home-search-hint" id="homeSearchHint" style="display:none;"></p>
    </div>
  </div>
</section>


<!-- ================================================================
     SECTION 3: FEATURE CARDS
     ================================================================ -->
<section class="section features-section" aria-labelledby="features-heading">
  <div class="container">

    <div class="section-header">
      <span class="section-label">Why QMGames Store</span>
      <h2 id="features-heading">Built for Legal Gaming</h2>
      <p>Everything you need for safe, legal game discovery — all in one place.</p>
    </div>

    <div class="features-grid">

      <div class="feature-card card">
        <div class="card-body">
          <div class="feature-icon-wrap">🔓</div>
          <h3 class="feature-card-title">Legal Game Downloads</h3>
          <p class="text-muted text-sm">
            Browse games that are authorized, freeware, open-source, demo,
            official, or permission-based. No unauthorized content. Ever.
          </p>
        </div>
      </div>

      <div class="feature-card card">
        <div class="card-body">
          <div class="feature-icon-wrap">🔍</div>
          <h3 class="feature-card-title">Fast Game Discovery</h3>
          <p class="text-muted text-sm">
            Search, filter, and explore games through a clean gamer-friendly
            interface with category browsing and platform filters.
          </p>
        </div>
      </div>

      <div class="feature-card card">
        <div class="card-body">
          <div class="feature-icon-wrap">💻</div>
          <h3 class="feature-card-title">PC-Focused Library</h3>
          <p class="text-muted text-sm">
            Organized Windows PC games with categories, system requirements,
            screenshots, and multiple verified download mirrors.
          </p>
        </div>
      </div>

      <div class="feature-card card">
        <div class="card-body">
          <div class="feature-icon-wrap">📚</div>
          <h3 class="feature-card-title">Future Game Account</h3>
          <p class="text-muted text-sm">
            Future updates may include user accounts, saved game libraries,
            wishlists, and purchased game access.
          </p>
          <span class="badge badge-muted" style="margin-top:.5rem;">Coming Soon</span>
        </div>
      </div>

    </div><!-- /.features-grid -->
  </div>
</section>


<!-- ================================================================
     SECTION 4: FEATURED GAMES
     ================================================================ -->
<section class="game-preview-section section-sm"
         aria-labelledby="featured-heading">
  <div class="container">

    <div class="section-header">
      <span class="section-label">Editor's Pick</span>
      <h2 id="featured-heading">Featured Games</h2>
      <p>Handpicked legal games and demos — verified and ready to explore.</p>
    </div>

    <?php if (!empty($featuredGames)): ?>
      <div class="game-card-grid">
        <?php foreach ($featuredGames as $game): ?>
          <?php renderGameCard($game); ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <!-- Polished empty state with placeholder cards -->
      <div class="game-card-grid">
        <?php for ($i = 0; $i < 4; $i++): ?>
          <div class="game-card card">
            <div class="game-card-image game-card-image-placeholder">🎮</div>
            <div class="game-card-content">
              <p class="game-card-meta">Windows PC &bull; TBA</p>
              <h3 class="game-card-title">Game Coming Soon</h3>
              <p class="game-card-desc">
                Legal game listings will be added from the admin dashboard.
              </p>
              <div class="game-card-actions">
                <span class="badge badge-muted">Coming Soon</span>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>
      <div class="game-preview-notice">
        ⚙️ Featured games will appear here once added via the admin panel.
      </div>
    <?php endif; ?>

    <div class="section-cta">
      <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
        View All Games →
      </a>
    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 5: TRENDING GAMES
     ================================================================ -->
<section class="game-preview-section section-sm alt-section"
         aria-labelledby="trending-heading">
  <div class="container">

    <div class="section-header">
      <span class="section-label">Popular Now</span>
      <h2 id="trending-heading">Trending Games</h2>
      <p>Popular game downloads and community favorites.</p>
    </div>

    <?php if (!empty($trendingGames)): ?>
      <div class="game-card-grid">
        <?php foreach ($trendingGames as $game): ?>
          <?php renderGameCard($game); ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="game-card-grid">
        <?php for ($i = 0; $i < 4; $i++): ?>
          <div class="game-card card">
            <div class="game-card-image game-card-image-placeholder">🕹️</div>
            <div class="game-card-content">
              <p class="game-card-meta">Windows PC &bull; TBA</p>
              <h3 class="game-card-title">Trending Game Coming Soon</h3>
              <p class="game-card-desc">
                Popular game listings will appear here as the store grows.
              </p>
              <div class="game-card-actions">
                <span class="badge badge-muted">Coming Soon</span>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>
      <div class="game-preview-notice">
        📈 Trending games will appear here based on download activity.
      </div>
    <?php endif; ?>

    <div class="section-cta">
      <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-outline">
        View All Games →
      </a>
    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 6: LATEST ADDITIONS
     ================================================================ -->
<section class="game-preview-section section-sm"
         aria-labelledby="latest-heading">
  <div class="container">

    <div class="section-header">
      <span class="section-label">Fresh Picks</span>
      <h2 id="latest-heading">Latest Additions</h2>
      <p>Newly added legal games and demos — always growing.</p>
    </div>

    <?php if (!empty($latestGames)): ?>
      <div class="game-card-grid">
        <?php foreach ($latestGames as $game): ?>
          <?php renderGameCard($game, true); ?>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="game-card-grid">
        <?php for ($i = 0; $i < 4; $i++): ?>
          <div class="game-card card">
            <div class="game-card-image game-card-image-placeholder">✨</div>
            <div class="game-card-content">
              <p class="game-card-meta">Windows PC &bull; TBA</p>
              <h3 class="game-card-title">New Game Coming Soon</h3>
              <p class="game-card-desc">
                New legal game additions will appear here as they are verified and added.
              </p>
              <div class="game-card-actions">
                <span class="badge badge-muted">Coming Soon</span>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      </div>
    <?php endif; ?>

    <div class="section-cta">
      <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
        Browse All Games →
      </a>
    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 7: CATEGORIES PREVIEW
     ================================================================ -->
<section class="section-sm alt-section" aria-labelledby="cat-heading">
  <div class="container">

    <div class="section-header">
      <span class="section-label">Explore</span>
      <h2 id="cat-heading">Browse by Category</h2>
      <p>Find your next game by genre, style, or system type.</p>
    </div>

    <?php
    /* Use DB categories if available, else use hardcoded placeholders */
    $categoryItems = !empty($homeCategories) ? $homeCategories : [
      ['name'=>'Action',          'slug'=>'action',           'icon'=>'⚔️'],
      ['name'=>'Adventure',       'slug'=>'adventure',        'icon'=>'🗺️'],
      ['name'=>'Racing',          'slug'=>'racing',           'icon'=>'🏎️'],
      ['name'=>'RPG',             'slug'=>'rpg',              'icon'=>'🧙'],
      ['name'=>'Strategy',        'slug'=>'strategy',         'icon'=>'♟️'],
      ['name'=>'Simulation',      'slug'=>'simulation',       'icon'=>'🏗️'],
      ['name'=>'Sports',          'slug'=>'sports',           'icon'=>'⚽'],
      ['name'=>'Horror',          'slug'=>'horror',           'icon'=>'👻'],
      ['name'=>'Low-End PC',      'slug'=>'low-end-pc-games', 'icon'=>'🖥️'],
      ['name'=>'Offline Games',   'slug'=>'offline-games',    'icon'=>'📴'],
      ['name'=>'Indie Games',     'slug'=>'indie-games',      'icon'=>'🎨'],
      ['name'=>'Open Source',     'slug'=>'open-source-games','icon'=>'📖'],
    ];

    $catIcons = ['⚔️','🗺️','🏎️','🧙','♟️','🏗️','⚽','👻','🖥️','📴','🎨','📖','🕹️','🎯'];
    ?>
    <div class="category-grid">
      <?php foreach ($categoryItems as $idx => $cat): ?>
        <?php
        $catSlug = e($cat['slug'] ?? createSlug($cat['name'] ?? ''));
        $catName = e($cat['name'] ?? 'Category');
        $catIcon = $cat['icon'] ?? ($catIcons[$idx % count($catIcons)] ?? '🎮');
        ?>
        <a href="<?= e(getCategoryUrl($cat['slug'] ?? createSlug($cat['name'] ?? ''))) ?>"
           class="category-card card"
           aria-label="Browse <?= $catName ?> games">
          <div class="category-card-inner">
            <span class="category-icon" aria-hidden="true"><?= $catIcon ?></span>
            <span class="category-name"><?= $catName ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="section-cta" style="margin-top:1.75rem;">
      <a href="<?= e(getCategoryDirectoryUrl()) ?>" class="btn btn-secondary">
        📁 View All Categories →
      </a>
    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 8: LOW-END PC FRIENDLY
     ================================================================ -->
<section class="low-end-section section-sm" aria-labelledby="lowend-heading">
  <div class="container">
    <div class="low-end-inner">

      <!-- Left: text -->
      <div class="low-end-content">
        <span class="section-label">Accessible Gaming</span>
        <h2 id="lowend-heading">Low-End PC Friendly</h2>
        <p class="text-muted">
          Not everyone has a high-end gaming rig. This section features games
          that run smoothly on older or budget Windows PCs — verified by
          minimum system requirements.
        </p>
        <ul class="low-end-list">
          <li>✅ Low CPU and RAM requirements</li>
          <li>✅ Integrated graphics compatible</li>
          <li>✅ Small download sizes</li>
          <li>✅ Windows 7+ compatible titles</li>
        </ul>
        <a href="<?= e(siteUrl('games.php?filter=low_end')) ?>"
           class="btn btn-outline mt-3">
          🖥️ Explore Low-End Games
        </a>
      </div>

      <!-- Right: preview cards or empty state -->
      <div class="low-end-grid">
        <?php if (!empty($lowEndGames)): ?>
          <?php foreach ($lowEndGames as $game): ?>
            <a href="<?= e(siteUrl('game-details.php?slug=' . e($game['slug'] ?? ''))) ?>"
               class="low-end-card card">
              <div class="low-end-card-img">
                <img src="<?= !empty($game['cover_image']) ? e(siteUrl($game['cover_image'])) : getPlaceholderImage('cover') ?>"
                     alt="<?= e($game['title'] ?? '') ?>"
                     loading="lazy">
              </div>
              <div class="low-end-card-body">
                <p class="low-end-card-title"><?= e($game['title'] ?? 'Game') ?></p>
                <span class="badge badge-success"><?= getLicenseLabel($game['license_type'] ?? 'freeware') ?></span>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <?php for ($i = 0; $i < 3; $i++): ?>
            <div class="low-end-card card">
              <div class="low-end-card-img low-end-card-placeholder">🖥️</div>
              <div class="low-end-card-body">
                <p class="low-end-card-title">Game Coming Soon</p>
                <span class="badge badge-muted">TBA</span>
              </div>
            </div>
          <?php endfor; ?>
          <p class="low-end-notice">
            Low-end friendly games will appear here once added.
          </p>
        <?php endif; ?>
      </div>

    </div><!-- /.low-end-inner -->
  </div>
</section>


<!-- ================================================================
     SECTION 9: SAFE & AUTHORIZED NOTICE
     ================================================================ -->
<section class="legal-notice-section section-sm" aria-label="Legal notice">
  <div class="container">
    <div class="legal-notice-inner">
      <div class="legal-notice-icon" aria-hidden="true">🛡️</div>
      <div class="legal-notice-content">
        <h2 class="legal-notice-title">Safe and Authorized Downloads</h2>
        <p>
          QMGames Store is designed for legal, authorized, official, freeware,
          open-source, demo, or permission-based game downloads only.
          Always scan downloaded files and verify the source before installing
          software on your device.
        </p>
        <div class="legal-notice-links">
          <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>">Read Disclaimer</a>
          <span aria-hidden="true">·</span>
          <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>">Privacy Policy</a>
          <span aria-hidden="true">·</span>
          <a href="<?= e(siteUrl('report-link.php')) ?>">Report a Link</a>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ================================================================
     SECTION 10: FUTURE UPGRADE
     ================================================================ -->
<section class="future-upgrade-section section alt-section"
         aria-labelledby="future-heading">
  <div class="container">

    <div class="section-header">
      <span class="section-label">Roadmap</span>
      <h2 id="future-heading">Future Premium Store Features</h2>
      <p>
        These features are not active yet. Future versions of QMGames Store may
        include them.
      </p>
    </div>

    <div class="future-feature-grid">

      <div class="future-card card">
        <div class="card-body">
          <span class="future-icon">👤</span>
          <h3 class="future-card-title">User Accounts</h3>
          <p class="text-muted text-sm">Personal profiles, game tracking, and preferences.</p>
          <span class="badge badge-muted">Future</span>
        </div>
      </div>

      <div class="future-card card">
        <div class="card-body">
          <span class="future-icon">📚</span>
          <h3 class="future-card-title">Bought Games Library</h3>
          <p class="text-muted text-sm">Access purchased or saved games in one place.</p>
          <span class="badge badge-muted">Future</span>
        </div>
      </div>

      <div class="future-card card">
        <div class="card-body">
          <span class="future-icon">⭐</span>
          <h3 class="future-card-title">Wishlist</h3>
          <p class="text-muted text-sm">Save games you want to try or revisit later.</p>
          <span class="badge badge-muted">Future</span>
        </div>
      </div>

      <div class="future-card card">
        <div class="card-body">
          <span class="future-icon">💳</span>
          <h3 class="future-card-title">Secure Purchases</h3>
          <p class="text-muted text-sm">Pay for premium games through a safe checkout.</p>
          <span class="badge badge-muted">Future</span>
        </div>
      </div>

      <div class="future-card card">
        <div class="card-body">
          <span class="future-icon">🔄</span>
          <h3 class="future-card-title">Game Updates</h3>
          <p class="text-muted text-sm">Get notified when games you own are updated.</p>
          <span class="badge badge-muted">Future</span>
        </div>
      </div>

      <div class="future-card card">
        <div class="card-body">
          <span class="future-icon">🎧</span>
          <h3 class="future-card-title">Support System</h3>
          <p class="text-muted text-sm">Help center and ticket support for account issues.</p>
          <span class="badge badge-muted">Future</span>
        </div>
      </div>

    </div><!-- /.future-feature-grid -->

  </div>
</section>


<!-- ================================================================
     SECTION 11: FINAL CTA
     ================================================================ -->
<section class="cta-section" aria-labelledby="cta-heading">
  <div class="container">
    <div class="cta-inner">
      <h2 class="cta-title" id="cta-heading">Ready to Explore Games?</h2>
      <p class="cta-subtitle">
        Browse the growing QMGames Store collection and discover legal gaming
        content in one clean place.
      </p>
      <div class="cta-actions">
        <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-primary btn-lg">
          🎮&nbsp; Browse Games
        </a>
        <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-outline btn-lg">
          💬&nbsp; Contact Us
        </a>
      </div>
    </div>
  </div>
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
