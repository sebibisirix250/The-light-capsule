<?php

//DASHBOARD MAIN PLATFORM DATA RETRIEVAL

require_once __DIR__ . '/../../includes/db.php';

//DATA DISPLAYER
function getAdminStats($pdo)
{
    //MAIL INBOX SCANNER
    $logDir = __DIR__ . '/../../storage/emails'; 
    $unreadCount = 0;
    if (is_dir($logDir)) {
        foreach (scandir($logDir) as $file) {
            if (is_file($logDir . '/' . $file) && !str_starts_with($file, 'read_') && $file !== '.' && $file !== '..') {
                $unreadCount++;
            }
        }
    }

    //OTHER DATA
    return [
        'users'      => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
        'categories' => $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
        'orders'     => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(), 
        'items'      => $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn(),
        'products'   => $pdo->query("SELECT COUNT(*) FROM items WHERE type IN ('product', 'digital_product')")->fetchColumn(),
        'gallery'    => $pdo->query("SELECT COUNT(*) FROM items WHERE type = 'gallery'")->fetchColumn(),
        'unread_emails' => $unreadCount
    ];
}

//RECENT USER DISPLAYER
function getRecentUsers($pdo, $limit = 5)
{
    $stmt = $pdo->prepare("SELECT full_name, email, created_at FROM users ORDER BY created_at DESC LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

$stats = getAdminStats($pdo);
$recentUsers = getRecentUsers($pdo);
