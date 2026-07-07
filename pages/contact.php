<?php
/**
 * QMGames Store — Contact Page
 * Step: 15 — Contact Page
 *
 * POST-Redirect-GET pattern:
 *   POST  → validate → insert → redirect to ?sent=1
 *   GET ?sent=1 → show success card
 */

require_once __DIR__ . '/../includes/init.php';
startSafeSession();

const CONTACT_CSRF_KEY  = 'contact_form';
const CONTACT_COOLDOWN  = 60; /* seconds between submissions */
const CONTACT_SESS_TIME = 'qmgames_last_contact_submit';

/* ================================================================
   1. DETECT SUCCESS REDIRECT
   ================================================================ */
$showSuccess = (isset($_GET['sent']) && $_GET['sent'] === '1');

/* ================================================================
   2. HANDLE POST SUBMISSION
   ================================================================ */
$formErrors    = [];
$submittedData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ── CSRF check ── */
    $csrfToken = trim((string)($_POST['csrf_token'] ?? ''));
    if (!validateCsrfToken($csrfToken, CONTACT_CSRF_KEY)) {
        $formErrors[] = 'Your contact session expired. Please refresh and try again.';
    }

    /* ── Honeypot ── */
    $honeypot = trim((string)($_POST['website_url'] ?? ''));
    if ($honeypot !== '') {
        /* Fake success — redirect as if submitted */
        redirect(siteUrl('pages/contact.php?sent=1'));
    }

    /* ── Session cooldown ── */
    if (empty($formErrors)) {
        $lastSubmit = (int)($_SESSION[CONTACT_SESS_TIME] ?? 0);
        if ((time() - $lastSubmit) < CONTACT_COOLDOWN) {
            $formErrors[] = 'Please wait a moment before sending another message.';
        }
    }

    /* ── Field validation ── */
    if (empty($formErrors)) {
        $validation = validateContactForm($_POST);
        if (!$validation['valid']) {
            $formErrors = $validation['errors'];
            /* Keep entered values for re-display */
            $submittedData = [
                'name'    => trim((string)($_POST['name']    ?? '')),
                'email'   => trim((string)($_POST['email']   ?? '')),
                'subject' => trim((string)($_POST['subject'] ?? '')),
                'message' => trim((string)($_POST['message'] ?? '')),
            ];
        } else {
            /* ── Insert + redirect ── */
            $inserted = submitContactMessage($validation['data']);
            if ($inserted) {
                $_SESSION[CONTACT_SESS_TIME] = time(); /* update cooldown */
                redirect(siteUrl('pages/contact.php?sent=1'));
            } else {
                $formErrors[] = 'Something went wrong while sending your message. '
                              . 'Please try again later.';
                $submittedData = $validation['data'];
            }
        }
    }
}

/* ================================================================
   3. GENERATE CSRF TOKEN (for form display)
   ================================================================ */
if (!$showSuccess) {
    $csrfTokenValue = generateCsrfToken(CONTACT_CSRF_KEY);
}

/* ================================================================
   4. LOAD CONTACT CARDS
   ================================================================ */
$contactCards = getContactCards();

/* ================================================================
   5. PAGE META
   ================================================================ */
$pageTitle       = 'Contact Us';
$pageDescription = 'Contact ' . SITE_NAME
                 . ' for questions, support, collaborations, and general messages.';
$activePage      = 'contact';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main class="contact-page">

<!-- ── Hero ── -->
<section class="page-hero contact-hero" aria-labelledby="contact-title">
  <div class="container">
    <span class="section-label">Get in Touch</span>
    <h1 class="page-title" id="contact-title">Contact <?= e(SITE_SHORT_NAME) ?></h1>
    <p class="page-subtitle">
      Have a question, suggestion, or support request?
      Send us a message and we will review it.
    </p>
    <p class="text-muted text-sm" style="margin-top:.35rem;">
      For broken download links, please use the
      <a href="<?= e(siteUrl('report-link.php')) ?>">Report Link</a>
      option on the game or download page.
    </p>
  </div>
</section>


<section class="section-sm">
  <div class="container">

    <?php if ($showSuccess): ?>
    <!-- ════════════════════════════
         SUCCESS CARD
         ════════════════════════════ -->
    <div class="contact-success-card card" style="max-width:600px;margin:0 auto;">
      <div class="empty-state" style="padding:3rem 2rem;">
        <span class="empty-state-icon">✅</span>
        <h2 style="font-size:1.3rem;margin-bottom:.65rem;">
          Message Sent Successfully
        </h2>
        <p class="text-muted" style="max-width:440px;margin:0 auto 1.5rem;">
          Thank you for contacting <?= e(SITE_SHORT_NAME) ?>. Your message has been
          saved and will be reviewed by our team.
        </p>
        <div class="empty-state-actions">
          <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-primary">
            🏠 Back to Home
          </a>
          <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-secondary">
            🎮 Browse Games
          </a>
          <a href="<?= e(siteUrl('pages/contact.php')) ?>" class="btn btn-ghost">
            Send Another Message
          </a>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- ════════════════════════════
         CONTACT LAYOUT
         ════════════════════════════ -->

    <div class="contact-layout">

      <!-- ═══════ LEFT: Form ═══════ -->
      <div class="contact-form-area">

        <!-- Validation errors -->
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

        <!-- Form card -->
        <div class="contact-form-card card">
          <div class="card-header">
            <strong>💬 Send a Message</strong>
          </div>
          <div class="card-body">

            <form method="POST"
                  action="<?= e(siteUrl('pages/contact.php')) ?>"
                  class="contact-form"
                  id="contactForm"
                  novalidate>

              <!-- CSRF -->
              <input type="hidden" name="csrf_token"
                     value="<?= e($csrfTokenValue) ?>">

              <!-- Honeypot (hidden from real users) -->
              <div class="honeypot-field" aria-hidden="true">
                <label for="hp_website">Website (leave empty)</label>
                <input type="text" id="hp_website"
                       name="website_url" value=""
                       tabindex="-1" autocomplete="off">
              </div>

              <!-- Name + Email row -->
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="contact_name">
                    Your Name <span style="color:var(--danger);">*</span>
                  </label>
                  <input type="text"
                         id="contact_name"
                         name="name"
                         class="form-control<?= in_array('Please enter your name.', $formErrors) || in_array('Name must be at least 2 characters.', $formErrors) ? ' form-control-error' : '' ?>"
                         placeholder="Your name"
                         value="<?= e($submittedData['name'] ?? '') ?>"
                         maxlength="120"
                         required>
                </div>

                <div class="form-group">
                  <label class="form-label" for="contact_email">
                    Your Email <span style="color:var(--danger);">*</span>
                  </label>
                  <input type="email"
                         id="contact_email"
                         name="email"
                         class="form-control"
                         placeholder="your@email.com"
                         value="<?= e($submittedData['email'] ?? '') ?>"
                         maxlength="150"
                         required>
                </div>
              </div>

              <!-- Subject -->
              <div class="form-group">
                <label class="form-label" for="contact_subject">
                  Subject <span style="color:var(--danger);">*</span>
                </label>
                <input type="text"
                       id="contact_subject"
                       name="subject"
                       class="form-control"
                       placeholder="What is this about?"
                       value="<?= e($submittedData['subject'] ?? '') ?>"
                       maxlength="200"
                       required>
              </div>

              <!-- Message -->
              <div class="form-group">
                <label class="form-label" for="contact_message">
                  Message <span style="color:var(--danger);">*</span>
                </label>
                <textarea id="contact_message"
                          name="message"
                          class="form-control"
                          rows="6"
                          placeholder="Write your message here... describe your question, suggestion, or request."
                          maxlength="3000"
                          data-character-counter
                          data-max-length="3000"
                          required><?= e($submittedData['message'] ?? '') ?></textarea>
                <span class="form-hint">
                  <span id="contactMsgCounter">0</span> / 3000 characters
                </span>
              </div>

              <!-- Privacy note -->
              <p class="contact-privacy-note">
                🔒 Your message details are used only to review and respond to
                your contact request. Do not include sensitive personal information.
                <a href="<?= e(siteUrl('pages/privacy-policy.php')) ?>">Privacy Policy</a>
              </p>

              <!-- Actions -->
              <div class="form-actions">
                <button type="submit"
                        class="btn btn-primary"
                        id="contactSubmitBtn">
                  💬 Send Message
                </button>
                <a href="<?= e(siteUrl('games.php')) ?>"
                   class="btn btn-secondary">
                  🎮 Browse Games
                </a>
                <a href="<?= e(siteUrl('index.php')) ?>"
                   class="btn btn-ghost">
                  🏠 Back to Home
                </a>
              </div>

            </form>

          </div>
        </div><!-- /.contact-form-card -->

      </div><!-- /.contact-form-area -->


      <!-- ═══════ RIGHT: Details + Support ═══════ -->
      <div class="contact-sidebar">

        <!-- Contact detail cards -->
        <div class="contact-details-card card">
          <div class="card-header">
            <strong>📋 Contact Details</strong>
          </div>
          <div class="card-body" style="padding:1rem;">
            <div class="contact-card-grid">
              <?php foreach ($contactCards as $card): ?>
                <div class="contact-info-card">
                  <span class="contact-info-icon" aria-hidden="true">
                    <?= $card['icon'] ?>
                  </span>
                  <div>
                    <p class="contact-info-label"><?= e($card['label']) ?></p>
                    <p class="contact-info-value"><?= e($card['value']) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Before you send / support tips -->
        <div class="support-info-section card">
          <div class="card-header">
            <strong>💡 Before You Send</strong>
          </div>
          <div class="card-body">
            <div class="support-info-grid">

              <div class="support-info-card">
                <span>🔗</span>
                <div>
                  <p class="support-info-title">Broken download link?</p>
                  <p class="text-muted text-xs">
                    Use the <a href="<?= e(siteUrl('report-link.php')) ?>">Report Link</a>
                    button on the game or download page.
                  </p>
                </div>
              </div>

              <div class="support-info-card">
                <span>🎮</span>
                <div>
                  <p class="support-info-title">Want to suggest a game?</p>
                  <p class="text-muted text-xs">
                    Include the game name and its official or legal download source.
                  </p>
                </div>
              </div>

              <div class="support-info-card">
                <span>⚖️</span>
                <div>
                  <p class="support-info-title">Legal or ownership request?</p>
                  <p class="text-muted text-xs">
                    Include the game title, your role, and proof of authorization.
                  </p>
                </div>
              </div>

              <div class="support-info-card">
                <span>🐛</span>
                <div>
                  <p class="support-info-title">Website issue?</p>
                  <p class="text-muted text-xs">
                    Describe the page, your browser, and what happened.
                  </p>
                </div>
              </div>

            </div><!-- /.support-info-grid -->
          </div>
        </div><!-- /.support-info-section -->

      </div><!-- /.contact-sidebar -->

    </div><!-- /.contact-layout -->

    <?php endif; /* end success/form toggle */ ?>

  </div><!-- /.container -->
</section>

</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
