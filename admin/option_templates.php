<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Option templates';
$pageCss = ['style_admin_option_templates.css'];

require_once __DIR__ . '/../includes/page_start.php';

$stmt = $pdo->query("
    SELECT id, name, item_type, is_active, created_at
    FROM option_templates
    ORDER BY item_type, name
");
$templates = $stmt->fetchAll();

?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <h2>Admin panel</h2>
        <ul>
            <li><a href="<?= BASE_URL ?>/admin/dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/admin/users.php">👥 Users</a></li>
            <li class="dropdown-container">
                <a href="<?= BASE_URL ?>/admin/gallery_items.php" class="dropdown-trigger" style="color: var(--primary);">🖼️ Gallery</a>
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
            <h1>Option templates</h1>
            <p>
                <a href="<?= BASE_URL ?>/admin/option_template_edit.php">Create template</a>
            </p>
        </div>

        <?php if (!$templates): ?>
            <p>No templates found.</p>
        <?php else: ?>
            <div class="data-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Item type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <td><?= (int)$template['id'] ?></td>
                                <td><?= htmlspecialchars($template['name']) ?></td>
                                <td><?= htmlspecialchars($template['item_type']) ?></td>
                                <td><?= (int)$template['is_active'] === 1 ? 'Active' : 'Disabled' ?></td>
                                <td><?= htmlspecialchars($template['created_at']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/option_template_edit.php?id=<?= (int)$template['id'] ?>">✏️</a>
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