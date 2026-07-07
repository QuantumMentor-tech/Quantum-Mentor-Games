<?php
/**
 * QMGames Store - 404 Not Found
 * Step: 4 — Global Layout Design
 */

require_once __DIR__ . '/includes/init.php';

http_response_code(404);

$pageTitle       = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found.';

require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/navbar.php';
?>

<main>
  <section style="min-height:72vh;display:flex;align-items:center;
                  justify-content:center;padding:4rem 1.5rem;">
    <div class="container-sm text-center">

      <!-- Glowing 404 number -->
      <div style="font-size:clamp(5rem,16vw,10rem);font-weight:900;line-height:1;
                  margin-bottom:1rem;
                  background:linear-gradient(135deg,var(--text-heading) 20%,var(--primary) 100%);
                  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                  background-clip:text;filter:drop-shadow(0 0 28px rgba(0,212,255,0.25));"
           aria-hidden="true">
        404
      </div>

      <h1 style="margin-bottom:.65rem;">
        This page wandered into another dimension.
      </h1>

      <p class="text-muted mb-4" style="max-width:440px;margin-left:auto;margin-right:auto;">
        The page you&rsquo;re looking for doesn&rsquo;t exist or may have been moved.
        Let&rsquo;s get you back to the games!
      </p>

      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
        <a href="<?= e(siteUrl('index.php')) ?>" class="btn btn-primary btn-lg">
          &#127968;&nbsp; Go to Homepage
        </a>
        <a href="<?= e(siteUrl('games.php')) ?>" class="btn btn-outline btn-lg">
          &#127918;&nbsp; Browse Games
        </a>
      </div>

      <p class="text-muted text-sm mt-4">
        Found a broken link?
        <a href="<?= e(siteUrl('report-link.php')) ?>">Report it here</a>.
      </p>

    </div>
  </section>
</main>

<?php require_once INCLUDES_PATH . '/footer.php'; ?>
