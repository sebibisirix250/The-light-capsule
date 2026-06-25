<?php

require_once __DIR__ . '/payment.php'; //PULLS PAYMENT CONFIGURATIONS

function startPaymentSession(
    PDO $pdo,
    int $orderId,
    string $orderType,
    string $methodKey,
    float $amount
): array {
    $paymentMeta = getPaymentMethodMeta($methodKey, $orderType);

    if (!$paymentMeta) {
        return [
            'success' => false,
            'message' => 'Invalid payment method.',
        ];
    }

    $provider = (string)$paymentMeta['provider'];
    $mode = (string)(paymentConfig()['mode'] ?? 'stub');

    if ($mode === 'stub') {
        return startStubPaymentSession($pdo, $orderId, $orderType, $methodKey, $provider, $amount);
    }

    /*
   
    FUTURE REAL PROVIDERS - INACTIVE
    
    */

    return [
        'success' => false,
        'message' => 'Payment provider mode is not available.',
    ];
}

function startStubPaymentSession(
    PDO $pdo,
    int $orderId,
    string $orderType,
    string $methodKey,
    string $provider,
    float $amount
): array {
    $requestPayload = json_encode([
        'mode' => 'stub',
        'order_id' => $orderId,
        'order_type' => $orderType,
        'method_key' => $methodKey,
        'amount' => $amount,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $responsePayload = json_encode([
        'message' => 'Payment session created in stub mode.',
        'next_action' => 'none',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $transactionId = createPaymentTransaction(
        $pdo,
        $orderId,
        $methodKey,
        $provider,
        $amount,
        $requestPayload,
        $responsePayload
    );

    return [
        'success' => true,
        'transaction_id' => $transactionId,
        'provider' => $provider,
        'next_action' => 'none',
        'redirect_url' => null,
        'message' => 'Stub payment session created.',
    ];
}

function handlePaymentCallback(PDO $pdo, string $provider, array $payload): array
{
    /*

    FUTURE REAL GATEWAYS CALLBACKS/WEBHOOKS - INACTIVE
    
    */

    return [
        'success' => false,
        'message' => 'No callback handler implemented for this provider.',
    ];
}

function verifyPaymentTransaction(PDO $pdo, int $transactionId): array
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM payment_transactions
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$transactionId]);
    $transaction = $stmt->fetch();

    if (!$transaction) {
        return [
            'success' => false,
            'message' => 'Transaction not found.',
        ];
    }

    return [
        'success' => true,
        'transaction' => $transaction,
    ];
}
