<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_login.php';

$pageTitle = 'Order Confirmed | The Light Capsule';
$pageDescription = 'Your photography order has been successfully placed.';
$pageKeywords = 'order success, confirmation, photography checkout, client portal';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_account.css', 'style_order_success.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$orderId = (int)($_GET['order_id'] ?? 0);
$userId = currentUserId();

$order = null;
$payment = null;

if ($orderId > 0) {
    $stmt = $pdo->prepare("
        SELECT id, order_type, payment_status, total_price, created_at
        FROM orders
        WHERE id = ?
        AND user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch();

    if ($order) {
        $stmt = $pdo->prepare("
            SELECT method_key, provider, internal_reference, status, amount, currency, created_at
            FROM payment_transactions
            WHERE order_id = ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([$orderId]);
        $payment = $stmt->fetch();
    }
}
?>

<main class="dashboard-container" oncontextmenu="return false;">
    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <div class="dashboard-wrapper success-wrapper">

        <article class="glass-panel success-card">

            <div class="success-header">
                <div class="icon-wrapper">
                    <i data-lucide="check-circle"></i>
                </div>
                <h1>Order confirmed.</h1>
                <p>Your visual investment has been successfully secured.</p>
            </div>

            <?php if ($order): ?>
                <?php
                $cleanType = ucwords(str_replace('_', ' ', $order['order_type']));
                $cleanPaymentStatus = ucwords(str_replace('_', ' ', $order['payment_status']));
                $paymentClass = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $order['payment_status']));
                ?>

                <div class="receipt-section">
                    <h2 class="section-title">Order summary</h2>

                    <div class="info-row id-row">
                        <span class="info-label">Order reference</span>
                        <span class="info-value">#<?= (int)$order['id'] ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Service category</span>
                        <span class="info-value"><?= htmlspecialchars($cleanType, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total amount</span>
                        <span class="info-value accent">€<?= number_format((float)$order['total_price'], 2) ?></span>
                    </div>
                    <div class="info-row last-row">
                        <span class="info-label">Payment verification</span>
                        <span class="payment-badge-text badge-<?= htmlspecialchars($paymentClass, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($cleanPaymentStatus, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>

                <?php if ($payment): ?>
                    <?php
                    $cleanMethod = ucwords(str_replace('_', ' ', $payment['method_key']));
                    $cleanProvider = ucwords(str_replace('_', ' ', $payment['provider']));
                    $cleanTransStatus = ucwords(str_replace('_', ' ', $payment['status']));
                    ?>
                    <div class="receipt-section payment-section">
                        <h2 class="section-title">Transaction details</h2>

                        <div class="info-row">
                            <span class="info-label">Method</span>
                            <span class="info-value"><?= htmlspecialchars($cleanMethod, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Provider</span>
                            <span class="info-value"><?= htmlspecialchars($cleanProvider, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value"><?= htmlspecialchars($cleanTransStatus, ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="info-row last-row">
                            <span class="info-label">Reference</span>
                            <span class="info-value hash-text"><?= htmlspecialchars($payment['internal_reference'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif ($orderId > 0): ?>

                <div class="receipt-section">
                    <div class="info-row last-row">
                        <span class="info-label">Order reference</span>
                        <span class="info-value">#<?= (int)$orderId ?></span>
                    </div>
                    <p style="text-align: center; color: var(--text-muted); margin-top: 20px; font-size: 13px;">
                        We have received your request, but additional details are currently syncing.
                    </p>
                </div>

            <?php else: ?>
                <div class="receipt-section">
                    <p style="text-align: center; color: var(--text-muted); font-size: 14px;">
                        Invalid order reference. Please check your dashboard for recent activity.
                    </p>
                </div>
            <?php endif; ?>

            <div class="success-footer">
                <a href="<?= BASE_URL ?>/pages/account_orders.php" class="btn-primary-action">
                    View my orders <i data-lucide="arrow-right"></i>
                </a>
            </div>

        </article>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>