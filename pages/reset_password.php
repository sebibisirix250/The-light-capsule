<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/validation.php';

$pageTitle = 'Reset Password';
$pageCss = ['style_pass_reset.css'];

$token = trim((string)($_GET['token'] ?? ''));

if ($token === '') {
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT token_hash, expires_at FROM password_resets WHERE used_at IS NULL");
    $stmt->execute();
    $allTokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $isValid = false;
    $currentTime = time();

    foreach ($allTokens as $t) {
        if (password_verify($token, $t['token_hash'])) {
            if (strtotime($t['expires_at']) > $currentTime) {
                $isValid = true;
                break;
            }
        }
    }

    if (!$isValid) {
        setFlashMessage('error', 'Invalid or expired reset link.');
        header('Location: ' . BASE_URL . '/pages/forgot_password.php');
        exit;
    }
} catch (PDOException $e) {
    setFlashMessage('error', 'Database error.');
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main>
    <h1>Reset Password</h1>
    <form method="POST" action="<?= BASE_URL ?>/backend/handlers/reset_password_handler.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

        <div>
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" required minlength="8">
        </div>

        <div>
            <label for="password_confirm">Confirm New Password</label>
            <input type="password" name="password_confirm" id="password_confirm" required minlength="8">
        </div>

        <button type="submit">Reset Password</button>
    </form>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>