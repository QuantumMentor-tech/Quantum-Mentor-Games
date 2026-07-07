<?php
/**
 * QMGames Store — Admin Panel Entry Point
 * Step: 19 — Admin Login System
 *
 * Redirects to dashboard if logged in, otherwise to login.
 */

require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAdminLoggedIn()) {
    redirect('admin/dashboard.php');
} else {
    redirectToAdminLogin('required');
}
