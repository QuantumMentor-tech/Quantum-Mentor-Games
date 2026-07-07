<?php
/**
 * QMGames Store - Site Footer
 * Step: 4 — Global Layout Design
 *
 * DEPENDENCY: init.php must be loaded before this file.
 */
?>
<!-- ===== FOOTER ===== -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <div class="footer-grid">

      <!-- ── Brand Column ── -->
      <div class="footer-brand-col">

        <div class="footer-brand-wrap">
          <div class="footer-brand-logo" aria-hidden="true">QM</div>
          <span class="footer-brand-name">
            <span class="qm">QM</span>Games Store
          </span>
        </div>

        <p class="footer-desc">
          A legal game discovery and download platform for safe,
          authorized, and high-quality gaming content. We only link to
          officially authorized, freeware, open-source, demo, or
          permission-based game downloads.
        </p>

        <div class="footer-legal-note">
          &#9888; All downloads are external links. We do not host game
          files directly. Piracy is strictly prohibited.
        </div>

      </div><!-- /.footer-brand-col -->

      <!-- ── Quick Links ── -->
      <div>
        <p class="footer-col-title">Quick Links</p>
        <nav class="footer-nav" aria-label="Quick links">
          <a href="<?= e(siteUrl('index.php')) ?>">
            <span>&#8250;</span> Home
          </a>
          <a href="<?= e(siteUrl('games.php')) ?>">
            <span>&#8250;</span> All Games
          </a>
          <a href="<?= e(siteUrl('category.php')) ?>">
            <span>&#8250;</span> Categories
          </a>
          <a href="<?= e(siteUrl('search.php')) ?>">
            <span>&#8250;</span> Search
          </a>
        </nav>
      </div>

      <!-- ── Legal Links ── -->
      <div>
        <p class="footer-col-title">Info &amp; Legal</p>
        <nav class="footer-nav" aria-label="Legal and info links">
          <a href="<?= e(siteUrl('pages/about.php')) ?>">
            <span>&#8250;</span> About Us
          </a>
          <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>">
            <span>&#8250;</span> Privacy Policy
          </a>
          <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>">
            <span>&#8250;</span> Disclaimer
          </a>
          <a href="<?= e(siteUrl('report-link.php')) ?>">
            <span>&#8250;</span> Report a Link
          </a>
        </nav>
      </div>

      <!-- ── Contact ── -->
      <div>
        <p class="footer-col-title">Contact</p>

        <div class="footer-contact-item">
          <span class="footer-contact-icon">&#128231;</span>
          <span>Coming soon</span>
        </div>
        <div class="footer-contact-item">
          <span class="footer-contact-icon">&#128241;</span>
          <span>Coming soon</span>
        </div>
        <div class="footer-contact-item">
          <span class="footer-contact-icon">&#127909;</span>
          <span>Coming soon</span>
        </div>
        <div class="footer-contact-item">
          <span class="footer-contact-icon">&#127760;</span>
          <span>Coming soon</span>
        </div>

        <p class="text-muted" style="font-size:.78rem;margin-top:1rem;line-height:1.6;">
          Future updates will include user accounts, game libraries,
          and premium purchase options.
        </p>

      </div><!-- /.contact col -->

    </div><!-- /.footer-grid -->
  </div><!-- /.container -->

  <!-- Footer Bottom Bar -->
  <div class="footer-bottom">
    <div class="container">
      <div class="footer-bottom-inner">
        <p class="footer-copy">
          &copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.
        </p>
        <nav class="footer-copy-links" aria-label="Footer legal links">
          <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>">Privacy</a>
          <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>">Disclaimer</a>
          <a href="<?= e(siteUrl('pages/contact.php')) ?>">Contact</a>
        </nav>
      </div>
    </div>
  </div>

</footer>
<!-- ===== / FOOTER ===== -->

<!-- JavaScript -->
<script src="<?= e(assetUrl('js/theme.js')) ?>"></script>
<script src="<?= e(assetUrl('js/main.js')) ?>"></script>

</body>
</html>
