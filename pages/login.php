<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Client login | The Light Capsule';
$pageDescription = 'Access your private client galleries, manage your orders, and view your premium photography collections.';
$pageKeywords = 'client login, photography portal, secure gallery access';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_auth.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="auth-fullscreen" oncontextmenu="return false;">

    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <section class="auth-card" aria-labelledby="login-heading">

        <header class="auth-header">
            <h1 id="login-heading" class="auth-title">Welcome back</h1>
            <p class="auth-subtitle">Sign in to access your private collections.</p>
        </header>

        <form action="<?= BASE_URL ?>/backend/handlers/login_handler.php" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="input-group">
                <i data-lucide="mail" class="input-icon" aria-hidden="true"></i>
                <input type="email" name="email" id="email" class="auth-input" placeholder=" " required autocomplete="email">
                <label for="email" class="auth-label">Email address</label>
            </div>

            <div class="input-group">
                <i data-lucide="lock" class="input-icon" aria-hidden="true"></i>
                <input type="password" name="password" id="password" class="auth-input" placeholder=" " required autocomplete="current-password">
                <label for="password" class="auth-label">Password</label>
            </div>

            <div class="form-utilities">
                <a href="<?= BASE_URL ?>/pages/forgot_password.php" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" class="btn-primary auth-submit">Sign in</button>
        </form>

        <footer class="auth-footer">
            <p>Don’t have an account? <a href="<?= BASE_URL ?>/pages/register.php" class="auth-link highlight-text">Create one</a></p>
        </footer>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>