<?php
/**
 * QMGames Store — Safe Download System
 * Step: 10 — Safe Download Link System
 *
 * LEGAL NOTICE:
 *   This page ONLY handles internal download link IDs.
 *   It NEVER accepts a URL from GET parameters.
 *   It NEVER redirects to arbitrary user-provided URLs.
 *   It ONLY supports legal, authorized, official, freeware,
 *   open-source, demo, or permission-based download links.
 *
 * FLOW:
 *   Mode A (confirmation): download.php?id=5
 *     → Show confirmation page + generate session token
 *   Mode B (redirect):     download.php?id=5&action=go&token=XYZ
 *     → Validate ID, token, link, URL → increment counters → redirect
 */

require_once __DIR__ . '/includes/init.php';

/* ================================================================
   ENSURE SESSION IS ACTIVE (needed for tokens)
   init.php starts the session, but guard here just in case.
   ================================================================ */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================================================================
   1. READ & VALIDATE PARAMETERS
   Only accept numeric ID — nothing else.
   ================================================================ */
$rawId  = $_GET['id'] ?? '';
$linkId = (int) filter_var($rawId, FILTER_VALIDATE_INT);
$action = trim((string)($_GET['action'] ?? ''));
$token  = trim((string)($_GET['token']  ?? ''));

/* ================================================================
   2. VALIDATE ID RANGE
   If ID is 0 or invalid, show missing-ID error immediately.
   ================================================================ */
if ($linkId <= 0) {
    /* Render error without loading any DB data */
    $errorType = 'missing_id';
    goto renderPage;
}

/* ================================================================
   3. HANDLE MODE B — CONTINUE REDIRECT
   Runs before any HTML output so redirect header can be sent.
   ================================================================ */
if ($action === 'go') {

    /* ── 3a. Token validation ── */
    if ($token === '' || !validateDownloadToken($linkId, $token)) {
        clearDownloadToken($linkId);
        $errorType = 'invalid_token';
        goto renderPage;
    }

    /* ── 3b. Load active link + active game ── */
    $linkData = getDownloadLinkDetails($linkId);

    if ($linkData === null) {
        /* Try to determine a better error message */
        $partialData = getDownloadLinkAnyStatus($linkId);
        clearDownloadToken($linkId);

        if ($partialData !== null) {
            /* Link exists but is inactive/broken */
            $errorType   = 'link_unavailable';
            $partialLink = $partialData;
        } else {
            $errorType = 'link_not_found';
        }
        goto renderPage;
    }

    /* ── 3c. Validate stored URL ── */
    $storedUrl  = $linkData['download_url'] ?? '';
    $linkType   = $linkData['link_type'] ?? null;
    $urlIsValid = validateStoredDownloadUrl($storedUrl, $linkType);

    if (!$urlIsValid) {
        logAppError(
            'Download URL validation failed for link #' . $linkId .
            ' — scheme rejected or URL malformed.',
            'DOWNLOAD'
        );
        clearDownloadToken($linkId);
        $errorType = 'invalid_url';
        $linkData  = $linkData; /* keep for back link */
        goto renderPage;
    }

    /* ── 3d. Increment counters via transaction-safe version ── */
    incrementDownloadCountersSafe((int)$linkData['game_id'], $linkId);

    /* ── 3e. Log download event (privacy-safe, silently fails if table missing) ── */
    logDownloadEvent((int)$linkData['game_id'], $linkId);

    /* ── 3e. Clear token (one-time use) ── */
    clearDownloadToken($linkId);

    /* ── 3f. Safe redirect — NO HTML output before this ── */
    header('Location: ' . $storedUrl, true, 302);
    exit;
}

/* ================================================================
   4. HANDLE MODE A — CONFIRMATION PAGE
   Load link data and generate token.
   ================================================================ */
$linkData = getDownloadLinkDetails($linkId);

if ($linkData === null) {
    $partialData = getDownloadLinkAnyStatus($linkId);

    if ($partialData !== null) {
        $errorType   = 'link_unavailable';
        $partialLink = $partialData;
    } else {
        $errorType = 'link_not_found';
    }
    goto renderPage;
}

/* Generate one-time session token for the continue action */
$confirmToken = createDownloadToken($linkId);

/* Build the continue URL using only internal parameters */
$continueUrl = e(siteUrl(
    'download.php?id=' . $linkId .
    '&action=go&token=' . urlencode($confirmToken)
));

/* Page meta */
$pageTitle       = 'Download Ready — ' . ($linkData['game_title'] ?? 'Game');
$pageDescription = 'Secure download confirmation page for '
                 . ($linkData['game_title'] ?? 'this game')
                 . ' on QMGames Store.';
$activePage      = 'games';
$errorType       = null; /* No error */

/* ================================================================
   renderPage — all paths converge here
   ================================================================ */
renderPage:

/* ── Set page meta for error states ── */
if ($errorType !== null && !isset($pageTitle)) {
    $pageTitle       = 'Download Error';
    $pageDescription = 'Download link issue on QMGames Store.';
    $activePage      = 'games';
}

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="download-page">

<?php /* ================================================================
          ERROR STATES
          ================================================================ */
if ($errorType !== null): ?>

<section class="section" aria-label="Download error">
  <div class="container-sm">

    <?php
    /* Choose the right error content */
    switch ($errorType) {

        case 'missing_id':
        case 'link_not_found':
            $errTitle = 'Download Link Not Found';
            $errText  = ($errorType === 'missing_id')
                ? 'The download link is missing or invalid. Please return to the game page and try again.'
                : 'The download link you are looking for does not exist or has been removed.';
            $errShowGame = false;
            break;

        case 'link_unavailable':
            $errTitle    = 'Download Link Unavailable';
            $errText     = 'This download link is currently unavailable. '
                         . 'It may be broken, under review, or removed. '
                         . 'Please try another source or report the issue.';
            $errShowGame = isset($partialLink);
            break;

        case 'game_unavailable':
            $errTitle = 'Game Not Available';
            $errText  = 'The related game is not available for public download right now.';
            $errShowGame = false;
            break;

        case 'invalid_token':
            $errTitle = 'Download Session Expired';
            $errText  = 'Your download confirmation session has expired or is invalid. '
                      . 'Please open the download page again and continue from there.';
            $errShowGame = false;
            break;

        case 'invalid_url':
            $errTitle    = 'Download URL Unavailable';
            $errText     = 'This download source is currently unavailable or could not be verified. '
                         . 'Please report the issue or try another source.';
            $errShowGame = true;
            break;

        default:
            $errTitle = 'Download Error';
            $errText  = 'An unexpected error occurred. Please try again.';
            $errShowGame = false;
    }
    ?>

    <div class="download-error-card card">
      <div class="empty-state">
        <span class="empty-state-icon">🔗</span>
        <h1 style="font-size:1.5rem;margin-bottom:.65rem;">
          <?= e($errTitle) ?>
        </h1>
        <p class="text-muted" style="max-width:480px;margin:0 auto 1.5rem;">
          <?= e($errText) ?>
        </p>

        <div class="empty-state-actions">
          <?php
          /* Show Back to Game Details if we have game info */
          $backSlug = $linkData['game_slug'] ?? ($partialLink['game_slug'] ?? null);
          $backId   = (int)($linkData['game_id'] ?? ($partialLink['game_id'] ?? 0));
          ?>
          <?php if ($errorType === 'invalid_token' && $linkId > 0): ?>
            <a href="<?= e(siteUrl('download.php?id=' . $linkId)) ?>"
               class="btn btn-primary">🔄 Try Again</a>
          <?php endif; ?>

          <?php if ($backSlug !== null): ?>
            <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($backSlug))) ?>"
               class="btn btn-secondary">← Back to Game</a>
          <?php endif; ?>

          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-ghost">
            Browse Games
          </a>

          <?php if ($backId > 0): ?>
            <a href="<?= e(siteUrl('report-link.php?game=' . $backId)) ?>"
               class="btn btn-ghost">Report a Problem</a>
          <?php endif; ?>

          <?php if (!$backSlug && !$backId): ?>
            <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-ghost">
              Back to Home
            </a>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>
</section>

<?php /* ================================================================
          CONFIRMATION PAGE (success state)
          ================================================================ */
else: ?>

<!-- ── Page hero ── -->
<section class="page-hero" aria-labelledby="dl-page-title">
  <div class="container">
    <span class="section-label">Secure Download</span>
    <h1 class="page-title" id="dl-page-title">⬇ Download Ready</h1>
    <p class="page-subtitle">
      Review the game and download source below, then click
      <strong>Continue to Download</strong> to proceed.
    </p>
  </div>
</section>

<!-- ── Confirmation card ── -->
<section class="section-sm">
  <div class="container-sm">
    <div class="download-confirmation">

      <!-- ── Game info block ── -->
      <div class="download-card card">
        <div class="card-header">
          <strong>🎮 Game Information</strong>
        </div>
        <div class="download-game-preview">

          <!-- Cover -->
          <div class="download-cover">
            <?php
            $dlCover = !empty($linkData['game_cover'])
                ? e(siteUrl($linkData['game_cover']))
                : getPlaceholderImage('cover');
            ?>
            <img src="<?= $dlCover ?>"
                 alt="<?= e($linkData['game_title']) ?> cover"
                 class="download-cover-img">
          </div>

          <!-- Game details -->
          <div class="download-game-info">
            <h2 class="download-game-title">
              <?= e($linkData['game_title'] ?? 'Unknown Game') ?>
            </h2>

            <?php if (!empty($linkData['game_desc'])): ?>
              <p class="download-game-desc text-muted text-sm">
                <?= e(truncateText($linkData['game_desc'], 120)) ?>
              </p>
            <?php endif; ?>

            <div class="download-info-grid">
              <?php
              $dlInfoItems = [
                  'Platform' => $linkData['game_platform'] ?? null,
                  'License'  => getLicenseLabel($linkData['game_license'] ?? 'freeware'),
                  'Size'     => !empty($linkData['game_size']) ? $linkData['game_size'] : null,
              ];
              foreach ($dlInfoItems as $lbl => $val):
                if (empty($val)) continue;
              ?>
              <div class="download-info-item">
                <span class="download-info-label"><?= e($lbl) ?></span>
                <span class="download-info-value"><?= e($val) ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

        </div><!-- /.download-game-preview -->
      </div><!-- /.card -->


      <!-- ── Download source ── -->
      <div class="download-source-card card">
        <div class="card-header">
          <strong>🔗 Download Source</strong>
        </div>
        <div class="card-body">

          <div class="download-source-meta">
            <div>
              <p class="download-source-title">
                <?= e($linkData['link_title'] ?? 'Download') ?>
              </p>
              <p class="download-provider">
                📡 <?= e($linkData['provider_name'] ?? '') ?>
              </p>
            </div>
            <div class="download-source-badges">
              <?php
              $dlLinkType  = $linkData['link_type'] ?? '';
              $dlTypeBadge = getLinkTypeBadgeClass($dlLinkType);
              $dlTypeLabel = getReadableLinkType($dlLinkType);
              ?>
              <span class="badge <?= $dlTypeBadge ?>">
                <?= e($dlTypeLabel) ?>
              </span>
              <?php if (!empty($linkData['file_size'])): ?>
                <span class="badge badge-muted">
                  📦 <?= e($linkData['file_size']) ?>
                </span>
              <?php endif; ?>
              <span class="badge badge-success download-status-badge">
                ✅ Active
              </span>
            </div>
          </div>

          <!-- Download count note -->
          <?php $dlCount = (int)($linkData['game_downloads'] ?? 0); ?>
          <?php if ($dlCount > 0): ?>
            <p class="download-count-note text-muted text-xs">
              ⬇ <?= e(formatNumberShort($dlCount)) ?> downloads so far
            </p>
          <?php endif; ?>

        </div>
      </div><!-- /.download-source-card -->


      <!-- ── Safety notice ── -->
      <div class="download-safety-notice">
        <span class="safety-icon" aria-hidden="true">🛡️</span>
        <p>
          <strong>QMGames Store only supports legal, authorized, official, freeware,
          open-source, demo, or permission-based download links.</strong>
          Always verify the source and scan downloaded files before installing
          software on your device.
        </p>
      </div>


      <!-- ── Action buttons ── -->
      <div class="download-actions">

        <!-- Continue button — JS disables it after click to prevent double-submit -->
        <a href="<?= $continueUrl ?>"
           class="btn btn-primary btn-lg"
           id="continueDownloadBtn"
           data-download-continue>
          ⬇&nbsp; Continue to Download
        </a>

        <?php $gameSlug = $linkData['game_slug'] ?? ''; ?>
        <?php if ($gameSlug !== ''): ?>
          <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($gameSlug))) ?>"
             class="btn btn-secondary">
            ← Back to Game
          </a>
        <?php endif; ?>

        <a href="<?= e(siteUrl('report-link.php?game=' . (int)$linkData['game_id'] . '&link=' . $linkId)) ?>"
           class="btn btn-ghost btn-sm">
          🚩 Report a Problem
        </a>

      </div><!-- /.download-actions -->

    </div><!-- /.download-confirmation -->
  </div><!-- /.container-sm -->
</section>

<?php endif; /* end confirmation page */ ?>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
