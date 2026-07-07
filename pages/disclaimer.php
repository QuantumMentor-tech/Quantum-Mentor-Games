<?php
/**
 * QMGames Store — Disclaimer Page
 * Step: 18 — Disclaimer Page
 *
 * 12 sections: general disclaimer, legal download policy,
 * external links, copyright, file safety, user responsibility,
 * accuracy, no warranty, limitation of responsibility,
 * reports/removal, future features, contact.
 */

require_once __DIR__ . '/../includes/init.php';

$lastUpdated     = 'July 2026';
$pageTitle       = 'Disclaimer';
$pageDescription = 'Read the ' . SITE_SHORT_NAME
                 . ' disclaimer about legal game listings, external download links, '
                 . 'file safety, copyright, and user responsibility.';
$activePage      = 'disclaimer';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';

$tocItems = [
    ['id' => 'general',     'label' => 'General Disclaimer'],
    ['id' => 'policy',      'label' => 'Legal Download Policy'],
    ['id' => 'external',    'label' => 'External Links'],
    ['id' => 'copyright',   'label' => 'Game Ownership & Copyright'],
    ['id' => 'safety',      'label' => 'File Safety Notice'],
    ['id' => 'user',        'label' => 'User Responsibility'],
    ['id' => 'accuracy',    'label' => 'Accuracy of Information'],
    ['id' => 'warranty',    'label' => 'No Warranty'],
    ['id' => 'liability',   'label' => 'Limitation of Responsibility'],
    ['id' => 'reports',     'label' => 'Reports & Removal Requests'],
    ['id' => 'future',      'label' => 'Future Features'],
    ['id' => 'contact',     'label' => 'Contact'],
];
?>

<main class="disclaimer-page">

<!-- ── Hero ── -->
<section class="disclaimer-hero page-hero" aria-labelledby="disclaimer-title">
  <div class="container">
    <span class="section-label">Legal</span>
    <h1 class="page-title" id="disclaimer-title">Disclaimer</h1>
    <p class="page-subtitle">
      Important information about <?= e(SITE_SHORT_NAME) ?>, legal game listings,
      external download sources, user responsibility, and file safety.
    </p>
    <p class="text-muted text-sm" style="margin-top:.35rem;">
      Please read this page carefully before using any download link or
      external source listed on <?= e(SITE_SHORT_NAME) ?>.
    </p>
    <div class="disclaimer-hero-actions">
      <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-primary btn-sm">
        💬 Contact Us
      </a>
      <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>" class="btn btn-outline btn-sm">
        🔒 Privacy Policy
      </a>
      <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-ghost btn-sm">
        🎮 Browse Games
      </a>
    </div>
  </div>
</section>


<!-- ── Main content ── -->
<section class="section-sm">
  <div class="container">
    <div class="disclaimer-layout">

      <!-- ════════ TABLE OF CONTENTS ════════ -->
      <aside class="disclaimer-toc" aria-label="Table of contents">
        <div class="card">
          <div class="card-header">
            <strong>📋 Contents</strong>
          </div>
          <nav>
            <ol class="disclaimer-toc-list">
              <?php foreach ($tocItems as $toc): ?>
                <li>
                  <a href="#<?= e($toc['id']) ?>"
                     class="disclaimer-toc-link">
                    <?= e($toc['label']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ol>
          </nav>
        </div>
      </aside>


      <!-- ════════ DISCLAIMER CONTENT ════════ -->
      <div class="disclaimer-content">

        <!-- Last Updated card -->
        <div class="disclaimer-meta-card card">
          <div class="card-body"
               style="display:flex;align-items:center;
                      justify-content:space-between;flex-wrap:wrap;
                      gap:.75rem;padding:1rem 1.25rem;">
            <div>
              <span class="filter-label">Last Updated</span>
              <p style="margin:.1rem 0 0;font-weight:600;color:var(--text-heading);">
                <?= e($lastUpdated) ?>
              </p>
            </div>
            <p class="text-sm text-muted" style="max-width:380px;margin:0;">
              This disclaimer may be updated as <?= e(SITE_SHORT_NAME) ?> adds
              new features such as user accounts, game libraries, purchases,
              or support systems.
            </p>
          </div>
        </div>

        <!-- ─── 1. General Disclaimer ─── -->
        <section class="disclaimer-section" id="general"
                 aria-labelledby="heading-general">
          <h2 class="disclaimer-section-title" id="heading-general">
            1. General Disclaimer
          </h2>
          <div class="disclaimer-section-text">
            <p>
              <?= e(SITE_NAME) ?> is a game discovery and download information
              platform designed to help users browse legal, authorized, official,
              freeware, open-source, demo, indie-permission, and permission-based
              game listings. The website is built to organize game information,
              screenshots, system requirements, categories, and authorized download
              source references in a clean webstore-style experience.
            </p>
            <p class="mb-0">
              Information on the website is provided for general informational and
              browsing purposes. <?= e(SITE_SHORT_NAME) ?> does not guarantee that
              every listing, link, description, file size, version, or external
              source will always be accurate, complete, current, or available.
            </p>
          </div>
        </section>

        <!-- ─── 2. Legal Download Policy ─── -->
        <section class="disclaimer-section" id="policy"
                 aria-labelledby="heading-policy">
          <h2 class="disclaimer-section-title" id="heading-policy">
            2. Legal Download Policy
          </h2>
          <div class="disclaimer-section-text">

            <!-- Highlighted policy statement -->
            <div class="legal-highlight-card">
              <span class="legal-highlight-icon" aria-hidden="true">✅</span>
              <p>
                <?= e(SITE_SHORT_NAME) ?> is designed for <strong>legal,
                authorized, official, freeware, open-source, demo,
                indie-permission, official mirror, or permission-based game
                downloads only.</strong>
              </p>
            </div>

            <p>
              The website should not be used to share, promote, request, or
              distribute unauthorized copies of games, license-bypassing files,
              unsafe files, or content that violates the rights of developers,
              publishers, or copyright owners.
            </p>

            <p class="mb-0">
              If a listing or link is found to be inaccurate, outdated,
              unauthorized, unsafe, or problematic, users can report it through
              the <a href="<?= e(siteUrl('report-link.php')) ?>">Report Link</a>
              option or contact the site owner through the
              <a href="<?= e(siteUrl('pages/contact.php')) ?>">Contact page</a>.
            </p>

          </div>
        </section>

        <!-- ─── 3. External Links Disclaimer ─── -->
        <section class="disclaimer-section" id="external"
                 aria-labelledby="heading-external">
          <h2 class="disclaimer-section-title" id="heading-external">
            3. External Links Disclaimer
          </h2>
          <div class="disclaimer-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> may include links to external websites,
              cloud storage pages, developer websites, official mirrors, store
              pages, or other authorized sources. These external websites are not
              fully controlled by <?= e(SITE_SHORT_NAME) ?>.
            </p>
            <p>
              When users click an external link, they leave
              <?= e(SITE_SHORT_NAME) ?> and become subject to the external
              website&rsquo;s own terms, privacy policy, security practices,
              availability, and file handling.
            </p>
            <p class="disclaimer-note mb-0">
              <?= e(SITE_SHORT_NAME) ?> is not responsible for external website
              changes, downtime, removed files, access restrictions, account
              requirements, file scanning results, download speed, or third-party
              policies.
            </p>
          </div>
        </section>

        <!-- ─── 4. Game Ownership and Copyright ─── -->
        <section class="disclaimer-section" id="copyright"
                 aria-labelledby="heading-copyright">
          <h2 class="disclaimer-section-title" id="heading-copyright">
            4. Game Ownership and Copyright
          </h2>
          <div class="disclaimer-section-text">
            <p>
              Game names, logos, screenshots, trailers, artwork, descriptions,
              trademarks, and related materials may belong to their respective
              developers, publishers, studios, or rights holders.
            </p>
            <p>
              <?= e(SITE_SHORT_NAME) ?> does not claim ownership of third-party
              games or third-party intellectual property unless clearly stated.
              Listings should be created only for games that are legally allowed
              to be referenced, shared, mirrored, or downloaded through authorized
              sources.
            </p>
            <p class="disclaimer-note mb-0">
              If you are a developer, publisher, copyright owner, or authorized
              representative and believe a listing should be corrected, updated,
              credited, restricted, or removed, please contact the site owner
              through the
              <a href="<?= e(siteUrl('pages/contact.php')) ?>">Contact page</a>
              with relevant details.
            </p>
          </div>
        </section>

        <!-- ─── 5. File Safety Notice ─── -->
        <section class="disclaimer-section" id="safety"
                 aria-labelledby="heading-safety">
          <h2 class="disclaimer-section-title" id="heading-safety">
            5. File Safety Notice
          </h2>
          <div class="disclaimer-section-text">
            <p>
              Users should always be careful when downloading and installing any
              software, game setup, archive, patch, update, or external file.
            </p>

            <p><strong>Safety recommendations:</strong></p>
            <ul class="safety-checklist">
              <?php
              $safetyTips = [
                'Verify the download source before proceeding.',
                'Scan downloaded files with trusted security software.',
                'Read installation prompts carefully.',
                'Avoid sharing sensitive account information.',
                'Keep your operating system and security tools updated.',
                'Do not install files from sources you do not trust.',
              ];
              foreach ($safetyTips as $tip):
              ?>
              <li class="safety-check-item">
                <span class="check-yes" aria-hidden="true">✓</span>
                <?= e($tip) ?>
              </li>
              <?php endforeach; ?>
            </ul>

            <p class="disclaimer-note mb-0">
              <?= e(SITE_SHORT_NAME) ?> may provide information about download
              sources, but users remain responsible for deciding whether to
              download, install, or run any file.
            </p>
          </div>
        </section>

        <!-- ─── 6. User Responsibility ─── -->
        <section class="disclaimer-section" id="user"
                 aria-labelledby="heading-user">
          <h2 class="disclaimer-section-title" id="heading-user">
            6. User Responsibility
          </h2>
          <div class="disclaimer-section-text">
            <p>
              Users are responsible for how they use <?= e(SITE_SHORT_NAME) ?>,
              external links, downloaded files, and any software installed on
              their devices.
            </p>
            <p>
              Users should comply with applicable laws, game licenses, developer
              terms, publisher terms, platform rules, and local regulations.
            </p>
            <p class="mb-0">
              Users should not use <?= e(SITE_SHORT_NAME) ?> to request, submit,
              share, or promote unauthorized content, unsafe files, or content
              that violates another party&rsquo;s rights.
            </p>
          </div>
        </section>

        <!-- ─── 7. Accuracy of Information ─── -->
        <section class="disclaimer-section" id="accuracy"
                 aria-labelledby="heading-accuracy">
          <h2 class="disclaimer-section-title" id="heading-accuracy">
            7. Accuracy of Information
          </h2>
          <div class="disclaimer-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> aims to provide clear and useful game
              information, including title, description, category, platform, file
              size, version, screenshots, system requirements, license type, and
              download source details.
            </p>
            <p>
              However, information may become outdated, incomplete, incorrect, or
              unavailable. Users should verify important details from official or
              trusted sources before downloading or installing software.
            </p>
            <p class="disclaimer-note mb-0">
              Users can report outdated or incorrect information using the
              <a href="<?= e(siteUrl('report-link.php')) ?>">Report Link</a>
              system or the
              <a href="<?= e(siteUrl('pages/contact.php')) ?>">Contact page</a>.
            </p>
          </div>
        </section>

        <!-- ─── 8. No Warranty ─── -->
        <section class="disclaimer-section" id="warranty"
                 aria-labelledby="heading-warranty">
          <h2 class="disclaimer-section-title" id="heading-warranty">
            8. No Warranty
          </h2>
          <div class="disclaimer-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> is provided on an &ldquo;as available&rdquo;
              and &ldquo;as is&rdquo; basis for browsing and informational purposes.
            </p>
            <p class="mb-0">
              The website does not guarantee uninterrupted access, error-free
              operation, permanent availability of links, perfect accuracy,
              complete compatibility with every device, or guaranteed file safety
              from external sources.
            </p>
          </div>
        </section>

        <!-- ─── 9. Limitation of Responsibility ─── -->
        <section class="disclaimer-section" id="liability"
                 aria-labelledby="heading-liability">
          <h2 class="disclaimer-section-title" id="heading-liability">
            9. Limitation of Responsibility
          </h2>
          <div class="disclaimer-section-text">
            <p>
              To the maximum extent appropriate, <?= e(SITE_SHORT_NAME) ?> and
              its project owner should not be responsible for damage, loss, data
              issues, device problems, account issues, installation problems,
              third-party website behaviour, or other issues caused by external
              downloads, external websites, user decisions, or third-party
              software.
            </p>
            <p class="disclaimer-note mb-0">
              This is a starter disclaimer section and should be reviewed by a
              qualified professional before the website is published publicly.
            </p>
          </div>
        </section>

        <!-- ─── 10. Reports, Corrections, and Removal Requests ─── -->
        <section class="disclaimer-section" id="reports"
                 aria-labelledby="heading-reports">
          <h2 class="disclaimer-section-title" id="heading-reports">
            10. Reports, Corrections, and Removal Requests
          </h2>
          <div class="disclaimer-section-text">
            <p>
              Users can help improve <?= e(SITE_SHORT_NAME) ?> by reporting:
            </p>
            <ul class="disclaimer-list">
              <li>Broken links</li>
              <li>Wrong files or incorrect game information</li>
              <li>Slow or unavailable download sources</li>
              <li>Unsafe file concerns</li>
              <li>Outdated game details, file sizes, or versions</li>
              <li>Incorrect license information</li>
              <li>Ownership or copyright concerns</li>
            </ul>

            <div style="display:flex;gap:.65rem;flex-wrap:wrap;margin-top:1rem;">
              <a href="<?= e(siteUrl('report-link.php')) ?>"
                 class="btn btn-primary btn-sm">
                🚩 Report a Link
              </a>
              <a href="<?= e(siteUrl('pages/contact.php')) ?>"
                 class="btn btn-outline btn-sm">
                💬 Contact Us
              </a>
            </div>

            <p class="disclaimer-note mt-3 mb-0">
              Reports are reviewed when possible. Submitting a report does not
              guarantee immediate removal, correction, or response, but it helps
              the site owner improve the platform.
            </p>
          </div>
        </section>

        <!-- ─── 11. Future Features ─── -->
        <section class="disclaimer-section" id="future"
                 aria-labelledby="heading-future">
          <h2 class="disclaimer-section-title" id="heading-future">
            11. Future Features
          </h2>
          <div class="disclaimer-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> may later include user accounts, saved
              games, personal libraries, purchases, order history, support systems,
              email notifications, and other premium store features.
            </p>
            <p class="mb-0 text-muted">
              Future features may have their own terms, privacy updates, payment
              rules, refund rules, account rules, and additional notices. Until
              those features are officially built, they should be treated as
              planned ideas only.
            </p>
          </div>
        </section>

        <!-- ─── 12. Contact ─── -->
        <section class="disclaimer-section" id="contact"
                 aria-labelledby="heading-contact">
          <h2 class="disclaimer-section-title" id="heading-contact">
            12. Contact
          </h2>
          <div class="disclaimer-section-text">
            <p>
              For questions, corrections, ownership concerns, privacy questions,
              or listing issues, contact <?= e(SITE_SHORT_NAME) ?> through the
              Contact page. Final contact details will be added before public
              launch.
            </p>

            <div class="disclaimer-contact-card">
              <?php
              $contacts = [
                ['📧', 'Email',    'Coming soon'],
                ['💬', 'WhatsApp', 'Coming soon'],
                ['▶️', 'YouTube',  'Coming soon'],
                ['🌐', 'Website',  'Coming soon'],
              ];
              foreach ($contacts as [$icon, $label, $val]):
              ?>
              <div class="policy-contact-item">
                <span class="policy-contact-icon"><?= $icon ?></span>
                <div>
                  <span class="filter-label"><?= e($label) ?></span>
                  <span class="policy-contact-value"><?= e($val) ?></span>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <a href="<?= e(siteUrl('pages/contact.php')) ?>"
               class="btn btn-primary">
              💬 Contact Us
            </a>

          </div>
        </section>

        <!-- ─── Legal Review Note ─── -->
        <div class="alert alert-info disclaimer-alert">
          <span class="alert-icon">⚖️</span>
          <span>
            <strong>Legal Review Note:</strong>
            This Disclaimer is a project starter document and should be reviewed
            by a qualified professional before the website is published publicly,
            especially if user accounts, purchases, advertising, third-party
            analytics, or official game partnerships are added.
          </span>
        </div>

        <!-- Actions -->
        <div class="disclaimer-actions">
          <a href="<?= e(siteUrl('pages/contact.php')) ?>"
             class="btn btn-secondary">💬 Contact Us</a>
          <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>"
             class="btn btn-ghost">🔒 Privacy Policy</a>
          <a href="<?= e(siteUrl('report-link.php')) ?>"
             class="btn btn-ghost">🚩 Report a Link</a>
          <a href="#disclaimer-title"
             class="btn btn-ghost">↑ Back to Top</a>
        </div>

      </div><!-- /.disclaimer-content -->

    </div><!-- /.disclaimer-layout -->
  </div><!-- /.container -->
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
