<?php

//USER INFO UPDATING

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../middleware/require_admin.php';

//SECURITY CHECKS
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_id'])) {
    header("Location: " . BASE_URL . "/admin/users.php?error=invalid_request");
    exit();
}

//INPUTS
$userId = (int)$_POST['user_id'];
$updateAction = $_POST['update_action'] ?? '';

//ACCOUNT PROFILING
try {

//UPDATE PERSONAL DATA
    if ($updateAction === 'update_info') {
        $fullName = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->execute([$fullName, $email, $phone, $userId]);

        header("Location: " . BASE_URL . "/admin/edit_user.php?id=$userId&success=1");
        exit();

//FORCE PASSWORD RESET E-MAIL 
    } elseif ($updateAction === 'force_reset') {
        
        $stmt = $pdo->prepare("SELECT full_name, email FROM users WHERE id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user) {
            $pdo->beginTransaction();

           
            $invalidateStmt = $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL");
            $invalidateStmt->execute([$userId]);

            
            $rawToken = bin2hex(random_bytes(32));
            $tokenHash = password_hash($rawToken, PASSWORD_DEFAULT);
            $expiresAt = date('Y-m-d H:i:s', time() + 3600);

            
            $insertStmt = $pdo->prepare("INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
            $insertStmt->execute([$userId, $tokenHash, $expiresAt]);

            //FINAL COMMIT
            $pdo->commit();

            //E-MAIL TEMPLATE
            $resetLink = BASE_URL . '/pages/reset_password.php?token=' . urlencode($rawToken);
            $subject = 'Password reset request (Admin initiated)';
            $name = $user['full_name'];

            $text = "Hello {$name},\n\nAn administrator has initiated a password reset for your account.\n\nUse this link to reset your password:\n" . $resetLink . "\n\nThis link will expire in 1 hour.";

            $htmlBody = '
                <p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ',</p>
                <p style="margin:0 0 16px 0;">An administrator has initiated a password reset for your account.</p>
                <p style="margin:0 0 16px 0;">
                    <a href="' . htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8') . '" style="display:inline-block;padding:12px 18px;background:#111;color:#fff;text-decoration:none;">Reset Password</a>
                </p>
                <p style="margin:0;">This link will expire in 1 hour.</p>';

            $html = buildEmailHtmlLayout($subject, $htmlBody);

            sendMailMessage($user['email'], $subject, $text, 'password_reset_request', $html);
        }

        header("Location: " . BASE_URL . "/admin/edit_user.php?id=$userId&success=reset_sent");
        exit();
    }

    
    header("Location: " . BASE_URL . "/admin/edit_user.php?id=$userId&error=missing_action");

    //ERROR
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: " . BASE_URL . "/admin/edit_user.php?id=$userId&error=db_fail");
}
exit();
