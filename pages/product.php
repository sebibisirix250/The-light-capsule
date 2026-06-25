<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$slug = $_GET['slug'] ?? '';

if ($slug === '') {
    header('Location: ' . BASE_URL . '/shop.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        items.id, items.title, items.short_description, items.full_description, 
        items.price, items.cover_image, items.is_physical, items.stock_quantity, 
        items.is_limited_edition, categories.name AS category_name
    FROM items
    LEFT JOIN item_categories ON item_categories.item_id = items.id
    LEFT JOIN categories ON categories.id = item_categories.category_id
    WHERE items.slug = ?
    AND (items.type = 'product' OR items.type = 'digital_product')
    AND items.is_active = 1
    LIMIT 1
");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    $pageTitle = 'Collection not found';
    require_once __DIR__ . '/../includes/page_start.php';
    require_once __DIR__ . '/../includes/header.php';
    echo "<main class='showroom-container error-state'>
            <div class='glass-panel'>
                <h1>Unavailable</h1>
                <p>This masterpiece is currently unavailable or has been archived.</p>
                <a href='" . BASE_URL . "/pages/shop.php' class='btn-return'>Return to the shop</a>
            </div>
          </main>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$galStmt = $pdo->prepare("SELECT image_path FROM item_images WHERE item_id = ? ORDER BY display_order ASC");
$galStmt->execute([$product['id']]);
$galleryImages = $galStmt->fetchAll();

$pageTitle = htmlspecialchars($product['title']) . ' | The Light Capsule';
$cleanDesc = trim(strip_tags($product['short_description']));
$pageDescription = mb_strlen($cleanDesc) > 150 ? mb_substr($cleanDesc, 0, 147) . '...' : $cleanDesc;
$categoryKeyword = $product['category_name'] ?? 'premium collection';
$pageKeywords = htmlspecialchars($product['title']) . ", " . htmlspecialchars($categoryKeyword) . ", premium photography, exclusive collection, LightCapsule";
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_product_showroom.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="cinematic-bg-layer" style="--bg-image: url('<?= BASE_URL ?>/<?= htmlspecialchars($product['cover_image']) ?>');"></div>

<main class="showroom-container">

    <header class="product-header">
        <span class="category-eyebrow"><?= htmlspecialchars($product['category_name'] ?? 'Boutique collection') ?></span>
        <h1 class="product-title"><?= htmlspecialchars($product['title']) ?></h1>
    </header>

    <div class="product-main-grid">

        <div class="visual-stage">
            <div class="cover-photo-wrapper glass-panel">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product['cover_image']) ?>"
                    alt="<?= htmlspecialchars($product['title']) ?>"
                    loading="eager"
                    decoding="async">
                <?php if ($product['is_limited_edition']): ?>
                    <div class="exclusive-ribbon">Limited edition</div>
                <?php endif; ?>
            </div>
        </div>

        <aside class="boutique-sidebar glass-panel">
            <div class="sidebar-content">
                <p class="product-pitch"><?= nl2br(htmlspecialchars($product['short_description'])) ?></p>

                <div class="specs-grid">
                    <div class="spec-item">
                        <span class="spec-label">Format</span>
                        <span class="spec-value"><?= $product['is_physical'] ? 'Physical pack' : 'Digital assets' ?></span>
                    </div>
                    <div class="spec-item">
                        <span class="spec-label">Access</span>
                        <span class="spec-value"><?= ($product['is_physical']) ? 'Premium shipping' : 'Instant download' ?></span>
                    </div>
                </div>

                <div class="purchase-zone">
                    <div class="price-display">
                        <span class="currency">RON</span>
                        <span class="amount"><?= number_format((float)$product['price'], 2) ?></span>
                    </div>

                    <form action="<?= BASE_URL ?>/backend/handlers/add_to_cart_handler.php" method="POST" class="showroom-cart-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="item_id" value="<?= (int)$product['id'] ?>">

                        <?php if ($product['is_physical'] && $product['stock_quantity'] !== null && $product['stock_quantity'] <= 0): ?>
                            <button type="button" class="btn-purchase btn-sold-out" disabled>Sold out</button>
                        <?php else: ?>
                            <button type="submit" class="btn-purchase btn-primary-action">
                                Acquire collection
                            </button>
                        <?php endif; ?>
                    </form>

                    <?php if ($product['is_physical'] && $product['stock_quantity'] !== null && $product['stock_quantity'] > 0 && $product['stock_quantity'] <= 5): ?>
                        <div class="stock-alert">
                            <span class="pulse-dot"></span> Only <?= (int)$product['stock_quantity'] ?> left
                        </div>
                    <?php endif; ?>
                </div>

                <article class="details-accordion">
                    <h3 class="details-title">Product manifest</h3>
                    <div class="details-text">
                        <?= nl2br(htmlspecialchars($product['full_description'])) ?>
                    </div>
                </article>
            </div>
        </aside>

    </div>

    <?php if (!empty($galleryImages)): ?>
        <section class="lookbook-section">
            <h2 class="lookbook-title">Showcase details</h2>
            <div class="lookbook-grid">
                <?php foreach ($galleryImages as $index => $img): ?>
                    <div class="lookbook-item <?= ($index % 3 == 0) ? 'span-2' : '' ?>">
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_path']) ?>"
                            alt="Detail view"
                            loading="lazy"
                            decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>