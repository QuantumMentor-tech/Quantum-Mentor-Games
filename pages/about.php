<?php
/**
 * QMGames Store — About Page
 * Step: 16 — About Page
 *
 * Full brand introduction, mission, legal policy,
 * trust/safety, roadmap, and CTA sections.
 */

require_once __DIR__ . '/../includes/init.php';

$pageTitle       = 'About';
$pageDescription = 'Learn about ' . SITE_NAME
                 . ', a legal game discovery and download platform for authorized, '
                 . 'freeware, open-source, demo, and permission-based games.';
$activePage      = 'about';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="about-page">

<!-- ================================================================
     SECTION 1: HERO
     ================================================================ -->
<section class="about-hero" aria-labelledby="about-hero-title">
  <div class="container">
    <div class="about-hero-grid">

      <!-- Left: Content -->
      <div class="about-hero-content">
        <span class="section-label">About the Platform</span>

        <h1 class="page-title" id="about-hero-title">
          About <?= e(SITE_SHORT_NAME) ?>
        </h1>

        <p class="about-hero-subtitle">
          A clean, modern, and legal game discovery platform built for gamers who
          want safe and organized access to high-quality gaming content.
        </p>

        <p class="text-muted" style="font-size:.92rem;line-height:1.75;margin-bottom:2rem;">
          <?= e(SITE_NAME) ?> helps players discover authorized freeware,
          open-source, demo, official, indie-permission, and other legally approved
          games from one professional gaming hub.
        </p>

        <div class="about-hero-actions">
          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-primary btn-lg">
            🎮 Browse Games
          </a>
          <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-outline btn-lg">
            💬 Contact Us
          </a>
        </div>
      </div>

      <!-- Right: Visual badge panel -->
      <div class="about-hero-visual" aria-hidden="true">
        <div class="about-brand-card">
          <div class="about-brand-badge">
            <span class="about-brand-qm">QM</span>
            <span class="about-brand-name">Games Store</span>
          </div>

          <ul class="about-brand-features">
            <li><span class="check-yes">✅</span> Legal Downloads</li>
            <li><span class="check-yes">✅</span> Gamer-Friendly UI</li>
            <li><span class="check-yes">✅</span> Future Game Library</li>
            <li><span class="check-yes">✅</span> Safe Source Focus</li>
            <li><span class="check-yes">✅</span> Community Reports</li>
          </ul>

          <div class="about-brand-tagline">
            <?= e(SITE_TAGLINE) ?>
          </div>
        </div>
      </div>

    </div><!-- /.about-hero-grid -->
  </div>
</section>


<!-- ================================================================
     SECTION 2: WHAT IS QMGAMES STORE
     ================================================================ -->
<section class="about-section section-sm alt-section"
         aria-labelledby="what-heading">
  <div class="container-sm">

    <div class="about-section-header">
      <span class="section-label">Introduction</span>
      <h2 id="what-heading">What Is <?= e(SITE_SHORT_NAME) ?>?</h2>
    </div>

    <div class="about-card">
      <p>
        <strong><?= e(SITE_NAME) ?></strong> is a custom-built game discovery and
        download website designed to help gamers browse legal, authorized, and safe
        game listings in one clean place.
      </p>
      <p>
        The platform focuses on organized game pages, clear categories, system
        requirements, screenshots, download source information, and a smooth user
        experience.
      </p>
      <p class="text-muted" style="margin:0;">
        <?= e(SITE_SHORT_NAME) ?> does not host game files directly. All download
        links point to their original, authorized external sources. The platform
        does not claim ownership of any listed games.
      </p>
    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 3: MISSION
     ================================================================ -->
<section class="about-section section-sm" aria-labelledby="mission-heading">
  <div class="container">

    <div class="about-section-header">
      <span class="section-label">Purpose</span>
      <h2 id="mission-heading">Our Mission</h2>
      <p class="text-muted" style="max-width:540px;margin:0 auto;">
        The mission of <?= e(SITE_SHORT_NAME) ?> is to create a fast, professional,
        and easy-to-use game platform where users can discover legal gaming content
        without confusion.
      </p>
    </div>

    <div class="mission-grid">

      <div class="mission-card card">
        <div class="card-body">
          <div class="about-card-icon">🔍</div>
          <h3 class="about-card-title">Make Game Discovery Easier</h3>
          <p class="about-card-text text-muted">
            Help users find games by category, search, platform, requirements,
            and popularity — without jumping across multiple websites.
          </p>
        </div>
      </div>

      <div class="mission-card card">
        <div class="card-body">
          <div class="about-card-icon">📋</div>
          <h3 class="about-card-title">Keep Downloads Organized</h3>
          <p class="about-card-text text-muted">
            Present game details, screenshots, requirements, and download options
            in a clean, structured layout that is easy to scan.
          </p>
        </div>
      </div>

      <div class="mission-card card">
        <div class="card-body">
          <div class="about-card-icon">✅</div>
          <h3 class="about-card-title">Support Legal Game Access</h3>
          <p class="about-card-text text-muted">
            Focus exclusively on authorized, official, freeware, open-source,
            demo, and permission-based game sources.
          </p>
        </div>
      </div>

    </div><!-- /.mission-grid -->

  </div>
</section>


<!-- ================================================================
     SECTION 4: WHAT USERS CAN FIND
     ================================================================ -->
<section class="about-section section-sm alt-section" aria-labelledby="find-heading">
  <div class="container">

    <div class="about-section-header">
      <span class="section-label">Content</span>
      <h2 id="find-heading">What Users Can Find</h2>
      <p class="text-muted">Legal game types available on the platform.</p>
    </div>

    <div class="find-grid">

      <?php
      $findItems = [
        ['icon'=>'🆓', 'title'=>'Freeware Games',
         'text'=>'Games that are legally available for free distribution by the developer or publisher.'],
        ['icon'=>'📖', 'title'=>'Open-Source Games',
         'text'=>'Games with publicly available source code or open licenses such as GPL, MIT, or similar.'],
        ['icon'=>'🎯', 'title'=>'Demo Games',
         'text'=>'Trial or preview versions shared officially for users to test before full release or purchase.'],
        ['icon'=>'🎨', 'title'=>'Indie Permission Games',
         'text'=>'Games listed with explicit creator or publisher permission for discovery and download.'],
        ['icon'=>'🌐', 'title'=>'Official Mirrors',
         'text'=>'Links that guide users toward official or approved download sources and verified locations.'],
        ['icon'=>'🖥️', 'title'=>'Low-End PC Friendly',
         'text'=>'Games optimized to run on lightweight or older systems with low hardware requirements.'],
      ];
      foreach ($findItems as $item):
      ?>
      <div class="find-card card">
        <div class="card-body">
          <div class="about-card-icon"><?= $item['icon'] ?></div>
          <h3 class="about-card-title"><?= e($item['title']) ?></h3>
          <p class="about-card-text text-muted"><?= e($item['text']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- /.find-grid -->

  </div>
</section>


<!-- ================================================================
     SECTION 5: WHY THIS PROJECT EXISTS
     ================================================================ -->
<section class="why-section section-sm" aria-labelledby="why-heading">
  <div class="container">

    <div class="why-grid">

      <!-- Left: Text -->
      <div>
        <span class="section-label">Background</span>
        <h2 id="why-heading" style="margin-bottom:.85rem;">
          Why This Project Exists
        </h2>
        <p>
          Many gamers want a simple place to discover games without jumping across
          many different websites. <?= e(SITE_SHORT_NAME) ?> is designed to solve
          that problem by organizing game information, categories, screenshots,
          requirements, and authorized download options inside one clean
          webstore-style experience.
        </p>
        <p class="text-muted" style="margin-bottom:0;">
          The goal is a reliable, professional-quality game discovery platform
          that respects both game creators and players.
        </p>
      </div>

      <!-- Right: Feature checklist -->
      <div class="feature-checklist card">
        <div class="card-body">
          <p class="filter-label" style="margin-bottom:.85rem;">Platform Features</p>
          <ul class="about-check-list">
            <?php
            $checks = [
              'Clean game detail pages',
              'Category browsing',
              'Search and filters',
              'System requirements',
              'Screenshots and trailers',
              'Download source preview',
              'Download issue reporting',
              'Low-End PC tag',
              'Future user accounts',
              'Future game libraries',
            ];
            foreach ($checks as $chk):
            ?>
            <li class="feature-check-item">
              <span class="check-yes" aria-hidden="true">✓</span>
              <?= e($chk) ?>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

    </div><!-- /.why-grid -->

  </div>
</section>


<!-- ================================================================
     SECTION 6: LEGAL DOWNLOAD POLICY
     ================================================================ -->
<section class="legal-policy-section section-sm" aria-labelledby="legal-heading">
  <div class="container">

    <div class="about-section-header">
      <span class="section-label">Policy</span>
      <h2 id="legal-heading">Legal Download Policy</h2>
    </div>

    <div class="legal-policy-card">

      <div class="legal-policy-statement">
        <span class="legal-policy-icon" aria-hidden="true">⚖️</span>
        <p>
          <?= e(SITE_SHORT_NAME) ?> is designed for <strong>legal, authorized,
          official, freeware, open-source, demo, or permission-based game downloads
          only</strong>. The platform should not be used for unauthorized
          redistribution, illegal file sharing, cracked games, DRM bypassing,
          keygens, or unsafe downloads.
        </p>
      </div>

      <hr class="divider" style="margin:1.5rem 0;">

      <p class="text-muted" style="font-size:.9rem;margin-bottom:1.25rem;">
        If a game owner, developer, publisher, or authorized representative has a
        concern about a listing, they can contact the website team through the
        Contact page. Reports about specific download links can also be submitted
        through the Report Link system on each game page.
      </p>

      <div class="legal-policy-actions">
        <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>" class="btn btn-secondary">
          📄 Read Disclaimer
        </a>
        <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-outline">
          💬 Contact Us
        </a>
      </div>

    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 7: TRUST AND SAFETY
     ================================================================ -->
<section class="about-section section-sm alt-section" aria-labelledby="trust-heading">
  <div class="container">

    <div class="about-section-header">
      <span class="section-label">Safety</span>
      <h2 id="trust-heading">Trust and Safety</h2>
      <p class="text-muted">
        <?= e(SITE_SHORT_NAME) ?> is designed with transparency and user safety
        in mind.
      </p>
    </div>

    <div class="trust-grid">

      <?php
      $trustCards = [
        ['icon'=>'🔎', 'title'=>'Source Review',
         'text'=>'Download links should point to authorized, official, or permission-based sources. Links can be reviewed and reported if issues arise.'],
        ['icon'=>'🚩', 'title'=>'Report System',
         'text'=>'Users can report broken, outdated, wrong, slow, or unsafe download links through the Report Link button on each game page.'],
        ['icon'=>'📋', 'title'=>'Clear Information',
         'text'=>'Game pages are designed to show platform, file size, license type, version, and requirements where available.'],
        ['icon'=>'🛡️', 'title'=>'User Awareness',
         'text'=>'Users are encouraged to verify sources and scan downloaded files with reputable antivirus software before installation.'],
      ];
      foreach ($trustCards as $tc):
      ?>
      <div class="trust-card card">
        <div class="card-body">
          <div class="about-card-icon"><?= $tc['icon'] ?></div>
          <h3 class="about-card-title"><?= e($tc['title']) ?></h3>
          <p class="about-card-text text-muted"><?= e($tc['text']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- /.trust-grid -->

  </div>
</section>


<!-- ================================================================
     SECTION 8: FUTURE ROADMAP
     ================================================================ -->
<section class="roadmap-section section-sm" aria-labelledby="roadmap-heading">
  <div class="container">

    <div class="about-section-header">
      <span class="section-label">Roadmap</span>
      <h2 id="roadmap-heading">Future Roadmap</h2>
      <p class="text-muted">
        <?= e(SITE_SHORT_NAME) ?> is planned to grow step by step after the first
        stable version. Versions 2 onward are future ideas, not active features.
      </p>
    </div>

    <div class="roadmap-grid">

      <?php
      $roadmap = [
        [
          'version' => 'Version 1',
          'title'   => 'Game Discovery Platform',
          'badge'   => 'In Progress',
          'badgeCls'=> 'badge-primary',
          'features'=> ['Homepage','Games listing','Game detail pages',
                        'Categories','Search','Download options',
                        'Contact and report systems'],
        ],
        [
          'version' => 'Version 2',
          'title'   => 'User Accounts',
          'badge'   => 'Future',
          'badgeCls'=> 'badge-muted',
          'features'=> ['Register / login','User profile',
                        'Saved games','Download history','Favourites'],
        ],
        [
          'version' => 'Version 3',
          'title'   => 'Game Library',
          'badge'   => 'Future',
          'badgeCls'=> 'badge-muted',
          'features'=> ['Personal game library','Free game collection',
                        'Wishlist','Game update tracking'],
        ],
        [
          'version' => 'Version 4',
          'title'   => 'Premium Store',
          'badge'   => 'Future Idea',
          'badgeCls'=> 'badge-muted',
          'features'=> ['Legal purchases','Order history',
                        'Secure payment integration','Support system'],
        ],
        [
          'version' => 'Version 5',
          'title'   => 'Game Launcher',
          'badge'   => 'Future Idea',
          'badgeCls'=> 'badge-muted',
          'features'=> ['Desktop launcher','Download manager',
                        'Installed games list','Update checker'],
        ],
      ];
      foreach ($roadmap as $rm):
      ?>
      <div class="roadmap-card card">
        <div class="card-body">
          <div style="display:flex;align-items:center;justify-content:space-between;
                      margin-bottom:.75rem;flex-wrap:wrap;gap:.4rem;">
            <span class="roadmap-version"><?= e($rm['version']) ?></span>
            <span class="badge <?= $rm['badgeCls'] ?>"><?= e($rm['badge']) ?></span>
          </div>
          <h3 class="about-card-title" style="margin-bottom:.65rem;">
            <?= e($rm['title']) ?>
          </h3>
          <ul class="roadmap-list">
            <?php foreach ($rm['features'] as $feat): ?>
              <li><?= e($feat) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- /.roadmap-grid -->

  </div>
</section>


<!-- ================================================================
     SECTION 9: BUILT BY QUANTUM MENTOR
     ================================================================ -->
<section class="brand-section section-xs alt-section"
         aria-labelledby="brand-heading">
  <div class="container-sm">

    <div class="about-section-header" style="margin-bottom:1.5rem;">
      <span class="section-label">Brand</span>
      <h2 id="brand-heading" style="font-size:1.4rem;">
        Built by Quantum Mentor
      </h2>
    </div>

    <div class="about-card">
      <p>
        <?= e(SITE_SHORT_NAME) ?> is part of the <strong>Quantum Mentor</strong>
        project family, focused on building clean, useful, and professional digital
        products for users, creators, learners, and gamers.
      </p>

      <ul class="about-brand-points">
        <?php
        $points = [
          '🔨 Custom-built website',
          '🎮 Gamer-focused experience',
          '🏗️ Future-ready structure',
          '🎨 Clean UI/UX direction',
        ];
        foreach ($points as $pt):
        ?>
        <li class="about-brand-point"><?= e($pt) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

  </div>
</section>


<!-- ================================================================
     SECTION 10: FINAL CTA
     ================================================================ -->
<section class="about-cta" aria-labelledby="about-cta-title">
  <div class="container">
    <div class="about-cta-inner">
      <span class="section-label">Get Started</span>
      <h2 class="about-cta-title" id="about-cta-title">
        Explore the <?= e(SITE_SHORT_NAME) ?> Collection
      </h2>
      <p class="about-cta-sub">
        Start browsing legal game listings, categories, and future-ready gaming
        content from one clean webstore experience.
      </p>
      <div class="about-cta-actions">
        <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-primary btn-lg">
          🎮 Browse Games
        </a>
        <a href="<?= e(siteUrl('search.php')) ?>" class="btn btn-outline btn-lg">
          🔍 Search Games
        </a>
        <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-secondary btn-lg">
          💬 Contact Us
        </a>
      </div>
    </div>
  </div>
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
