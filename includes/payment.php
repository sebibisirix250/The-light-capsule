<?php

//LOAD AND CACHE PAYMENT CONFIGURATION
function paymentConfig(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/payment_config.php';
    }

    return (array)$config;
}

//DETERMINE ORDER TYPE
function detectCheckoutOrderType(array $cart): string
{
    if (empty($cart)) {
        return 'digital';
    }

    $hasGallery = false;
    $hasDigital = false;
    $hasService = false;

    foreach ($cart as $item) {
        $type = (string)($item['type'] ?? '');

        $isPhysical = (isset($item['is_physical']) && (int)$item['is_physical'] === 1);

        if ($type === 'gallery' || ($type === 'product' && $isPhysical)) {
            $hasGallery = true;
        } elseif ($type === 'digital_product' || $type === 'digital' || ($type === 'product' && !$isPhysical)) {
            $hasDigital = true;
        } elseif ($type === 'service') {
            $hasService = true;
        }
    }

    //SERVICE
    if ($hasService && !$hasGallery && !$hasDigital) {
        return 'service';
    }

    //DIGITAL/GALLERY
    if (($hasGallery || $hasDigital) && !$hasService) {
        return $hasGallery ? 'mixed' : 'digital';
    }

    //SERVICE AND PRODUCT COMBINATION
    if ($hasService && ($hasGallery || $hasDigital)) {
        return 'mixed';
    }

    return 'digital';
}

//RETURN CORRECT METHODS
function getEnabledPaymentMethods(?string $orderType = null): array
{
    $config = paymentConfig();
    $methods = $config['methods'] ?? [];

    $enabled = array_filter($methods, function ($method) {
        return !empty($method['enabled']);
    });

    if ($orderType === null || $orderType === '') {
        return $enabled;
    }

    return array_filter($enabled, function ($method) use ($orderType) {
        $allowed = (array)($method['allowed_order_types'] ?? []);
        return in_array($orderType, $allowed, true);
    });
}

function getDefaultPaymentMethodKey(?string $orderType = null): string
{
    $enabled = getEnabledPaymentMethods($orderType);
    $keys = array_keys($enabled);
    return !empty($keys) ? (string)$keys[0] : '';
}

function isValidPaymentMethodKey(string $methodKey, ?string $orderType = null): bool
{
    $enabled = getEnabledPaymentMethods($orderType);
    return isset($enabled[$methodKey]);
}

function getPaymentMethodMeta(string $methodKey, ?string $orderType = null): ?array
{
    $enabled = getEnabledPaymentMethods($orderType);
    return $enabled[$methodKey] ?? null;
}

function getPaymentCurrency(): string
{
    $config = paymentConfig();
    return (string)($config['currency'] ?? 'EUR');
}

// GENERATE UNIQUE TRANSACTION LOGS
function buildPaymentInternalReference(int $orderId): string
{
    return 'PAY-' . $orderId . '-' . strtoupper(bin2hex(random_bytes(4)));
}

//DETERMINE STATUS FOR ORDER TABLE
function getOrderPaymentStatusForMethod(string $methodKey): string
{
    switch ($methodKey) {
        case 'manual_review':
        case 'bank_transfer':
        case 'card_gateway':
        default:
            return 'awaiting_manual_confirmation';
    }
}

//DETERMINE STATUS FOR PAYMENT TABLE
function getInitialPaymentTransactionStatus(string $methodKey): string
{
    switch ($methodKey) {
        case 'manual_review':
            return 'awaiting_confirmation';
        case 'bank_transfer':
        case 'card_gateway':
        default:
            return 'pending';
    }
}

//PAPERTRAIL 
function createPaymentTransaction(
    PDO $pdo,
    int $orderId,
    string $methodKey,
    string $provider,
    float $amount,
    ?string $requestPayload = null,
    ?string $responsePayload = null
): int {
    $internalReference = buildPaymentInternalReference($orderId);
    $currency = getPaymentCurrency();
    $status = getInitialPaymentTransactionStatus($methodKey);

    $stmt = $pdo->prepare("
        INSERT INTO payment_transactions (
            order_id,
            method_key,
            provider,
            internal_reference,
            external_reference,
            status,
            amount,
            currency,
            request_payload,
            response_payload,
            created_at
        ) VALUES (?, ?, ?, ?, NULL, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $orderId,
        $methodKey,
        $provider,
        $internalReference,
        $status,
        $amount,
        $currency,
        $requestPayload,
        $responsePayload
    ]);

    return (int)$pdo->lastInsertId();
}
