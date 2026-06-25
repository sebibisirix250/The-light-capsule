<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Join the collection | The Light Capsule';
$pageDescription = 'Request access to premium client services and private photography collections by creating an account.';
$pageKeywords = 'register, create account, photography client portal, sign up';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_auth.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="auth-fullscreen" oncontextmenu="return false;">

    <div class="auth-bg-overlay" aria-hidden="true"></div>

    <section class="auth-card" aria-labelledby="register-heading">

        <header class="auth-header">
            <h1 id="register-heading" class="auth-title">Join the collection</h1>
            <p class="auth-subtitle">Create an account to access premium services.</p>
        </header>

        <form action="<?= BASE_URL ?>/backend/handlers/register_handler.php" method="POST" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="input-group">
                <i data-lucide="user" class="input-icon" aria-hidden="true"></i>
                <input type="text" name="full_name" id="full_name" class="auth-input" placeholder=" " required autocomplete="name" minlength="2">
                <label for="full_name" class="auth-label">Full name</label>
            </div>

            <div class="input-group">
                <i data-lucide="mail" class="input-icon" aria-hidden="true"></i>
                <input type="email" name="email" id="email" class="auth-input" placeholder=" " required autocomplete="email">
                <label for="email" class="auth-label">Email address</label>
            </div>

            <div class="input-group">
                <i data-lucide="phone" class="input-icon" aria-hidden="true"></i>
                <input type="tel" name="phone" id="phone" class="auth-input" placeholder=" " autocomplete="tel" pattern="[0-9\-\+\s\(\)]{8,20}" title="Please enter a valid phone number or leave blank.">
                <label for="phone" class="auth-label">Phone (optional)</label>
            </div>

            <div class="input-group">
                <i data-lucide="lock" class="input-icon" aria-hidden="true"></i>
                <input type="password" name="password" id="password" class="auth-input" placeholder=" " required autocomplete="new-password" minlength="8">
                <label for="password" class="auth-label">Password</label>
            </div>

            <div class="input-group">
                <i data-lucide="lock" class="input-icon" aria-hidden="true"></i>
                <input type="password" name="password_confirm" id="password_confirm" class="auth-input" placeholder=" " required autocomplete="new-password" minlength="8">
                <label for="password_confirm" class="auth-label">Confirm password</label>
            </div>

            <div style="margin-top: 10px;"></div>

            <button type="submit" class="btn-primary auth-submit">Request access</button>
        </form>

        <footer class="auth-footer">
            <p>Already a member? <a href="<?= BASE_URL ?>/pages/login.php" class="auth-link highlight-text">Sign in</a></p>
        </footer>

    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>