/**
 * QMGames Store - Theme Controller
 * Step: 5 — Dark/Light Theme Polish
 *
 * Single source of truth for theme management.
 * Method: data-theme attribute on document.documentElement (<html>).
 * No body class mixing. No conflicts with main.js.
 *
 * Storage key : localStorage 'qmgames_theme'
 * Values      : 'dark' | 'light'
 * Default     : 'dark'
 *
 * Toggle selector : [data-theme-toggle]  (supports multiple buttons)
 * Custom event    : 'qmgames:themechange' dispatched on document
 */

'use strict';

(function () {

  /* ── Constants ── */
  var STORAGE_KEY  = 'qmgames_theme';
  var DARK         = 'dark';
  var LIGHT        = 'light';

  /* ── Read saved preference ── */
  function getSaved() {
    try {
      var v = localStorage.getItem(STORAGE_KEY);
      return (v === LIGHT || v === DARK) ? v : DARK;
    } catch (e) {
      return DARK;
    }
  }

  /* ── Persist preference ── */
  function persist(theme) {
    try { localStorage.setItem(STORAGE_KEY, theme); } catch (e) {}
  }

  /* ── Apply theme to <html> ── */
  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);

    /* Update every toggle button on the page */
    var buttons = document.querySelectorAll('[data-theme-toggle]');
    buttons.forEach(function (btn) { updateButton(btn, theme); });

    /* Fire custom event so other scripts can react */
    try {
      document.dispatchEvent(new CustomEvent('qmgames:themechange', {
        detail: { theme: theme },
        bubbles: false
      }));
    } catch (e) {}
  }

  /* ── Update a single toggle button's icon + ARIA ── */
  function updateButton(btn, theme) {
    if (!btn) return;
    if (theme === LIGHT) {
      /* Currently light — clicking will go dark */
      btn.setAttribute('aria-label', 'Switch to Dark Mode');
      btn.setAttribute('aria-pressed', 'false');
      btn.title = 'Switch to Dark Mode';
      /* Show moon to indicate "click for dark" */
      var iconEl = btn.querySelector('[data-theme-icon]');
      if (iconEl) {
        iconEl.textContent = '🌙';
      } else {
        btn.innerHTML = '<span data-theme-icon aria-hidden="true">🌙</span>'
                      + '<span class="theme-btn-label">Light</span>';
      }
    } else {
      /* Currently dark — clicking will go light */
      btn.setAttribute('aria-label', 'Switch to Light Mode');
      btn.setAttribute('aria-pressed', 'true');
      btn.title = 'Switch to Light Mode';
      /* Show sun to indicate "click for light" */
      var iconEl2 = btn.querySelector('[data-theme-icon]');
      if (iconEl2) {
        iconEl2.textContent = '☀️';
      } else {
        btn.innerHTML = '<span data-theme-icon aria-hidden="true">☀️</span>'
                      + '<span class="theme-btn-label">Dark</span>';
      }
    }
  }

  /* ── Toggle between dark ↔ light ── */
  function toggleTheme() {
    var current = getSaved();
    var next    = (current === DARK) ? LIGHT : DARK;
    persist(next);
    applyTheme(next);
  }

  /* ── Wire up toggle buttons on DOM ready ── */
  document.addEventListener('DOMContentLoaded', function () {
    /* Re-apply (theme already set by header inline script,
       but this ensures buttons get correct icons/aria) */
    var theme = getSaved();
    applyTheme(theme);

    /* Bind all [data-theme-toggle] buttons */
    var buttons = document.querySelectorAll('[data-theme-toggle]');
    buttons.forEach(function (btn) {
      btn.addEventListener('click', toggleTheme);
    });
  });

  /* ── Public API ── */
  window.QMTheme = {
    toggle  : toggleTheme,
    apply   : applyTheme,
    current : getSaved
  };

}());
