<?php
/**
 * QMGames Store — Report a Download Issue
 * Step: 12 — Broken Link Report System
 *
 * Supports:
 *   report-link.php                          — General (no context)
 *   report-link.php?game=GAME_ID             — Game-specific
 *   report-link.php?game=GAME_ID&link=LINK_ID — Game + link specific
 */

require_once __DIR__ . '/includes/init.php';
startSafeSession();

/* ================================================================
   CSRF KEY for this form
   ================================================================ */
const REPORT_CSRF_KEY = 'report_form';

/* ================================================================
   1. READ & VALIDATE GET PARAMETERS
   ================================================================ */
$rawGameId = isset($_GET['game']) ? (int)$_GET['game'] : 0;
$rawLinkId = isset($_GET['link']) ? (int)$_GET['link'] : 0;

$game      = null;
$linkData  = null;

if ($rawGameId > 0) {
    $game = getPublicGameById($rawGameId);
}

if ($rawLinkId > 0) {
    if ($rawGameId > 0 && $game !== null) {
        /* Confirm link belongs to this game */
        $linkData = getPublicDownloadLinkForGame($rawGameId, $rawLinkId);
    } else {
        /* No game specified — derive game from link */
        $linkData = getPublicDownloadLinkById($rawLinkId);
        if ($linkData !== null && $game === null) {
            $game = getPublicGameById((int)$linkData['game_id']);
        }
    }
}

/* ================================================================
   2. FORM PROCESSING (POST)
   ================================================================ */
$formErrors    = [];
$formSuccess   = false;
$submittedData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── 2a. CSRF validation ── */
    $csrfToken = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($csrfToken, REPORT_CSRF_KEY)) {
        $formErrors[] = 'Your report session expired. Please refresh and try again.';
    }

    /* ── 2b. Honeypot check — silent rejection ── */
    $honeypot = trim((string)($_POST['website_url'] ?? ''));
    if ($honeypot !== '') {
        /* Spam — fake success without saving */
        $formSuccess = true;
    }

    if (!$formSuccess && empty($formErrors)) {

        /* ── 2c. Collect POST values ── */
        $postGameId  = (int)($_POST['game_id'] ?? 0);
        $postLinkId  = (int)($_POST['download_link_id'] ?? 0);
        $reportType  = trim((string)($_POST['report_type'] ?? ''));
        $message     = cleanReportMessage((string)($_POST['message'] ?? ''));
        $userEmail   = trim((string)($_POST['user_email'] ?? ''));

        /* ── 2d. Validate game_id (required by DB schema) ── */
        if ($postGameId <= 0) {
            $formErrors[] = 'Please open this report form from a game page so we can identify the issue correctly.';
        } else {
            $postGame = getPublicGameById($postGameId);
            if ($postGame === null) {
                $formErrors[] = 'The selected game could not be verified. Please try again from the game page.';
                $postGameId = 0;
            }
        }

        /* ── 2e. Validate link_id if provided ── */
        if ($postLinkId > 0 && $postGameId > 0 && empty($formErrors)) {
            $postLink = getPublicDownloadLinkForGame($postGameId, $postLinkId);
            if ($postLink === null) {
                $formErrors[] = 'This download link could not be verified. Please report using the link from the game page.';
                $postLinkId = 0;
            }
        } elseif ($postLinkId <= 0) {
            $postLinkId = null;
        }

        /* ── 2f. Validate report_type ── */
        $validTypes = array_keys(getReportTypeOptions());
        if (!in_array($reportType, $validTypes, true)) {
            $formErrors[] = 'Please choose a valid report type.';
        }

        /* ── 2g. Validate message length ── */
        $msgLen = mb_strlen($message, 'UTF-8');
        if ($msgLen > 0 && $msgLen < 10) {
            $formErrors[] = 'Please provide a bit more detail (at least 10 characters) so we can help.';
        }

        /* ── 2h. Validate optional email ── */
        if ($userEmail !== '') {
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL) || mb_strlen($userEmail) > 150) {
                $formErrors[] = 'Please enter a valid email address, or leave the email field empty.';
            }
        }

        /* ── 2i. Submit if valid ── */
        if (empty($formErrors)) {
            $submitted = submitDownloadReport([
                'game_id'          => $postGameId,
                'download_link_id' => ($postLinkId > 0) ? $postLinkId : null,
                'report_type'      => $reportType,
                'message'          => $message !== '' ? $message : null,
                'user_email'       => $userEmail !== '' ? $userEmail : null,
            ]);

            if ($submitted) {
                $formSuccess = true;
                /* Refresh game/link context from POST values for success page */
                if ($postGameId > 0 && $game === null) {
                    $game = getPublicGameById($postGameId);
                }
            } else {
                $formErrors[] = 'Something went wrong while submitting your report. Please try again later.';
            }
        }

        /* Keep submitted values for re-display on error */
        $submittedData = [
            'report_type' => $reportType,
            'message'     => $message,
            'user_email'  => $userEmail,
        ];
    }
}

/* ================================================================
   3. PAGE META
   ================================================================ */
$pageTitle       = 'Report a Download Issue';
$pageDescription = 'Report broken links, wrong files, slow downloads, or '
                 . 'unsafe download concerns on ' . SITE_SHORT_NAME . '.';
$activePage      = 'games';

/* Generate CSRF token for GET-loaded form */
if (!$formSuccess) {
    $csrfTokenValue = generateCsrfToken(REPORT_CSRF_KEY);
}

/* ================================================================
   4. RENDER
   ================================================================ */
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="report-page">

<!-- ── Hero ── -->
<section class="page-hero report-hero" aria-labelledby="report-title">
  <div class="container">
    <span class="section-label">Community</span>
    <h1 class="page-title" id="report-title">🚩 Report a Download Issue</h1>
    <p class="page-subtitle">
      Help us keep <?= e(SITE_SHORT_NAME) ?> clean, accurate, and safe by
      reporting broken or problematic download links.
    </p>
  </div>
</section>

<section class="section-sm">
  <div class="container">
    <div class="report-layout">

      <!-- ════════════ LEFT: Context + Safety ════════════ -->
      <div class="report-sidebar">

        <!-- Context card -->
        <div class="report-context-card card">
          <div class="card-header">
            <strong>📋 Report Context</strong>
          </div>
          <div class="card-body">

            <?php if ($game !== null): ?>
              <!-- Game context -->
              <div class="report-game-preview">
                <?php if (!empty($game['cover_image'])): ?>
                  <img src="<?= e(siteUrl($game['cover_image'])) ?>"
                       alt="<?= e($game['title']) ?>"
                       class="report-cover-img">
                <?php endif; ?>
                <div>
                  <p class="report-context-label">Game</p>
                  <p class="report-context-value"><?= e($game['title']) ?></p>
                  <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug']))) ?>"
                     class="text-xs" style="color:var(--primary);">
                    View game details →
                  </a>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($linkData !== null): ?>
              <!-- Link context -->
              <div class="report-link-preview <?= $game !== null ? 'mt-2' : '' ?>">
                <p class="report-context-label">Download Link</p>
                <p class="report-context-value">
                  <?= e($linkData['link_title'] ?? 'Download Link') ?>
                </p>
                <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.3rem;">
                  <span class="badge <?= getLinkTypeBadgeClass($linkData['link_type'] ?? '') ?>">
                    <?= e(getReadableLinkType($linkData['link_type'] ?? '')) ?>
                  </span>
                  <span class="badge badge-muted">
                    📡 <?= e($linkData['provider_name'] ?? '') ?>
                  </span>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($game === null && $linkData === null): ?>
              <div class="alert alert-info" style="margin:0;">
                <span class="alert-icon">ℹ️</span>
                <span class="text-sm">
                  For the best experience, please open this page from a game
                  detail page or download page to auto-fill the context.
                </span>
              </div>
            <?php endif; ?>

          </div>
        </div>

        <!-- Safety note -->
        <div class="report-safety-note">
          <span class="safety-icon" aria-hidden="true">🛡️</span>
          <div>
            <strong>Reports help us improve</strong>
            <p class="text-sm text-muted" style="margin:.35rem 0 0;">
              Reports help us review download availability, file accuracy,
              and safety concerns. <?= e(SITE_SHORT_NAME) ?> is designed for
              legal, authorized, official, freeware, open-source, demo, or
              permission-based game downloads only.
            </p>
          </div>
        </div>

      </div><!-- /.report-sidebar -->


      <!-- ════════════ RIGHT: Form or Success ════════════ -->
      <div class="report-main">

        <?php if ($formSuccess): ?>
          <!-- ── SUCCESS STATE ── -->
          <div class="report-success-card card">
            <div class="empty-state" style="padding:3rem 2rem;">
              <span class="empty-state-icon">✅</span>
              <h2 style="font-size:1.3rem;margin-bottom:.6rem;">Report Submitted</h2>
              <p class="text-muted">
                Thank you. Your report has been submitted and will be reviewed
                by our team. We appreciate your help keeping the store safe.
              </p>
              <div class="empty-state-actions mt-3">
                <?php if ($game !== null): ?>
                  <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug']))) ?>"
                     class="btn btn-primary">
                    ← Back to Game
                  </a>
                <?php endif; ?>
                <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
                  Browse Games
                </a>
                <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-ghost">
                  Home
                </a>
              </div>
            </div>
          </div>

        <?php else: ?>
          <!-- ── REPORT FORM ── -->

          <?php if (!empty($formErrors)): ?>
            <div class="alert alert-danger mb-3">
              <span class="alert-icon">✕</span>
              <div>
                <?php foreach ($formErrors as $err): ?>
                  <p style="margin:.1rem 0;"><?= e($err) ?></p>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="report-form-card card">
            <div class="card-header">
              <strong>📝 Submit a Report</strong>
            </div>
            <div class="card-body">

              <form method="POST"
                    action="<?= e(siteUrl('report-link.php'
                              . ($rawGameId > 0 ? '?game=' . $rawGameId : '')
                              . ($rawLinkId > 0 ? '&link=' . $rawLinkId : ''))) ?>"
                    class="report-form"
                    id="reportForm"
                    novalidate>

                <!-- CSRF token -->
                <input type="hidden" name="csrf_token"
                       value="<?= e($csrfTokenValue) ?>">

                <!-- Hidden game_id -->
                <?php $formGameId = $game['id'] ?? 0; ?>
                <input type="hidden" name="game_id"
                       value="<?= (int)$formGameId ?>">

                <!-- Hidden link_id -->
                <?php $formLinkId = $linkData['id'] ?? 0; ?>
                <input type="hidden" name="download_link_id"
                       value="<?= (int)$formLinkId ?>">

                <!-- Honeypot (hidden from real users, traps bots) -->
                <div class="honeypot-field" aria-hidden="true">
                  <label for="website_url">Website (leave empty)</label>
                  <input type="text" id="website_url" name="website_url"
                         value="" tabindex="-1" autocomplete="off">
                </div>

                <!-- Report Type -->
                <div class="form-group">
                  <label class="form-label" for="report_type">
                    Report Type <span style="color:var(--danger);">*</span>
                  </label>
                  <select id="report_type" name="report_type"
                          class="form-control" required>
                    <option value="">— Select a report type —</option>
                    <?php foreach (getReportTypeOptions() as $val => $label): ?>
                      <option value="<?= e($val) ?>"
                        <?= ($submittedData['report_type'] ?? '') === $val ? 'selected' : '' ?>>
                        <?= e($label) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <!-- Message -->
                <div class="form-group">
                  <label class="form-label" for="report_message">
                    Message
                    <span class="text-muted">(optional but helpful)</span>
                  </label>
                  <textarea id="report_message"
                            name="message"
                            class="form-control"
                            rows="5"
                            placeholder="Describe the issue so we can review it. Include what happened and what you expected."
                            maxlength="2000"
                            data-character-counter
                            data-max-length="2000"><?= e($submittedData['message'] ?? '') ?></textarea>
                  <span class="form-hint">
                    <span id="reportMsgCounter">0</span> / 2000 characters
                  </span>
                </div>

                <!-- Email -->
                <div class="form-group">
                  <label class="form-label" for="report_email">
                    Your Email
                    <span class="text-muted">(optional — for follow-up only)</span>
                  </label>
                  <input type="email"
                         id="report_email"
                         name="user_email"
                         class="form-control"
                         placeholder="your@email.com"
                         maxlength="150"
                         value="<?= e($submittedData['user_email'] ?? '') ?>">
                  <span class="form-hint">
                    Your email will not be published or shared.
                  </span>
                </div>

                <!-- Actions -->
                <div class="report-actions">

                  <button type="submit"
                          class="btn btn-primary"
                          id="reportSubmitBtn">
                    🚩 Submit Report
                  </button>

                  <?php if ($game !== null): ?>
                    <a href="<?= e(siteUrl('game-details.php?slug=' . rawurlencode($game['slug']))) ?>"
                       class="btn btn-secondary">
                      ← Back to Game
                    </a>
                  <?php endif; ?>

                  <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-ghost">
                    Browse Games
                  </a>

                </div><!-- /.report-actions -->

              </form><!-- /#reportForm -->

            </div><!-- /.card-body -->
          </div><!-- /.report-form-card -->

        <?php endif; /* end success/form toggle */ ?>

      </div><!-- /.report-main -->

    </div><!-- /.report-layout -->
  </div><!-- /.container -->
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
