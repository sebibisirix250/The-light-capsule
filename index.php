<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'The Light Capsule | Premium Photography & Cinematography';
$pageDescription = 'Professional photography, cinematic videography, and premium prints. Capturing weddings, portraits, and commercial events with a unique storytelling perspective.';
$pageKeywords = 'professional photographer, cinematic videography, wedding photography, portrait photographer, event videographer, fine art photo prints, commercial photography';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_index.css'];

require_once __DIR__ . '/includes/page_start.php';
require_once __DIR__ . '/includes/header.php';
?>

<main class="hero-fullscreen" oncontextmenu="return false;">

  <section class="slider-container" id="bgSlider" aria-label="Background image showcase">

    <div class="slide active" style="background-image: url('<?= BASE_URL ?>/assets/images/INDEX/1.jpg');" role="img" aria-label="Featured photography"></div>

    <div class="anti-theft-overlay" aria-hidden="true"></div>
  </section>

  <section class="hero-content" aria-labelledby="hero-heading">
    <h1 id="hero-heading" class="visually-hidden" style="display: none;">The Light Capsule - Explore our work</h1>

    <nav class="cta-group" aria-label="Primary Quick Links">
      <a href="<?= BASE_URL ?>/pages/services.php" class="btn-primary">Services</a>
      <a href="<?= BASE_URL ?>/pages/gallery.php" class="btn-primary highlight">The gallery</a>
      <a href="<?= BASE_URL ?>/pages/shop.php" class="btn-primary">Products</a>
    </nav>
  </section>

</main>

<script src="<?= BASE_URL ?>/js/index.js" defer></script>

</body>

</html>