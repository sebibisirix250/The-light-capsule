<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

if (
    isset($_SESSION['user_id']) &&
    (!isset($_SESSION['last_activity_update']) || time() - $_SESSION['last_activity_update'] > 60)
) {
    $pdo->prepare("
        UPDATE users
        SET last_activity = NOW(),
            updated_at = updated_at
        WHERE id = ?
    ")->execute([$_SESSION['user_id']]);

    $_SESSION['last_activity_update'] = time();
}

if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
