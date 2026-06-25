<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Cart | The Light Capsule';
$pageDescription = 'Review your photography session bookings and fine art digital prints before proceeding to our secure checkout.';
$pageKeywords = 'photography cart, secure checkout, book photography session, buy digital prints, The Light Capsule cart, fine art prints';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_cart.css'];
$pageJs = ['cart_ui.js'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<main class="cart-container" oncontextmenu="return false;">
    <div class="cart-bg-overlay" aria-hidden="true"></div>

    <div class="cart-wrapper">
        <header class="cart-header">
            <h1>Your collection</h1>
            <p>Review your selected sessions and artwork.</p>
        </header>

        <?php if (empty($cart)): ?>
            <div class="empty-state glass-panel">
                <i data-lucide="camera" class="empty-icon"></i>
                <h2>Your cart is empty</h2>
                <p>Discover our products, book a session or explore the fine art print collection.</p>
                <div class="empty-actions">
                    <a href="<?= BASE_URL ?>/pages/shop.php" class="btn-secondary">Browse shop</a>
                    <a href="<?= BASE_URL ?>/pages/gallery.php" class="btn-primary">Browse prints</a>
                    <a href="<?= BASE_URL ?>/pages/services.php" class="btn-secondary">Book a session</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" action="<?= BASE_URL ?>/backend/handlers/update_cart_handler.php" id="cart-form" class="cart-form">
                <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                <div class="cart-grid">
                    <section class="cart-items-section custom-scrollbar" aria-label="Cart items">
                        <?php foreach ($cart as $cartKey => $item): ?>
                            <?php
                            $lineKey = $item['line_key'] ?? (string)$cartKey;
                            $quantity = (int)($item['quantity'] ?? 1);
                            $price = (float)($item['price'] ?? 0);
                            $lineTotal = $price * $quantity;
                            $total += $lineTotal;

                            $typeLower = strtolower($item['type'] ?? '');
                            $lucideIcon = (strpos($typeLower, 'session') !== false || strpos($typeLower, 'booking') !== false) ? 'camera' : 'image';
                            ?>
                            <article class="cart-item-card glass-panel" data-line-key="<?= htmlspecialchars($lineKey, ENT_QUOTES, 'UTF-8') ?>" data-unit-price="<?= $price ?>">

                                <div class="item-thumbnail">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($item['title'] ?? 'Item', ENT_QUOTES, 'UTF-8') ?>"
                                            loading="lazy"
                                            class="cart-thumb-img">
                                    <?php else: ?>
                                        <div class="thumb-placeholder">
                                            <i data-lucide="<?= $lucideIcon ?>" class="placeholder-icon"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="item-info">
                                    <span class="item-type-badge"><?= htmlspecialchars(ucfirst($item['type'] ?? 'Standard'), ENT_QUOTES, 'UTF-8') ?></span>
                                    <h3 class="item-title"><?= htmlspecialchars($item['title'] ?? 'Untitled', ENT_QUOTES, 'UTF-8') ?></h3>

                                    <?php if (!empty($item['option_summary'])): ?>
                                        <div class="item-options-container">
                                            <?php
                                            $options = explode(' | ', $item['option_summary']);
                                            foreach ($options as $option): ?>
                                                <span class="option-badge"><?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="item-controls">
                                    <div class="price-block">
                                        <span class="price-label">Unit</span>
                                        <span class="unit-price">RON &nbsp;<?= number_format($price, 2) ?></span>
                                    </div>

                                    <div class="quantity-wrapper">
                                        <button type="button" class="qty-btn minus" aria-label="decrease quantity"><i data-lucide="minus"></i></button>
                                        <input type="number" name="quantities[<?= htmlspecialchars($lineKey, ENT_QUOTES, 'UTF-8') ?>]" value="<?= $quantity ?>" min="1" class="qty-input" aria-label="Quantity">
                                        <button type="button" class="qty-btn plus" aria-label="increase quantity"><i data-lucide="plus"></i></button>
                                    </div>

                                    <div class="line-total-block">
                                        <span class="price-label">Subtotal</span>
                                        <span class="line-total" data-line-total="<?= $lineTotal ?>">RON &nbsp;<?= number_format($lineTotal, 2) ?></span>
                                    </div>

                                    <button type="submit" name="quantities[<?= htmlspecialchars($lineKey, ENT_QUOTES, 'UTF-8') ?>]" value="0" class="btn-remove" aria-label="remove item" title="remove item">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </section>

                    <aside class="cart-summary-section">
                        <div class="summary-card glass-panel">
                            <h2>Order summary</h2>
                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span id="summary-subtotal">RON &nbsp;<?= number_format($total, 2) ?></span>
                                </div>
                                <div class="summary-row hint">
                                    <span>Shipping & taxes calculated at checkout</span>
                                </div>

                                <div class="promo-section">
                                    <div class="input-group">
                                        <input type="text" id="promo-input" class="promo-input" placeholder="Promo code" aria-label="Promo code">
                                        <button type="button" id="apply-promo-btn" class="promo-btn">Apply</button>
                                    </div>
                                    <span id="promo-message" class="promo-message"></span>
                                </div>
                            </div>

                            <div class="summary-divider"></div>

                            <div class="summary-row total-row">
                                <span>Total</span>
                                <span id="summary-total">RON &nbsp;<?= number_format($total, 2) ?></span>
                            </div>

                            <div class="cart-main-actions">
                                <button type="submit" name="checkout_action" value="proceed" class="btn-checkout">
                                    Checkout now <i data-lucide="arrow-right"></i>
                                </button>
                            </div
                        </div>
                    </aside>
                </div>
            </form>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>