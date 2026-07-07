<?php
/**
 * QMGames Store - Admin Panel HTML Header
 * Step: 19 — Admin Login System
 */

if (!defined('QMGAMES_INIT')) {
    die('Direct access not permitted. Load init.php first.');
}

$_adminTitle = isset($adminPageTitle) && trim($adminPageTitle) !== ''
    ? e(trim($adminPageTitle)) . ' — Admin | ' . e(SITE_SHORT_NAME)
    : 'Admin Panel | ' . e(SITE_SHORT_NAME);
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $_adminTitle ?></title>
  <meta name="robots" content="noindex, nofollow">

  <link rel="stylesheet" href="<?= e(assetUrl('css/style.css')) ?>">
  <link rel="stylesheet" href="<?= e(assetUrl('css/admin.css')) ?>">

  <script>
    (function () {
      try {
        var t = localStorage.getItem('qmgames_theme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
    }());
  </script>
</head>
<body class="admin-body">
