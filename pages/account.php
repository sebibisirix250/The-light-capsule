<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../backend/middleware/require_login.php';

$pageTitle = 'Client dashboard | The Light Capsule';
$pageDescription = 'Manage your professional photography profile, view exclusive galleries, and track your orders.';
$pageKeywords = 'photography dashboard, client portal, secure account, order tracking';
$pageAuthor = 'Ontijt Sébastian';

$pageJs = ['account_orders.js'];
$pageCss = ['style_auth.css', 'style_account.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$userId = currentUserId();

$stmt = $pdo->prepare("SELECT full_name, email, phone, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$memberSince = date('M Y', strtotime($user['created_at']));
$firstName = explode(' ', trim($user['full_name']))[0];
?>

<main class="dashboard-container" oncontextmenu="return false;">

    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <div class="dashboard-wrapper">

        <header class="dashboard-intro">
            <div class="welcome-text">
                <h1>Welcome, <?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>.</h1>
                <p>Manage your account preferences.</p>
            </div>
            <div class="stat-badge">
                <span class="label">Client since</span>
                <span class="value"><?= htmlspecialchars($memberSince, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </header>

        <div class="dashboard-grid">

            <section class="dashboard-main">

                <div class="glass-panel profile-section">
                    <div class="panel-header">
                        <i data-lucide="settings"></i>
                        <h2>Account details</h2>
                    </div>

                    <form action="<?= BASE_URL ?>/backend/handlers/account_update_handler.php" method="POST" class="dashboard-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-row">
                            <div class="input-group">
                                <i data-lucide="user" class="input-icon"></i>
                                <input type="text" name="full_name" id="full_name" class="auth-input" placeholder=" " value="<?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                                <label for="full_name" class="auth-label">Full name</label>
                            </div>

                            <div class="input-group">
                                <i data-lucide="mail" class="input-icon"></i>
                                <input type="email" name="email" id="email" class="auth-input" placeholder=" " value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" required>
                                <label for="email" class="auth-label">Email address</label>
                            </div>
                        </div>

                        <div class="input-group" style="margin-bottom: 15px;">
                            <i data-lucide="phone" class="input-icon"></i>
                            <input type="text" name="phone" id="phone" class="auth-input" placeholder=" " value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <label for="phone" class="auth-label">Phone number</label>
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="dashboard-submit">Save changes</button>
                        </div>
                    </form>
                </div>

                <div class="glass-panel activity-section">
                    <div class="panel-header">
                        <i data-lucide="clock"></i>
                        <h2>Recent activity</h2>
                    </div>

                    <div id="recent-activity-container" data-baseurl="<?= BASE_URL ?>">
                        <div class="empty-state">
                            <p style="color: rgba(255,255,255,0.3);">Loading order data...</p>
                        </div>
                    </div>
                </div>

            </section>

            <aside class="dashboard-sidebar">

                <div class="glass-panel actions-panel">
                    <h3>Quick actions</h3>
                    <nav class="action-list">
                        <a href="<?= BASE_URL ?>/pages/account_orders.php" class="nav-item">
                            <i data-lucide="package"></i>
                            <span>View all orders</span>
                        </a>
                        <a href="<?= BASE_URL ?>/pages/forgot_password.php" class="nav-item">
                            <i data-lucide="shield-check"></i>
                            <span>Security & password</span>
                        </a>
                        <a href="<?= BASE_URL ?>/backend/handlers/logout_handler.php" class="nav-item logout">
                            <i data-lucide="log-out"></i>
                            <span>Sign out</span>
                        </a>
                    </nav>
                </div>

                <div class="glass-panel support-panel">
                    <h3>Need assistance?</h3>
                    <div class="empty-state" style="padding: 0;">
                        <p style="margin-bottom: 15px;">Your dedicated photographer is just a message away.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/pages/contact.php" class="btn-secondary">Contact studio</a>
                </div>

            </aside>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>