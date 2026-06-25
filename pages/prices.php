<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Investment & pricing | The Light Capsule';
$pageDescription = 'Transparent pricing and custom packages for premium photography and videography services, including weddings, portraits, real estate, and commercial projects.';
$pageKeywords = 'photography pricing, wedding photographer cost, premium videography packages, commercial photography rates, professional photo sessions';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_prices.css'];
$pageJs = ['prices.js'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="prices-fullscreen" oncontextmenu="return false;">

  <div class="prices-bg-overlay" aria-hidden="true"></div>

  <div class="prices-container">

    <section class="service-selection glass-panel" aria-labelledby="services-heading">
      <h1 id="services-heading" class="sr-only" style="display:none;">Our services</h1>
      <div class="service-grid">
        <button type="button" class="service-button" data-service="individual">Individual session</button>
        <button type="button" class="service-button" data-service="group">Group session</button>
        <button type="button" class="service-button" data-service="weddings">Weddings</button>
        <button type="button" class="service-button" data-service="automotive">Automotive</button>
        <button type="button" class="service-button" data-service="realEstate">Real estate</button>
        <button type="button" class="service-button" data-service="advertisement">Advertising</button>
        <button type="button" class="service-button" data-service="baptism">Baptism</button>
        <button type="button" class="service-button" data-service="sports">Sports</button>
        <button type="button" class="service-button" data-service="events">Events</button>
        <button type="button" class="service-button" data-service="landscapes">Landscapes</button>
        <button type="button" class="service-button" data-service="wildlife">Wildlife</button>
        <button type="button" class="service-button" data-service="aerial">Aerial photography</button>
        <button type="button" class="service-button" data-service="editing">Editing services</button>
      </div>
    </section>

    <aside class="service-pricing-description glass-panel" aria-live="polite" aria-atomic="true">

      <div class="pricing-placeholder" id="pricing-placeholder">
        <i data-lucide="camera" class="placeholder-icon" aria-hidden="true"></i>
        <p>Select a service to view investment details and package options.</p>
      </div>

      <div id="price-content-wrapper" class="hidden" style="display: flex; flex-direction: column; height: 100%;">
        <div class="pricing-header" id="price-header"></div>

        <form id="options-form" class="options-list">
        </form>

        <div class="final-price-container" style="margin-top: auto;">
          <p class="total-price-label">Estimated investment</p>
          <div class="total-price-value" id="total-price-display"></div>
          <p class="service-desc-text" id="service-description"></p>

          <button type="button" id="book-package-btn" class="service-button" style="margin-top: 25px; width: 100%; background: #59C5B8; color: #162935; font-weight: bold; border-color: #59C5B8;">Book this package</button>
        </div>
      </div>

    </aside>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>