<?php
/**
 * QMGames Store - Responsive Navbar
 * Step: 24 — Final Polish
 *
 * Theme toggle uses [data-theme-toggle] attribute.
 * Theme icons/labels are updated by theme.js — no PHP logic needed.
 *
 * Active page highlighting: set $activePage before including this file.
 * Valid keys: 'home', 'games', 'categories', 'search', 'about', 'contact'
 *
 * DEPENDENCY: init.php must be loaded before this file.
 */

/* Ensure $activePage is always defined to avoid undefined variable notices */
if (!isset($activePage)) {
    $activePage = '';
}

/* Helper: returns 'active' if the key matches, empty string otherwise */
function navActive(string $key, string $activePage): string {
    return ($activePage === $key) ? 'active' : '';
}
?>
<!-- ===== NAVBAR ===== -->
<nav class="navbar" role="navigation" aria-label="Main Navigation">
  <div class="container">
    <div class="navbar-inner">

      <!-- ── Brand ── -->
      <a href="<?= e(siteUrl('index.php')) ?>"
         class="navbar-brand"
         aria-label="<?= e(SITE_SHORT_NAME) ?> — Go to homepage">

        <div class="brand-logo-wrap" aria-hidden="true">
          <span class="brand-logo-text">QM</span>
        </div>

        <div class="brand-name">
          <span class="brand-name-main">
            <span class="qm">QM</span>Games Store
          </span>
          <span class="brand-name-badge">Legal Game Store</span>
        </div>

      </a>

      <!-- ── Desktop Nav Links ── -->
      <ul class="navbar-nav" role="list" aria-label="Main menu">
        <li>
          <a href="<?= e(siteUrl('index.php')) ?>"
             class="nav-link <?= navActive('home', $activePage) ?>"
             <?= ($activePage === 'home') ? 'aria-current="page"' : '' ?>
             aria-label="Home">Home</a>
        </li>
        <li>
          <a href="<?= e(siteUrl('games.php')) ?>"
             class="nav-link <?= navActive('games', $activePage) ?>"
             <?= ($activePage === 'games') ? 'aria-current="page"' : '' ?>
             aria-label="Browse games">Games</a>
        </li>
        <li>
          <a href="<?= e(siteUrl('category.php')) ?>"
             class="nav-link <?= navActive('categories', $activePage) ?>"
             <?= ($activePage === 'categories') ? 'aria-current="page"' : '' ?>
             aria-label="Browse categories">Categories</a>
        </li>
        <li>
          <a href="<?= e(siteUrl('pages/about.php')) ?>"
             class="nav-link <?= navActive('about', $activePage) ?>"
             <?= ($activePage === 'about') ? 'aria-current="page"' : '' ?>
             aria-label="About us">About</a>
        </li>
        <li>
          <a href="<?= e(siteUrl('pages/contact.php')) ?>"
             class="nav-link <?= navActive('contact', $activePage) ?>"
             <?= ($activePage === 'contact') ? 'aria-current="page"' : '' ?>
             aria-label="Contact us">Contact</a>
        </li>
      </ul>

      <!-- ── Actions ── -->
      <div class="navbar-actions" role="toolbar" aria-label="Site tools">

        <!-- Compact Navbar Search (desktop) -->
        <form class="nav-search"
              method="GET"
              action="<?= e(siteUrl('search.php')) ?>"
              role="search"
              aria-label="Quick search">
          <input type="search"
                 name="q"
                 class="nav-search-input"
                 placeholder="Search games..."
                 aria-label="Search games"
                 autocomplete="off"
                 maxlength="100">
          <button type="submit" class="nav-search-button" aria-label="Submit search">
            🔍
          </button>
        </form>

        <!--
          Theme Toggle Button
          - data-theme-toggle  : picked up by theme.js
          - aria-label + aria-pressed set by theme.js on load
          - Inner span updated by theme.js with icon + label
        -->
        <button class="theme-toggle"
                type="button"
                data-theme-toggle
                aria-label="Switch to Light Mode"
                aria-pressed="true"
                title="Switch to Light Mode">
          <span data-theme-icon aria-hidden="true">☀️</span>
          <span class="theme-btn-label">Dark</span>
        </button>

        <!-- Hamburger (visible on ≤992px) -->
        <button id="hamburger"
                class="hamburger"
                type="button"
                aria-controls="mobileNav"
                aria-expanded="false"
                aria-label="Toggle navigation menu">
          <span></span>
          <span></span>
          <span></span>
        </button>

      </div><!-- /.navbar-actions -->

    </div><!-- /.navbar-inner -->
  </div><!-- /.container -->

  <!-- ── Mobile Nav Drawer ── -->
  <div id="mobileNav"
       class="mobile-nav"
       role="menu"
       aria-label="Mobile navigation"
       aria-hidden="true">

    <!-- Mobile search form -->
    <form class="mobile-search"
          method="GET"
          action="<?= e(siteUrl('search.php')) ?>"
          role="search"
          aria-label="Mobile search">
      <div style="display:flex;gap:0;">
        <input type="search"
               name="q"
               class="form-control"
               style="border-radius:var(--radius-sm) 0 0 var(--radius-sm);font-size:.88rem;height:38px;"
               placeholder="Search games..."
               aria-label="Search games"
               maxlength="100"
               autocomplete="off">
        <button type="submit"
                class="btn btn-primary btn-sm"
                style="border-radius:0 var(--radius-sm) var(--radius-sm) 0;padding:0 .85rem;"
                aria-label="Search">
          🔍
        </button>
      </div>
    </form>

    <div class="mobile-nav-divider" role="separator"></div>

    <a href="<?= e(siteUrl('index.php')) ?>"
       class="nav-link <?= navActive('home', $activePage) ?>"
       <?= ($activePage === 'home') ? 'aria-current="page"' : '' ?>
       role="menuitem">🏠&nbsp; Home</a>
    <a href="<?= e(siteUrl('games.php')) ?>"
       class="nav-link <?= navActive('games', $activePage) ?>"
       <?= ($activePage === 'games') ? 'aria-current="page"' : '' ?>
       role="menuitem">🎮&nbsp; Games</a>
    <a href="<?= e(siteUrl('category.php')) ?>"
       class="nav-link <?= navActive('categories', $activePage) ?>"
       <?= ($activePage === 'categories') ? 'aria-current="page"' : '' ?>
       role="menuitem">📁&nbsp; Categories</a>
    <a href="<?= e(siteUrl('search.php')) ?>"
       class="nav-link <?= navActive('search', $activePage) ?>"
       <?= ($activePage === 'search') ? 'aria-current="page"' : '' ?>
       role="menuitem">🔍&nbsp; Search</a>

    <div class="mobile-nav-divider" role="separator"></div>

    <a href="<?= e(siteUrl('pages/about.php')) ?>"
       class="nav-link <?= navActive('about', $activePage) ?>"
       <?= ($activePage === 'about') ? 'aria-current="page"' : '' ?>
       role="menuitem">ℹ️&nbsp; About</a>
    <a href="<?= e(siteUrl('pages/contact.php')) ?>"
       class="nav-link <?= navActive('contact', $activePage) ?>"
       <?= ($activePage === 'contact') ? 'aria-current="page"' : '' ?>
       role="menuitem">💬&nbsp; Contact</a>
    <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>"
       class="nav-link" role="menuitem">🔒&nbsp; Privacy Policy</a>
    <a href="<?= e(siteUrl('pages/disclaimer.php')) ?>"
       class="nav-link" role="menuitem">✏️&nbsp; Disclaimer</a>

    <div class="mobile-nav-divider" role="separator"></div>

    <!-- Theme toggle also in mobile menu for easy access -->
    <button class="mobile-theme-toggle nav-link"
            type="button"
            data-theme-toggle
            aria-label="Switch to Light Mode"
            aria-pressed="true"
            style="background:none;border:none;cursor:pointer;
                   text-align:left;width:100%;font-size:0.95rem;">
      <span data-theme-icon aria-hidden="true">☀️</span>&nbsp;
      <span class="theme-btn-label">Dark Mode</span>
    </button>

  </div><!-- /#mobileNav -->

</nav>
<!-- ===== / NAVBAR ===== -->
