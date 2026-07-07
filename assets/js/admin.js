/**
 * QMGames Store - Admin Panel JavaScript
 * Step: 19 — Admin Login System
 *
 * Handles: password visibility toggle, login form submit guard.
 * No jQuery. Fails safely if elements are missing.
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  initPasswordToggle();
  initLoginSubmitGuard();
  initAdminTheme();
  initAdminSidebar();
  initAdminAlerts();
  initSlugGenerator();
  initImagePreviews();
  initAdminCharCounters();
  initConfirmActions();
  initFormSubmitGuard();
  initUrlSchemeCheck();
  initIconPreview();
});

/* ============================================================
   SLUG GENERATOR
   Auto-generates slug from title (data-slug-source → data-slug-target)
   Stops auto-generating once user manually edits the slug.
   ============================================================ */
function initSlugGenerator() {
  var titleInput = document.querySelector('[data-slug-source]');
  var slugInput  = document.querySelector('[data-slug-target]');
  if (!titleInput || !slugInput) return;

  var autoSlug = slugInput.value === ''; /* only auto when empty */

  function makeSlug(str) {
    return str.toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/[\s-]+/g, '-')
      .replace(/^-+|-+$/g, '')
      .substring(0, 220);
  }

  titleInput.addEventListener('input', function () {
    if (autoSlug) {
      slugInput.value = makeSlug(titleInput.value);
    }
  });

  /* User manually edited slug → stop auto-generating */
  slugInput.addEventListener('input', function () {
    autoSlug = false;
  });

  slugInput.addEventListener('blur', function () {
    /* Clean up on blur */
    slugInput.value = makeSlug(slugInput.value);
  });
}

/* ============================================================
   IMAGE PREVIEWS
   [data-image-preview="previewElementId"] on file input
   ============================================================ */
function initImagePreviews() {
  document.querySelectorAll('[data-image-preview]').forEach(function (input) {
    var previewId = input.getAttribute('data-image-preview');
    var preview   = previewId ? document.getElementById(previewId) : null;
    if (!preview) return;

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) { preview.style.display = 'none'; return; }

      /* Basic front-end type check */
      if (!['image/jpeg','image/png','image/webp'].includes(file.type)) {
        preview.style.display = 'none';
        return;
      }

      var reader = new FileReader();
      reader.onload = function (e) {
        preview.src           = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });
  });
}

/* ============================================================
   ADMIN CHARACTER COUNTERS
   [data-character-counter] + [data-max-length] on textareas
   ============================================================ */
function initAdminCharCounters() {
  document.querySelectorAll('[data-character-counter]').forEach(function (ta) {
    var maxLen = parseInt(ta.getAttribute('data-max-length') || '350', 10);
    /* Find sibling span with id ending in Count */
    var parentGroup = ta.closest('.admin-form-group');
    var counter     = parentGroup ? parentGroup.querySelector('[id$="Count"]') : null;

    function update() {
      var len = ta.value.length;
      if (counter) counter.textContent = len;
      ta.style.borderColor = (len > maxLen * 0.9) ? 'var(--warning)' : '';
    }

    ta.addEventListener('input', update);
    update();
  });
}

/* ============================================================
   CONFIRM ACTIONS
   [data-confirm-action="message"] on buttons/links
   ============================================================ */
function initConfirmActions() {
  document.querySelectorAll('[data-confirm-action]').forEach(function (el) {
    el.addEventListener('click', function (e) {
      var msg = el.getAttribute('data-confirm-action') || 'Are you sure?';
      if (!window.confirm(msg)) {
        e.preventDefault();
        e.stopPropagation();
      }
    });
  });
}

/* ============================================================
   URL SCHEME CHECKER
   [data-url-scheme-check] on URL inputs.
   Client-side hint only — server validates.
   ============================================================ */
function initUrlSchemeCheck() {
  document.querySelectorAll('[data-url-scheme-check]').forEach(function (inp) {
    var help = inp.parentElement
                  ? inp.parentElement.querySelector('#urlSchemeHelp')
                  : null;

    function check() {
      var val    = inp.value.trim();
      var scheme = val.split(':')[0].toLowerCase();
      var unsafe = ['javascript','data','file','ftp','mailto','vbscript'];
      if (val === '') return;
      if (unsafe.includes(scheme)) {
        inp.style.borderColor = 'var(--danger)';
        if (help) {
          help.textContent = '⛔ Unsafe URL scheme "' + scheme + ':" is not allowed.';
          help.style.color = 'var(--danger)';
        }
      } else if (scheme === 'http' || scheme === 'https' || scheme === 'magnet') {
        inp.style.borderColor = 'var(--success)';
        if (help) {
          help.textContent = '✅ URL scheme looks valid.';
          help.style.color = 'var(--success)';
        }
      } else {
        inp.style.borderColor = '';
        if (help) {
          help.textContent = '⚠️ Unrecognised scheme. Only https:// and http:// are allowed.';
          help.style.color = 'var(--warning)';
        }
      }
    }

    inp.addEventListener('input', check);
    inp.addEventListener('blur',  check);
  });
}

/* ============================================================
   GAME FORM SUBMIT GUARD
   Disables submit button after click to prevent double-submit
   ============================================================ */
function initFormSubmitGuard() {
  var form   = document.getElementById('gameForm');
  var submit = document.getElementById('gameFormSubmit');
  if (!form || !submit) return;

  form.addEventListener('submit', function () {
    submit.disabled    = true;
    submit.textContent = '⏳ Saving...';
    setTimeout(function () {
      if (submit) { submit.disabled = false; submit.textContent = '💾 Save Changes'; }
    }, 8000);
  });
}

/* ============================================================
   ICON PREVIEW
   [data-icon-source] input → [data-icon-preview] span
   ============================================================ */
function initIconPreview() {
  var src     = document.querySelector('[data-icon-source]');
  var preview = document.querySelector('[data-icon-preview]');
  if (!src || !preview) return;

  function update() {
    var val = src.value.trim();
    preview.textContent = val !== '' ? val : '🏷️';
  }

  src.addEventListener('input', update);
  update();
}

/* ============================================================
   ADMIN SIDEBAR TOGGLE (mobile)
   Opens/closes the sidebar drawer on small screens.
   ============================================================ */
function initAdminSidebar() {
  var toggle  = document.querySelector('[data-admin-sidebar-toggle]');
  var sidebar = document.querySelector('[data-admin-sidebar]');
  if (!toggle || !sidebar) return;

  function openSidebar() {
    sidebar.classList.add('open');
    toggle.setAttribute('aria-expanded', 'true');
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
  }

  toggle.addEventListener('click', function (e) {
    e.stopPropagation();
    sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
  });

  /* Close on outside click */
  document.addEventListener('click', function (e) {
    if (sidebar.classList.contains('open') &&
        !sidebar.contains(e.target) &&
        !toggle.contains(e.target)) {
      closeSidebar();
    }
  });

  /* Close on Escape */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
      closeSidebar();
      toggle.focus();
    }
  });
}

/* ============================================================
   DISMISSIBLE ADMIN ALERTS
   ============================================================ */
function initAdminAlerts() {
  document.querySelectorAll('[data-admin-alert-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var alert = btn.closest('.admin-alert');
      if (!alert) return;
      alert.style.transition = 'opacity .3s';
      alert.style.opacity    = '0';
      setTimeout(function () { if (alert.parentNode) alert.remove(); }, 320);
    });
  });
}

/* ============================================================
   PASSWORD VISIBILITY TOGGLE
   ============================================================ */
function initPasswordToggle() {
  var toggle = document.getElementById('pwToggle');
  var input  = document.getElementById('admin_password');
  if (!toggle || !input) return;

  toggle.addEventListener('click', function () {
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    toggle.textContent = isPassword ? '🙈' : '👁';
    toggle.setAttribute('aria-label',
      isPassword ? 'Hide password' : 'Show password');
  });
}

/* ============================================================
   LOGIN SUBMIT GUARD
   Disables button after first valid click to prevent double-submit.
   ============================================================ */
function initLoginSubmitGuard() {
  var form   = document.getElementById('adminLoginForm');
  var submit = document.getElementById('loginSubmitBtn');
  if (!form || !submit) return;

  form.addEventListener('submit', function () {
    submit.disabled    = true;
    submit.textContent = '⏳ Signing in...';
    /* Re-enable after 8s in case server-side redirect doesn't happen */
    setTimeout(function () {
      if (submit) {
        submit.disabled    = false;
        submit.textContent = '🔐 Login to Dashboard';
      }
    }, 8000);
  });
}

/* ============================================================
   ADMIN THEME SYNC
   Ensures admin pages respect the saved theme preference.
   Works in tandem with theme.js on pages that include it.
   ============================================================ */
function initAdminTheme() {
  /* theme.js handles the full toggle; this just ensures initial apply */
  try {
    var saved = localStorage.getItem('qmgames_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
  } catch (e) { /* localStorage blocked */ }
}
