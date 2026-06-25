<?php

//CHECKOUT HANDLING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/payment.php';
require_once __DIR__ . '/../../includes/payment_gateway.php';
require_once __DIR__ . '/../../includes/validation.php';

//SECURITY CHECKS

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

//LOGIN, CLIENT VERIFICATION
if (!isLoggedIn()) {
    setFlashMessage('error', 'Please log in to continue.');
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/checkout.php');

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    setFlashMessage('error', 'Your cart is empty.');
    header('Location: ' . BASE_URL . '/pages/cart.php');
    exit;
}

$userId = currentUserId();

//INPUT CLEANING
$contactName = sanitizeHtml($_POST['contact_name'] ?? '', 255);
$contactEmail = sanitizeHtml($_POST['contact_email'] ?? '', 255);
$contactPhone = sanitizeHtml($_POST['contact_phone'] ?? '', 50);
$paymentMethod = sanitizeHtml($_POST['payment_method'] ?? '', 50);

//INPUT VALIDATION
if ($contactName === '' || $contactEmail === '') {
    setFlashMessage('error', 'Name and email are required.');
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'Invalid email address.');
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

//ORDER TYPE DETECTION
$orderType = detectCheckoutOrderType($cart);

if (!isValidPaymentMethodKey($paymentMethod, $orderType)) {
    setFlashMessage('error', 'Invalid payment method.');
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

//PAYMENT VALIDATION
$paymentMeta = getPaymentMethodMeta($paymentMethod, $orderType);

if (!$paymentMeta) {
    setFlashMessage('error', 'Payment method unavailable.');
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

//FINAL CALCULATION
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += (float)$item['price'] * (int)$item['quantity'];
}

//EXECUTION
try {
    $pdo->beginTransaction();

    $orderPaymentStatus = getOrderPaymentStatusForMethod($paymentMethod);

    //MULTI-TABLE INSERTION

    //ORDERS
    $orderStmt = $pdo->prepare("
        INSERT INTO orders (
            user_id,
            order_type,
            status,
            payment_status,
            total_price,
            contact_name,
            contact_email,
            contact_phone
        ) VALUES (
            ?, ?, 'pending', ?, ?, ?, ?, ?
        )
    ");

    $orderStmt->execute([
        $userId,
        $orderType,
        $orderPaymentStatus,
        $totalPrice,
        $contactName,
        $contactEmail,
        $contactPhone !== '' ? $contactPhone : null
    ]);

    $orderId = (int)$pdo->lastInsertId();

    //ITEMS
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id,
            item_id,
            item_type,
            item_title,
            selected_options,
            quantity,
            unit_price,
            line_total
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    //ORDER SUMMARY
    foreach ($cart as $item) {
        $price = (float)$item['price'];
        $qty = (int)$item['quantity'];
        $lineTotal = $price * $qty;

        $itemStmt->execute([
            $orderId,
            $item['item_id'],
            $item['type'],
            $item['title'],
            !empty($item['option_summary']) ? $item['option_summary'] : null,
            $qty,
            $price,
            $lineTotal
        ]);
    }

    //PAYMENT
    $paymentSession = startPaymentSession(
        $pdo,
        $orderId,
        $orderType,
        $paymentMethod,
        (float)$totalPrice
    );

    if (empty($paymentSession['success'])) {
        throw new RuntimeException($paymentSession['message'] ?? 'Payment session could not be created.');
    }


    //FINAL COMMIT
    $pdo->commit();

    //ERROR
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    setFlashMessage('error', 'Order could not be created: ' . $e->getMessage());
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

//SECURITY RESET
rotateCsrfToken();


//E-MAIL INITIATION
try {
    sendOrderConfirmationEmail(
        $contactEmail,
        $contactName,
        $orderId,
        $orderType,
        (float)$totalPrice
    );

    sendAdminNewOrderNotification(
        $orderId,
        $orderType,
        $contactName,
        $contactEmail,
        (float)$totalPrice
    );
} catch (Throwable $e) {
    //SILENTLY FAILS ON E-MAIL
}

unset($_SESSION['cart']);

if (!empty($paymentSession['redirect_url'])) {
    header('Location: ' . $paymentSession['redirect_url']);
    exit;
}

//SUCCESS
setFlashMessage('success', 'Order placed successfully.');
header('Location: ' . BASE_URL . '/pages/order_success.php?order_id=' . $orderId);
exit;
