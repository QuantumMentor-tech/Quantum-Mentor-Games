<?php
/**
 * QMGames Store — Game Detail Page
 * Step: 9 — Professional Game Detail Page
 *
 * Loads one active game by slug. Shows hero, info panel,
 * description, screenshots, trailer, requirements, categories,
 * tags, download link previews, related games, and report link.
 * Never exposes draft/inactive/archived games publicly.
 */

require_once __DIR__ . '/includes/init.php';

/* ================================================================
   1. READ & VALIDATE SLUG
   ================================================================ */
$rawSlug   = trim((string)($_GET['slug'] ?? ''));
/* Allow letters, digits, hyphens, underscores — nothing else */
$safeSlug  = preg_match('/^[a-zA-Z0-9_\-]+$/', $rawSlug) ? $rawSlug : '';
$game      = ($safeSlug !== '') ? getGameDetailsBySlug($safeSlug) : null;
$notFound  = ($game === null);

/* ================================================================
   2. LOAD GAME DATA (only if game found)
   ================================================================ */
if (!$notFound) {
    $gameId       = (int)$game['id'];
    $categories   = getGameCategories($gameId);
    $tags         = getGameTags($gameId);
    $requirements = getGameRequirements($gameId);
    $screenshots  = getGameScreenshots($gameId);
    $dlLinks      = getGameDownloadLinksPreview($gameId);
    $catIds       = array_column($categories, 'id');
    $relatedGames = getRelatedGames($gameId, $catIds, 4);

    /* Increment view counter with 30-min session cooldown */
    incrementGameViewsWithCooldown($gameId);

    /* ── Convenience variables ── */
    $gameTitle    = $game['title'] ?? '';
    $gameCover    = !empty($game['cover_image'])
                        ? siteUrl($game['cover_image'])
                        : getPlaceholderImage('cover');
    $gameBanner   = !empty($game['banner_image'])
                        ? siteUrl($game['banner_image'])
                        : getPlaceholderImage('banner');
    $gameLicense  = $game['license_type'] ?? 'freeware';
    $gameLicLabel = getLicenseLabel($gameLicense);
    $gameLicBadge = getLicenseBadgeClass($gameLicense);

    /* Trailer embed (only safe YouTube) */
    $embedUrl = '';
    if (!empty($game['trailer_url']) && isValidUrl($game['trailer_url'])) {
        $embedUrl = getYouTubeEmbedUrl($game['trailer_url']);
    }

    /* ── SEO ── */
    $pageTitle = $gameTitle;
    if (!empty($game['meta_description'])) {
        $pageDescription = $game['meta_description'];
    } elseif (!empty($game['short_description'])) {
        $pageDescription = $game['short_description'];
    } else {
        $pageDescription = 'View legal game details, system requirements, screenshots, '
                         . 'and authorized download information on QMGames Store.';
    }
    $ogImage = $gameCover;

} else {
    $pageTitle       = 'Game Not Found';
    $pageDescription = 'The game you are looking for is not available on QMGames Store.';
}

$activePage = 'games';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="game-detail-page">

<?php if ($notFound): ?>
<!-- ============================================================
     GAME NOT FOUND
     ============================================================ -->
<section class="section" aria-label="Game not found">
  <div class="container-sm">
    <div class="card">
      <div class="empty-state">
        <span class="empty-state-icon">🎮</span>
        <h1 style="font-size:1.5rem;margin-bottom:.6rem;">Game Not Found</h1>
        <p>
          The game you are looking for does not exist, is not active,
          or has been removed from public view.
        </p>
        <div class="empty-state-actions">
          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-primary">
            🎮 Browse Games
          </a>
          <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-secondary">
            🏠 Back to Home
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php else: ?>
<!-- ============================================================
     FULL GAME DETAIL
     ============================================================ -->

<!-- ── Hero ── -->
<section class="game-detail-hero"
         aria-labelledby="game-hero-title"
         style="--banner-url: url('<?= e($gameBanner) ?>');">
  <div class="game-detail-hero-overlay" aria-hidden="true"></div>
  <div class="container">
    <div class="game-detail-hero-grid">

      <!-- Cover image card -->
      <div class="game-cover-card">
        <img src="<?= e($gameCover) ?>"
             alt="<?= e($gameTitle) ?> cover art"
             class="game-cover-image">
        <!-- Overlay badges on cover -->
        <div class="game-cover-badges">
          <?php if (!empty($game['is_featured'])): ?>
            <span class="badge badge-warning">⭐ Featured</span>
          <?php endif; ?>
          <?php if (!empty($game['is_trending'])): ?>
            <span class="badge badge-primary">📈 Trending</span>
          <?php endif; ?>
          <?php if (!empty($game['is_low_end_pc'])): ?>
            <span class="badge badge-success">🖥️ Low-End</span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Hero content -->
      <div class="game-hero-content">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <a href="<?= e(siteUrl('index.php')) ?>">Home</a>
          <span class="breadcrumb-sep">›</span>
          <a href="<?= e(siteUrl('games.php')) ?>">Games</a>
          <span class="breadcrumb-sep">›</span>
          <span><?= e($gameTitle) ?></span>
        </nav>

        <h1 class="game-title" id="game-hero-title"><?= e($gameTitle) ?></h1>

        <?php if (!empty($game['short_description'])): ?>
          <p class="game-short-description">
            <?= e($game['short_description']) ?>
          </p>
        <?php endif; ?>

        <!-- Quick meta row -->
        <div class="game-meta-row">
          <span class="badge <?= $gameLicBadge ?>"><?= e($gameLicLabel) ?></span>
          <?php if (!empty($game['platform'])): ?>
            <span class="game-meta-chip">💻 <?= e($game['platform']) ?></span>
          <?php endif; ?>
          <?php if (!empty($game['game_size'])): ?>
            <span class="game-meta-chip">📦 <?= e($game['game_size']) ?></span>
          <?php endif; ?>
          <?php if (!empty($game['version'])): ?>
            <span class="game-meta-chip">v<?= e($game['version']) ?></span>
          <?php endif; ?>
        </div>

        <!-- Stat chips -->
        <div class="game-meta-row" style="margin-top:.5rem;">
          <?php if ((int)($game['views_count'] ?? 0) > 0): ?>
            <span class="game-meta-chip stat-pill">
              👁 <?= e(formatNumberShort((int)$game['views_count'])) ?> views
            </span>
          <?php endif; ?>
          <?php if ((int)($game['downloads_count'] ?? 0) > 0): ?>
            <span class="game-meta-chip stat-pill">
              ⬇ <?= e(formatNumberShort((int)$game['downloads_count'])) ?> downloads
            </span>
          <?php endif; ?>
        </div>

        <!-- CTA buttons -->
        <div class="game-hero-actions">
          <a href="#download-options" class="btn btn-primary btn-lg">
            ⬇ View Download Options
          </a>
          <a href="#related-games" class="btn btn-outline btn-lg">
            🎮 Browse Similar Games
          </a>
        </div>

      </div><!-- /.game-hero-content -->

    </div><!-- /.game-detail-hero-grid -->
  </div>
</section>


<!-- ── Main content + info panel ── -->
<div class="container game-detail-body">
  <div class="game-detail-main-grid">

    <!-- ════════ LEFT COLUMN: main sections ════════ -->
    <div class="game-detail-main">

      <!-- ── About This Game ── -->
      <section class="game-detail-section" aria-labelledby="about-heading">
        <h2 class="game-detail-section-title" id="about-heading">
          📖 About This Game
        </h2>
        <div class="game-description">
          <?php
          $desc = trim($game['full_description'] ?? $game['short_description'] ?? '');
          if ($desc !== '') {
              /* strip_tags first, then nl2br for plain text; preserve if HTML */
              $stripped = strip_tags($desc);
              if (strlen($stripped) === strlen($desc)) {
                  /* Plain text stored — safe to nl2br */
                  echo nl2br(e($desc));
              } else {
                  /* Contains HTML — already escaped by admin; output safely */
                  /* Only allow a safe subset to prevent XSS */
                  echo nl2br(e(strip_tags($desc,
                      '<p><br><strong><em><ul><ol><li><h3><h4>')));
              }
          } else {
              echo '<p class="text-muted">Game description will be added soon.</p>';
          }
          ?>
        </div>
      </section>


      <!-- ── Screenshots ── -->
      <?php if (!empty($screenshots)): ?>
      <section class="game-detail-section" aria-labelledby="screenshots-heading">
        <h2 class="game-detail-section-title" id="screenshots-heading">
          📸 Screenshots
        </h2>
        <div class="screenshot-grid" id="screenshotGrid">
          <?php foreach ($screenshots as $idx => $ss):
            $ssPath = !empty($ss['image_path'])
                          ? e(siteUrl($ss['image_path']))
                          : getPlaceholderImage('screenshot');
            $ssAlt  = !empty($ss['alt_text']) ? e($ss['alt_text']) : e($gameTitle) . ' screenshot';
          ?>
          <button class="screenshot-card"
                  type="button"
                  aria-label="View screenshot <?= $idx + 1 ?>"
                  data-src="<?= $ssPath ?>"
                  data-alt="<?= $ssAlt ?>">
            <img src="<?= $ssPath ?>"
                 alt="<?= $ssAlt ?>"
                 class="screenshot-image"
                 loading="lazy">
          </button>
          <?php endforeach; ?>
        </div>
      </section>
      <?php else: ?>
      <section class="game-detail-section" aria-labelledby="screenshots-heading">
        <h2 class="game-detail-section-title" id="screenshots-heading">
          📸 Screenshots
        </h2>
        <div class="info-box text-center" style="padding:2rem;">
          <p class="text-muted">Screenshots will be added soon.</p>
        </div>
      </section>
      <?php endif; ?>


      <!-- ── Trailer ── -->
      <?php if ($embedUrl !== ''): ?>
      <section class="game-detail-section" aria-labelledby="trailer-heading">
        <h2 class="game-detail-section-title" id="trailer-heading">
          🎬 Game Trailer
        </h2>
        <div class="trailer-wrapper">
          <iframe
            src="<?= e($embedUrl) ?>"
            title="<?= e($gameTitle) ?> trailer"
            loading="lazy"
            allowfullscreen
            referrerpolicy="strict-origin-when-cross-origin"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
          </iframe>
        </div>
      </section>
      <?php elseif (!empty($game['trailer_url'])): ?>
      <section class="game-detail-section" aria-labelledby="trailer-heading">
        <h2 class="game-detail-section-title" id="trailer-heading">
          🎬 Game Trailer
        </h2>
        <div class="info-box">
          <p class="text-muted" style="margin:0;">
            A trailer is available.
            <a href="<?= e($game['trailer_url']) ?>"
               target="_blank"
               rel="noopener noreferrer">Watch the trailer here ↗</a>
          </p>
        </div>
      </section>
      <?php endif; ?>


      <!-- ── System Requirements ── -->
      <section class="game-detail-section" aria-labelledby="reqs-heading">
        <h2 class="game-detail-section-title" id="reqs-heading">
          🖥️ System Requirements
        </h2>

        <?php if ($requirements !== null): ?>
          <div class="requirements-grid">

            <!-- Minimum -->
            <div class="requirement-card">
              <h3 class="requirement-card-title">Minimum</h3>
              <ul class="requirement-list">
                <?php
                $minFields = [
                    'OS'        => 'minimum_os',
                    'Processor' => 'minimum_processor',
                    'RAM'       => 'minimum_ram',
                    'GPU'       => 'minimum_gpu',
                    'Storage'   => 'minimum_storage',
                ];
                foreach ($minFields as $label => $col):
                  $val = !empty($requirements[$col]) ? $requirements[$col] : 'Not listed';
                ?>
                <li class="requirement-item">
                  <span class="requirement-label"><?= e($label) ?></span>
                  <span class="requirement-value"><?= e($val) ?></span>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>

            <!-- Recommended -->
            <div class="requirement-card">
              <h3 class="requirement-card-title">Recommended</h3>
              <ul class="requirement-list">
                <?php
                $recFields = [
                    'OS'        => 'recommended_os',
                    'Processor' => 'recommended_processor',
                    'RAM'       => 'recommended_ram',
                    'GPU'       => 'recommended_gpu',
                    'Storage'   => 'recommended_storage',
                ];
                foreach ($recFields as $label => $col):
                  $val = !empty($requirements[$col]) ? $requirements[$col] : 'Not listed';
                ?>
                <li class="requirement-item">
                  <span class="requirement-label"><?= e($label) ?></span>
                  <span class="requirement-value"><?= e($val) ?></span>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>

          </div><!-- /.requirements-grid -->
        <?php else: ?>
          <div class="info-box text-center" style="padding:1.75rem;">
            <p class="text-muted" style="margin:0;">
              System requirements will be added soon.
            </p>
          </div>
        <?php endif; ?>
      </section>


      <!-- ── Categories & Tags ── -->
      <?php if (!empty($categories) || !empty($tags)): ?>
      <section class="game-detail-section" aria-labelledby="meta-heading">
        <h2 class="game-detail-section-title" id="meta-heading">
          🏷️ Categories &amp; Tags
        </h2>
        <?php if (!empty($categories)): ?>
          <div class="metadata-badges" style="margin-bottom:.85rem;">
            <span class="metadata-label">Categories:</span>
            <?php foreach ($categories as $cat): ?>
              <a href="<?= e(getCategoryUrl($cat['slug'])) ?>"
                 class="badge badge-primary metadata-badge-link">
                <?= e($cat['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($tags)): ?>
          <div class="metadata-badges">
            <span class="metadata-label">Tags:</span>
            <?php foreach ($tags as $tag): ?>
              <a href="<?= e(siteUrl('games.php?q=' . rawurlencode($tag['name']))) ?>"
                 class="badge badge-muted metadata-badge-link">
                <?= e($tag['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
      <?php endif; ?>


      <!-- ── Download Options Preview ── -->
      <section class="game-detail-section" id="download-options"
               aria-labelledby="dl-heading">
        <h2 class="game-detail-section-title" id="dl-heading">
          ⬇ Download Options
        </h2>
        <p class="game-detail-section-sub">
          Choose an authorized download source. All links are verified as
          legal, authorized, or permission-based downloads only.
        </p>

        <?php if (!empty($dlLinks)): ?>
          <div class="download-options-grid">
            <?php foreach ($dlLinks as $dl):
              $dlTypeBadge = getLinkTypeBadgeClass($dl['link_type'] ?? '');
              $dlTypeLabel = getReadableLinkType($dl['link_type'] ?? '');
            ?>
            <div class="download-option-card">
              <div class="download-option-header">
                <div>
                  <h3 class="download-option-title">
                    <?= e($dl['link_title'] ?? 'Download') ?>
                  </h3>
                  <p class="download-provider">
                    📡 <?= e($dl['provider_name'] ?? '') ?>
                  </p>
                </div>
                <div class="download-option-badges">
                  <span class="badge <?= $dlTypeBadge ?>">
                    <?= e($dlTypeLabel) ?>
                  </span>
                  <?php if (!empty($dl['file_size'])): ?>
                    <span class="badge badge-muted">
                      📦 <?= e($dl['file_size']) ?>
                    </span>
                  <?php endif; ?>
                </div>
              </div>

              <p class="download-safety-note">
                ✅ Only authorized, official, freeware, open-source, demo, or
                permission-based download links are used on QMGames Store.
              </p>

              <a href="<?= e(siteUrl('download.php?id=' . (int)$dl['id'])) ?>"
                 class="btn btn-primary btn-block">
                ⬇ Continue to Download
              </a>
              <a href="<?= e(siteUrl('report-link.php?game=' . $gameId . '&link=' . (int)$dl['id'])) ?>"
                 class="btn btn-ghost btn-sm" style="margin-top:.5rem;">
                🚩 Report This Link
              </a>
            </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="info-box text-center" style="padding:2rem;">
            <div style="font-size:2.5rem;margin-bottom:.75rem;">🔗</div>
            <p class="text-muted mb-2">
              Download links will be added to this game soon.<br>
              Check back later or
              <a href="<?= e(siteUrl('report-link.php')) ?>">report a missing link</a>.
            </p>
          </div>
        <?php endif; ?>
      </section>


      <!-- ── Report Problem ── -->
      <section class="game-detail-section report-problem-card"
               aria-label="Report a problem">
        <div class="report-problem-inner">
          <div>
            <h3 class="report-problem-title">🚩 Found a Problem?</h3>
            <p class="text-muted text-sm" style="margin:0;">
              If a download link is broken, outdated, or suspicious,
              you can report it to help keep the store safe.
            </p>
          </div>
          <a href="<?= e(siteUrl('report-link.php?game=' . $gameId)) ?>"
             class="btn btn-ghost btn-sm">
            Report a Link
          </a>
        </div>
      </section>

    </div><!-- /.game-detail-main -->


    <!-- ════════ RIGHT COLUMN: info panel ════════ -->
    <aside class="game-info-panel" aria-label="Game information">
      <div class="card">
        <div class="card-header">
          <strong style="font-size:.88rem;">ℹ️ Game Info</strong>
        </div>
        <ul class="game-info-list">
          <?php
          $infoItems = [
              ['label'=>'Platform',   'value'=> $game['platform'] ?? null,    'icon'=>'💻'],
              ['label'=>'Game Size',  'value'=> $game['game_size'] ?? null,   'icon'=>'📦'],
              ['label'=>'License',    'value'=> $gameLicLabel,                'icon'=>'⚖️'],
              ['label'=>'Version',    'value'=> $game['version'] ?? null,     'icon'=>'🔖'],
              ['label'=>'Released',
               'value'=> (!empty($game['release_date'])
                           ? date('d M Y', strtotime($game['release_date'])) : null),
               'icon'=>'📅'],
              ['label'=>'Developer',  'value'=> $game['developer'] ?? null,   'icon'=>'👤'],
              ['label'=>'Publisher',  'value'=> $game['publisher'] ?? null,   'icon'=>'🏢'],
              ['label'=>'Views',
               'value'=> formatNumberShort((int)($game['views_count'] ?? 0)), 'icon'=>'👁'],
              ['label'=>'Downloads',
               'value'=> formatNumberShort((int)($game['downloads_count'] ?? 0)), 'icon'=>'⬇'],
              ['label'=>'Status',     'value'=> 'Available',                  'icon'=>'✅'],
          ];
          foreach ($infoItems as $item):
            $val = !empty($item['value']) ? $item['value'] : 'Not listed';
          ?>
          <li class="game-info-item">
            <span class="game-info-label">
              <span aria-hidden="true"><?= $item['icon'] ?></span>
              <?= e($item['label']) ?>
            </span>
            <span class="game-info-value"><?= e($val) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Quick category links in panel -->
      <?php if (!empty($categories)): ?>
      <div class="card mt-2">
        <div class="card-body" style="padding:1rem;">
          <p class="filter-label" style="margin-bottom:.6rem;">📁 Categories</p>
          <div class="tag-list">
            <?php foreach ($categories as $cat): ?>
              <a href="<?= e(getCategoryUrl($cat['slug'])) ?>"
                 class="badge badge-primary" style="text-decoration:none;">
                <?= e($cat['name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Quick actions -->
      <div class="card mt-2">
        <div class="card-body" style="padding:1rem;display:flex;flex-direction:column;gap:.5rem;">
          <a href="#download-options" class="btn btn-primary btn-sm btn-block">
            ⬇ Download Options
          </a>
          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-ghost btn-sm btn-block">
            ← Back to Games
          </a>
        </div>
      </div>

    </aside><!-- /.game-info-panel -->

  </div><!-- /.game-detail-main-grid -->
</div><!-- /.container.game-detail-body -->


<!-- ── Related Games ── -->
<?php if (!empty($relatedGames)): ?>
<section class="game-detail-section related-games-section section-sm"
         id="related-games"
         aria-labelledby="related-heading">
  <div class="container">
    <div class="section-header" style="margin-bottom:2rem;">
      <span class="section-label">More Like This</span>
      <h2 id="related-heading">Related Games</h2>
      <p>Other legal games you might enjoy.</p>
    </div>
    <div class="game-card-grid">
      <?php foreach ($relatedGames as $rg):
        $rgSlug  = e($rg['slug'] ?? '');
        $rgTitle = e($rg['title'] ?? 'Game');
        $rgDesc  = e(truncateText($rg['short_description'] ?? '', 80));
        $rgLic   = $rg['license_type'] ?? 'freeware';
        $rgBadge = getLicenseBadgeClass($rgLic);
        $rgLabel = getLicenseLabel($rgLic);
        $rgCover = !empty($rg['cover_image'])
                       ? e(siteUrl($rg['cover_image']))
                       : getPlaceholderImage('cover');
        $rgHref  = $rgSlug
                       ? e(siteUrl('game-details.php?slug=' . rawurlencode($rg['slug'])))
                       : '#';
      ?>
      <a href="<?= $rgHref ?>"
         class="game-card card"
         aria-label="View <?= $rgTitle ?>">
        <div class="game-card-cover">
          <img src="<?= $rgCover ?>" alt="<?= $rgTitle ?>" loading="lazy">
        </div>
        <div class="game-card-body">
          <p class="game-card-meta"><?= e($rg['platform'] ?? 'PC') ?></p>
          <h3 class="game-card-title"><?= $rgTitle ?></h3>
          <p class="game-card-description"><?= $rgDesc ?></p>
          <div class="game-card-actions-row">
            <span class="badge <?= $rgBadge ?>"><?= $rgLabel ?></span>
            <span class="btn btn-sm btn-outline">View →</span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Screenshot Lightbox -->
<div class="lightbox" id="screenshotLightbox" role="dialog"
     aria-modal="true" aria-label="Screenshot viewer" aria-hidden="true">
  <div class="lightbox-overlay" id="lightboxOverlay"></div>
  <div class="lightbox-content">
    <button class="lightbox-close" id="lightboxClose"
            type="button" aria-label="Close screenshot viewer">✕</button>
    <img src="" alt="" class="lightbox-image" id="lightboxImg"
         loading="eager">
  </div>
</div>

<?php endif; // end game found ?>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
