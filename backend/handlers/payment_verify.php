<?php

//PAYMENT VALIDATION - MANUAL ADMIN TRIGGER

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/payment_gateway.php';
require_once __DIR__ . '/../../includes/validation.php';

if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

$transactionId = forceIntRange($_GET['transaction_id'] ?? 0);

if ($transactionId <= 0) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid transaction ID.'
    ]);
    exit;
}

try {
    $result = verifyPaymentTransaction($pdo, $transactionId);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        $result,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Verification failed: ' . $e->getMessage()
    ]);
}

exit;
