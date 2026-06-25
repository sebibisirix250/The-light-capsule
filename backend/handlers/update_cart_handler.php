<?php

//CART HANDLING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/cart.php');
    exit;
}

verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/cart.php');

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: ' . BASE_URL . '/pages/cart.php');
    exit;
}


$cart = &$_SESSION['cart'];
$quantities = $_POST['quantities'] ?? [];

if (is_array($quantities)) {
    foreach ($quantities as $lineKey => $qty) {
        $lineKey = (string)$lineKey;
        $qty = (int)$qty;

        if (!isset($cart[$lineKey])) {
            continue;
        }

        if ($qty <= 0) {
            unset($cart[$lineKey]);
        } else {
            $cart[$lineKey]['quantity'] = min(99, $qty);
        }
    }
}


if (empty($cart)) {
    unset($_SESSION['cart']);
}

rotateCsrfToken();

if (isset($_POST['checkout_action']) && $_POST['checkout_action'] === 'proceed') {
    header('Location: ' . BASE_URL . '/pages/checkout.php');
    exit;
}

setFlashMessage('success', 'Cart updated successfully.');
header('Location: ' . BASE_URL . '/pages/cart.php');
exit;
