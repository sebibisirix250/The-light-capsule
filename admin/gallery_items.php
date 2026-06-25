<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Gallery items';
$pageCss = ['style_admin_gallery.css'];

require_once __DIR__ . '/../includes/page_start.php';

$search = trim($_GET['search'] ?? '');
$typeId = (int)($_GET['type_id'] ?? 0);
$status = $_GET['status'] ?? '';

$typeStmt = $pdo->query("
    SELECT id, name
    FROM gallery_types
    ORDER BY sort_order ASC, name ASC
");
$galleryTypes = $typeStmt->fetchAll();

$where = ["items.type = 'gallery'"];
$params = [];

if ($search !== '') {
    $where[] = "(items.title LIKE ? OR items.slug LIKE ?)";
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($typeId > 0) {
    $where[] = "EXISTS (
        SELECT 1
        FROM gallery_item_types git_filter
        WHERE git_filter.item_id = items.id
        AND git_filter.type_id = ?
    )";
    $params[] = $typeId;
}

if ($status === 'active') {
    $where[] = "items.is_active = 1";
} elseif ($status === 'disabled') {
    $where[] = "items.is_active = 0";
}

$sql = "
    SELECT
        items.id,
        items.title,
        items.slug,
        items.price,
        items.is_active,
        items.created_at,
        gallery_metadata.thumb_image,
        GROUP_CONCAT(
            DISTINCT gallery_types.name
            ORDER BY gallery_types.sort_order ASC, gallery_types.name ASC
            SEPARATOR ', '
        ) AS type_names
    FROM items
    LEFT JOIN gallery_metadata
        ON gallery_metadata.item_id = items.id
    LEFT JOIN gallery_item_types
        ON gallery_item_types.item_id = items.id
    LEFT JOIN gallery_types
        ON gallery_types.id = gallery_item_types.type_id
    WHERE " . implode(' AND ', $where) . "
    GROUP BY
        items.id,
        items.title,
        items.slug,
        items.price,
        items.is_active,
        items.created_at,
        gallery_metadata.thumb_image
    ORDER BY items.created_at DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$galleryItems = $stmt->fetchAll();

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
            <h1>Gallery items</h1>
            <div class="filter-links">
                <a href="<?= BASE_URL ?>/admin/gallery_edit.php">Create gallery item</a>
                <a href="<?= BASE_URL ?>/admin/gallery_bulk_import.php">Bulk import</a>
            </div>
        </div>

        <form method="GET" action="<?= BASE_URL ?>/admin/gallery_items.php" class="search-filter-form">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Title or slug">

            <select name="type_id">
                <option value="0">All types</option>
                <?php foreach ($galleryTypes as $type): ?>
                    <option value="<?= (int)$type['id'] ?>" <?= $typeId === (int)$type['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">All status</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="disabled" <?= $status === 'disabled' ? 'selected' : '' ?>>Disabled</option>
            </select>

            <button type="submit">Filter</button>

            <?php if ($search !== '' || $typeId > 0 || $status !== ''): ?>
                <a href="<?= BASE_URL ?>/admin/gallery_items.php">Reset</a>
            <?php endif; ?>
        </form>

        <div class="data-table-container">
            <?php if (!$galleryItems): ?>
                <p>No gallery items found.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Types</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($galleryItems as $item):
                            $isActive = (int)$item['is_active'] === 1;
                        ?>
                            <tr class="<?= !$isActive ? 'item-disabled' : '' ?>">
                                <td data-label="ID"><?= (int)$item['id'] ?></td>

                                <td data-label="Preview">
                                    <?php if (!empty($item['thumb_image'])): ?>
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($item['thumb_image']) ?>">
                                    <?php endif; ?>
                                </td>

                                <td data-label="Title"><?= htmlspecialchars($item['title']) ?></td>

                                <td data-label="Slug"><?= htmlspecialchars($item['slug']) ?></td>

                                <td data-label="Types"><?= htmlspecialchars($item['type_names'] ?? '') ?></td>

                                <td data-label="Price">€<?= number_format((float)($item['price'] ?? 0), 2) ?></td>

                                <td data-label="Status" class="status-cell">
                                    <span class="status-label <?= $isActive ? 'label-active' : 'label-disabled' ?>">
                                        <?= $isActive ? 'ACTIVE' : 'DISABLED' ?>
                                    </span>
                                </td>

                                <td data-label="Created"><?= htmlspecialchars($item['created_at']) ?></td>

                                <td data-label="Actions">
                                    <div style="display: flex; gap: 15px; justify-content: flex-end;">
                                        <a href="<?= BASE_URL ?>/admin/gallery_edit.php?id=<?= (int)$item['id'] ?>">✏️</a>
                                        <a href="<?= BASE_URL ?>/backend/handlers/gallery_items_status.php?id=<?= $item['id'] ?>&action=<?= $isActive ? 'deactivate' : 'activate' ?>">
                                            <?= $isActive ? '🚫' : '🔄' ?>
                                        </a>
                                    </div>
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