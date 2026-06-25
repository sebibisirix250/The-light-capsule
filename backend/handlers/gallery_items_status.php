<?php

//GALLERY ITEMS STATUS CHANGE

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../middleware/require_admin.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: " . BASE_URL . "/admin/gallery_items.php?error=missing_params");
    exit();
}

$itemId = (int)$_GET['id'];
$action = $_GET['action'];

try {
    if ($action === 'deactivate') {
        $stmt = $pdo->prepare("UPDATE items SET is_active = 0 WHERE id = ?");
        $stmt->execute([$itemId]);
    } elseif ($action === 'activate') {
        $stmt = $pdo->prepare("UPDATE items SET is_active = 1 WHERE id = ?");
        $stmt->execute([$itemId]);
    }

    header("Location: " . BASE_URL . "/admin/gallery_items.php?success=status_updated");
} catch (PDOException $e) {
    header("Location: " . BASE_URL . "/admin/gallery_items.php?error=db_fail");
}
exit();
