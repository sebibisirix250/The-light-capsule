<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Reset password | The Light Capsule';
$pageDescription = 'Recover your account access to view your private photography collections.';
$pageKeywords = 'forgot password, account recovery, The Light Capsule';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_auth.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/functions.php';
?>

<main class="auth-fullscreen" oncontextmenu="return false;">

    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <section class="auth-card" aria-labelledby="forgot-password-heading">

        <header class="auth-header">
            <h1 id="forgot-password-heading" class="auth-title">Reset access</h1>
            <p class="auth-subtitle">Enter your email and we'll send you recovery instructions.</p>
        </header>

        <form action="<?= BASE_URL ?>/backend/handlers/forgot_password_handler.php" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="input-group">
                <i data-lucide="mail" class="input-icon" aria-hidden="true"></i>
                <input type="email" name="email" id="email" class="auth-input" placeholder=" " required autocomplete="email">
                <label for="email" class="auth-label">Email address</label>
            </div>

            <button type="submit" class="auth-submit">Send reset link</button>
        </form>

        <footer class="auth-footer">
            <p>Remember your password? <a href="<?= BASE_URL ?>/pages/login.php" class="auth-link">Back to login</a></p>
        </footer>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>