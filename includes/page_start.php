<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$pageTitle       = $pageTitle ?? 'TheLightCapsule - Professional Photography';
$pageDescription = $pageDescription ?? 'Professional photography services, prints, and products.';
$pageKeywords    = $pageKeywords ?? 'photography, prints, photo shop, professional photographer';
$pageAuthor      = $pageAuthor ?? 'LightCapsule';
$pageCss         = $pageCss ?? [];
$pageJs          = $pageJs ?? [];
$pageImage       = $pageImage ?? BASE_URL . '/assets/images/default-og-image.jpg';
$pageUrl         = $pageUrl ?? BASE_URL . $_SERVER['REQUEST_URI'];
$pageType        = $pageType ?? 'website';

csrfToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />

  <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
  <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">

  <meta name="robots" content="index, follow, max-image-preview:large">
  <link rel="canonical" href="<?= htmlspecialchars($pageUrl) ?>" />
  <meta name="copyright" content="&copy; <?= date('Y') ?> <?= htmlspecialchars($pageAuthor) ?>">

  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>" />
  <meta name="author" content="<?= htmlspecialchars($pageAuthor) ?>" />

  <meta property="og:type" content="<?= htmlspecialchars($pageType) ?>" />
  <meta property="og:url" content="<?= htmlspecialchars($pageUrl) ?>" />
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>" />
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <meta property="og:image" content="<?= htmlspecialchars($pageImage) ?>" />

  <meta property="twitter:card" content="summary_large_image" />
  <meta property="twitter:url" content="<?= htmlspecialchars($pageUrl) ?>" />
  <meta property="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>" />
  <meta property="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>" />
  <meta property="twitter:image" content="<?= htmlspecialchars($pageImage) ?>" />

  <meta name="referrer" content="strict-origin-when-cross-origin">
  <meta http-equiv="Content-Security-Policy" content="
    default-src 'self'; 
    img-src 'self' data: blob: https:; 
    style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; 
    font-src 'self' https://fonts.gstatic.com data:;
    script-src 'self' 'unsafe-inline' https://js.stripe.com https://www.paypal.com https://unpkg.com; 
    connect-src 'self' https://api.stripe.com;
    frame-src 'self' https://js.stripe.com https://www.sandbox.paypal.com;
  ">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/images/logo.png" />
  <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/images/apple-touch-icon.png">
  <link rel="manifest" href="<?= BASE_URL ?>/site.webmanifest">

  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style_global.css" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style_header.css" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/style_footer.css" />

  <?php foreach ($pageCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= htmlspecialchars($cssFile) ?>" />
  <?php endforeach; ?>

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "<?= $pageType === 'product' ? 'Product' : 'ProfessionalService' ?>",
      "name": "<?= htmlspecialchars($pageTitle) ?>",
      "image": "<?= htmlspecialchars($pageImage) ?>",
      "description": "<?= htmlspecialchars($pageDescription) ?>",
      "url": "<?= htmlspecialchars($pageUrl) ?>"
    }
  </script>

  <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body>