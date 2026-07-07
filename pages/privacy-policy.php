<?php
/**
 * QMGames Store — Privacy Policy Page
 * Step: 17 — Privacy Policy Page
 *
 * Covers: contact forms, download reports, basic analytics,
 * localStorage preferences, external links, security, retention,
 * user choices, children's privacy, future features.
 */

require_once __DIR__ . '/../includes/init.php';

$lastUpdated     = 'July 2026';
$pageTitle       = 'Privacy Policy';
$pageDescription = 'Read the Privacy Policy for ' . SITE_NAME
                 . ' and learn how contact messages, download reports, basic analytics, '
                 . 'and local preferences may be handled.';
$activePage      = 'privacy';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';

/* Table of contents entries — used for both ToC links and section IDs */
$tocItems = [
    ['id' => 'intro',      'label' => 'Introduction'],
    ['id' => 'collect',    'label' => 'Information We May Collect'],
    ['id' => 'use',        'label' => 'How We Use Information'],
    ['id' => 'external',   'label' => 'External Download Links'],
    ['id' => 'cookies',    'label' => 'Cookies and Local Storage'],
    ['id' => 'security',   'label' => 'Data Security'],
    ['id' => 'retention',  'label' => 'Data Retention'],
    ['id' => 'choices',    'label' => 'User Choices'],
    ['id' => 'children',   'label' => "Children's Privacy"],
    ['id' => 'future',     'label' => 'Future Features'],
    ['id' => 'contact',    'label' => 'Contact'],
];
?>

<main class="privacy-page">

<!-- ── Hero ── -->
<section class="privacy-hero page-hero" aria-labelledby="privacy-title">
  <div class="container">
    <span class="section-label">Legal</span>
    <h1 class="page-title" id="privacy-title">Privacy Policy</h1>
    <p class="page-subtitle">
      Learn how <?= e(SITE_SHORT_NAME) ?> handles basic website information,
      contact messages, reports, preferences, and external download links.
    </p>
    <p class="text-muted text-sm" style="margin-top:.35rem;">
      This policy is a starter website privacy document and should be reviewed
      before public launch.
    </p>
    <div class="privacy-hero-actions">
      <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-primary btn-sm">
        💬 Contact Us
      </a>
      <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>" class="btn btn-outline btn-sm">
        📄 Read Disclaimer
      </a>
    </div>
  </div>
</section>


<!-- ── Main Content ── -->
<section class="section-sm">
  <div class="container">
    <div class="policy-layout">

      <!-- ════════ TABLE OF CONTENTS (sidebar) ════════ -->
      <aside class="policy-toc" aria-label="Table of contents">
        <div class="card">
          <div class="card-header">
            <strong>📋 Contents</strong>
          </div>
          <nav>
            <ol class="policy-toc-list">
              <?php foreach ($tocItems as $toc): ?>
                <li>
                  <a href="#<?= e($toc['id']) ?>"
                     class="policy-toc-link">
                    <?= e($toc['label']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ol>
          </nav>
        </div>
      </aside>


      <!-- ════════ POLICY CONTENT ════════ -->
      <div class="policy-content">

        <!-- Last Updated card -->
        <div class="policy-meta-card card">
          <div class="card-body" style="display:flex;align-items:center;
               justify-content:space-between;flex-wrap:wrap;gap:.75rem;padding:1rem 1.25rem;">
            <div>
              <span class="filter-label">Last Updated</span>
              <p style="margin:.1rem 0 0;font-weight:600;color:var(--text-heading);">
                <?= e($lastUpdated) ?>
              </p>
            </div>
            <div class="text-sm text-muted" style="max-width:380px;">
              This Privacy Policy may be updated as <?= e(SITE_SHORT_NAME) ?> adds
              new features such as user accounts, libraries, purchases, or analytics.
            </div>
          </div>
        </div>

        <!-- ─── 1. Introduction ─── -->
        <section class="policy-section" id="intro" aria-labelledby="heading-intro">
          <h2 class="policy-section-title" id="heading-intro">
            1. Introduction
          </h2>
          <div class="policy-section-text">
            <p>
              <?= e(SITE_NAME) ?> is a game discovery and download platform designed
              for legal, authorized, official, freeware, open-source, demo, or
              permission-based game listings. This Privacy Policy explains what
              information may be collected, how it may be used, and what choices
              users may have when using the website.
            </p>
            <p class="mb-0">
              By using this website, users agree to the basic handling of information
              described in this policy.
            </p>
          </div>
        </section>

        <!-- ─── 2. Information We May Collect ─── -->
        <section class="policy-section" id="collect" aria-labelledby="heading-collect">
          <h2 class="policy-section-title" id="heading-collect">
            2. Information We May Collect
          </h2>
          <div class="policy-section-text">

            <h3 class="policy-sub-heading">A. Contact Form Information</h3>
            <p>
              When users submit the Contact page form,
              <?= e(SITE_SHORT_NAME) ?> may collect:
            </p>
            <ul class="policy-list">
              <li>Name</li>
              <li>Email address</li>
              <li>Subject</li>
              <li>Message content</li>
              <li>Submission time</li>
            </ul>

            <h3 class="policy-sub-heading">B. Download Report Information</h3>
            <p>
              When users report a broken or problematic download link,
              <?= e(SITE_SHORT_NAME) ?> may collect:
            </p>
            <ul class="policy-list">
              <li>Report type</li>
              <li>Message description</li>
              <li>Optional email address</li>
              <li>Related game information</li>
              <li>Related download link information</li>
              <li>Submission time</li>
            </ul>

            <h3 class="policy-sub-heading">C. Basic Website Usage Information</h3>
            <p>The website may count the following for quality and improvement purposes:</p>
            <ul class="policy-list">
              <li>Game page views</li>
              <li>Download confirmation actions</li>
              <li>Download link clicks</li>
              <li>Broken link reports</li>
              <li>Popular or trending game activity</li>
            </ul>
            <p class="policy-note">
              When a download redirect occurs, technical identifiers such as IP
              address and browser type may be processed in a privacy-safe way:
              these values are hashed (irreversibly transformed) before any storage.
              Raw identifiers are never stored.
            </p>

            <h3 class="policy-sub-heading">D. Local Browser Preferences</h3>
            <p>
              The website may store simple preferences in the user&rsquo;s browser,
              such as dark/light theme preference. This uses browser
              <code>localStorage</code> with the key
              <code>qmgames_theme</code>. No personal information is stored in
              this preference.
            </p>

          </div>
        </section>

        <!-- ─── 3. How We Use Information ─── -->
        <section class="policy-section" id="use" aria-labelledby="heading-use">
          <h2 class="policy-section-title" id="heading-use">
            3. How We Use Information
          </h2>
          <div class="policy-section-text">
            <p>Information may be used to:</p>
            <ul class="policy-list">
              <li>Respond to contact messages</li>
              <li>Review and address download issue reports</li>
              <li>Improve website content and user experience</li>
              <li>Identify broken or outdated download links</li>
              <li>Improve search, categories, and game discovery</li>
              <li>Maintain basic website security</li>
              <li>Understand which legal game listings are most useful</li>
              <li>Prepare future features carefully and responsibly</li>
            </ul>
            <p class="policy-note mb-0">
              <?= e(SITE_SHORT_NAME) ?> does not sell user contact messages or
              report details to third parties.
            </p>
          </div>
        </section>

        <!-- ─── 4. External Links ─── -->
        <section class="policy-section" id="external" aria-labelledby="heading-external">
          <h2 class="policy-section-title" id="heading-external">
            4. External Download Links and Third-Party Websites
          </h2>
          <div class="policy-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> may provide links to external websites,
              cloud storage pages, official mirrors, developer websites, store pages,
              or other authorized sources. When users leave <?= e(SITE_SHORT_NAME) ?>
              and visit an external website, that website may have its own privacy
              policy, cookies, analytics, security practices, and terms.
            </p>
            <p class="policy-note mb-0">
              Users should review the privacy policy and safety practices of external
              websites before downloading or sharing information. External websites
              and their practices are not controlled by <?= e(SITE_SHORT_NAME) ?>.
            </p>
          </div>
        </section>

        <!-- ─── 5. Cookies and LocalStorage ─── -->
        <section class="policy-section" id="cookies" aria-labelledby="heading-cookies">
          <h2 class="policy-section-title" id="heading-cookies">
            5. Cookies and Local Storage
          </h2>
          <div class="policy-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> may use browser storage such as
              <code>localStorage</code> to remember user preferences, including
              dark/light theme selection. This helps improve the browsing experience
              and does not involve personal data.
            </p>

            <div class="policy-code-block">
              <span class="filter-label" style="display:block;margin-bottom:.3rem;">
                Current Storage Key
              </span>
              <code>localStorage.qmgames_theme</code>
              &nbsp;— Values: <code>"dark"</code> or <code>"light"</code>
            </div>

            <p class="mb-0">
              The current version may not require account cookies, but future
              features such as user login, saved games, or purchases may require
              cookies or session storage. This policy will be updated when those
              features are added.
            </p>
          </div>
        </section>

        <!-- ─── 6. Data Security ─── -->
        <section class="policy-section" id="security" aria-labelledby="heading-security">
          <h2 class="policy-section-title" id="heading-security">
            6. Data Security
          </h2>
          <div class="policy-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> is designed to use safe coding practices
              including:
            </p>
            <ul class="policy-list">
              <li>Server-side input validation on all public forms</li>
              <li>Prepared database statements to prevent SQL injection</li>
              <li>CSRF token protection on forms</li>
              <li>Honeypot spam protection on public forms</li>
              <li>Hashed identifiers instead of raw IP storage</li>
              <li>Controlled admin access in future dashboard steps</li>
            </ul>
            <p class="policy-note mb-0">
              No website can guarantee complete security. Users should avoid
              submitting sensitive personal information through public forms.
            </p>
          </div>
        </section>

        <!-- ─── 7. Data Retention ─── -->
        <section class="policy-section" id="retention" aria-labelledby="heading-retention">
          <h2 class="policy-section-title" id="heading-retention">
            7. Data Retention
          </h2>
          <div class="policy-section-text">
            <p>
              Contact messages and download reports may be stored as long as
              needed to review, respond, improve the website, maintain records,
              or handle support and legal concerns. Old messages or reports may
              be deleted during maintenance.
            </p>
            <p class="mb-0 text-muted">
              If user accounts or purchases are added later, data retention rules
              will be updated in a revised version of this policy.
            </p>
          </div>
        </section>

        <!-- ─── 8. User Choices ─── -->
        <section class="policy-section" id="choices" aria-labelledby="heading-choices">
          <h2 class="policy-section-title" id="heading-choices">
            8. User Choices
          </h2>
          <div class="policy-section-text">
            <ul class="policy-list">
              <li>
                Users may choose not to submit contact forms or download
                report forms.
              </li>
              <li>
                Users may clear <code>localStorage</code> in their browser to
                reset the theme preference or other stored values.
              </li>
              <li>
                Users may contact <?= e(SITE_SHORT_NAME) ?> to request review
                or deletion of messages they submitted, where technically
                possible.
              </li>
            </ul>
            <p class="policy-note mb-0">
              Final contact details will be added before public launch.
              Current placeholders are listed on the
              <a href="<?= e(siteUrl('pages/contact.php')) ?>">Contact page</a>.
            </p>
          </div>
        </section>

        <!-- ─── 9. Children's Privacy ─── -->
        <section class="policy-section" id="children" aria-labelledby="heading-children">
          <h2 class="policy-section-title" id="heading-children">
            9. Children&rsquo;s Privacy
          </h2>
          <div class="policy-section-text">
            <p>
              <?= e(SITE_SHORT_NAME) ?> is intended for general gaming audiences.
              Users should follow local age rules and parental guidance where
              required. The website should not knowingly request sensitive
              information from children.
            </p>
            <p class="mb-0 text-muted">
              If a parent or guardian believes a child submitted personal
              information through a form, they may contact the site owner
              after contact details are finalized.
            </p>
          </div>
        </section>

        <!-- ─── 10. Future Features ─── -->
        <section class="policy-section" id="future" aria-labelledby="heading-future">
          <h2 class="policy-section-title" id="heading-future">
            10. Future Features
          </h2>
          <div class="policy-section-text">
            <p>
              Future versions of <?= e(SITE_SHORT_NAME) ?> may include the
              following features, which are <strong>not active right now</strong>:
            </p>
            <ul class="policy-list">
              <li>User account registration and login</li>
              <li>Personal game libraries and favourites</li>
              <li>Purchase history and paid game access</li>
              <li>Support tickets and help system</li>
              <li>Email notifications</li>
            </ul>
            <p class="mb-0 text-muted">
              When these features are added, this Privacy Policy will be updated
              to explain what additional information is collected and how it is
              used.
            </p>
          </div>
        </section>

        <!-- ─── 11. Contact ─── -->
        <section class="policy-section" id="contact" aria-labelledby="heading-contact">
          <h2 class="policy-section-title" id="heading-contact">
            11. Contact
          </h2>
          <div class="policy-section-text">
            <p>
              For privacy questions or requests related to your submitted data,
              you can contact <?= e(SITE_SHORT_NAME) ?> through the Contact page.
              Current contact placeholders are listed below — final details will
              be added before public launch.
            </p>

            <div class="policy-contact-card">
              <?php
              $contactItems = [
                ['📧', 'Email',    'Coming soon'],
                ['💬', 'WhatsApp', 'Coming soon'],
                ['▶️', 'YouTube',  'Coming soon'],
                ['🌐', 'Website',  'Coming soon'],
              ];
              foreach ($contactItems as [$icon, $label, $val]):
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
        <div class="alert alert-info policy-alert">
          <span class="alert-icon">⚖️</span>
          <span>
            <strong>Legal Review Note:</strong>
            This Privacy Policy is a project starter document and should be
            reviewed by a qualified professional before the website is published
            publicly, especially if user accounts, purchases, advertising,
            analytics, or email notifications are added.
          </span>
        </div>

        <!-- Back to top / actions -->
        <div class="policy-actions">
          <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-secondary">
            💬 Contact Us
          </a>
          <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>" class="btn btn-ghost">
            📄 Read Disclaimer
          </a>
          <a href="#privacy-title" class="btn btn-ghost">
            ↑ Back to Top
          </a>
        </div>

      </div><!-- /.policy-content -->

    </div><!-- /.policy-layout -->
  </div><!-- /.container -->
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
