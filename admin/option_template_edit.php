<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Option template';
$pageCss = ['style_admin_option_template_edit.css'];

require_once __DIR__ . '/../includes/page_start.php';

$id = (int)($_GET['id'] ?? 0);
$template = null;
$rows = [];

if ($id > 0) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM option_templates
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $template = $stmt->fetch();

    if ($template) {
        $rowStmt = $pdo->prepare("
            SELECT option_name, option_value, extra_price, sort_order
            FROM option_template_items
            WHERE template_id = ?
            AND is_active = 1
            ORDER BY option_name, sort_order
        ");
        $rowStmt->execute([$id]);
        $rows = $rowStmt->fetchAll();
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
            <h1><?= $template ? 'Edit template' : 'Create template' ?></h1>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_option_template_save.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="hidden" name="id" value="<?= $template['id'] ?? '' ?>">

            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($template['name'] ?? '') ?>" required>

            <label>Item type</label>
            <select name="item_type">
                <option value="gallery" <?= (($template['item_type'] ?? 'gallery') === 'gallery') ? 'selected' : '' ?>>Gallery</option>
                <option value="digital_product" <?= (($template['item_type'] ?? '') === 'digital_product') ? 'selected' : '' ?>>Digital product</option>
                <option value="service" <?= (($template['item_type'] ?? '') === 'service') ? 'selected' : '' ?>>Service</option>
            </select>

            <label>Active</label>
            <select name="is_active">
                <option value="1" <?= ((int)($template['is_active'] ?? 1) === 1) ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= ((int)($template['is_active'] ?? 1) === 0) ? 'selected' : '' ?>>Disabled</option>
            </select>

            <h3>Template rows</h3>

            <div class="data-table-container">
                <table class="admin-table" id="templateRowsTable">
                    <thead>
                        <tr>
                            <th>Option name</th>
                            <th>Option value</th>
                            <th>Extra price</th>
                            <th>Sort</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($rows): ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><input type="text" name="option_name[]" value="<?= htmlspecialchars($row['option_name']) ?>"></td>
                                    <td><input type="text" name="option_value[]" value="<?= htmlspecialchars($row['option_value']) ?>"></td>
                                    <td><input type="number" step="0.01" name="option_price[]" value="<?= htmlspecialchars($row['extra_price']) ?>"></td>
                                    <td><input type="number" name="option_sort[]" value="<?= (int)$row['sort_order'] ?>"></td>
                                    <td><button type="button" onclick="removeRow(this)">X</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td><input type="text" name="option_name[]" value="file_format"></td>
                                <td><input type="text" name="option_value[]" value="jpg"></td>
                                <td><input type="number" step="0.01" name="option_price[]" value="0"></td>
                                <td><input type="number" name="option_sort[]" value="1"></td>
                                <td><button type="button" onclick="removeRow(this)">X</button></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="option_name[]" value="file_format"></td>
                                <td><input type="text" name="option_value[]" value="tif"></td>
                                <td><input type="number" step="0.01" name="option_price[]" value="10"></td>
                                <td><input type="number" name="option_sort[]" value="2"></td>
                                <td><button type="button" onclick="removeRow(this)">X</button></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="option_name[]" value="resolution_package"></td>
                                <td><input type="text" name="option_value[]" value="web"></td>
                                <td><input type="number" step="0.01" name="option_price[]" value="0"></td>
                                <td><input type="number" name="option_sort[]" value="1"></td>
                                <td><button type="button" onclick="removeRow(this)">X</button></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="option_name[]" value="resolution_package"></td>
                                <td><input type="text" name="option_value[]" value="print_high_res"></td>
                                <td><input type="number" step="0.01" name="option_price[]" value="30"></td>
                                <td><input type="number" name="option_sort[]" value="2"></td>
                                <td><button type="button" onclick="removeRow(this)">X</button></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="option_name[]" value="usage"></td>
                                <td><input type="text" name="option_value[]" value="personal"></td>
                                <td><input type="number" step="0.01" name="option_price[]" value="0"></td>
                                <td><input type="number" name="option_sort[]" value="1"></td>
                                <td><button type="button" onclick="removeRow(this)">X</button></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="option_name[]" value="usage"></td>
                                <td><input type="text" name="option_value[]" value="commercial"></td>
                                <td><input type="number" step="0.01" name="option_price[]" value="50"></td>
                                <td><input type="number" name="option_sort[]" value="2"></td>
                                <td><button type="button" onclick="removeRow(this)">X</button></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <button type="button" onclick="addRow()">Add row</button>
            <br><br>
            <button type="submit">Save template</button>
        </form>
    </main>
</div>

<script>
    function addRow() {
        const table = document.getElementById('templateRowsTable').getElementsByTagName('tbody')[0];
        const row = table.insertRow(-1);
        row.innerHTML = `
            <td><input type="text" name="option_name[]"></td>
            <td><input type="text" name="option_value[]"></td>
            <td><input type="number" step="0.01" name="option_price[]" value="0"></td>
            <td><input type="number" name="option_sort[]" value="0"></td>
            <td><button type="button" onclick="removeRow(this)">X</button></td>
        `;
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
    }
</script>

</body>

</html>