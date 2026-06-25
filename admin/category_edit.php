<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';

$logDir = __DIR__ . '/../storage/emails';
$unreadCount = 0;
if (is_dir($logDir)) {
    foreach (scandir($logDir) as $file) {
        if ($file !== '.' && $file !== '..' && !str_starts_with($file, 'read_')) {
            $unreadCount++;
        }
    }
}

$pageTitle = 'Category edit';
$pageCss = ['style_admin_categories.css'];

require_once __DIR__ . '/../includes/page_start.php';

$id = (int)($_GET['id'] ?? 0);
$category = null;

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
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
            <h1><?= $category ? 'Edit Category' : 'Create Category' ?></h1>
            <a href="<?= BASE_URL ?>/admin/categories.php" style="color:var(--text-dark); text-decoration:none; font-weight:600;">⬅ Back to list</a>
        </div>

        <div class="search-filter-form">
            <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_category_save.php" style="width: 100%; display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string)($category['id'] ?? '')) ?>">

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="name" style="font-weight: bold;">Name</label>
                    <input type="text" name="name" id="name" value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="slug" style="font-weight: bold;">Slug</label>
                    <input type="text" name="slug" id="slug" value="<?= htmlspecialchars($category['slug'] ?? '') ?>" required>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="type" style="font-weight: bold;">Type</label>
                    <select name="type" id="type">
                        <option value="product" <?= (($category['type'] ?? '') === 'product') ? 'selected' : '' ?>>Product</option>
                        <option value="gallery" <?= (($category['type'] ?? '') === 'gallery') ? 'selected' : '' ?>>Gallery</option>
                        <option value="service" <?= (($category['type'] ?? '') === 'service') ? 'selected' : '' ?>>Service</option>
                    </select>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <label for="is_active" style="font-weight: bold;">Status</label>
                    <select name="is_active" id="is_active">
                        <option value="1" <?= ((int)($category['is_active'] ?? 1) === 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (isset($category['is_active']) && (int)$category['is_active'] === 0) ? 'selected' : '' ?>>Disabled</option>
                    </select>
                </div>

                <div style="padding-top: 10px;">
                    <button type="submit" style="background:var(--primary); color:white; padding:12px 25px; border:none; border-radius:5px; cursor:pointer; font-weight:600;">Save category</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>

</html>