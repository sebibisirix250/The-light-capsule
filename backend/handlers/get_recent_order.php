<?php

//DISPLAY MOST RECENT ORDER FOR CLIENT - ACCOUNT

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../backend/middleware/require_login.php';
require_once __DIR__ . '/../../includes/db.php';

//OUTPUT TYPE
header('Content-Type: application/json');

$userId = currentUserId();

//SECURITY CHECK
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

try {
    //LATEST ORDER
    $orderStmt = $pdo->prepare("
        SELECT id, order_type, status, payment_status, total_price, created_at 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");

    $orderStmt->execute([$userId]);
    $latestOrder = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if ($latestOrder) {
        //CLEAN DATA
        $response = [
            'success' => true,
            'has_order' => true,
            'order' => [
                'id' => (int)($latestOrder['id'] ?? 0),
                'type' => htmlspecialchars(ucfirst($latestOrder['order_type'] ?? 'Standard')),
                'status' => htmlspecialchars(ucfirst($latestOrder['status'] ?? 'Pending')),
                'price' => number_format((float)($latestOrder['total_price'] ?? 0), 2),
                'date' => date('F j, Y', strtotime($latestOrder['created_at']))
            ]
        ];
    } else {
        //USER HAS NO ORDERS
        $response = [
            'success' => true,
            'has_order' => false
        ];
    }

    echo json_encode($response);
    exit;

    //ERROR
} catch (PDOException $e) {
    error_log("Dashboard Order Fetch Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to retrieve order data.']);
    exit;
}
