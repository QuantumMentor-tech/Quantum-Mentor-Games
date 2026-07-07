<?php
/**
 * QMGames Store - Error Handler
 * Step: 3 — Backend Connection System
 *
 * PURPOSE:
 *   - Intercept PHP errors and exceptions safely.
 *   - Log all errors to /logs/error.log.
 *   - NEVER expose raw errors, SQL details, file paths,
 *     DB credentials, or stack traces to public users.
 *   - Show only a safe, friendly message when something breaks.
 *
 * LOADED BY: includes/init.php (after config.php)
 */

/* ============================================================
   Guard: must be loaded after config.php
   ============================================================ */
if (!defined('SITE_NAME')) {
    die('Direct access not permitted.');
}

/* ============================================================
   logAppError()
   Writes a timestamped, clean error message to the log file.
   Silently fails if the log directory/file is not writable —
   never crashes the website over a logging issue.

   @param  string $message   Human-readable error description
   @param  string $context   Optional context label (e.g. 'DB', 'PDO')
   @return void
   ============================================================ */
function logAppError(string $message, string $context = 'APP'): void
{
    // Only log if configured to do so
    if (!defined('LOG_ERRORS') || !LOG_ERRORS) {
        return;
    }

    // Resolve log file path — fall back gracefully if constant missing
    $logDir  = defined('LOGS_PATH') ? LOGS_PATH : __DIR__ . '/../logs';
    $logFile = $logDir . '/error.log';

    // Do not crash the app if the log directory does not exist
    if (!is_dir($logDir)) {
        return;
    }

    // Format: [2025-07-05 14:32:11] [DB] Connection refused on localhost
    $timestamp = date('Y-m-d H:i:s');
    $entry     = "[{$timestamp}] [{$context}] {$message}" . PHP_EOL;

    // file_put_contents with FILE_APPEND is atomic enough for single-server use
    // Suppress errors with @ so a write failure never breaks the page
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

/* ============================================================
   Custom PHP Error Handler
   Converts PHP errors into log entries without printing them.
   ============================================================ */
function qmgamesErrorHandler(
    int    $errno,
    string $errstr,
    string $errfile,
    int    $errline
): bool {
    // Respect the @ error-suppression operator
    if (!(error_reporting() & $errno)) {
        return false;
    }

    // Map PHP error constants to readable labels
    $levels = [
        E_ERROR             => 'ERROR',
        E_WARNING           => 'WARNING',
        E_NOTICE            => 'NOTICE',
        E_USER_ERROR        => 'USER_ERROR',
        E_USER_WARNING      => 'USER_WARNING',
        E_USER_NOTICE       => 'USER_NOTICE',
        E_DEPRECATED        => 'DEPRECATED',
        E_USER_DEPRECATED   => 'USER_DEPRECATED',
        E_STRICT            => 'STRICT',
    ];

    $level = $levels[$errno] ?? "ERROR({$errno})";

    // Strip the server's absolute path from the file path before logging
    // to avoid exposing server directory structure
    $safeFile = defined('ROOT_PATH')
        ? str_replace(ROOT_PATH, '[ROOT]', $errfile)
        : basename($errfile);

    logAppError("{$level}: {$errstr} in {$safeFile} on line {$errline}", 'PHP');

    // Return false lets PHP's internal handler also run (for display_errors, etc.)
    // Since display_errors=0, nothing is shown publicly.
    return false;
}

/* ============================================================
   Custom Exception Handler
   Catches any unhandled Throwable and logs it safely.
   Shows a generic user-friendly error page.
   ============================================================ */
function qmgamesExceptionHandler(Throwable $e): void
{
    $safeFile = defined('ROOT_PATH')
        ? str_replace(ROOT_PATH, '[ROOT]', $e->getFile())
        : basename($e->getFile());

    $message = sprintf(
        '%s: %s in %s on line %d',
        get_class($e),
        $e->getMessage(),
        $safeFile,
        $e->getLine()
    );

    logAppError($message, 'EXCEPTION');

    // Only show detailed error in development AND to localhost
    $isLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);

    if (defined('APP_ENV') && APP_ENV === 'development' && $isLocal) {
        // Safe dev output — still sanitised
        echo '<div style="font-family:monospace;background:#1a1a2e;color:#e0e0e0;'
           . 'padding:1.5rem;margin:1rem;border-radius:8px;border:1px solid #ef4444;">'
           . '<strong style="color:#ef4444;">Development Error (localhost only)</strong><br>'
           . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
           . '</div>';
    } else {
        // Generic public-safe message — no technical details
        showErrorPage();
    }
}

/* ============================================================
   showErrorPage()
   Renders a minimal, user-friendly error page.
   Called when an unrecoverable error occurs on a public page.
   ============================================================ */
function showErrorPage(string $friendlyMessage = ''): void
{
    if (empty($friendlyMessage)) {
        $friendlyMessage = 'Something went wrong. Please try again later.';
    }

    // Avoid sending headers if already sent
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    $siteName = defined('SITE_SHORT_NAME') ? SITE_SHORT_NAME : 'QMGames Store';
    $homeUrl  = defined('SITE_URL')        ? SITE_URL . '/index.php' : '/';

    echo '<!DOCTYPE html><html lang="en"><head>'
       . '<meta charset="UTF-8">'
       . '<meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>Error &mdash; ' . htmlspecialchars($siteName, ENT_QUOTES) . '</title>'
       . '<style>'
       . 'body{margin:0;font-family:system-ui,sans-serif;background:#0d0f1a;color:#e8eaf0;'
       . 'display:flex;align-items:center;justify-content:center;min-height:100vh;}'
       . '.box{text-align:center;max-width:480px;padding:2rem;}'
       . 'h1{font-size:3rem;font-weight:900;color:#00c8ff;margin:0 0 .5rem;}'
       . 'p{color:#8892a4;margin:.75rem 0 2rem;}'
       . 'a{display:inline-block;padding:.65rem 1.5rem;background:#00c8ff;color:#0d0f1a;'
       . 'text-decoration:none;border-radius:8px;font-weight:700;}'
       . '</style></head><body>'
       . '<div class="box">'
       . '<h1>Oops!</h1>'
       . '<p>' . htmlspecialchars($friendlyMessage, ENT_QUOTES, 'UTF-8') . '</p>'
       . '<a href="' . htmlspecialchars($homeUrl, ENT_QUOTES) . '">Go to Homepage</a>'
       . '</div></body></html>';
    exit;
}

/* ============================================================
   Register the handlers
   ============================================================ */
set_error_handler('qmgamesErrorHandler');
set_exception_handler('qmgamesExceptionHandler');
