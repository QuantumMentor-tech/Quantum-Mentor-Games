<?php
/**
 * QMGames Store - Admin Authentication System
 * Step: 19 — Admin Login System
 *
 * LOADED BY: admin pages via require_once after init.php.
 *
 * !! SECURITY RULES !!
 *   - Passwords are NEVER stored or compared in plain text.
 *   - Password hashes are NEVER stored in sessions.
 *   - session_regenerate_id(true) is called after every login.
 *   - All queries use prepared statements.
 *   - Raw SQL/DB errors are never exposed publicly.
 */

/* ============================================================
   Guard: init.php must be loaded first
   ============================================================ */
if (!defined('QMGAMES_INIT')) {
    die('Direct access not permitted. Load init.php first.');
}

/* ============================================================
   SESSION KEY CONSTANTS
   ============================================================ */
define('ADMIN_SESS_LOGGED_IN',     'admin_logged_in');
define('ADMIN_SESS_ID',            'admin_id');
define('ADMIN_SESS_NAME',          'admin_name');
define('ADMIN_SESS_EMAIL',         'admin_email');
define('ADMIN_SESS_ROLE',          'admin_role');
define('ADMIN_SESS_LOGIN_TIME',    'admin_login_time');
define('ADMIN_SESS_LAST_ACTIVITY', 'admin_last_activity');

/* ============================================================
   isAdminLoggedIn()
   Returns true if a valid active admin session exists and
   the session has not exceeded the inactivity timeout.

   @return bool
   ============================================================ */
function isAdminLoggedIn(): bool
{
    startSafeSession();

    if (empty($_SESSION[ADMIN_SESS_LOGGED_IN]) ||
        empty($_SESSION[ADMIN_SESS_ID])         ||
        empty($_SESSION[ADMIN_SESS_ROLE])) {
        return false;
    }

    /* ── Timeout check ── */
    $timeout      = defined('ADMIN_SESSION_TIMEOUT') ? ADMIN_SESSION_TIMEOUT : 7200;
    $lastActivity = (int)($_SESSION[ADMIN_SESS_LAST_ACTIVITY] ?? 0);

    if ((time() - $lastActivity) > $timeout) {
        clearAdminSession();
        return false;
    }

    /* Refresh activity timestamp on each authenticated request */
    $_SESSION[ADMIN_SESS_LAST_ACTIVITY] = time();
    return true;
}

/* ============================================================
   requireAdmin()
   Guards an admin page. Redirects to login if not authenticated.
   Must be called at the top of every protected admin page,
   BEFORE any HTML output.

   @return void  (never returns on failure — always exits)
   ============================================================ */
function requireAdmin(): void
{
    startSafeSession();

    if (!isAdminLoggedIn()) {
        /* Determine why we're redirecting */
        $lastActivity = (int)($_SESSION[ADMIN_SESS_LAST_ACTIVITY] ?? 0);
        $timeout      = defined('ADMIN_SESSION_TIMEOUT') ? ADMIN_SESSION_TIMEOUT : 7200;

        if (!empty($_SESSION[ADMIN_SESS_LOGGED_IN]) &&
            (time() - $lastActivity) > $timeout) {
            clearAdminSession();
            redirectToAdminLogin('expired');
        } else {
            redirectToAdminLogin('required');
        }
    }
}

/* ============================================================
   loginAdmin()
   Authenticates an admin by email and password.
   On success: regenerates session ID, sets session keys,
               updates last_login_at, returns success.
   On failure: returns failure with a generic message.
   NEVER reveals whether email or password was the problem.

   @param  string $email
   @param  string $password  Plain-text password to verify
   @return array  ['success'=>bool, 'message'=>string]
   ============================================================ */
function loginAdmin(string $email, string $password): array
{
    $fail = ['success' => false,
             'message' => 'Invalid email or password.'];

    $email    = trim($email);
    $password = trim($password);

    if ($email === '' || $password === '') {
        return $fail;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return $fail;
    }

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, name, email, password_hash, role, status
             FROM   admins
             WHERE  email = ?
             LIMIT  1'
        );
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        /* Admin not found */
        if ($admin === false) {
            return $fail;
        }

        /* Admin inactive */
        if ($admin['status'] !== 'active') {
            return $fail;
        }

        /* Password mismatch */
        if (!password_verify($password, $admin['password_hash'])) {
            return $fail;
        }

        /* ── Successful authentication ── */

        startSafeSession();

        /* Regenerate session ID to prevent session fixation */
        session_regenerate_id(true);

        /* Store safe session data — NEVER store password or hash */
        $_SESSION[ADMIN_SESS_LOGGED_IN]     = true;
        $_SESSION[ADMIN_SESS_ID]            = (int)$admin['id'];
        $_SESSION[ADMIN_SESS_NAME]          = $admin['name'];
        $_SESSION[ADMIN_SESS_EMAIL]         = $admin['email'];
        $_SESSION[ADMIN_SESS_ROLE]          = $admin['role'];
        $_SESSION[ADMIN_SESS_LOGIN_TIME]    = time();
        $_SESSION[ADMIN_SESS_LAST_ACTIVITY] = time();

        /* Update last_login_at (non-blocking — login still succeeds if this fails) */
        updateAdminLastLogin((int)$admin['id']);

        return ['success' => true, 'message' => 'Login successful.'];

    } catch (PDOException $e) {
        logAppError('loginAdmin() DB error: ' . $e->getMessage(), 'AUTH');
        return ['success' => false,
                'message' => 'Login is temporarily unavailable. Please try again later.'];
    }
}

/* ============================================================
   logoutAdmin()
   Clears admin session keys and destroys the session.
   Safe to call even if no session exists.

   @return void
   ============================================================ */
function logoutAdmin(): void
{
    startSafeSession();
    clearAdminSession();

    /* Destroy the session entirely */
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }

    /* Start fresh session for flash messages */
    session_start();
}

/* ============================================================
   getCurrentAdmin()
   Returns the current admin's session data.

   @return array|null  null if not logged in
   ============================================================ */
function getCurrentAdmin(): ?array
{
    if (!isAdminLoggedIn()) return null;

    return [
        'id'    => (int)($_SESSION[ADMIN_SESS_ID]    ?? 0),
        'name'  => (string)($_SESSION[ADMIN_SESS_NAME]  ?? ''),
        'email' => (string)($_SESSION[ADMIN_SESS_EMAIL] ?? ''),
        'role'  => (string)($_SESSION[ADMIN_SESS_ROLE]  ?? ''),
    ];
}

/* ============================================================
   hasAdminRole()
   Checks if the current admin has one of the specified roles.
   Example: hasAdminRole(['super_admin', 'admin'])

   @param  array $roles  Allowed role values
   @return bool
   ============================================================ */
function hasAdminRole(array $roles): bool
{
    if (!isAdminLoggedIn()) return false;
    $currentRole = (string)($_SESSION[ADMIN_SESS_ROLE] ?? '');
    return in_array($currentRole, $roles, true);
}

/* ============================================================
   updateAdminLastLogin()
   Updates admins.last_login_at for the given admin ID.
   Silently logs errors — never crashes the login flow.

   @param  int $adminId
   @return void
   ============================================================ */
function updateAdminLastLogin(int $adminId): void
{
    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'UPDATE admins SET last_login_at = NOW() WHERE id = ?'
        );
        $stmt->execute([$adminId]);
    } catch (PDOException $e) {
        logAppError('updateAdminLastLogin() failed for #' . $adminId . ': ' . $e->getMessage(), 'AUTH');
    }
}

/* ============================================================
   clearAdminSession()
   Removes all admin-specific session keys without destroying
   the entire session (preserves flash messages etc.).

   @return void
   ============================================================ */
function clearAdminSession(): void
{
    $keys = [
        ADMIN_SESS_LOGGED_IN,
        ADMIN_SESS_ID,
        ADMIN_SESS_NAME,
        ADMIN_SESS_EMAIL,
        ADMIN_SESS_ROLE,
        ADMIN_SESS_LOGIN_TIME,
        ADMIN_SESS_LAST_ACTIVITY,
    ];
    foreach ($keys as $key) {
        unset($_SESSION[$key]);
    }
}

/* ============================================================
   redirectToAdminLogin()
   Redirects to the admin login page with a safe reason parameter.
   Only whitelisted reason values are forwarded.

   @param  string $reason  'expired' | 'required' | 'logged_out'
   @return void            (exits)
   ============================================================ */
function redirectToAdminLogin(string $reason = ''): void
{
    $allowedReasons = ['expired', 'required', 'logged_out'];
    $qs = in_array($reason, $allowedReasons, true) ? '?' . $reason . '=1' : '';
    header('Location: ' . siteUrl('admin/login.php') . $qs, true, 302);
    exit;
}
