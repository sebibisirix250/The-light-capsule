<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

$pageTitle = 'Categories';
$pageCss = ['style_admin_categories.css'];

require_once __DIR__ . '/../includes/page_start.php';


$stmt = $pdo->prepare("
    SELECT id, name, slug, type, is_active
    FROM categories
    ORDER BY type ASC, name ASC
");
$stmt->execute();
$categories = $stmt->fetchAll();


$logDir = __DIR__ . '/../storage/emails';
$unreadCount = 0;
if (is_dir($logDir)) {
    foreach (scandir($logDir) as $file) {
        if ($file !== '.' && $file !== '..' && !str_starts_with($file, 'read_')) {
            $unreadCount++;
        }
    }
}
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
            <li><a href="<?= BASE_URL ?>/admin/categories.php" style="color: var(--primary);">📁 Categories</a></li>
            <li>
                <hr style="border:0; border-top:1px solid #444;">
            </li>
            <li><a href="<?= BASE_URL ?>" style="color:var(--danger)">⬅️ Exit</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <div class="page-header">
            <h1>Categories</h1>
            <a href="<?= BASE_URL ?>/admin/category_edit.php" class="btn-create" style="background:var(--primary); color:white; padding:10px 20px; border-radius:5px; text-decoration:none; font-weight:600;">+ Create category</a>
        </div>

        <div class="data-table-container">
            <?php if (!$categories): ?>
                <p style="padding: 20px;">No categories found.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr class="<?= (int)$category['is_active'] !== 1 ? 'item-disabled' : '' ?>">
                                <td data-label="ID">#<?= (int)$category['id'] ?></td>
                                <td data-label="Name"><strong><?= htmlspecialchars($category['name']) ?></strong></td>
                                <td data-label="Slug"><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                <td data-label="Type">
                                    <span style="font-size: 0.75rem; background: #eee; padding: 2px 8px; border-radius: 10px; text-transform: uppercase;">
                                        <?= htmlspecialchars($category['type']) ?>
                                    </span>
                                </td>
                                <td data-label="Status">
                                    <?php if ((int)$category['is_active'] === 1): ?>
                                        <span class="label-active">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="label-disabled">DISABLED</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Actions" style="text-align: right;">
                                    <a href="<?= BASE_URL ?>/admin/category_edit.php?id=<?= (int)$category['id'] ?>" title="Edit">
                                        📝
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>

</body>

</html>