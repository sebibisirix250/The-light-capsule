<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../middleware/require_admin.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: " . BASE_URL . "/admin/users.php?error=missing_params");
    exit();
}

$userId = (int)$_GET['id'];
$action = $_GET['action'];


if ($userId == $_SESSION['user_id']) {
    header("Location: " . BASE_URL . "/admin/users.php?error=self_action");
    exit();
}

try {
    if ($action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
        $stmt->execute([$userId]);
    } elseif ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
        $stmt->execute([$userId]);
    }

    header("Location: " . BASE_URL . "/admin/users.php?success=1");
} catch (PDOException $e) {
    header("Location: " . BASE_URL . "/admin/users.php?error=db_fail");
}
exit();
