<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';

require_once __DIR__ . '/../backend/handlers/admin_portfolio_data.php';

$pageTitle = 'Portfolio management';
$pageCss = ['style_admin_dashboard.css'];

require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
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
            <h1>Portfolio stories</h1>
            <div style="display: flex; gap: 15px;">
                <a href="<?= BASE_URL ?>/admin/admin_portfolio_archived.php" style="background: #e0e0e0; color: #333; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.2s;">📁 View archive</a>

                <a href="<?= BASE_URL ?>/admin/admin_portfolio_create.php" style="background: var(--primary); color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">➕ Create new story</a>
            </div>
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
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($portfolioProjects)): ?>
                        <?php foreach ($portfolioProjects as $project): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($project['cover_image']) && $project['cover_image'] !== 'pending'): ?>
                                        <img src="<?= BASE_URL . sanitizeHtml($project['cover_image']) ?>" alt="Cover" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 40px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #999;">No IMG</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= html_entity_decode($project['title'], ENT_QUOTES, 'UTF-8') ?></strong><br><small><?= $project['slug'] ?></small></td>
                                <td><?= sanitizeHtml($project['category']) ?></td>
                                <td><?= date('M d, Y', strtotime($project['event_date'])) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/admin_portfolio_edit.php?id=<?= $project['id'] ?>" style="color: var(--primary); margin-right: 10px; text-decoration: none;">✏️ Edit</a>

                                    <form action="<?= BASE_URL ?>/backend/handlers/admin_portfolio_delete.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to archive this story?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                        <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 1rem;">🗑️ Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #888;">No cinematic stories found. Click "Create new story" to begin.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>

</html>