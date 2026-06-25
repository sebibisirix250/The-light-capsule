<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Our story & team | The Light Capsule';
$pageDescription = 'Discover the passionate family-driven team behind The Light Capsule. Based in Bistrița, we specialize in capturing authentic moments through premium photography and cinematic videography.';
$pageKeywords = 'about us, photography team, professional videographers, Bistrita photography, our story, visual storytelling, cinematic wedding video';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_about.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="about-wrapper" oncontextmenu="return false;">

  <section class="parallax-section bgimg-1" aria-label="About us cover">
    <div class="caption">
      <h1 class="glass-pill">ABOUT US</h1>
    </div>
  </section>

  <article class="content-section glass-panel">
    <div class="content-container">
      <h2 class="section-title">Our passionate team</h2>
      <div class="text-block">
        <p>At The light capsule, we are driven by a deep passion for capturing moments that tell a story. Our dedication to the art of photography allows us to provide unparalleled service, focusing on the unique aspects of each shoot. From the initial consultation to the final edit, we ensure every detail is meticulously crafted to meet your expectations.</p>
        <p>Despite being a compact team, our collective expertise and enthusiasm shine through in every project we undertake. Our goal is to create images that are not just visually stunning but also resonate on a personal level. We believe in the power of photography to convey emotions and preserve memories in a way that truly reflects your vision.</p>
        <p>Whether you’re seeking a portrait that captures your essence or a bespoke photo session for a special occasion, PhotoNet is committed to delivering exceptional results with a personal touch. We look forward to working with you and turning your moments into lasting memories.</p>
      </div>
    </div>
  </article>

  <section class="parallax-section bgimg-2" aria-hidden="true">
    <div class="caption">
      <span class="glass-pill">OUR STORY</span>
    </div>
  </section>

  <article class="content-section glass-panel">
    <div class="content-container">
      <div class="text-block">
        <p>A family business focused on passion. We are about bringing what is best in people with whatever we can. Coming from Belgium, we moved to Bistrița, a little town in Romania. As most people, we were always attracted by travelling. Coming across the wonders of this country, we fell in love with it. Seeing things you can't see everywhere, in such an undeveloped part of Europe, made us want to show the true beauty of this piece of paradise. We acquired knowledge in photography, videography, and recently even 3D printing. We think that we can provide something to this world, to you, and we are delighted to be at your service.</p>
      </div>
    </div>
  </article>

  <section class="parallax-section bgimg-3" aria-hidden="true">
    <div class="caption">
      <span class="glass-pill">OUR WORK</span>
    </div>
  </section>

  <section class="content-section glass-panel center-action">
    <div class="content-container">
      <a href="<?= BASE_URL ?>/pages/portfolio.php" class="action-link">
        <span class="action-text">Explore our portfolio</span>
        <i data-lucide="arrow-right" class="action-icon"></i>
      </a>
    </div>
  </section>

  <section class="parallax-section bgimg-4" aria-hidden="true">
    <div class="caption">
      <span class="glass-pill">F.A.Q.</span>
    </div>
  </section>

  <section class="content-section glass-panel faq-section" aria-labelledby="faq-heading">
    <h2 id="faq-heading" class="sr-only" style="display:none;">Frequently asked questions</h2>
    <div class="faq-container">

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-1">
          <span class="faq-question">What do we provide?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-1" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>We provide four main services: photography, videography, editing, and printing. Each service has several purposes, requiring different methods of work and equipment. However, even with the uniqueness of the demands, the goal remains the same: provide the client with the best of everything to reach the maximum potential of every project we embark on.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-2">
          <span class="faq-question">Why should you hire us?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-2" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>We consider every client's ask a project. Each project is personalized to best fit your needs. Our prices match the work we do. With every session, we will never capture the same ambiance, neither work after a similar pattern; we believe every project is unique and needs to be treated as such. Communication is most important between our company and our clients.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-3">
          <span class="faq-question">How is our work done?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-3" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>We do as much on-site as we do behind the curtains. We use the best equipment to provide the highest quality products. Locations can be chosen by our clients or provided by us. We always make sure to familiarize ourselves with our clients, the locations, and the people we will collaborate with ahead of time. Updates will be provided, giving our clients the chance to modify their initial ask.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-4">
          <span class="faq-question">What are the formats of our final products?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-4" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>We supply raw photos with up to 6k resolution and videos in 4k resolution. The final product formats are up to the client. We can create premium albums, print pictures, and frame them. For videography, we provide simple clips up to fully edited cinematic videos. We offer many possibilities to deliver our work, tailored to its intended usage.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-5">
          <span class="faq-question">What equipment do we use?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-5" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>We use high-end DSLR and mirrorless systems equipped with a versatile range of lenses (ranging from wide 18mm to 300mm telephoto, including fixed prime and macro lenses). For printing, we utilize the Canon imagePROGRAF PRO-1000. For albums, we collaborate closely with a specialized local printing house to ensure museum-grade quality.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-6">
          <span class="faq-question">How much do our services cost?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-6" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>Prices are approximated based on the chosen services in our <a href="<?= BASE_URL ?>/pages/prices.php" class="inline-link">interactive pricing guide</a>. Final prices are determined by factors such as travel distances, specific equipment requirements, and project length, all of which are thoroughly discussed prior to beginning our work.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-7">
          <span class="faq-question">How do I schedule an appointment?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-7" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>You can reach out to us directly through our <a href="<?= BASE_URL ?>/pages/contact.php" class="inline-link">Contact Portal</a> via phone, email, or our secure online form to reserve your date.</p>
          </div>
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-toggle" aria-expanded="false" aria-controls="faq-8">
          <span class="faq-question">Are my files secure and backed up?</span>
          <i data-lucide="plus" class="faq-icon plus"></i>
          <i data-lucide="minus" class="faq-icon minus"></i>
        </button>
        <div id="faq-8" class="faq-answer-wrapper" role="region">
          <div class="faq-answer">
            <p>Absolutely. We maintain several secure, redundant backups of everything we capture. Your private content will never be uploaded or shared publicly without your explicit written consent.</p>
          </div>
        </div>
      </div>

    </div>
  </section>

</main>

<script src="<?= BASE_URL ?>/js/FAQ_about_page.js?v=<?= time() ?>" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>