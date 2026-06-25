<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_admin.php';
require_once __DIR__ . '/../backend/handlers/admin_dashboard_data.php';

$pageTitle = 'Email logs';
$pageCss = ['style_admin_emails.css'];

require_once __DIR__ . '/../includes/page_start.php';

$logDir = __DIR__ . '/../storage/emails';
$files = [];

if (is_dir($logDir)) {
    foreach (scandir($logDir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $logDir . '/' . $file;

        if (is_file($path)) {
            $files[] = [
                'name' => $file,
                'path' => $path,
                'time' => filemtime($path),
                'is_read' => str_starts_with($file, 'read_'),
            ];
        }
    }

    usort($files, function ($a, $b) {
        return $b['time'] <=> $a['time'];
    });
}

$selectedFile = $_GET['file'] ?? null;
$content = null;
$currentFile = null;

if ($selectedFile) {
    $safeFile = basename($selectedFile);
    $fullPath = $logDir . '/' . $safeFile;

    if (is_file($fullPath)) {
        $content = file_get_contents($fullPath);
        $currentFile = $safeFile;
    }
}

$unreadCount = 0;
foreach ($files as $file) {
    if (!$file['is_read']) {
        $unreadCount++;
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
            <li><a href="<?= BASE_URL ?>/admin/products.php">🛒 Products</a></li>
            <li>
                <a href="<?= BASE_URL ?>/admin/email_logs.php" style="position: relative;" style="color: var(--primary);">
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
            <div class="header-info">
                <h1>Email logs</h1>
                <p>You have <strong><?= $unreadCount ?></strong> unread email logs.</p>
            </div>
        </div>

        <div class="email-log-grid">

            <section class="inbox-column">
                <div class="inbox-title-bar">
                    <h3>Inbox</h3>
                </div>
                <div class="inbox-list">
                    <?php if (!$files): ?>
                        <p class="empty-msg">No email logs found.</p>
                    <?php else: ?>
                        <?php foreach ($files as $file): ?>
                            <a href="?file=<?= urlencode($file['name']) ?>"
                                class="email-item <?= $currentFile === $file['name'] ? 'active' : '' ?>">
                                <div class="email-item-header">
                                    <span class="email-name">
                                        <?= htmlspecialchars(str_replace(['read_', '.html', '.txt'], '', $file['name'])) ?>
                                    </span>
                                    <?php if (!$file['is_read']): ?>
                                        <span class="notification-dot"></span>
                                    <?php endif; ?>
                                </div>
                                <div class="email-item-meta">
                                    <span><?= date('d M, H:i', $file['time']) ?></span>
                                    <span><?= $file['is_read'] ? 'Read' : '<b>New</b>' ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>

            <section class="preview-column">
                <?php if ($content === null): ?>
                    <div class="preview-placeholder">
                        <span class="placeholder-icon">📧</span>
                        <p>Select an email.</p>
                    </div>
                <?php else: ?>
                    <div class="preview-header">
                        <h3>Log preview</h3>
                        <div class="preview-buttons">
                            <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_email_log_action.php">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <input type="hidden" name="file" value="<?= htmlspecialchars($currentFile) ?>">
                                <input type="hidden" name="action" value="mark_read">
                                <button type="submit" class="btn-log btn-success">Mark as read</button>
                            </form>

                            <form method="POST" action="<?= BASE_URL ?>/backend/handlers/admin_email_log_action.php">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                <input type="hidden" name="file" value="<?= htmlspecialchars($currentFile) ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn-log btn-danger" onclick="return confirm('Delete this email log?')">Delete</button>
                            </form>
                        </div>
                    </div>

                    <div class="log-body-container">
                        <pre class="log-content"><?= htmlspecialchars($content) ?></pre>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</div>
</body>

</html>