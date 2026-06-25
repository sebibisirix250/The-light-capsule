<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/validation.php';

//SECURITY CHECKS

//LOGIN, ADMIN VERIFICATION
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/orders.php');

//INPUT EXTRACTION & CLEANING
$orderId = forceIntRange($_POST['order_id'] ?? 0);
$status = sanitizeHtml($_POST['status'] ?? '');
$paymentStatus = sanitizeHtml($_POST['payment_status'] ?? '');

//ACCEPTED VALUES 
$allowedStatus = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
$allowedPayment = ['unpaid', 'awaiting_manual_confirmation', 'paid', 'not_required'];

//ORDER CHECK
if ($orderId <= 0) {
    setFlashMessage('error', 'Invalid order.');
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

//STATUS VALUES CHECK
if (!in_array($status, $allowedStatus, true)) {
    setFlashMessage('error', 'Invalid status.');
    header('Location: ' . BASE_URL . '/admin/order_view.php?id=' . $orderId);
    exit;
}

//PAYMENT CHECK
if (!in_array($paymentStatus, $allowedPayment, true)) {
    setFlashMessage('error', 'Invalid payment status.');
    header('Location: ' . BASE_URL . '/admin/order_view.php?id=' . $orderId);
    exit;
}

//ORDER FETCHING
$stmt = $pdo->prepare("
    SELECT id, status, payment_status, contact_name, contact_email 
    FROM orders 
    WHERE id = ? 
    LIMIT 1
");
$stmt->execute([$orderId]);
$current = $stmt->fetch();

//ERROR
if (!$current) {
    setFlashMessage('error', 'Order not found.');
    header('Location: ' . BASE_URL . '/admin/orders.php');
    exit;
}

$hasChanged = (
    $current['status'] !== $status ||
    $current['payment_status'] !== $paymentStatus
);

//ERROR
if (!$hasChanged) {
    setFlashMessage('success', 'No changes made.');
    header('Location: ' . BASE_URL . '/admin/order_view.php?id=' . $orderId);
    exit;
}

//DB CHANGES EXECUTION
try {
    $pdo->beginTransaction();

    $updateOrder = $pdo->prepare("
        UPDATE orders 
        SET status = ?, 
            payment_status = ? 
        WHERE id = ? 
        LIMIT 1
    ");
    $updateOrder->execute([$status, $paymentStatus, $orderId]);

    $stmt = $pdo->prepare("
        SELECT id, status 
        FROM payment_transactions 
        WHERE order_id = ? 
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $latestPayment = $stmt->fetch();

    if ($latestPayment) {
        $newTransactionStatus = null;

        if ($status === 'cancelled') {
            if ($latestPayment['status'] !== 'completed') {
                $newTransactionStatus = 'failed';
            }
        } elseif ($paymentStatus === 'paid') {
            $newTransactionStatus = 'completed';
        } elseif ($paymentStatus === 'awaiting_manual_confirmation') {
            $newTransactionStatus = 'awaiting_confirmation';
        } elseif ($paymentStatus === 'unpaid') {
            if ($latestPayment['status'] !== 'completed') {
                $newTransactionStatus = 'pending';
            }
        } elseif ($paymentStatus === 'not_required') {
            if ($latestPayment['status'] !== 'completed') {
                $newTransactionStatus = 'not_required';
            }
        }

        if ($newTransactionStatus !== null && $newTransactionStatus !== $latestPayment['status']) {
            $updateTx = $pdo->prepare("
                UPDATE payment_transactions 
                SET status = ? 
                WHERE id = ? 
                LIMIT 1
            ");
            $updateTx->execute([$newTransactionStatus, (int)$latestPayment['id']]);
        }
    }

    //FINAL COMMIT
    $pdo->commit();

    //SUCCES & ERROR MESSAGES
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'Order could not be updated.');
    header('Location: ' . BASE_URL . '/admin/order_view.php?id=' . $orderId);
    exit;
}

//SECURITY RESET
rotateCsrfToken();

//E-MAIL INITIATION
try {
    $contactEmail = trim((string)($current['contact_email'] ?? ''));
    $contactName = trim((string)($current['contact_name'] ?? ''));

    if ($contactEmail !== '' && filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
        sendOrderStatusUpdateEmail(
            $contactEmail,
            $contactName !== '' ? $contactName : 'Customer',
            $orderId,
            $status,
            $paymentStatus
        );
    }
} catch (Throwable $e) {
    /* SILENT ERROR WITH E-MAIL*/
}

setFlashMessage('success', 'Order updated successfully.');
header('Location: ' . BASE_URL . '/admin/order_view.php?id=' . $orderId . '&success=1');
exit;
