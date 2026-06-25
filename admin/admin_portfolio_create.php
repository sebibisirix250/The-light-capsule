<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php'; 

$pageTitle = 'Create portfolio story';
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
            <h1>Create cinematic story</h1>
            <a href="<?= BASE_URL ?>/admin/admin_portfolio.php" style="color: #666; text-decoration: none;">← Back to portfolio</a>
        </div>

        <?php if ($flash = getFlashMessage()): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background: <?= $flash['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $flash['type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= sanitizeHtml($flash['text']) ?>
            </div>
        <?php endif; ?>

        <div class="data-table-container" style="max-width: 900px;">
            <form action="<?= BASE_URL ?>/backend/handlers/admin_portfolio_upload.php" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Event title</label>
                    <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="event name">
                </div>

                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: bold; margin-bottom: 8px;">Event date</label>
                        <input type="date" name="event_date" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-weight: bold; margin-bottom: 8px;">Category</label>
                        <select name="category" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="Wedding">Wedding</option>
                            <option value="Automotive">Automotive</option>
                            <option value="Portrait">Portrait</option>
                            <option value="Documentary">Documentary</option>
                        </select>
                    </div>
                    <div style="flex: 0.5;">
                        <label style="display: block; font-weight: bold; margin-bottom: 8px;">Ambient glow</label>
                        <input type="color" name="theme_color" value="#d4af37" style="width: 100%; height: 42px; border: 1px solid #ccc; border-radius: 4px; padding: 2px; cursor: pointer;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">The narrative</label>
                    <textarea name="narrative" rows="8" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; line-height: 1.6;" placeholder="Editorial story of this event."></textarea>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: bold; margin-bottom: 8px;">Gallery images</label>
                    <div style="border: 2px dashed #ccc; padding: 40px; text-align: center; border-radius: 8px; background: #fafafa;">
                        <input type="file" name="gallery_images[]" multiple accept="image/jpeg, image/png, image/webp" required style="width: 100%; cursor: pointer;">
                        <p style="color: #666; font-size: 14px; margin-top: 15px;">
                            Select images. 
                        </p>
                    </div>
                </div>

                <button type="submit" style="background: var(--primary); color: white; border: none; padding: 15px 30px; font-size: 16px; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%;">
                    Build & publish
                </button>
            </form>
        </div>
    </main>
</div>

</body>

</html>