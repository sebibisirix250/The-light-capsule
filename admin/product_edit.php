<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

$pageTitle = 'Product edit';
$pageCss = ['style_admin_products_edit.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/db.php';

$id = (int)($_GET['id'] ?? 0);
$product = null;
$selectedCategoryId = 0;
$galleryImages = [];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ? AND type = 'product' LIMIT 1");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $catLinkStmt = $pdo->prepare("SELECT category_id FROM item_categories WHERE item_id = ? LIMIT 1");
        $catLinkStmt->execute([$id]);
        $existingCategory = $catLinkStmt->fetch();
        if ($existingCategory) {
            $selectedCategoryId = (int)$existingCategory['category_id'];
        }

        $galStmt = $pdo->prepare("SELECT * FROM item_images WHERE item_id = ? ORDER BY display_order ASC");
        $galStmt->execute([$id]);
        $galleryImages = $galStmt->fetchAll();
    }
}

$catStmt = $pdo->prepare("SELECT id, name FROM categories WHERE type = 'product' AND is_active = 1 ORDER BY name");
$catStmt->execute();
$categories = $catStmt->fetchAll();

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
            <li><a href="<?= BASE_URL ?>/admin/products.php" style="color: var(--primary);">🛒 Products</a></li>
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
            <h1><?= $product ? 'Edit Product' : 'Create Product' ?></h1>
            <a href="<?= BASE_URL ?>/admin/products.php" class="btn-back">← Back to list</a>
        </div>

        <div class="form-card">
            <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_product_save.php" enctype="multipart/form-data" class="admin-form">

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= $product['id'] ?? '' ?>">

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="short_description">Short description</label>
                    <textarea id="short_description" name="short_description" class="form-control textarea-sm"><?= htmlspecialchars($product['short_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="full_description">Full description</label>
                    <textarea id="full_description" name="full_description" class="form-control textarea-lg"><?= htmlspecialchars($product['full_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-grid-3">
                    <div class="form-group">
                        <label for="price">Price (RON)</label>
                        <input type="number" id="price" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?? '0.00' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-control">
                            <option value="0">No category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" <?= $selectedCategoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_active">Status</label>
                        <select id="is_active" name="is_active" class="form-control">
                            <option value="1" <?= ($product['is_active'] ?? 1) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= isset($product['is_active']) && !$product['is_active'] ? 'selected' : '' ?>>Disabled</option>
                        </select>
                    </div>
                </div>

                <div class="settings-panel">
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label for="is_physical">Item type</label>
                            <select id="is_physical" name="is_physical" class="form-control">
                                <option value="0" <?= isset($product['is_physical']) && !$product['is_physical'] ? 'selected' : '' ?>>Digital download</option>
                                <option value="1" <?= isset($product['is_physical']) && $product['is_physical'] ? 'selected' : '' ?>>Physical good (shipping)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="stock_quantity">Stock quantity</label>
                            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?? '0' ?>" placeholder="Leave 0 for unlimited">
                        </div>
                        <div class="form-group">
                            <label for="is_limited_edition">Limited edition?</label>
                            <select id="is_limited_edition" name="is_limited_edition" class="form-control">
                                <option value="0" <?= isset($product['is_limited_edition']) && !$product['is_limited_edition'] ? 'selected' : '' ?>>No</option>
                                <option value="1" <?= isset($product['is_limited_edition']) && $product['is_limited_edition'] ? 'selected' : '' ?>>Yes, limited run</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="upload-zone primary-upload">
                    <div class="upload-header">
                        <label>Cover image</label>
                    </div>
                    <input type="file" name="cover_image" class="file-input" accept="image/*">
                    <?php if (!empty($product['cover_image'])): ?>
                        <div class="current-media">
                            <p class="media-label">Current cover:</p>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($product['cover_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Current cover" class="media-preview">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="upload-zone secondary-upload">
                    <div class="upload-header">
                        <label>Secondary gallery images</label>
                        <p class="upload-hint">Upload new images here.</p>
                    </div>
                    <input type="file" name="gallery_images[]" class="file-input" multiple accept="image/*">

                    <?php if (!empty($galleryImages)): ?>
                        <div class="current-media">
                            <p class="media-label" style="color: #2c7a71;">Interactive gallery:</p>

                            <div class="gallery-grid" id="gallery-grid">
                                <?php foreach ($galleryImages as $img): ?>
                                    <div class="gallery-item" draggable="true">
                                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Gallery image">
                                        <button type="button" class="btn-delete-img" title="Remove Image">✖</button>

                                        <input type="hidden" name="existing_gallery_id[]" value="<?= (int)$img['id'] ?>">
                                        <input type="hidden" name="existing_gallery_delete[]" value="0" class="delete-flag">
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        💾 Save product
                    </button>
                </div>

            </form>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const grid = document.getElementById('gallery-grid');

        if (grid) {
            grid.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-delete-img')) {
                    e.preventDefault();
                    const item = e.target.closest('.gallery-item');
                    item.style.display = 'none'; 
                    item.querySelector('.delete-flag').value = '1'; 
                }
            });

            let draggedItem = null;

            grid.addEventListener('dragstart', e => {
                if (e.target.closest('.gallery-item')) {
                    draggedItem = e.target.closest('.gallery-item');
                    setTimeout(() => draggedItem.classList.add('dragging'), 0);
                }
            });

            grid.addEventListener('dragend', e => {
                if (draggedItem) {
                    draggedItem.classList.remove('dragging');
                    draggedItem = null;
                }
            });

            grid.addEventListener('dragover', e => {
                e.preventDefault();
                const afterElement = getDragAfterElement(grid, e.clientX);
                const current = document.querySelector('.dragging');
                if (current) {
                    if (afterElement == null) {
                        grid.appendChild(current);
                    } else {
                        grid.insertBefore(current, afterElement);
                    }
                }
            });

            function getDragAfterElement(container, x) {
                const draggableElements = [...container.querySelectorAll('.gallery-item:not(.dragging)')];
                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = x - box.left - box.width / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return {
                            offset: offset,
                            element: child
                        };
                    } else {
                        return closest;
                    }
                }, {
                    offset: Number.NEGATIVE_INFINITY
                }).element;
            }
        }
    });
</script>

</body>

</html>