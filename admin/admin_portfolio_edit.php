<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

$projectId = forceIntRange($_GET['id'] ?? 0, 1);
$project = null;
$activeImages = [];
$archivedImages = [];

if ($projectId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ? AND is_active = 1");
    $stmt->execute([$projectId]);
    $project = $stmt->fetch();

    if ($project) {
        $imgStmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? AND is_active = 1 ORDER BY sort_order ASC");
        $imgStmt->execute([$projectId]);
        $activeImages = $imgStmt->fetchAll();

        $archStmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? AND is_active = 0 ORDER BY sort_order ASC");
        $archStmt->execute([$projectId]);
        $archivedImages = $archStmt->fetchAll();
    }
}

if (!$project) {
    setFlashMessage('error', 'Story not found.');
    header('Location: ' . BASE_URL . '/admin/admin_portfolio.php');
    exit;
}

$pageTitle = 'Edit story: ' . html_entity_decode($project['title'], ENT_QUOTES, 'UTF-8');
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
            <h1>Edit cinematic story</h1>
            <a href="<?= BASE_URL ?>/admin/admin_portfolio.php" style="color: #666; text-decoration: none;">← Back to portfolio</a>
        </div>

        <?php if ($flash = getFlashMessage()): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 5px; background: <?= $flash['type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $flash['type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= sanitizeHtml($flash['text']) ?>
            </div>
        <?php endif; ?>

        <div class="data-table-container portfolio-edit-container" style="max-width: 1000px;">
            <form id="portfolioEditForm" action="<?= BASE_URL ?>/backend/handlers/admin_portfolio_update.php" method="POST" enctype="multipart/form-data">
                <?= csrfField() ?>
                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                <input type="hidden" name="project_slug" value="<?= $project['slug'] ?>">
                <input type="hidden" name="image_order" id="imageOrderInput" value="">

                <div class="form-group">
                    <label>Event title</label>
                    <input type="text" name="title" class="form-control" value="<?= html_entity_decode($project['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="responsive-flex">
                    <div class="responsive-flex-item">
                        <label>Event date</label>
                        <input type="date" name="event_date" class="form-control" value="<?= sanitizeHtml($project['event_date']) ?>" required>
                    </div>
                    <div class="responsive-flex-item">
                        <label>Category</label>
                        <select name="category" class="form-control">
                            <option value="Wedding" <?= $project['category'] === 'Wedding' ? 'selected' : '' ?>>Wedding</option>
                            <option value="Automotive" <?= $project['category'] === 'Automotive' ? 'selected' : '' ?>>Automotive</option>
                            <option value="Portrait" <?= $project['category'] === 'Portrait' ? 'selected' : '' ?>>Portrait</option>
                            <option value="Documentary" <?= $project['category'] === 'Documentary' ? 'selected' : '' ?>>Documentary</option>
                        </select>
                    </div>
                    <div class="responsive-flex-color">
                        <label>Ambient glow</label>
                        <input type="color" name="theme_color" class="form-control color-picker" value="<?= sanitizeHtml($project['theme_color']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>The narrative</label>
                    <textarea name="narrative" rows="6" class="form-control" required><?= sanitizeHtml($project['narrative_text']) ?></textarea>
                </div>

                <div class="form-group section-divider">
                    <label>Append new images</label>
                    <input type="file" name="new_images[]" class="form-control" multiple accept="image/jpeg, image/png, image/webp">
                </div>

                <div class="form-group">
                    <label>Manage active gallery</label>

                    <div class="drag-grid" id="dragGrid">
                        <?php foreach ($activeImages as $img):
                            $thumbPath = str_replace('.webp', '_thumb.webp', $img['file_path']);
                            $displaySrc = BASE_URL . (file_exists(__DIR__ . '/..' . $thumbPath) ? $thumbPath : $img['file_path']);
                        ?>
                            <div class="draggable-item" draggable="true" data-id="<?= $img['id'] ?>">
                                <img src="<?= $displaySrc ?>" class="draggable-img">
                                <div class="draggable-controls">
                                    <label><input type="radio" name="new_cover_id" value="<?= $img['id'] ?>" <?= ($img['file_path'] === $project['cover_image']) ? 'checked' : '' ?>> Cover</label>
                                    <label class="delete-label"><input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>"> Archive</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($archivedImages)): ?>
                    <div class="form-group section-divider archived-section">
                        <label>Archived photos</label>
                        <div class="drag-grid archived-grid">
                            <?php foreach ($archivedImages as $img):
                                $thumbPath = str_replace('.webp', '_thumb.webp', $img['file_path']);
                                $displaySrc = BASE_URL . (file_exists(__DIR__ . '/..' . $thumbPath) ? $thumbPath : $img['file_path']);
                            ?>
                                <div class="archived-item">
                                    <img src="<?= $displaySrc ?>" class="draggable-img grayscale">
                                    <div class="draggable-controls">
                                        <label class="restore-label"><input type="checkbox" name="restore_images[]" value="<?= $img['id'] ?>"> Restore</label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-primary-block">💾 Save changes & update story</button>
            </form>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const grid = document.getElementById('dragGrid');
        const orderInput = document.getElementById('imageOrderInput');
        let draggedItem = null;

        const updateOrderInput = () => {
            const items = [...grid.querySelectorAll('.draggable-item')];
            orderInput.value = items.map(item => item.dataset.id).join(',');
        };

        grid.addEventListener('dragstart', (e) => {
            draggedItem = e.target.closest('.draggable-item');
            if (draggedItem) {
                e.dataTransfer.setData('text/plain', draggedItem.dataset.id);
                e.dataTransfer.effectAllowed = 'move';
                setTimeout(() => draggedItem.classList.add('dragging'), 0);
            }
        });

        grid.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';

            const target = e.target.closest('.draggable-item');
            if (target && target !== draggedItem) {
                const rect = target.getBoundingClientRect();
                const next = (e.clientY - rect.top) / (rect.bottom - rect.top) > 0.5;
                grid.insertBefore(draggedItem, next ? target.nextSibling : target);
            }
        });

        grid.addEventListener('dragend', () => {
            if (draggedItem) {
                draggedItem.classList.remove('dragging');
                draggedItem = null;
                updateOrderInput();
            }
        });

        updateOrderInput();
    });
</script>
</body>

</html>