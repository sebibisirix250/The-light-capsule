<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_login.php';

if (!isLoggedIn()) {
    header("Location: " . BASE_URL . "/pages/login.php");
    exit();
}

$pageTitle = 'My orders | The Light Capsule';
$pageDescription = 'Manage and track your orders.';
$pageKeywords = 'photography dashboard, client portal, secure account, order tracking';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_account.css', 'style_orders.css']; 

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$userId = currentUserId();

$stmt = $pdo->prepare("
    SELECT
        id,
        order_type,
        status,
        payment_status,
        total_price,
        created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();
?>

<main class="dashboard-container" oncontextmenu="return false;">

    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <div class="dashboard-wrapper">

        <header class="dashboard-intro orders-header">
            <div class="welcome-text">
                <h1>Your orders</h1>
                <p>Track your orders, products and session packages.</p>
            </div>
            <div class="header-actions">
                <a href="<?= BASE_URL ?>/pages/account.php" class="btn-secondary back-btn">
                    <i data-lucide="arrow-left"></i> Back to dashboard
                </a>
            </div>
        </header>

        <section class="glass-panel orders-panel">

            <?php if (!$orders): ?>

                <div class="empty-state">
                    <i data-lucide="package-open" class="empty-icon"></i>
                    <h2>No orders yet</h2>
                    <p>Explore our collections to start your journey.</p>
                    <a href="<?= BASE_URL ?>/pages/shop.php" class="text-link">Explore collections</a>
                </div>

            <?php else: ?>

                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <?php
                        $date = date('F j, Y', strtotime($order['created_at']));

                        $statusClass = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $order['status']));
                        $paymentClass = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $order['payment_status']));

                        $displayStatus = ucwords(str_replace('_', ' ', $order['status']));
                        $displayPaymentStatus = ucwords(str_replace('_', ' ', $order['payment_status']));
                        ?>

                        <article class="order-card">
                            <div class="order-card-content">

                                <div class="info-row id-row">
                                    <span class="info-label">Order reference</span>
                                    <span class="info-value">#<?= htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <div class="info-row date-row">
                                    <span class="info-label">Order date</span>
                                    <span class="info-value"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <div class="info-row type-row">
                                    <span class="info-label">Order type</span>
                                    <span class="info-value"><?= htmlspecialchars($order['order_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>

                                <div class="info-row price-row">
                                    <span class="info-label">Total amount</span>
                                    <span class="info-value total-price">€<?= number_format((float)$order['total_price'], 2) ?></span>
                                </div>

                                <div class="info-row status-row">
                                    <span class="info-label">Workflow status</span>
                                    <span class="status-badge badge-<?= htmlspecialchars($statusClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($displayStatus, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <div class="info-row payment-row last-row">
                                    <span class="info-label">Payment verification</span>
                                    <span class="payment-badge-text badge-<?= htmlspecialchars($paymentClass, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($displayPaymentStatus, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            </div>

                            <div class="order-card-footer">
                                <a href="<?= BASE_URL ?>/pages/order_detail.php?id=<?= (int)$order['id'] ?>" class="btn-view-order" aria-label="View details for order #<?= (int)$order['id'] ?>">
                                    View details <i data-lucide="chevron-right"></i>
                                </a>
                            </div>
                        </article>

                    <?php endforeach; ?>
                </div>

            <?php endif; ?>

        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>