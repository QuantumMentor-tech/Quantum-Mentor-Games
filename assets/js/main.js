/**
 * QMGames Store - Main JavaScript
 * Step: 5 — Dark/Light Theme Polish
 *
 * Handles: mobile menu, navbar scroll, active nav link,
 *          smooth scroll, dismissible alerts.
 *
 * NOTE: Theme logic lives entirely in theme.js. No theme code here.
 *       No jQuery. Fails safely if any element is missing.
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  initMobileMenu();
  initNavbarScroll();
  initActiveNavLink();
  initSmoothScroll();
  initDismissibleAlerts();
  initHomeSearch();
  initGamesFilters();
  initScreenshotLightbox();
  initDownloadContinue();
  initReportForm();
  initSearchFilters();
  initContactForm();
});

/* ============================================================
   MOBILE MENU
   ============================================================ */
function initMobileMenu() {
  var hamburger = document.getElementById('hamburger');
  var mobileNav = document.getElementById('mobileNav');
  if (!hamburger || !mobileNav) return;

  function openMenu() {
    hamburger.classList.add('open');
    mobileNav.classList.add('open');
    hamburger.setAttribute('aria-expanded', 'true');
    mobileNav.setAttribute('aria-hidden', 'false');
  }

  function closeMenu() {
    hamburger.classList.remove('open');
    mobileNav.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    mobileNav.setAttribute('aria-hidden', 'true');
  }

  hamburger.addEventListener('click', function (e) {
    e.stopPropagation();
    mobileNav.classList.contains('open') ? closeMenu() : openMenu();
  });

  /* Close when a menu link is clicked */
  mobileNav.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', closeMenu);
  });

  /* Close on outside click */
  document.addEventListener('click', function (e) {
    if (
      mobileNav.classList.contains('open') &&
      !hamburger.contains(e.target) &&
      !mobileNav.contains(e.target)
    ) {
      closeMenu();
    }
  });

  /* Close on Escape */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
      closeMenu();
      hamburger.focus();
    }
  });
}

/* ============================================================
   NAVBAR SCROLL
   Adds .scrolled class past 50px for shadow/border effect
   ============================================================ */
function initNavbarScroll() {
  var navbar = document.querySelector('.navbar');
  if (!navbar) return;

  function check() {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  }

  window.addEventListener('scroll', check, { passive: true });
  check();
}

/* ============================================================
   ACTIVE NAV LINK
   Highlights the link matching the current URL
   ============================================================ */
function initActiveNavLink() {
  var currentPath = window.location.pathname;

  function norm(p) {
    return p
      .replace(/^https?:\/\/[^/]+/, '') /* strip protocol + host */
      .replace(/\/index\.php$/, '/')
      .replace(/\.php$/, '')
      .replace(/\/$/, '') || '/';
  }

  document.querySelectorAll('.nav-link[href]').forEach(function (link) {
    var href = link.getAttribute('href');
    if (!href || href === '#') return;
    if (norm(href) === norm(currentPath)) {
      link.classList.add('active');
    }
  });
}

/* ============================================================
   SMOOTH SCROLL
   ============================================================ */
function initSmoothScroll() {
  var navbar = document.querySelector('.navbar');

  document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
      var id = link.getAttribute('href');
      if (id === '#') return;
      var target = document.querySelector(id);
      if (!target) return;
      e.preventDefault();
      var offset = (navbar ? navbar.offsetHeight : 70) + 16;
      window.scrollTo({
        top: target.getBoundingClientRect().top + window.scrollY - offset,
        behavior: 'smooth'
      });
    });
  });
}

/* ============================================================
   DISMISSIBLE ALERTS
   ============================================================ */
function initDismissibleAlerts() {
  document.querySelectorAll('.alert-close').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var alert = btn.closest('.alert');
      if (!alert) return;
      alert.style.transition = 'opacity 0.3s ease';
      alert.style.opacity    = '0';
      setTimeout(function () { if (alert.parentNode) alert.remove(); }, 320);
    });
  });
}

/* ============================================================
   UTILITY: showAlert()
   Injects a dismissible alert into a container by ID.
   Usage: showAlert('containerId', 'info', 'Message text')
   ============================================================ */
function showAlert(containerId, type, message, autoDismiss) {
  var container = document.getElementById(containerId);
  if (!container) return;

  var icons = { success: '✓', danger: '✕', warning: '⚠', info: 'ℹ' };

  var div     = document.createElement('div');
  div.className = 'alert alert-' + (type || 'info');

  var iconSpan = document.createElement('span');
  iconSpan.className   = 'alert-icon';
  iconSpan.textContent = icons[type] || 'ℹ';

  var textSpan = document.createElement('span');
  textSpan.textContent = message || '';

  var closeBtn = document.createElement('button');
  closeBtn.className = 'alert-close';
  closeBtn.setAttribute('aria-label', 'Dismiss');
  closeBtn.innerHTML = '&times;';
  closeBtn.addEventListener('click', function () {
    div.style.transition = 'opacity 0.3s';
    div.style.opacity    = '0';
    setTimeout(function () { if (div.parentNode) div.remove(); }, 320);
  });

  div.append(iconSpan, textSpan, closeBtn);
  container.insertBefore(div, container.firstChild);

  if (autoDismiss !== false) {
    setTimeout(function () {
      if (div.parentNode) {
        div.style.transition = 'opacity 0.4s';
        div.style.opacity    = '0';
        setTimeout(function () { if (div.parentNode) div.remove(); }, 420);
      }
    }, 5000);
  }
}

/* ============================================================
   HOMEPAGE SEARCH VALIDATION
   Prevents empty search submits with a gentle inline hint.
   Does not interfere with the search.php page search form.
   ============================================================ */
function initHomeSearch() {
  var form  = document.getElementById('homeSearchForm');
  var input = document.getElementById('homeSearchInput');
  var hint  = document.getElementById('homeSearchHint');
  if (!form || !input) return;

  form.addEventListener('submit', function (e) {
    var val = input.value.trim();
    if (val === '') {
      e.preventDefault();
      input.focus();
      if (hint) {
        hint.textContent = 'Please enter a game name to search.';
        hint.style.display = 'block';
        setTimeout(function () {
          hint.style.display = 'none';
        }, 3500);
      }
    }
  });

  /* Clear hint once user starts typing */
  input.addEventListener('input', function () {
    if (hint) { hint.style.display = 'none'; }
  });
}

/* ============================================================
   GAMES LISTING PAGE — FILTER BEHAVIOUR
   Auto-submits select filters when changed (selects only).
   The search input still requires manual submit for UX.
   Does not submit empty search queries automatically.
   ============================================================ */
function initGamesFilters() {
  /* Wire up both games.php (#filterForm) and category.php (#catFilterForm) */
  var forms = ['filterForm', 'catFilterForm'];
  forms.forEach(function (formId) {
    var form = document.getElementById(formId);
    if (!form) return;
    var selects = form.querySelectorAll('select');
    selects.forEach(function (sel) {
      sel.addEventListener('change', function () { form.submit(); });
    });
  });
}

/* ============================================================
   SCREENSHOT LIGHTBOX
   Plain JS, no libraries. Closes on overlay click or Escape key.
   Silently does nothing if screenshot elements are missing.
   ============================================================ */
function initScreenshotLightbox() {
  var lightbox  = document.getElementById('screenshotLightbox');
  var overlay   = document.getElementById('lightboxOverlay');
  var closeBtn  = document.getElementById('lightboxClose');
  var imgEl     = document.getElementById('lightboxImg');
  if (!lightbox || !imgEl) return;

  function openLightbox(src, alt) {
    imgEl.src = src;
    imgEl.alt = alt || '';
    lightbox.classList.add('active');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    if (closeBtn) closeBtn.focus();
  }

  function closeLightbox() {
    lightbox.classList.remove('active');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    imgEl.src = '';
  }

  /* Bind screenshot cards */
  var cards = document.querySelectorAll('#screenshotGrid .screenshot-card');
  cards.forEach(function (card) {
    card.addEventListener('click', function () {
      var src = card.getAttribute('data-src') || '';
      var alt = card.getAttribute('data-alt') || '';
      if (src) openLightbox(src, alt);
    });
    /* Keyboard: Enter or Space */
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        card.click();
      }
    });
  });

  /* Close on overlay click */
  if (overlay) overlay.addEventListener('click', closeLightbox);

  /* Close button */
  if (closeBtn) closeBtn.addEventListener('click', closeLightbox);

  /* Close on Escape */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && lightbox.classList.contains('active')) {
      closeLightbox();
    }
  });
}

/* ============================================================
   DOWNLOAD CONTINUE BUTTON
   Prevents double-click by disabling the button and changing
   its text to "Preparing..." after first click.
   Does NOT block the normal browser navigation — just UX polish.
   ============================================================ */
function initDownloadContinue() {
  var btn = document.querySelector('[data-download-continue]');
  if (!btn) return;

  btn.addEventListener('click', function () {
    /* Visual feedback — disable after first click */
    btn.classList.add('preparing');
    btn.setAttribute('disabled', 'disabled');
    btn.textContent = '⏳ Preparing...';

    /* Re-enable after 8 seconds as a safety fallback
       (in case the page doesn't navigate away) */
    setTimeout(function () {
      if (btn) {
        btn.classList.remove('preparing');
        btn.removeAttribute('disabled');
        btn.innerHTML = '⬇&nbsp; Continue to Download';
      }
    }, 8000);
  });
}

/* ============================================================
   CONTACT FORM ENHANCEMENTS
   - Character counter for contact message textarea
   - Disable submit after click to prevent double-submit
   ============================================================ */
function initContactForm() {
  /* ── Character counter (contact message) ── */
  var textarea  = document.getElementById('contact_message');
  var counterEl = document.getElementById('contactMsgCounter');
  var maxLen    = 3000;

  if (textarea && counterEl) {
    function updateCount() {
      var len = textarea.value.length;
      counterEl.textContent = len;
      if (len > maxLen * 0.9) {
        textarea.style.borderColor = 'var(--warning)';
      } else {
        textarea.style.borderColor = '';
      }
    }
    textarea.addEventListener('input', updateCount);
    updateCount();
  }

  /* ── Disable submit after click ── */
  var form   = document.getElementById('contactForm');
  var submit = document.getElementById('contactSubmitBtn');
  if (form && submit) {
    form.addEventListener('submit', function () {
      submit.disabled    = true;
      submit.textContent = '⏳ Sending...';
    });
  }
}

/* ============================================================
   SEARCH FILTERS AUTO-SUBMIT
   Auto-submits the search filter form when a select changes.
   Does NOT affect the keyword input (requires manual submit).
   ============================================================ */
function initSearchFilters() {
  var form = document.getElementById('searchFilterForm');
  if (!form) return;
  form.querySelectorAll('select').forEach(function (sel) {
    sel.addEventListener('change', function () { form.submit(); });
  });
}

/* ============================================================
   REPORT FORM ENHANCEMENTS
   - Character counter for textarea (data-character-counter)
   - Disable submit button after click to prevent double-submit
   - Light email format hint (non-blocking)
   ============================================================ */
function initReportForm() {
  /* ── Character counter ── */
  var textareas = document.querySelectorAll('[data-character-counter]');
  textareas.forEach(function (ta) {
    var counterId = ta.id === 'report_message' ? 'reportMsgCounter' : null;
    var counterEl = counterId ? document.getElementById(counterId) : null;
    var maxLen    = parseInt(ta.getAttribute('data-max-length') || '2000', 10);

    function updateCounter() {
      var len = ta.value.length;
      if (counterEl) counterEl.textContent = len;
      if (len > maxLen * 0.9) {
        ta.style.borderColor = 'var(--warning)';
      } else {
        ta.style.borderColor = '';
      }
    }

    ta.addEventListener('input', updateCounter);
    updateCounter(); /* initialise */
  });

  /* ── Disable submit after click ── */
  var form   = document.getElementById('reportForm');
  var submit = document.getElementById('reportSubmitBtn');
  if (form && submit) {
    form.addEventListener('submit', function () {
      submit.disabled = true;
      submit.textContent = '⏳ Submitting...';
    });
  }
}

/* ============================================================
   UTILITY: confirmAction()
   ============================================================ */
function confirmAction(msg) {
  return window.confirm(msg || 'Are you sure?');
}
