<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Gallery item';
$pageCss = ['style_admin_gallery_edit.css'];

require_once __DIR__ . '/../includes/page_start.php';

$id = (int)($_GET['id'] ?? 0);

$item = null;
$meta = null;

if ($id > 0) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM items
        WHERE id = ?
        AND type = 'gallery'
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    $metaStmt = $pdo->prepare("
        SELECT *
        FROM gallery_metadata
        WHERE item_id = ?
        LIMIT 1
    ");
    $metaStmt->execute([$id]);
    $meta = $metaStmt->fetch();
}

$typeStmt = $pdo->query("
    SELECT id,name
    FROM gallery_types
    ORDER BY sort_order ASC,name ASC
");
$galleryTypes = $typeStmt->fetchAll();

$currentTypes = [];
if ($id > 0) {
    $typeLinkStmt = $pdo->prepare("
        SELECT type_id
        FROM gallery_item_types
        WHERE item_id = ?
    ");
    $typeLinkStmt->execute([$id]);
    $currentTypes = array_column($typeLinkStmt->fetchAll(), 'type_id');
}

$options = [];
if ($id > 0) {
    $optStmt = $pdo->prepare("
        SELECT *
        FROM item_options
        WHERE item_id = ?
        ORDER BY option_name, sort_order
    ");
    $optStmt->execute([$id]);
    $options = $optStmt->fetchAll();
}

if (!$options) {
    $options = [
        ['option_name' => 'file_format', 'option_value' => 'jpg', 'extra_price' => 0, 'sort_order' => 1],
        ['option_name' => 'file_format', 'option_value' => 'tif', 'extra_price' => 10, 'sort_order' => 2],
        ['option_name' => 'resolution_package', 'option_value' => 'web', 'extra_price' => 0, 'sort_order' => 1],
        ['option_name' => 'resolution_package', 'option_value' => 'print_high_res', 'extra_price' => 30, 'sort_order' => 2],
        ['option_name' => 'usage', 'option_value' => 'personal', 'extra_price' => 0, 'sort_order' => 1],
        ['option_name' => 'usage', 'option_value' => 'commercial', 'extra_price' => 50, 'sort_order' => 2]
    ];
}

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
            <h1><?= $item ? 'Edit gallery item' : 'Create gallery item' ?></h1>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_gallery_save.php" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="id" value="<?= $item['id'] ?? '' ?>">

            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($item['title'] ?? '') ?>" required>

            <label>Slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($item['slug'] ?? '') ?>" required>

            <label>Short description</label>
            <textarea name="short_description"><?= htmlspecialchars($item['short_description'] ?? '') ?></textarea>

            <label>Full description</label>
            <textarea name="full_description"><?= htmlspecialchars($item['full_description'] ?? '') ?></textarea>

            <label>Base price</label>
            <input type="number" step="0.01" name="price" value="<?= $item['price'] ?? '0.00' ?>" required>

            <label>Edit style</label>
            <select name="edit_style">
                <option value="">None</option>
                <option value="natural" <?= (($meta['edit_style'] ?? '') == 'natural') ? 'selected' : '' ?>>Natural</option>
                <option value="artistic" <?= (($meta['edit_style'] ?? '') == 'artistic') ? 'selected' : '' ?>>Artistic</option>
                <option value="cinematic" <?= (($meta['edit_style'] ?? '') == 'cinematic') ? 'selected' : '' ?>>Cinematic</option>
                <option value="documentary" <?= (($meta['edit_style'] ?? '') == 'documentary') ? 'selected' : '' ?>>Documentary</option>
            </select>

            <label>Capture location</label>
            <input type="text" name="capture_location" value="<?= htmlspecialchars($meta['capture_location'] ?? '') ?>">

            <label>Gallery types</label>
            <?php foreach ($galleryTypes as $type): ?>
                <label style="display:block">
                    <input type="checkbox" name="gallery_types[]" value="<?= $type['id'] ?>"
                        <?= in_array($type['id'], $currentTypes) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($type['name']) ?>
                </label>
            <?php endforeach; ?>

            <label>Printable</label>
            <select name="is_printable">
                <option value="1" <?= (($meta['is_printable'] ?? 1) == 1) ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= (($meta['is_printable'] ?? 1) == 0) ? 'selected' : '' ?>>No</option>
            </select>

            <label>Licensed</label>
            <select name="is_licensed">
                <option value="1" <?= (($meta['is_licensed'] ?? 1) == 1) ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= (($meta['is_licensed'] ?? 1) == 0) ? 'selected' : '' ?>>No</option>
            </select>

            <label>Downloadable</label>
            <select name="is_downloadable">
                <option value="1" <?= (($meta['is_downloadable'] ?? 1) == 1) ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= (($meta['is_downloadable'] ?? 1) == 0) ? 'selected' : '' ?>>No</option>
            </select>

            <h3>Purchase options</h3>
            <table id="optionsTable" class="admin-table">
                <tr>
                    <th>Option name</th>
                    <th>Value</th>
                    <th>Extra price</th>
                    <th>Sort</th>
                    <th>Remove</th>
                </tr>
                <?php foreach ($options as $opt): ?>
                    <tr>
                        <td><input type="text" name="option_name[]" value="<?= htmlspecialchars($opt['option_name']) ?>"></td>
                        <td><input type="text" name="option_value[]" value="<?= htmlspecialchars($opt['option_value']) ?>"></td>
                        <td><input type="number" step="0.01" name="option_price[]" value="<?= $opt['extra_price'] ?>"></td>
                        <td><input type="number" name="option_sort[]" value="<?= $opt['sort_order'] ?>"></td>
                        <td><button type="button" onclick="removeRow(this)">X</button></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="button" onclick="addOption()">Add option</button>

            <label>Image</label>
            <input type="file" name="image_file">

            <button type="submit">Save gallery item</button>
        </form>
    </main>
</div>

<script>
    function addOption() {
        let table = document.getElementById("optionsTable");
        let row = table.insertRow(-1);
        row.innerHTML = `
<td><input type="text" name="option_name[]"></td>
<td><input type="text" name="option_value[]"></td>
<td><input type="number" step="0.01" name="option_price[]" value="0"></td>
<td><input type="number" name="option_sort[]" value="0"></td>
<td><button type="button" onclick="removeRow(this)">X</button></td>`;
    }

    function removeRow(btn) {
        btn.closest("tr").remove();
    }
</script>


</body>

</html>