<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Orders';
$pageCss = ['Style_admin_orders.css'];

require_once __DIR__ . '/../includes/page_start.php';

$search = trim($_GET['search'] ?? '');
$orderType = trim($_GET['order_type'] ?? '');
$status = trim($_GET['status'] ?? '');
$paymentStatus = trim($_GET['payment_status'] ?? '');

$allowedOrderTypes = ['digital', 'service', 'mixed'];
$allowedStatuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
$allowedPaymentStatuses = ['unpaid', 'awaiting_manual_confirmation', 'paid', 'not_required'];

$where = [];
$params = [];

if ($search !== '') {
    if (preg_match('/^\d+$/', $search)) {
        $where[] = "(orders.id = ? OR users.full_name LIKE ? OR users.email LIKE ?)";
        $params[] = (int)$search;
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    } else {
        $where[] = "(users.full_name LIKE ? OR users.email LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }
}

if (in_array($orderType, $allowedOrderTypes, true)) {
    $where[] = "orders.order_type = ?";
    $params[] = $orderType;
}

if (in_array($status, $allowedStatuses, true)) {
    $where[] = "orders.status = ?";
    $params[] = $status;
}

if (in_array($paymentStatus, $allowedPaymentStatuses, true)) {
    $where[] = "orders.payment_status = ?";
    $params[] = $paymentStatus;
}

$sql = "
    SELECT
        orders.id,
        orders.order_type,
        orders.total_price,
        orders.status,
        orders.payment_status,
        orders.created_at,
        users.full_name,
        users.email,
        pt.method_key AS latest_payment_method,
        pt.status AS latest_payment_transaction_status,
        pt.internal_reference AS latest_payment_reference
    FROM orders
    JOIN users ON users.id = orders.user_id
    LEFT JOIN payment_transactions pt
        ON pt.id = (
            SELECT pt2.id
            FROM payment_transactions pt2
            WHERE pt2.order_id = orders.id
            ORDER BY pt2.id DESC
            LIMIT 1
        )
";

if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}

$sql .= " ORDER BY orders.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$orders = $stmt->fetchAll();
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <h2>Admin panel</h2>
        <ul>
            <li><a href="<?= BASE_URL ?>/admin/dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/admin/users.php">👥 Users</a></li>
            <li class="dropdown-container">
                <a href="<?= BASE_URL ?>/admin/gallery_items.php" class="dropdown-trigger">🖼️ Gallery</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= BASE_URL ?>/admin/gallery_edit.php">📤 Upload</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/gallery_bulk_import.php">📦 Bulk upload</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/option_templates.php">📄 Template</a></li>
                </ul>
            </li>

            <li><a href="<?= BASE_URL ?>/admin/admin_portfolio.php">🎬 Portfolio</a></li>

            <li class="dropdown-container">
                <a href="<?= BASE_URL ?>/admin/orders.php" class="dropdown-trigger" style="color: var(--primary);">📜 Orders</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= BASE_URL ?>/admin/orders.php?search=&order_type=&status=pending&payment_status=">⏳ Pending</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/orders.php?search=&order_type=&status=&payment_status=awaiting_manual_confirmation">💳 Manual confirmation</a></li>
                </ul>
            </li>
            <li><a href="<?= BASE_URL ?>/admin/products.php">🛒 Products</a></li>
            <li>
                <a href="<?= BASE_URL ?>/admin/email_logs.php" style="position: relative;">
                    📧 Emails
                    <?php if (($stats['unread_emails'] ?? 0) > 0): ?>
                        <span class="notification-dot"></span>
                    <?php endif; ?>
                </a>
            </li>
            <li><a href="<?= BASE_URL ?>/admin/categories.php">📁 Categories</a></li>
            <li>
                <hr style="border:0; border-top:1px solid #444;">
            </li>
            <li><a href="<?= BASE_URL ?>" style="color:var(--danger)">⬅️ Exit</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <div class="page-header">
            <h1>Orders</h1>
        </div>

        <section class="filter-section">
            <form method="GET" action="<?= BASE_URL ?>/admin/orders.php" class="admin-filter-form">
                <div class="filter-group">
                    <label for="search">Search</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        value="<?= htmlspecialchars($search) ?>"
                        placeholder="ID, customer, email">
                </div>

                <div class="filter-group">
                    <label for="order_type">Type</label>
                    <select name="order_type" id="order_type">
                        <option value="">All</option>
                        <option value="digital" <?= $orderType === 'digital' ? 'selected' : '' ?>>Digital</option>
                        <option value="service" <?= $orderType === 'service' ? 'selected' : '' ?>>Service</option>
                        <option value="mixed" <?= $orderType === 'mixed' ? 'selected' : '' ?>>Mixed</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="">All</option>
                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="payment_status">Payment</label>
                    <select name="payment_status" id="payment_status">
                        <option value="">All</option>
                        <option value="unpaid" <?= $paymentStatus === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                        <option value="awaiting_manual_confirmation" <?= $paymentStatus === 'awaiting_manual_confirmation' ? 'selected' : '' ?>>Awaiting confirmation</option>
                        <option value="paid" <?= $paymentStatus === 'paid' ? 'selected' : '' ?>>Paid</option>
                        <option value="not_required" <?= $paymentStatus === 'not_required' ? 'selected' : '' ?>>Not required</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter">Filter</button>
                    <?php if ($search !== '' || $orderType !== '' || $status !== '' || $paymentStatus !== ''): ?>
                        <a href="<?= BASE_URL ?>/admin/orders.php" class="btn-reset">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <?php if (!$orders): ?>
            <div class="empty-state">
                <p>No orders found matching your criteria.</p>
            </div>
        <?php else: ?>
            <div class="data-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Method</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td data-label="ID" style="font-weight:bold;">#<?= (int)$order['id'] ?></td>
                                <td data-label="Customer">
                                    <strong><?= htmlspecialchars($order['full_name']) ?></strong><br>
                                    <small style="color:#666;"><?= htmlspecialchars($order['email']) ?></small>
                                </td>
                                <td data-label="Type"><span class="badge type-<?= $order['order_type'] ?>"><?= htmlspecialchars($order['order_type']) ?></span></td>
                                <td data-label="Status"><span class="status-dot dot-<?= $order['status'] ?>"></span> <?= htmlspecialchars($order['status']) ?></td>
                                <td data-label="Payment"><?= htmlspecialchars($order['payment_status']) ?></td>
                                <td data-label="Method">
                                    <?= htmlspecialchars($order['latest_payment_method'] ?? '-') ?><br>
                                    <small><?= htmlspecialchars($order['latest_payment_reference'] ?? '') ?></small>
                                </td>
                                <td data-label="Total" style="font-weight:bold;">€<?= number_format((float)($order['total_price'] ?? 0), 2) ?></td>
                                <td data-label="Date"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                <td data-label="Actions" class="action-cell">
                                    <a href="<?= BASE_URL ?>/admin/order_view.php?id=<?= (int)$order['id'] ?>" title="View Order">
                                        👁️
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>

</html>