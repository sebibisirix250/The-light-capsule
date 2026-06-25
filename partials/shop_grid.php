<?php
if (!defined('BASE_URL')) exit('Direct access denied.');
?>

<?php if (!$products): ?>
    <div class="empty-state glass-panel">
        <i data-lucide="camera-off" class="empty-icon"></i>
        <h2>No collections found</h2>
        <p>We are currently curating new assets for this category.</p>
        <button class="pill-btn active" onclick="document.querySelector('[data-category-id=\'0\']').click()">
            View all packs
        </button>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <article class="product-card glass-panel">

                <a href="<?= BASE_URL ?>/pages/product.php?slug=<?= urlencode($product['slug']) ?>" class="card-image-wrapper">
                    <?php if ($product['cover_image']): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product['cover_image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>"
                            loading="lazy"
                            class="product-image">
                    <?php else: ?>
                        <div class="image-placeholder">
                            <i data-lucide="image"></i>
                        </div>
                    <?php endif; ?>

                    <div class="card-overlay">
                        <span class="view-text">View details</span>
                    </div>
                </a>

                <div class="card-content">
                    <div class="card-header">
                        <?php if (!empty($product['category_name'])): ?>
                            <span class="category-label"><?= htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                        <span class="price">€<?= number_format((float)$product['price'], 2) ?></span>
                    </div>

                    <h3 class="product-title"><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p class="product-desc"><?= htmlspecialchars($product['short_description'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="card-actions">
                    <form action="<?= BASE_URL ?>/backend/handlers/add_to_cart_handler.php" method="POST" class="quick-add-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="item_id" value="<?= (int)$product['id'] ?>">
                        <button type="submit" class="quick-add-btn">
                            <i data-lucide="shopping-bag"></i> Add to cart
                        </button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>