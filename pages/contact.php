<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Contact | The Light Capsule';
$pageDescription = 'Get in touch with Ontijt Sébastian at The Light Capsule to discuss your next photography, videography, or cinematic storytelling project.';
$pageKeywords = 'contact photographer, book photography session, videography services, The Light Capsule contact, Ontijt Sébastian';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_contact.css'];
$pageJs = ['contact.js'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="contact-page-container" oncontextmenu="return false;">
  <div class="contact-bg-overlay" aria-hidden="true"></div>

  <div class="contact-wrapper">
    <header class="contact-header">
      <h1>Contact us</h1>
      <p>Get in touch to discuss your next visual project.</p>
    </header>

    <div class="contact-grid">

      <section class="contact-form-section glass-panel">
        <form id="contactForm" method="POST" action="<?= BASE_URL ?>/backend/handlers/contact_handler.php" class="contact-form">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

          <div class="input-wrapper">
            <input type="text" id="name" name="name" class="form-input" required placeholder=" " />
            <label for="name" class="floating-label">Name</label>
          </div>

          <div class="input-wrapper">
            <input type="email" id="email" name="email" class="form-input" required placeholder=" " />
            <label for="email" class="floating-label">Email</label>
          </div>

          <div class="input-wrapper">
            <input type="text" id="subject" name="subject" class="form-input" required placeholder=" " />
            <label for="subject" class="floating-label">Subject</label>
          </div>

          <div class="input-wrapper">
            <textarea id="message" name="message" class="form-input textarea-input" rows="5" required placeholder=" "></textarea>
            <label for="message" class="floating-label">Message</label>
          </div>

          <div class="contact-actions">
            <button type="submit" class="btn-submit-contact">
              <i data-lucide="send"></i> Send message
            </button>
          </div>
        </form>

        <div id="contactFormStatus" class="form-status-message"></div>
      </section>

      <aside class="contact-details-section glass-panel">
        <h2>Our information</h2>

        <div class="info-list">
          <div class="info-item">
            <div class="info-icon-wrapper">
              <i data-lucide="mail"></i>
            </div>
            <div class="info-text">
              <h3>Email</h3>
              <p><a href="mailto:info@example.com">info@TheLightCapsule.com</a></p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon-wrapper">
              <i data-lucide="user"></i>
            </div>
            <div class="info-text">
              <h3>Name</h3>
              <p>Ontijt Sebastian</p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon-wrapper">
              <i data-lucide="phone"></i>
            </div>
            <div class="info-text">
              <h3>Telephone</h3>
              <p><a href="tel:+1234567890">+407xx</a></p>
            </div>
          </div>

          <div class="info-item">
            <div class="info-icon-wrapper">
              <i data-lucide="map-pin"></i>
            </div>
            <div class="info-text">
              <h3>Address</h3>
              <p>Address</p>
            </div>
          </div>
        </div>

        <div class="social-icons">
          <a href="#" class="social-link" aria-label="Instagram">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
              <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
              <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
            </svg>
          </a>
          <a href="#" class="social-link" aria-label="Facebook">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
            </svg>
          </a>
          <a href="#" class="social-link" aria-label="YouTube">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33 2.78 2.78 0 0 0 1.94 2C5.12 19.5 12 19.5 12 19.5s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"></path>
              <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
            </svg>
          </a>
        </div>
    </div>
    </aside>

  </div>

  </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>