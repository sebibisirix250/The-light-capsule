<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'The gallery | The Light Capsule';
$pageDescription = 'Exclusive fine art photography and professional cinematography by Ontijt Sébastian.';
$pageKeywords = 'photography, photographer, videography, video editing, professional photographer, wedding photographer, portrait photography, event photography, cinematography, product photography, commercial photography, lifestyle photography, travel photography, drone photography, real estate photography, nature photography, fashion photography, documentary photography, videography services, video production, cinematic video, short film maker, wedding videographer, video post-production, color grading, video effects, film editing, cinematic storytelling, fotografie, fotograf, videografie, editare video, fotograf profesionist, fotograf de nunți, fotografie portret, fotografie de eveniment, cinematografie, fotografie de produs, fotografie comercială, fotografie de stil de viață, fotografie de călătorie, fotografie cu dronă, fotografie imobiliară, fotografie de natură, fotografie de modă, fotografie documentară, servicii de videografie, producție video, video cinematic, realizator de scurtmetraje, videograf de nunți, post-producție video, gradare de culoare, efecte video, editare film, povestire cinematică, photographie, photographe, vidéographie, montage vidéo, photographe professionnel, photographe de mariage, photographie de portrait, photographie d\'événements, cinématographie, photographie de produit, photographie commerciale, photographie de style de vie, photographie de voyage, photographie par drone, photographie immobilière, photographie de nature, photographie de mode, photographie documentaire, services de vidéographie, production vidéo, vidéo cinématographique, créateur de courts-métrages, vidéographe de mariage, post-production vidéo, étalonnage des couleurs, effets vidéo, montage de films, narration cinématographique';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_gallery.css'];
$pageJs = ['gallery.js'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="gallery-page-main" oncontextmenu="return false;">

  <div class="rainbow-wrapper">
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
    <div class="rainbow"></div>
  </div>

  <section class="gallery-controls-wrapper">
    <div id="filters" class="gallery-controls glass-panel show">

      <div class="filter-group">
        <div class="select-wrapper">
          <label for="type-filter" class="sr-only" style="display:none;">Type</label>
          <select id="type-filter" class="custom-select" aria-label="Filter by Type">
            <option value="">All categories</option>
            <option value="individual">Individual</option>
            <option value="group">Group</option>
            <option value="weddings">Weddings</option>
            <option value="automotive">Automotive</option>
            <option value="real-estate">Real estate</option>
            <option value="advertisement">Advertisement</option>
            <option value="baptism">Baptism</option>
            <option value="sports">Sports</option>
            <option value="events">Events</option>
            <option value="landscapes">Landscapes</option>
            <option value="wildlife">Wildlife</option>
            <option value="aerial">Aerial</option>
          </select>
        </div>

        <div class="select-wrapper">
          <label for="resolution-filter" class="sr-only" style="display:none;">Resolution</label>
          <select id="resolution-filter" class="custom-select" aria-label="Filter by Resolution">
            <option value="">All resolutions</option>
            <option value="6000x4000">6000x4000</option>
            <option value="4000x6000">4000x6000</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="select-wrapper">
          <label for="edit-filter" class="sr-only" style="display:none;">Edit</label>
          <select id="edit-filter" class="custom-select" aria-label="Filter by Edit Style">
            <option value="">All styles</option>
            <option value="natural">Natural</option>
            <option value="artistic">Artistic</option>
            <option value="cinematic">Cinematic</option>
            <option value="documentary">Documentary</option>
          </select>
        </div>
      </div>

      <div class="view-controls">
        <span id="gallery-results-info" class="results-info"></span>
      </div>
    </div>
  </section>

  <div id="gallery-empty" class="gallery-empty-state" style="display:none;">
    <i data-lucide="camera-off" class="empty-icon"></i>
    <p>No images found matching your filters.</p>
    <button onclick="document.getElementById('type-filter').value=''; document.getElementById('resolution-filter').value=''; document.getElementById('edit-filter').value=''; applyFilters();" class="btn-clear-filters">Clear Filters</button>
  </div>

  <section id="gallery" class="gallery-grid" aria-label="Photography portfolio">
    <div class="box">
      <div class="collumn"></div>
      <div class="collumn"></div>
      <div class="collumn"></div>
    </div>
  </section>

  <div class="gallery-pagination">
    <button id="gallery-load-more" class="btn-load-more" style="display:none;">
      <i data-lucide="plus"></i> Load more art
    </button>
    <div id="gallery-load-more-status" class="loading-status" style="opacity:0;">
      Loading more pieces...
    </div>
  </div>

  <div id="gallery-loading" class="gallery-spinner" style="display:none;">
    <div class="spinner-ring"></div>
  </div>

  <div id="modal" class="cinematic-modal">
    <div class="modal-bg-blur"></div>

    <button class="close-modal close" aria-label="Close modal">&times;</button>

    <div class="modal-inner">
      <button class="nav-btn prev" aria-label="Previous image">&#10094;</button>

      <div class="modal-content-wrapper">
        <img class="modal-content" id="modal-img" alt="Gallery viewing room" />

        <div class="modal-actions">
          <a id="modal-detail-link" class="modal-detail-link glass-btn" href="#" style="display:none;">
            <i data-lucide="shopping-bag"></i> View & purchase
          </a>
        </div>
      </div>

      <button class="nav-btn next" aria-label="Next image">&#10095;</button>
    </div>
  </div>

</main>

<script>
  window.BASE_URL = <?= json_encode(BASE_URL, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  window.GALLERY_FEED_URL = <?= json_encode(BASE_URL . '/backend/handlers/gallery_feed.php', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  document.getElementById("modal-img").addEventListener("contextmenu", function(e) {
    e.preventDefault();
  });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>