<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_login.php';

$pageTitle = 'Checkout | The Light Capsule';
$pageDescription = 'Review your photography session bookings and fine art digital prints before proceeding to our secure checkout.';
$pageKeywords = 'photography cart, secure checkout, book photography session, buy digital prints, The Light Capsule cart, fine art prints';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_checkout.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/payment.php';

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo '
    <main class="checkout-empty-container" oncontextmenu="return false;">
        <div class="checkout-bg-overlay" aria-hidden="true"></div>
        <div class="empty-state glass-panel">
            <i data-lucide="camera" class="empty-icon"></i>
            <h2>Your cart is empty</h2>
            <p>Discover our exclusive portrait sessions or explore the fine art print collection.</p>
            <div class="empty-actions">
                <a href="' . BASE_URL . '/pages/shop.php" class="btn-secondary">Browse shop</a>
                <a href="' . BASE_URL . '/pages/gallery.php" class="btn-primary">Browse prints</a>
                <a href="' . BASE_URL . '/pages/services.php" class="btn-secondary">Book a session</a>
            </div>
        </div>
    </main>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$userId = currentUserId();

$stmt = $pdo->prepare("
    SELECT full_name, email, phone
    FROM users
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$total = 0;
$orderType = detectCheckoutOrderType($cart);
$paymentMethods = getEnabledPaymentMethods($orderType);
$defaultPaymentMethod = getDefaultPaymentMethodKey($orderType);
?>

<main class="checkout-container" oncontextmenu="return false;">
    <div class="checkout-bg-overlay" aria-hidden="true"></div>

    <div class="checkout-wrapper">
        <header class="checkout-header">
            <h1>Secure checkout</h1>
            <p>Complete your details below to finalize your order.</p>
        </header>

        <div class="checkout-grid">

            <div class="checkout-form-section">
                <form method="POST" action="<?= BASE_URL ?>/backend/handlers/checkout_handler.php" id="checkout-form" class="glass-panel form-panel">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <fieldset class="form-group">
                        <legend>Contact information</legend>

                        <div class="input-wrapper">
                            <input type="text" name="contact_name" id="contact_name" class="form-input" value="<?= htmlspecialchars($user['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required placeholder=" ">
                            <label for="contact_name" class="floating-label">Full name</label>
                        </div>

                        <div class="input-row">
                            <div class="input-wrapper half-width">
                                <input type="email" name="contact_email" id="contact_email" class="form-input" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required placeholder=" ">
                                <label for="contact_email" class="floating-label">Email address</label>
                            </div>

                            <div class="input-wrapper half-width">
                                <input type="tel" name="contact_phone" id="contact_phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder=" ">
                                <label for="contact_phone" class="floating-label">Phone number</label>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="form-group order-notes-group">
                        <legend>Order notes (Optional)</legend>
                        <div class="input-wrapper">
                            <textarea name="order_notes" id="order_notes" class="form-input textarea-input" rows="3" placeholder=" "></textarea>
                            <label for="order_notes" class="floating-label">Special instructions or session notes</label>
                        </div>
                    </fieldset>

                    <fieldset class="form-group payment-group">
                        <legend>Payment method</legend>

                        <?php if (!$paymentMethods): ?>
                            <div class="no-payment-warning">
                                <i data-lucide="alert-circle"></i>
                                <p>No payment methods are currently available for this order type.</p>
                            </div>
                        <?php else: ?>
                            <div class="payment-methods-grid">
                                <?php foreach ($paymentMethods as $methodKey => $method): ?>
                                    <div class="payment-method-block">
                                        <label class="payment-method-card <?= $methodKey === $defaultPaymentMethod ? 'is-selected' : '' ?>">
                                            <div class="radio-wrapper">
                                                <input type="radio" name="payment_method" value="<?= htmlspecialchars($methodKey, ENT_QUOTES, 'UTF-8') ?>" class="payment-radio" <?= $methodKey === $defaultPaymentMethod ? 'checked' : '' ?> required>
                                                <div class="custom-radio"></div>
                                            </div>
                                            <div class="payment-info">
                                                <span class="payment-title"><?= htmlspecialchars($method['label'] ?? $methodKey, ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php if (!empty($method['checkout_note'])): ?>
                                                    <span class="payment-note"><?= htmlspecialchars($method['checkout_note'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </label>

                                        <?php if (strtolower($methodKey) === 'credit_card' || strtolower($methodKey) === 'stripe'): ?>
                                            <div class="card-element-wrapper">
                                                <div id="card-element" class="gateway-inject-target"></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </fieldset>

                    <fieldset class="form-group legal-group">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="terms_agreed" id="terms_agreed" class="form-checkbox" required>
                            <span class="custom-checkbox"></span>
                            <span class="checkbox-label">I agree to the <a href="<?= BASE_URL ?>/pages/terms.php" target="_blank">terms & conditions</a> and <a href="<?= BASE_URL ?>/pages/refunds.php" target="_blank">refund policy</a>.</span>
                        </label>
                    </fieldset>

                    <div class="checkout-actions">
                        <button type="submit" class="btn-place-order" <?= !$paymentMethods ? 'disabled' : '' ?>>
                            <i data-lucide="lock"></i> Pay & place order
                        </button>
                    </div>
                </form>
            </div>

            <aside class="checkout-summary-section">
                <div class="summary-card glass-panel sticky-panel">
                    <h2>Order summary</h2>
                    <span class="order-type-badge"><?= htmlspecialchars($orderType, ENT_QUOTES, 'UTF-8') ?></span>

                    <div class="summary-items-list custom-scrollbar">
                        <?php foreach ($cart as $item): ?>
                            <?php
                            $lineTotal = (float)($item['price'] ?? 0) * (int)($item['quantity'] ?? 0);
                            $total += $lineTotal;

                            $typeLower = strtolower($item['type'] ?? '');
                            $lucideIcon = (strpos($typeLower, 'session') !== false || strpos($typeLower, 'booking') !== false) ? 'camera' : 'image';
                            ?>
                            <div class="summary-item">
                                <div class="summary-item-thumb">
                                    <?php if (!empty($item['image'])): ?>
                                        <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'] ?? 'Item', ENT_QUOTES, 'UTF-8') ?>" loading="lazy" class="checkout-thumb-img">
                                    <?php else: ?>
                                        <div class="thumb-placeholder">
                                            <i data-lucide="<?= $lucideIcon ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="summary-item-qty-badge"><?= (int)($item['quantity'] ?? 0) ?></span>
                                </div>

                                <div class="summary-item-details">
                                    <h4 class="summary-item-title"><?= htmlspecialchars($item['title'] ?? 'Unknown Item', ENT_QUOTES, 'UTF-8') ?></h4>
                                    <?php if (!empty($item['option_summary'])): ?>
                                        <span class="summary-item-options"><?= htmlspecialchars($item['option_summary'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="summary-item-price">
                                    RON &nbsp;<?= number_format($lineTotal, 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>RON &nbsp;<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="summary-row hint">
                            <span>Shipping</span>
                            <span>Calculated next</span>
                        </div>
                        <div class="summary-row total-row">
                            <span>Total</span>
                            <span>RON &nbsp;<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>