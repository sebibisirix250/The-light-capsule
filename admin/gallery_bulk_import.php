<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Bulk gallery import';
$pageCss = ['style_admin_gallery_bulk.css'];

require_once __DIR__ . '/../includes/page_start.php';

$typeStmt = $pdo->query("
    SELECT id,name
    FROM gallery_types
    ORDER BY sort_order,name
");
$galleryTypes = $typeStmt->fetchAll();

$templateStmt = $pdo->query("
    SELECT id, name
    FROM option_templates
    WHERE item_type = 'gallery'
    AND is_active = 1
    ORDER BY name
");
$templates = $templateStmt->fetchAll();

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
            <h1>Bulk import gallery images</h1>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_gallery_bulk_import.php" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

            <label>Select images</label>
            <input type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/webp">

            <h3>Shared metadata</h3>

            <label>Base price</label>
            <input type="number" step="0.01" name="price" value="40.00">

            <label>Edit style</label>
            <select name="edit_style">
                <option value="">None</option>
                <option value="natural">Natural</option>
                <option value="artistic">Artistic</option>
                <option value="cinematic">Cinematic</option>
                <option value="documentary">Documentary</option>
            </select>

            <label>Capture location</label>
            <input type="text" name="capture_location">

            <label>Gallery Types</label>
            <?php foreach ($galleryTypes as $type): ?>
                <label style="display:block">
                    <input type="checkbox" name="gallery_types[]" value="<?= $type['id'] ?>">
                    <?= htmlspecialchars($type['name']) ?>
                </label>
            <?php endforeach; ?>

            <h3>Licensing</h3>
            <label style="display:block">
                <input type="checkbox" name="is_printable" value="1" checked> Printable
            </label>
            <label style="display:block">
                <input type="checkbox" name="is_licensed" value="1" checked> Licensed
            </label>
            <label style="display:block">
                <input type="checkbox" name="is_downloadable" value="1" checked> Downloadable
            </label>

            <label>Option Template</label>
            <select name="template_id">
                <option value="0">No template</option>
                <?php foreach ($templates as $template): ?>
                    <option value="<?= (int)$template['id'] ?>"><?= htmlspecialchars($template['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <br><br>
            <button type="submit">Import images</button>
        </form>
    </main>
</div>


</body>

</html>