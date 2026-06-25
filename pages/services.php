<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Services | The Light Capsule';
$pageDescription = 'Professional photography and cinematography services.';
$pageKeywords = 'photography, weddings, automotive, aerial, events';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_services.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';

$safeBaseUrl = htmlspecialchars((string)BASE_URL, ENT_QUOTES, 'UTF-8');

$services = [
  ['title' => 'Individual Sessions', 'img' => '1.jpg', 'type' => 'individual', 'desc' => "Capture your unique moments with our personalized individual photography sessions. Whether it's for professional headshots, creative portraits, or simply celebrating yourself, we tailor each session to your style and personality."],
  ['title' => 'Group Sessions', 'img' => '2.jpg', 'type' => 'group', 'desc' => "Celebrate your shared moments with our dynamic group photography sessions. We capture the unique connection between you and your group, showcasing the bond that unites you."],
  ['title' => 'Weddings', 'img' => '3.jpg', 'type' => 'weddings', 'desc' => "From intimate moments to grand celebrations, we document every detail with a blend of candid and artistic shots. Your love story, beautifully preserved from the first vow to the last dance."],
  ['title' => 'Automotive', 'img' => '4.jpg', 'type' => 'automotive', 'desc' => "Showcase your vehicle in style. We highlight every curve and feature using dynamic angles and high-end lighting to create high-impact images for personal or commercial use."],
  ['title' => 'Real Estate', 'img' => '5.jpg', 'type' => 'realEstate', 'desc' => "Present your property in its best light. We focus on spaciousness and natural light to highlight the beauty and functionality of every area, helping you stand out."],
  ['title' => 'Advertising', 'img' => '6.jpg', 'type' => 'advertisement', 'desc' => "Elevate your brand with creative advertising photography. We produce eye-catching images that communicate your message and captivate your target audience."],
  ['title' => 'Baptism', 'img' => '7.jpg', 'type' => 'baptism', 'desc' => "Cherish the sacred moments of your child's baptism. We capture the joy, faith, and love shared during this special occasion, focusing on meaningful rituals."],
  ['title' => 'Sports', 'img' => '8.jpg', 'type' => 'sports', 'desc' => "Capture the thrill of the game. Using fast-paced techniques and precision timing, we deliver sharp, high-energy images that define the intensity and emotion."],
  ['title' => 'Events', 'img' => '9.jpg', 'type' => 'events', 'desc' => "Preserve the highlights of your special occasions. From corporate functions to social gatherings, we capture every key interaction with professionalism and creativity."],
  ['title' => 'Landscapes', 'img' => '10.jpg', 'type' => 'landscapes', 'desc' => "Experience the beauty of nature through exquisite composition. We capture stunning vistas and serene settings, focusing on the atmosphere that brings the outdoors to life."],
  ['title' => 'Wildlife', 'img' => '11.jpg', 'type' => 'wildlife', 'desc' => "Immerse yourself in the natural world. We specialize in capturing the grace and unique personalities of animals in their natural habitats."],
  ['title' => 'Aerial Photography', 'img' => '12.jpg', 'type' => 'aerial', 'desc' => "Elevate your perspective with cutting-edge drone photography. We capture expansive landscapes and architectural marvels from the sky."]
];
?>

<main class="services-page-main" oncontextmenu="return false;">
  <section class="services-grid-container">
    <?php foreach ($services as $service): ?>
      <div class="service-item-wrapper">
        <div class="button">
          <div class="button-content">
            <img src="<?= $safeBaseUrl ?>/assets/Images/PHOTOGRAPHY/<?= $service['img'] ?>" alt="<?= $service['title'] ?>" loading="lazy" />
          </div>
        </div>
        <div class="overlay">
          <h2 class="service-title"><?= $service['title'] ?></h2>
          <div class="overlay-content">
            <p><?= $service['desc'] ?></p>
            <div class="service-actions">
              <a href="<?= $safeBaseUrl ?>/pages/gallery.php?type=<?= $service['type'] ?>" class="service-btn">Gallery</a>
              <a href="<?= $safeBaseUrl ?>/pages/prices.php?service=<?= $service['type'] ?>" class="service-btn">Prices</a>
              <a href="<?= $safeBaseUrl ?>/pages/service_request.php?service=<?= $service['type'] ?>" class="service-btn primary-btn">Book now</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>