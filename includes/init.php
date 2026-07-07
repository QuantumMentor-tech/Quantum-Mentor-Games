<?php
/**
 * QMGames Store - Application Bootstrap (init.php)
 * Step: 3 — Backend Connection System
 *
 * PURPOSE:
 *   This is the single entry point for the entire bootstrap sequence.
 *   Every public page and admin page must include this file first.
 *
 * INCLUDE ORDER (matters — do not change):
 *   1. config.php        — Constants, paths, DB credentials, timezone
 *   2. error-handler.php — Custom error/exception handlers + logAppError()
 *   3. db.php            — PDO connection ($pdo) + getDB()
 *   4. functions.php     — All helper functions
 *
 * USAGE FROM ROOT-LEVEL PAGES (index.php, games.php, etc.):
 *   require_once __DIR__ . '/includes/init.php';
 *
 * USAGE FROM /pages/ SUBDIRECTORY (pages/about.php, etc.):
 *   require_once __DIR__ . '/../includes/init.php';
 *
 * USAGE FROM /admin/ SUBDIRECTORY (admin/dashboard.php, etc.):
 *   require_once __DIR__ . '/../includes/init.php';
 *
 * !! Never include config.php, db.php, or functions.php directly
 *    in page files. Always use init.php as the single bootstrap. !!
 *
 * SESSION:
 *   init.php starts the PHP session here, so individual files
 *   do not need to call session_start() themselves.
 */

/* ============================================================
   Guard: prevent double-initialisation if included more than once
   ============================================================ */
if (defined('QMGAMES_INIT')) {
    return;
}
define('QMGAMES_INIT', true);

/* ============================================================
   1. Configuration — must come first
   ============================================================ */
require_once __DIR__ . '/config.php';

/* ============================================================
   2. Error Handler — must come before DB so DB errors are caught
   ============================================================ */
require_once __DIR__ . '/error-handler.php';

/* ============================================================
   3. Database Connection — requires config + error-handler
   ============================================================ */
require_once __DIR__ . '/db.php';

/* ============================================================
   4. Helper Functions — requires config + getDB()
   ============================================================ */
require_once __DIR__ . '/functions.php';

/* ============================================================
   5. Session — start once here; all pages share the same session
   ============================================================ */
if (session_status() === PHP_SESSION_NONE) {
    // Harden session cookie settings
    session_set_cookie_params([
        'lifetime' => defined('SESSION_LIFETIME') ? SESSION_LIFETIME : 3600,
        'path'     => '/',
        'secure'   => false,  // Set true when using HTTPS in production
        'httponly' => true,   // Block JavaScript access to session cookie
        'samesite' => 'Lax',  // CSRF mitigation
    ]);
    session_start();
}
