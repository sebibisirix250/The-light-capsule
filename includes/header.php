<?php
$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $cartItem) {
    $cartCount += isset($cartItem['quantity']) ? (int)$cartItem['quantity'] : 1;
  }
}
?>

<header class="site-header">
  <div class="header-container">

    <nav class="header-left" aria-label="primary navigation left">
      <ul class="nav-list desktop-only">
        <li><a href="<?= BASE_URL ?>/pages/services.php" class="nav-link">Services</a></li>
        <li><a href="<?= BASE_URL ?>/pages/portfolio.php" class="nav-link">Portfolio</a></li>
        <li><a href="<?= BASE_URL ?>/pages/gallery.php" class="nav-link">Gallery</a></li>
      </ul>
    </nav>

    <div class="header-center">
      <a href="<?= BASE_URL ?>/index.php" class="brand-logo" aria-label="The Light Capsule home">
        The Light Capsule
      </a>
    </div>

    <nav class="header-right" aria-label="Primary navigation right">
      <ul class="nav-list desktop-only">
        <li><a href="<?= BASE_URL ?>/pages/shop.php" class="nav-link">Shop</a></li>
        <li><a href="<?= BASE_URL ?>/pages/about.php" class="nav-link">About</a></li>
        <li><a href="<?= BASE_URL ?>/pages/contact.php" class="nav-link">Contact</a></li>
      </ul>

      <div class="header-utilities">
        <div class="nav-auth desktop-only">
          <?php if (isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/pages/account.php" class="icon-link" aria-label="Account"><i data-lucide="user"></i></a>
            <a href="<?= BASE_URL ?>/backend/handlers/logout_handler.php" class="icon-link" aria-label="Logout"><i data-lucide="log-out"></i></a>
          <?php else: ?>
            <a href="<?= BASE_URL ?>/pages/login.php" class="icon-link" aria-label="Login"><i data-lucide="user"></i></a>
          <?php endif; ?>
        </div>

        <a href="<?= BASE_URL ?>/pages/cart.php" class="icon-link cart-toggle" aria-label="Shopping cart">
          <i data-lucide="shopping-bag"></i>
          <?php if ($cartCount > 0): ?>
            <span class="cart-badge" id="cartItemCount"><?= $cartCount ?></span>
          <?php else: ?>
            <span class="cart-badge" id="cartItemCount" style="display: none;">0</span>
          <?php endif; ?>
        </a>

        <button type="button" class="mobile-menu-btn" aria-label="Toggle menu">
          <i data-lucide="menu" class="icon-menu"></i>
        </button>
      </div>
    </nav>

  </div>

  <div class="mobile-overlay" id="mobileMenuOverlay">
    <div class="mobile-overlay-header">
      <button type="button" class="mobile-close-btn" aria-label="Close menu">
        <i data-lucide="x"></i>
      </button>
    </div>
    <ul class="mobile-nav-list">
      <li><a href="<?= BASE_URL ?>/pages/services.php">Services</a></li>
      <li><a href="<?= BASE_URL ?>/pages/portfolio.php">Portfolio</a></li>
      <li><a href="<?= BASE_URL ?>/pages/gallery.php">Gallery</a></li>
      <li><a href="<?= BASE_URL ?>/pages/shop.php">Shop</a></li>
      <li><a href="<?= BASE_URL ?>/pages/about.php">About</a></li>
      <li><a href="<?= BASE_URL ?>/pages/contact.php">Contact</a></li>
      <?php if (!isLoggedIn()): ?>
        <li class="mobile-auth"><a href="<?= BASE_URL ?>/pages/login.php">Login / register</a></li>
      <?php else: ?>
        <li class="mobile-auth"><a href="<?= BASE_URL ?>/pages/account.php">My account</a></li>
        <li class="mobile-auth"><a href="<?= BASE_URL ?>/backend/handlers/logout_handler.php">Logout</a></li>
      <?php endif; ?>
    </ul>
  </div>
</header>

<?php $flash = getFlashMessage(); ?>
<?php if ($flash): ?>
  <div class="flash-alert flash-<?= htmlspecialchars($flash['type']) ?>">
    <?= htmlspecialchars($flash['text']) ?>
    <button class="flash-close" onclick="this.parentElement.remove();"><i data-lucide="x"></i></button>
  </div>
<?php endif; ?>

<script src="<?= BASE_URL ?>/js/header.js" defer></script>