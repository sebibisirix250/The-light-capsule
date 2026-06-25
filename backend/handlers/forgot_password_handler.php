<?php

//PASSWORD CHANGE REQUEST HANDLING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/validation.php';

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/forgot_password.php');

//INPUT CLEANING AND RATE LIMITER
$email = sanitizeHtml($_POST['email'] ?? '', 255);
$rateIdentifier = strtolower($email);

if (isRateLimited('forgot_password', 3, 1800, 1800, $rateIdentifier)) {
    setFlashMessage('error', 'Too many reset requests. Please try again later.');
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    recordRateLimitAttempt('forgot_password', $rateIdentifier);
    setFlashMessage('error', 'Please enter a valid email address.');
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

//SUCCESS - CLIENT
$successMessage = 'If an account with that email exists, password reset instructions have been sent.';

//PREPARE AND FETCH USER
$stmt = $pdo->prepare("
    SELECT id, 
           full_name, 
           email 
    FROM users 
    WHERE email = ? 
    AND is_active = 1 
    LIMIT 1
");
$stmt->execute([$email]);
$user = $stmt->fetch();

//SUCCES - SERVER
if (!$user) {
    recordRateLimitAttempt('forgot_password', $rateIdentifier);
    setFlashMessage('success', $successMessage);
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

//EXECUTION
try {
    $pdo->beginTransaction();

    $invalidateStmt = $pdo->prepare("
        UPDATE password_resets 
        SET used_at = NOW() 
        WHERE user_id = ? 
        AND used_at IS NULL
    ");
    $invalidateStmt->execute([(int)$user['id']]);

    $rawToken = bin2hex(random_bytes(32));
    $tokenHash = password_hash($rawToken, PASSWORD_DEFAULT);
    $expiresAt = date('Y-m-d H:i:s', time() + 3600);

    $insertStmt = $pdo->prepare("
        INSERT INTO password_resets (
            user_id, 
            token_hash, 
            expires_at
        ) VALUES (?, ?, ?)
    ");
    $insertStmt->execute([
        (int)$user['id'],
        $tokenHash,
        $expiresAt
    ]);

    //FINAL COMMIT
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    //ERROR - RATE LIMITER
    recordRateLimitAttempt('forgot_password', $rateIdentifier);
    setFlashMessage('error', 'Could not process your request.');
    header('Location: ' . BASE_URL . '/pages/forgot_password.php');
    exit;
}

$resetLink = BASE_URL . '/pages/reset_password.php?token=' . urlencode($rawToken);

//E-MAIL INITIATION
try {
    $subject = 'Password Reset Request';
    $name = $user['full_name'];

    $text = "Hello {$name},\n\n";
    $text .= "A password reset was requested for your account.\n\n";
    $text .= "Use this link to reset your password:\n";
    $text .= $resetLink . "\n\n";
    $text .= "This link will expire in 1 hour.\n";
    $text .= "If you did not request this, you can ignore this email.";

    $htmlBody = '
        <p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>
        <p style="margin:0 0 16px 0;">A password reset was requested for your account.</p>
        <p style="margin:0 0 16px 0;">
            <a href="' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:12px 18px;background:#111;color:#fff;text-decoration:none;">Reset Password</a>
        </p>
        <p style="margin:0 0 16px 0;">This link will expire in 1 hour.</p>
        <p style="margin:0;">If you did not request this, you can ignore this email.</p>
    ';

    $html = buildEmailHtmlLayout($subject, $htmlBody);

    sendMailMessage(
        $user['email'],
        $subject,
        $text,
        'password_reset_request',
        $html
    );
} catch (Throwable $e) {
    //SILENT FAIL ON E-MAIL
}

recordRateLimitAttempt('forgot_password', $rateIdentifier);
setFlashMessage('success', $successMessage);
header('Location: ' . BASE_URL . '/pages/forgot_password.php');
exit;
