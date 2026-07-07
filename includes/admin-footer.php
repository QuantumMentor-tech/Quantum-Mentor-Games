<?php
/**
 * QMGames Store - Admin Panel Footer
 * Step: 19 — Admin Login System
 */
if (!defined('QMGAMES_INIT')) {
    die('Direct access not permitted. Load init.php first.');
}
?>
    </div><!-- /.admin-content -->
  </main><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<script src="<?= e(assetUrl('js/theme.js')) ?>"></script>
<script src="<?= e(assetUrl('js/admin.js')) ?>"></script>

<footer class="admin-footer">
  &copy; <?= date('Y') ?> <?= e(SITE_SHORT_NAME) ?> &mdash; Admin Panel
</footer>

</body>
</html>
