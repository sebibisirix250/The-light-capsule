<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../backend/handlers/admin_users_data.php';

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'all';

$stats = getAdminstats($pdo);
$users = getUsers($pdo, $search, $filter);

$pageTitle = 'User management';
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
        <div class="page-header">
            <h1>User management</h1>
            <form action="" method="GET" class="search-filter-form">
                <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>"
                    style="padding: 8px 12px; border-radius: 5px; border: 1px solid #ddd;">
                <select name="filter" onchange="this.form.submit()" style="padding: 8px; border-radius: 5px;">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All users</option>
                    <option value="active" <?= $filter === 'active' ? 'selected' : '' ?>>Active only</option>
                    <option value="inactive" <?= $filter === 'inactive' ? 'selected' : '' ?>>Inactive only</option>
                </select>
            </form>
        </div>

        <div class="data-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Account</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No users found.</td>
                        </tr>
                        <?php else: foreach ($users as $user): ?>
                            <tr style="<?= !$user['is_active'] ? 'opacity: 0.6;' : '' ?>">
                                <td data-label="Status">
                                    <span title="<?= $user['is_online'] ? 'Online' : 'Offline' ?>"
                                        style="height: 10px; width: 10px; background-color: <?= $user['is_online'] ? 'var(--success)' : '#ccc' ?>; border-radius: 50%; display: inline-block;">
                                    </span>
                                </td>
                                <td data-label="Name"><strong><?= htmlspecialchars($user['full_name']) ?></strong></td>
                                <td data-label="Email"><?= htmlspecialchars($user['email']) ?></td>
                                <td data-label="Joined"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                <td data-label="Account" style="color: <?= $user['is_active'] ? 'var(--success)' : 'var(--danger)' ?>; font-weight: bold;">
                                    <?= $user['is_active'] ? 'ACTIVE' : 'INACTIVE' ?>
                                </td>
                                <td data-label="Actions" style="text-align: right;">
                                    <div style="display: flex; gap: 15px; justify-content: flex-end;">
                                        <a href="edit_user.php?id=<?= $user['id'] ?>" title="Edit User">✏️</a>
                                        <a href="../backend/handlers/user_status.php?id=<?= $user['id'] ?>&action=<?= $user['is_active'] ? 'deactivate' : 'activate' ?>">
                                            <?= $user['is_active'] ? '🚫' : '🔄' ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>

</html>