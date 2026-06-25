<?php

//ASYNCHRONOUS LISTENER - WEBHOOK ENDPOINT - EXTERNAL PAYMENT PROCESSORS NOTIFICATIONS

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/payment_gateway.php';
require_once __DIR__ . '/../../includes/validation.php';

$provider = sanitizeHtml($_GET['provider'] ?? $_POST['provider'] ?? '', 50);

if ($provider === '') {
    http_response_code(400);
    echo 'Missing provider.';
    exit;
}

$payload = [
    'get' => $_GET,
    'post' => $_POST,
    'raw' => file_get_contents('php://input'),
    'headers' => getallheaders()
];

try {
    $result = handlePaymentCallback($pdo, $provider, $payload);

    if (!empty($result['success'])) {
        http_response_code(200);
        echo 'OK';
        exit;
    }

    http_response_code(400);
    echo $result['message'] ?? 'Callback failed.';
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Internal Server Error';
}

exit;
