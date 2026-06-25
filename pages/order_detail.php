<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_login.php';

$pageTitle = 'Order details | The Light Capsule';
$pageDescription = 'Review the detailed breakdown, items, and logistics of your photography session.';
$pageKeywords = 'order details, session receipt, photography invoice, client portal';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_account.css', 'style_order_detail.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$orderId = (int)($_GET['id'] ?? 0);
$userId = currentUserId();

if ($orderId <= 0) {
    echo '<main class="dashboard-container"><div class="dashboard-wrapper"><div class="glass-panel empty-state"><h2>Order not found</h2><a href="' . BASE_URL . '/pages/account_orders.php" class="text-link">Return to orders</a></div></div></main>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch();

if (!$order) {
    echo '<main class="dashboard-container"><div class="dashboard-wrapper"><div class="glass-panel empty-state"><h2>Order not found</h2><a href="' . BASE_URL . '/pages/account_orders.php" class="text-link">Return to orders</a></div></div></main>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT item_title, item_type, selected_options, quantity, unit_price, line_total FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

$date = date('F j, Y - H:i', strtotime($order['created_at']));
$cleanType = ucwords(str_replace('_', ' ', $order['order_type']));
$cleanStatus = ucwords(str_replace('_', ' ', $order['status']));
$cleanPayment = ucwords(str_replace('_', ' ', $order['payment_status']));

$statusClass = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $order['status']));
$paymentClass = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $order['payment_status']));
?>

<main class="dashboard-container" oncontextmenu="return false;">
    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <div class="dashboard-wrapper">
        <header class="dashboard-intro detail-header">
            <div class="welcome-text">
                <h1>Order #<?= (int)$order['id'] ?></h1>
                <p>Complete breakdown of your session and investments.</p>
            </div>
            <div class="header-actions">
                <a href="<?= BASE_URL ?>/pages/account_orders.php" class="back-dashboard-link">
                    <i data-lucide="arrow-left"></i> Back to orders
                </a>
            </div>
        </header>

        <div class="order-detail-grid">

            <section class="receipt-section">
                <div class="glass-panel receipt-panel">
                    <h2 class="panel-heading"><i data-lucide="receipt"></i> Itemized breakdown</h2>

                    <div class="items-list">
                        <?php foreach ($items as $item): ?>
                            <div class="receipt-item">
                                <div class="item-primary-info">
                                    <h3 class="item-title"><?= htmlspecialchars($item['item_title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                    <span class="item-type"><?= htmlspecialchars($item['item_type'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if (!empty($item['selected_options'])): ?>
                                        <div class="item-options"><?= nl2br(htmlspecialchars($item['selected_options'], ENT_QUOTES, 'UTF-8')) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-financials">
                                    <div class="qty-price">
                                        <span class="qty"><?= (int)$item['quantity'] ?>x</span>
                                        <span class="unit-price">€<?= number_format((float)$item['unit_price'], 2) ?></span>
                                    </div>
                                    <div class="line-total">€<?= number_format((float)$item['line_total'], 2) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="receipt-footer">
                        <div class="total-row">
                            <span class="total-label">Grand total</span>
                            <span class="total-value">€<?= number_format((float)$order['total_price'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($order['notes'])): ?>
                    <div class="glass-panel notes-panel">
                        <h2 class="panel-heading"><i data-lucide="file-text"></i> Session notes</h2>
                        <div class="notes-content">
                            <?= nl2br(htmlspecialchars($order['notes'], ENT_QUOTES, 'UTF-8')) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <aside class="logistics-sidebar">

                <div class="glass-panel logistics-card">
                    <h2 class="panel-heading"><i data-lucide="info"></i> Order status</h2>
                    <div class="info-row">
                        <span class="info-label">Placement date</span>
                        <span class="info-value"><?= htmlspecialchars($date, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Service type</span>
                        <span class="info-value"><?= htmlspecialchars($cleanType, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Workflow status</span>
                        <span class="status-pill badge-<?= $statusClass ?>">
                            <?= htmlspecialchars($cleanStatus, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <div class="info-row last-row">
                        <span class="info-label">Payment status</span>
                        <span class="payment-status-text">
                            <?= htmlspecialchars($cleanPayment, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </div>

                <?php if (!empty($order['contact_name']) || !empty($order['contact_email']) || !empty($order['contact_phone'])): ?>
                    <div class="glass-panel logistics-card">
                        <h2 class="panel-heading"><i data-lucide="user"></i> Contact details</h2>
                        <?php if (!empty($order['contact_name'])): ?>
                            <div class="info-row">
                                <span class="info-label">Name</span>
                                <span class="info-value"><?= htmlspecialchars($order['contact_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($order['contact_email'])): ?>
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value"><?= htmlspecialchars($order['contact_email'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($order['contact_phone'])): ?>
                            <div class="info-row last-row">
                                <span class="info-label">Phone</span>
                                <span class="info-value"><?= htmlspecialchars($order['contact_phone'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order['address_line']) || !empty($order['city']) || !empty($order['postal_code'])): ?>
                    <div class="glass-panel logistics-card">
                        <h2 class="panel-heading"><i data-lucide="map-pin"></i> Location</h2>
                        <?php if (!empty($order['address_line'])): ?>
                            <div class="info-row">
                                <span class="info-label">Address</span>
                                <span class="info-value address-text"><?= htmlspecialchars($order['address_line'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($order['city'])): ?>
                            <div class="info-row">
                                <span class="info-label">City</span>
                                <span class="info-value"><?= htmlspecialchars($order['city'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($order['postal_code'])): ?>
                            <div class="info-row last-row">
                                <span class="info-label">Postal code</span>
                                <span class="info-value"><?= htmlspecialchars($order['postal_code'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </aside>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>