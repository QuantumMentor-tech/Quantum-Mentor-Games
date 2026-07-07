<?php
/**
 * QMGames Store - Site Configuration
 * Project: Quantum Mentor Games Store
 * Step:    3 — Backend Connection System
 *
 * !! SECURITY RULES !!
 *   - This file must NEVER be served directly to public users.
 *   - Apache denies direct access via .htaccess.
 *   - In production: move DB credentials to server-level env vars
 *     or a config file outside the web root entirely.
 *   - Never echo or print these constants on public-facing pages.
 */

/* ============================================================
   Guard: prevent double-loading
   ============================================================ */
if (defined('SITE_NAME')) {
    return;
}

/* ============================================================
   Site Identity
   ============================================================ */
define('SITE_NAME',       'Quantum Mentor Games Store');
define('SITE_SHORT_NAME', 'QMGames Store');
define('SITE_TAGLINE',    'Legal, Safe & High-Quality Game Downloads');
define('SITE_VERSION',    '1.0.0');

/* ============================================================
   URLs
   Update SITE_URL when deploying to a live domain.
   No trailing slash on any of these.
   ============================================================ */
define('SITE_URL',   'http://localhost/quantum-mentor-games-store');
define('ADMIN_URL',  'http://localhost/quantum-mentor-games-store/admin');
define('ASSETS_URL', 'http://localhost/quantum-mentor-games-store/assets');

/* ============================================================
   Filesystem Paths
   Use these instead of hardcoded paths anywhere in the app.
   ============================================================ */
define('ROOT_PATH',        realpath(__DIR__ . '/..'));
define('INCLUDES_PATH',    __DIR__);
define('ASSETS_PATH',      ROOT_PATH . '/assets');
define('UPLOADS_PATH',     ROOT_PATH . '/assets/uploads');
define('COVERS_PATH',      ROOT_PATH . '/assets/uploads/covers');
define('BANNERS_PATH',     ROOT_PATH . '/assets/uploads/banners');
define('SCREENSHOTS_PATH', ROOT_PATH . '/assets/uploads/screenshots');
define('LOGS_PATH',        ROOT_PATH . '/logs');

/* ============================================================
   Database Configuration
   XAMPP defaults: host=localhost, user=root, pass=(empty)
   !! Never expose these values in HTML output !!
   ============================================================ */
define('DB_HOST',    'localhost');
define('DB_NAME',    'qmgames_store');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

/* ============================================================
   Application Environment
   APP_ENV  : 'development' | 'production'
   DISPLAY_ERRORS: false keeps raw errors off public pages.
                   Error details go to the log file instead.
   LOG_ERRORS    : true writes errors to logs/error.log
   ============================================================ */
define('APP_ENV',       'development');
define('DISPLAY_ERRORS', false);   // Raw errors never shown publicly
define('LOG_ERRORS',     true);    // Always log internally

/* ============================================================
   File Upload Settings
   ============================================================ */
define('MAX_UPLOAD_SIZE',   5 * 1024 * 1024); // 5 MB in bytes
define('ALLOWED_IMG_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

/* ============================================================
   Pagination
   ============================================================ */
define('GAMES_PER_PAGE', 24);
define('ADMIN_PER_PAGE', 20);

/* ============================================================
   Security
   ============================================================ */
define('SESSION_LIFETIME', 3600);       // Session timeout: 1 hour
define('CSRF_TOKEN_NAME',  '_csrf_token');
define('ADMIN_SESSION_TIMEOUT', 7200);  // Admin inactivity timeout: 2 hours

/* ============================================================
   Timezone (Asia/Karachi — PKT, UTC+5)
   ============================================================ */
date_default_timezone_set('Asia/Karachi');

/* ============================================================
   PHP Error Reporting
   DISPLAY_ERRORS = false — errors are logged, not printed.
   In development we still log everything for debugging.
   ============================================================ */
if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(APP_ENV === 'development' ? E_ALL : E_ERROR | E_WARNING);
}

if (LOG_ERRORS) {
    ini_set('log_errors', 1);
    ini_set('error_log', LOGS_PATH . '/error.log');
}

/* ============================================================
   Legal Policy
   ============================================================ */
define('LEGAL_NOTICE',
    'QMGames Store only provides links to legal, authorized, official, freeware, ' .
    'open-source, demo, or permission-based game downloads. ' .
    'Piracy and unauthorized distribution are strictly prohibited.'
);

/* ============================================================
   Analytics Security Salt
   Used for hashing IP addresses and user agents in download
   event logs. Change this value before deploying to production.
   NEVER expose this value in HTML output or error messages.
   ============================================================ */
if (!defined('APP_SALT')) {
    define('APP_SALT', 'qmgames_change_this_salt_before_production_v1');
}
