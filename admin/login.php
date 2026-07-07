<?php
/**
 * QMGames Store — Admin Login Page
 * Step: 19 — Admin Login System
 *
 * Standalone page — uses its own HTML, not the public header/navbar.
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';

startSafeSession();

/* ── If already logged in, go straight to dashboard ── */
if (isAdminLoggedIn()) {
    redirect('admin/dashboard.php');
}

/* ================================================================
   SAFE STATUS MESSAGES (query-string based, whitelisted only)
   ================================================================ */
$statusMsg  = '';
$statusType = 'info';

if (isset($_GET['logged_out']) && $_GET['logged_out'] === '1') {
    $statusMsg  = 'You have been logged out successfully.';
    $statusType = 'success';
} elseif (isset($_GET['expired']) && $_GET['expired'] === '1') {
    $statusMsg  = 'Your admin session expired. Please log in again.';
    $statusType = 'warning';
} elseif (isset($_GET['required']) && $_GET['required'] === '1') {
    $statusMsg  = 'Please log in to access the admin area.';
    $statusType = 'info';
}

/* ================================================================
   CSRF TOKEN
   ================================================================ */
const LOGIN_CSRF_KEY = 'admin_login';
$csrfToken = generateCsrfToken(LOGIN_CSRF_KEY);

/* ================================================================
   FORM PROCESSING
   ================================================================ */
$formError = '';
$emailVal  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── CSRF ── */
    $postToken = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($postToken, LOGIN_CSRF_KEY)) {
        $formError = 'Your login session expired. Please refresh and try again.';
        $csrfToken = generateCsrfToken(LOGIN_CSRF_KEY); /* regenerate */
    }

    /* ── Honeypot ── */
    if ($formError === '' && trim((string)($_POST['website_url'] ?? '')) !== '') {
        /* Fake a slow delay and show generic invalid message */
        $formError = 'Invalid email or password.';
    }

    /* ── Credentials ── */
    if ($formError === '') {
        $postEmail    = trim((string)($_POST['email']    ?? ''));
        $postPassword = trim((string)($_POST['password'] ?? ''));
        $emailVal     = $postEmail; /* repopulate input on error */

        if ($postEmail === '' || $postPassword === '') {
            $formError = 'Please enter your email and password.';
        } else {
            $loginResult = loginAdmin($postEmail, $postPassword);
            if ($loginResult['success']) {
                redirect('admin/dashboard.php');
            } else {
                $formError = $loginResult['message'];
                $csrfToken = generateCsrfToken(LOGIN_CSRF_KEY); /* regenerate */
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | <?= e(SITE_SHORT_NAME) ?></title>
  <meta name="robots" content="noindex, nofollow">

  <link rel="stylesheet" href="<?= e(assetUrl('css/style.css')) ?>">
  <link rel="stylesheet" href="<?= e(assetUrl('css/admin.css')) ?>">

  <!-- Anti-flash theme script -->
  <script>
    (function () {
      try {
        var t = localStorage.getItem('qmgames_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
    }());
  </script>
</head>
<body class="admin-login-page">

<div class="admin-login-shell">

  <!-- Brand -->
  <div class="admin-login-brand">
    <div class="brand-logo-wrap" aria-hidden="true">
      <span class="brand-logo-text">QM</span>
    </div>
    <div>
      <span class="admin-login-brand-name">
        <span class="qm">QM</span>Games Admin
      </span>
    </div>
  </div>

  <!-- Login card -->
  <div class="admin-login-card">

    <div class="admin-login-header">
      <h1 class="admin-login-title">Admin Login</h1>
      <p class="admin-login-subtitle">
        Access the <?= e(SITE_SHORT_NAME) ?> management area.
      </p>
    </div>

    <!-- Status message (logged_out / expired / required) -->
    <?php if ($statusMsg !== ''): ?>
      <div class="admin-alert admin-alert-<?= e($statusType) ?>">
        <?= e($statusMsg) ?>
      </div>
    <?php endif; ?>

    <!-- Validation error -->
    <?php if ($formError !== ''): ?>
      <div class="admin-alert admin-alert-danger">
        <?= e($formError) ?>
      </div>
    <?php endif; ?>

    <form method="POST"
          action="<?= e(siteUrl('admin/login.php')) ?>"
          class="admin-login-form"
          id="adminLoginForm"
          novalidate>

      <!-- CSRF -->
      <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

      <!-- Honeypot -->
      <div class="honeypot-field" aria-hidden="true">
        <label for="hp_url_login">Website (leave empty)</label>
        <input type="text" id="hp_url_login" name="website_url"
               value="" tabindex="-1" autocomplete="off">
      </div>

      <!-- Email -->
      <div class="admin-form-group">
        <label class="admin-form-label" for="admin_email">Email Address</label>
        <input type="email"
               id="admin_email"
               name="email"
               class="admin-form-control"
               placeholder="admin@example.com"
               value="<?= e($emailVal) ?>"
               autocomplete="username"
               maxlength="150"
               required>
      </div>

      <!-- Password -->
      <div class="admin-form-group">
        <label class="admin-form-label" for="admin_password">Password</label>
        <div class="admin-password-wrap">
          <input type="password"
                 id="admin_password"
                 name="password"
                 class="admin-form-control"
                 placeholder="Your password"
                 autocomplete="current-password"
                 required>
          <button type="button"
                  class="admin-password-toggle"
                  id="pwToggle"
                  aria-label="Show or hide password"
                  title="Toggle password visibility">
            👁
          </button>
        </div>
      </div>

      <!-- Submit -->
      <div class="admin-login-actions">
        <button type="submit"
                class="admin-btn admin-btn-primary w-100"
                id="loginSubmitBtn">
          🔐 Login to Dashboard
        </button>
      </div>

    </form><!-- /#adminLoginForm -->

    <!-- Security note -->
    <p class="admin-security-note">
      🔒 Authorized administrators only.
      Unauthorized access attempts are not permitted.
    </p>

  </div><!-- /.admin-login-card -->

  <!-- Back to website link -->
  <div class="admin-back-link">
    <a href="<?= e(siteUrl('index.php')) ?>">
      ← Back to <?= e(SITE_SHORT_NAME) ?>
    </a>
  </div>

</div><!-- /.admin-login-shell -->

<script src="<?= e(assetUrl('js/admin.js')) ?>"></script>
</body>
</html>
