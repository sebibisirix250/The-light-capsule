<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

$pageTitle = 'Order details';
$pageCss = ['style_admin_orders_view.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/db.php';

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    echo "<main><p>Invalid order.</p></main>";
    exit;
}

/* ORDER */
$stmt = $pdo->prepare("
    SELECT o.*, u.full_name AS user_name, u.email AS user_email
    FROM orders o
    LEFT JOIN users u ON u.id = o.user_id
    WHERE o.id = ?
    LIMIT 1
");
$stmt->execute([$orderId]);
$order = $stmt->fetch();

if (!$order) {
    echo "<main><p>Order not found.</p></main>";
    exit;
}

/* ITEMS */
$stmt = $pdo->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll();

/* PAYMENT */
$stmt = $pdo->prepare("
    SELECT *
    FROM payment_transactions
    WHERE order_id = ?
    ORDER BY id DESC
");
$stmt->execute([$orderId]);
$payments = $stmt->fetchAll();

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

        <?php if (isset($_GET['success'])): ?>
            <div class="update-toast" id="updateToast">
                <span class="toast-icon">✅</span>
                <div class="toast-content">
                    <strong>Order updated</strong>
                    <p>Changes to order #<?= (int)$order['id'] ?> have been saved.</p>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('updateToast');
                    if (toast) {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(-20px)';
                        setTimeout(() => toast.remove(), 500);
                    }
                }, 4000);
            </script>
        <?php endif; ?>

        <div class="page-header">
            <h1>Order #<?= (int)$order['id'] ?></h1>
            <a href="<?= BASE_URL ?>/admin/orders.php" class="btn-reset">⬅️ Back to orders</a>
        </div>

        <div class="order-details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
            <section class="filter-section">
                <h3>👤 Customer</h3>
                <p style="margin-top:15px;"><strong>Name:</strong> <?= htmlspecialchars($order['contact_name'] ?? $order['user_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order['contact_email'] ?? $order['user_email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($order['contact_phone'] ?? '-') ?></p>
            </section>

            <section class="filter-section">
                <h3>⚙️ Management</h3>
                <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_order_update.php" class="admin-filter-form" style="display:block;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">

                    <div class="filter-group" style="margin-top:15px;">
                        <label>Order status</label>
                        <select name="status">
                            <?php
                            $statuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
                            foreach ($statuses as $s):
                            ?>
                                <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                    <?= ucfirst($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group" style="margin-top:10px;">
                        <label>Payment status</label>
                        <select name="payment_status">
                            <?php
                            $paymentsStatus = ['unpaid', 'awaiting_manual_confirmation', 'paid', 'not_required'];
                            foreach ($paymentsStatus as $ps):
                            ?>
                                <option value="<?= $ps ?>" <?= $order['payment_status'] === $ps ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $ps)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-filter" style="width:100%; margin-top:15px;">Update order</button>
                </form>
            </section>
        </div>

        <h3 style="margin-bottom:15px;">📦 Items</h3>
        <div class="data-table-container" style="margin-bottom: 40px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Options</th>
                        <th>Qty</th>
                        <th>Unit</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="font-weight:600;"><?= htmlspecialchars($item['item_title']) ?></td>
                            <td><span class="badge type-<?= $item['item_type'] ?>"><?= htmlspecialchars($item['item_type']) ?></span></td>
                            <td style="font-size: 0.85rem; color: #666;"><?= htmlspecialchars($item['selected_options'] ?? '-') ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td>€<?= number_format($item['unit_price'], 2) ?></td>
                            <td style="font-weight:bold;">€<?= number_format($item['line_total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h3 style="margin-bottom:15px;">💳 Payment transactions</h3>
        <?php if (!$payments): ?>
            <div class="empty-state" style="background:#fff; padding:20px; border-radius:8px; margin-bottom:40px;">
                <p>No payment records found.</p>
            </div>
        <?php else: ?>
            <div class="data-table-container" style="margin-bottom: 40px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td>#<?= (int)$p['id'] ?></td>
                                <td><strong><?= htmlspecialchars($p['method_key']) ?></strong><br><small><?= htmlspecialchars($p['provider']) ?></small></td>
                                <td><?= htmlspecialchars($p['status']) ?></td>
                                <td style="font-weight:bold;">€<?= number_format((float)$p['amount'], 2) ?></td>
                                <td><small><?= htmlspecialchars($p['internal_reference']) ?></small></td>
                                <td><?= htmlspecialchars($p['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <section class="filter-section" style="max-width: 400px; margin-left: auto;">
            <h3 style="border-bottom: 1px solid #eee; padding-bottom:10px;">Summary</h3>
            <div style="display:flex; justify-content: space-between; margin-top:15px;">
                <span>Order Type:</span>
                <span class="badge type-<?= $order['order_type'] ?>"><?= htmlspecialchars($order['order_type']) ?></span>
            </div>
            <div style="display:flex; justify-content: space-between; margin-top:10px;">
                <span>Created:</span>
                <span><?= date('d M Y H:i', strtotime($order['created_at'])) ?></span>
            </div>
            <div style="display:flex; justify-content: space-between; margin-top:15px; font-size:1.2rem; font-weight:bold; color:var(--primary);">
                <span>Total:</span>
                <span>€<?= number_format((float)$order['total_price'], 2) ?></span>
            </div>
        </section>
    </main>
</div>

</body>

</html>