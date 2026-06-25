<footer class="custom-footer">
  <div class="footer-content">
    <div class="footer-left">
      <p>
        &copy; <?= date('Y'); ?>
        <a href="<?= BASE_URL ?>/pages/copyright.php" class="footer-brand">TheLightCapsule</a>.
        All rights reserved.
      </p>
    </div>

    <div class="footer-right">
      <nav class="footer-nav">
        <a href="<?= BASE_URL ?>/pages/privacy.php">Privacy</a>
        <a href="<?= BASE_URL ?>/pages/terms.php">Terms</a>
        <a href="<?= BASE_URL ?>/pages/refunds.php">Refunds</a>
        <a href="<?= BASE_URL ?>/pages/contact.php">Contact</a>
      </nav>
    </div>
  </div>
</footer>

<?php
if (!empty($pageJs)):
  foreach ($pageJs as $js):
?>
    <script src="<?= BASE_URL ?>/js/<?= htmlspecialchars($js, ENT_QUOTES, 'UTF-8') ?>"></script>
<?php
  endforeach;
endif;
?>

</body>

</html>