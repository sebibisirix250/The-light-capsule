<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

if (!isset($_GET['id'])) {
    header("Location: " . BASE_URL . "/admin/users.php?error=no_user_selected");
    exit();
}

$userId = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT id, full_name, email, phone FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: " . BASE_URL . "/admin/users.php?error=user_not_found");
    exit();
}

$orderStmt = $pdo->prepare("SELECT id, order_type, total_price, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderStmt->execute([$userId]);
$userOrders = $orderStmt->fetchAll();

$pageTitle = 'Edit user: ' . htmlspecialchars($user['full_name']);
$pageCss = ['style_admin_user.css'];

require_once __DIR__ . '/../includes/page_start.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <h2>Admin panel</h2>
        <ul>
            <li><a href="<?= BASE_URL ?>/admin/dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/admin/users.php" style="color: var(--primary);">👥 Users</a></li>
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
                <a href="<?= BASE_URL ?>/admin/orders.php" class="dropdown-trigger">📜 Orders</a>
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
        <h1>Edit Profile: <?= htmlspecialchars($user['full_name']) ?></h1>

        <div class="data-table-container" style="max-width: 600px; padding: 20px;">
            <?php if (isset($_GET['success'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-radius: 4px;">User updated successfully.</div>
            <?php endif; ?>

            <form action="../backend/handlers/admin_update_user.php" method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <input type="hidden" name="update_action" value="update_info">

                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight:bold;">Full name</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required
                        style="width:100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight:bold;">Email address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                        style="width:100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display:block; margin-bottom: 5px; font-weight:bold;">Phone number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                        style="width:100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="background: var(--primary); color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight:bold;">💾 Save changes</button>
                    <a href="users.php" style="padding: 10px 20px; text-decoration: none; background: #eee; color: #333; border-radius: 4px;">Cancel</a>
                </div>
            </form>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

            <h3>Order history</h3>
            <?php if (!$userOrders): ?>
                <p>No orders found for this user.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userOrders as $order): ?>
                            <tr>
                                <td data-label="ID">#<?= (int)$order['id'] ?></td>
                                <td data-label="Date"><?= date('Y-m-d', strtotime($order['created_at'])) ?></td>
                                <td data-label="Status"><?= htmlspecialchars($order['status']) ?></td>
                                <td data-label="Total">€<?= number_format((float)$order['total_price'], 2) ?></td>
                                <td style="text-align: right;"><a href="orders.php?search=<?= (int)$order['id'] ?>" style="color: var(--primary); text-decoration: none; font-weight: bold;">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

            <h3>Security</h3>
            <form action="../backend/handlers/admin_update_user.php" method="POST" onsubmit="return confirm('Send a password reset email to this user?')">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <input type="hidden" name="update_action" value="force_reset">
                <button type="submit" style="background: var(--warning); color: #333; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">📧 Send password reset email</button>
            </form>
        </div>
    </main>
</div>
</body>

</html>