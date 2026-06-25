<?php

//PASSWORD MODIFICATION HANDLING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/validation.php';

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/login.php');

//INPUT CLEANING
$token = trim((string)($_POST['token'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

//INPUT VALIDATION
if ($token === '' || $password === '' || $passwordConfirm === '') {
    setFlashMessage('error', 'Please complete all required fields.');
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

if ($password !== $passwordConfirm) {
    setFlashMessage('error', 'Passwords do not match.');
    header('Location: ' . BASE_URL . '/pages/reset_password.php?token=' . urlencode($token));
    exit;
}

if (strlen($password) < 8) {
    setFlashMessage('error', 'Password must be at least 8 characters long.');
    header('Location: ' . BASE_URL . '/pages/reset_password.php?token=' . urlencode($token));
    exit;
}

//USER DATA FETCH
try {
    $stmt = $pdo->prepare("
        SELECT
            pr.id,
            pr.user_id,
            pr.token_hash,
            pr.expires_at,
            u.full_name,
            u.email
        FROM password_resets pr
        INNER JOIN users u ON u.id = pr.user_id
        WHERE pr.used_at IS NULL
    ");
    $stmt->execute();
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $matchedReset = null;
    $currentTime = time();

    //PASSWORD RESET TOKEN VALIDATION
    foreach ($candidates as $candidate) {
        if (password_verify($token, $candidate['token_hash'])) {
            if (strtotime($candidate['expires_at']) > $currentTime) {
                $matchedReset = $candidate;
                break;
            }
        }
    }
    //ERROR
    if (!$matchedReset) {
        setFlashMessage('error', 'Invalid or expired reset link.');
        header('Location: ' . BASE_URL . '/pages/forgot_password.php');
        exit;
    }

    $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);

    $pdo->beginTransaction();

    $updateUser = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ? LIMIT 1");
    $updateUser->execute([$newPasswordHash, (int)$matchedReset['user_id']]);

    $markUsed = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ? LIMIT 1");
    $markUsed->execute([(int)$matchedReset['id']]);

    $invalidateOthers = $pdo->prepare("
        UPDATE password_resets 
        SET used_at = NOW() 
        WHERE user_id = ? 
        AND used_at IS NULL 
        AND id <> ?
    ");
    $invalidateOthers->execute([
        (int)$matchedReset['user_id'],
        (int)$matchedReset['id']
    ]);

    //FINAL COMMIT
    $pdo->commit();

    //SECURITY RESET
    rotateCsrfToken();

    //ERROR
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'An error occurred during reset. Please try again.');
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

//E-MAIL INITIATION
try {
    $subject = 'Your Password Has Been Reset';
    $name = $matchedReset['full_name'];
    $text = "Hello {$name},\n\nYour password has been reset successfully.\nIf this was not you, please contact us immediately.";
    $htmlBody = '
        <p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>
        <p style="margin:0 0 16px 0;">Your password has been reset successfully.</p>
        <p style="margin:0;">If this was not you, please contact us immediately.</p>
    ';
    $html = buildEmailHtmlLayout($subject, $htmlBody);
    sendMailMessage(
        $matchedReset['email'],
        $subject,
        $text,
        'password_reset_success',
        $html
    );
} catch (Throwable $e) {
}

//SUCCESS
setFlashMessage('success', 'Password reset successfully. You can now log in.');
header('Location: ' . BASE_URL . '/pages/login.php');
exit;
