<?php
/**
 * QMGames Store - HTML Document Header
 * Step: 5 — Dark/Light Theme Polish
 *
 * DEPENDENCY: init.php must be loaded before this file.
 *
 * Theme method: data-theme="dark|light" on <html> element ONLY.
 * No body.light-theme class mixing. One consistent method.
 *
 * Variables accepted from calling page:
 *   $pageTitle       string — Page title
 *   $pageDescription string — Meta description
 *   $pageKeywords    string — Meta keywords (optional)
 *   $ogImage         string — OG image URL (optional)
 *   $bodyClass       string — Extra <body> class (optional)
 */

$_pt = (isset($pageTitle) && trim($pageTitle) !== '')
    ? e(trim($pageTitle)) . ' | ' . e(SITE_NAME)
    : e(SITE_SHORT_NAME) . ' — ' . e(SITE_TAGLINE);

$_desc = (isset($pageDescription) && trim($pageDescription) !== '')
    ? e(trim($pageDescription))
    : e('Discover legal, safe, and high-quality game downloads at ' . SITE_NAME . '.');

$_kw = (isset($pageKeywords) && trim($pageKeywords) !== '')
    ? e(trim($pageKeywords))
    : 'legal games, freeware download, open source games, demo games, safe game store';

$_canon = e(currentUrl());
$_ogImg = (isset($ogImage) && trim($ogImage) !== '')
    ? e(trim($ogImage))
    : e(assetUrl('images/logo-full.svg'));

$_bclass = (isset($bodyClass) && trim($bodyClass) !== '')
    ? ' class="' . e(trim($bodyClass)) . '"'
    : '';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title><?= $_pt ?></title>
  <meta name="description" content="<?= $_desc ?>">
  <meta name="keywords"    content="<?= $_kw ?>">
  <meta name="author"      content="<?= e(SITE_NAME) ?>">
  <meta name="robots"      content="index, follow">

  <link rel="canonical" href="<?= $_canon ?>">

  <!-- Open Graph -->
  <meta property="og:type"        content="website">
  <meta property="og:url"         content="<?= $_canon ?>">
  <meta property="og:title"       content="<?= $_pt ?>">
  <meta property="og:description" content="<?= $_desc ?>">
  <meta property="og:image"       content="<?= $_ogImg ?>">
  <meta property="og:site_name"   content="<?= e(SITE_NAME) ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card"        content="summary_large_image">
  <meta name="twitter:title"       content="<?= $_pt ?>">
  <meta name="twitter:description" content="<?= $_desc ?>">
  <meta name="twitter:image"       content="<?= $_ogImg ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml"
        href="<?= e(assetUrl('images/logo-symbol.svg')) ?>">
  <link rel="apple-touch-icon"
        href="<?= e(assetUrl('images/logo-symbol.svg')) ?>">

  <!-- Stylesheets — loaded AFTER theme script to avoid flash -->
  <link rel="stylesheet" href="<?= e(assetUrl('css/style.css')) ?>">
  <link rel="stylesheet" href="<?= e(assetUrl('css/responsive.css')) ?>">

  <!--
    THEME FLASH PREVENTION
    Runs synchronously before CSS paints, sets data-theme on <html>.
    Dark is default. If localStorage says 'light', override immediately.
    Fails silently if localStorage is blocked (private browsing, etc.).
  -->
  <script>
    (function () {
      try {
        var saved = localStorage.getItem('qmgames_theme');
        var theme = (saved === 'light' || saved === 'dark') ? saved : 'dark';
        document.documentElement.setAttribute('data-theme', theme);
      } catch (e) {
        /* localStorage unavailable — dark theme stays from HTML attribute */
      }
    }());
  </script>

</head>
<body<?= $_bclass ?>>
