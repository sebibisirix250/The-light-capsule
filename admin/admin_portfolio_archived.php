<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

require_once __DIR__ . '/../backend/handlers/admin_portfolio_archived_data.php';

$pageTitle = 'Archived stories';
$pageCss = ['style_admin_dashboard.css'];

require_once __DIR__ . '/../includes/page_start.php';
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
            <li><a href="<?= BASE_URL ?>/admin/admin_portfolio.php" style="color: var(--primary);">🎬 Portfolio</a></li>
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
            <h1>Archived stories</h1>
            <a href="<?= BASE_URL ?>/admin/admin_portfolio.php" style="background: #666; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">← Back to active portfolio</a>
        </div>

        <?php if ($flash = getFlashMessage()): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background: <?= $flash['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $flash['type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= sanitizeHtml($flash['text']) ?>
            </div>
        <?php endif; ?>

        <div class="data-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Archived date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($archivedProjects)): ?>
                        <?php foreach ($archivedProjects as $project): ?>
                            <tr style="opacity: 0.8; background: #fafafa;">
                                <td>
                                    <?php if (!empty($project['cover_image']) && $project['cover_image'] !== 'pending'): ?>
                                        <img src="<?= BASE_URL . sanitizeHtml($project['cover_image']) ?>" alt="Cover" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; filter: grayscale(100%);">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 40px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">No IMG</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= sanitizeHtml($project['title']) ?></strong><br><small><?= sanitizeHtml($project['slug']) ?></small></td>
                                <td><?= sanitizeHtml($project['category']) ?></td>
                                <td><?= date('M d, Y', strtotime($project['event_date'])) ?></td>
                                <td>
                                    <form action="<?= BASE_URL ?>/backend/handlers/admin_portfolio_restore.php" method="POST" style="display:inline;" onsubmit="return confirm('Restore this story to the live portfolio?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--success); cursor: pointer; font-size: 1rem; font-weight: bold;">♻️ Restore</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #888;">No archived stories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>

</html>