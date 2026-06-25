<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

$pageTitle = 'Products';
$pageCss = ['style_admin_products.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/db.php';

$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? 'all';

$query = "SELECT id, title, price, is_active, is_physical, created_at FROM items WHERE type = 'product'";
$params = [];

if (!empty($search)) {
    $query .= " AND title LIKE ?";
    $params[] = "%$search%";
}

if ($status_filter === 'active') {
    $query .= " AND is_active = 1";
} elseif ($status_filter === 'disabled') {
    $query .= " AND is_active = 0";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$totalProducts = count($products);
$activeCount = 0;
$totalValue = 0;
foreach ($products as $p) {
    if ($p['is_active']) $activeCount++;
    $totalValue += (float)$p['price'];
}

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
            <div class="header-titles">
                <h1>Shop inventory</h1>
                <p class="header-subtitle">Managing <strong><?= $totalProducts ?></strong> items</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/product_edit.php" class="btn-create">+ New product</a>
        </div>

        <?php if (isset($_SESSION['flash_message']) && !empty($_SESSION['flash_message'])): ?>
            <?php
            $rawMsg = $_SESSION['flash_message'];
            $displayMsg = is_array($rawMsg) ? implode('<br>', $rawMsg) : $rawMsg;
            ?>
            <div style="padding: 15px 20px; margin-bottom: 25px; border-radius: 8px; font-weight: 600; 
                background: <?= ($_SESSION['flash_type'] ?? '') === 'error' ? '#fdecea' : '#e8f5e9' ?>; 
                color: <?= ($_SESSION['flash_type'] ?? '') === 'error' ? 'var(--danger)' : 'var(--success)' ?>;
                border: 1px solid <?= ($_SESSION['flash_type'] ?? '') === 'error' ? '#f5c6cb' : '#c3e6cb' ?>;">
                <?= $displayMsg?>
            </div>
            <?php
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card products">
                <small>Total catalog</small>
                <div class="value"><?= $totalProducts ?></div>
            </div>
            <div class="stat-card categories">
                <small>Active products</small>
                <div class="value"><?= $activeCount ?></div>
            </div>
            <div class="stat-card items">
                <small>Inventory value</small>
                <div class="value">€<?= number_format($totalValue, 2) ?></div>
            </div>
        </div>

        <form class="search-filter-form" method="GET">
            <input type="text" name="search" placeholder="Search product title..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
            <select name="status">
                <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All statuses</option>
                <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>>Active only</option>
                <option value="disabled" <?= $status_filter === 'disabled' ? 'selected' : '' ?>>Disabled only</option>
            </select>
            <button type="submit" class="btn-filter">Filter results</button>
            <?php if (!empty($search) || $status_filter !== 'all'): ?>
                <a href="<?= BASE_URL ?>/admin/products.php" class="btn-clear-filter">Clear</a>
            <?php endif; ?>
        </form>

        <div class="data-table-container">
            <?php if (!$products): ?>
                <div class="empty-state-container">
                    <span class="empty-state-icon">🔍</span>
                    <p>No products match your current filters.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product info</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr class="<?= !$product['is_active'] ? 'item-disabled' : '' ?>">
                                <td data-label="ID">#<?= (int)$product['id'] ?></td>
                                <td data-label="Product Info">
                                    <div class="product-info-cell">
                                        <span class="product-title"><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="product-date">Created: <?= date('d M Y', strtotime($product['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td data-label="Type">
                                    <?php if ($product['is_physical']): ?>
                                        <span class="badge badge-physical">PHYSICAL</span>
                                    <?php else: ?>
                                        <span class="badge badge-digital">DIGITAL</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Price" class="price-cell">
                                    €<?= number_format((float)($product['price'] ?? 0), 2) ?>
                                </td>
                                <td data-label="Status">
                                    <?php if ($product['is_active']): ?>
                                        <span class="badge label-active">ACTIVE</span>
                                    <?php else: ?>
                                        <span class="badge label-disabled">DISABLED</span>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Actions" class="text-right">
                                    <div class="action-links">
                                        <a href="<?= BASE_URL ?>/admin/product_edit.php?id=<?= (int)$product['id'] ?>" title="Edit" class="btn-icon">📝</a>
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