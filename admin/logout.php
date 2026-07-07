<?php
/**
 * QMGames Store — Admin Logout
 * Step: 19 — Admin Login System
 *
 * Clears admin session and redirects to login page.
 * Safe to call even when not logged in.
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';

logoutAdmin();
redirectToAdminLogin('logged_out');
