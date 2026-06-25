<?php

//SHOP - CART

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//SECURITY CHECKS

//USED THE BUTTON?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/shop.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/shop.php');

//VERIFY AND CLEAN CHOSEN PRODUCT INFO
$itemId = forceIntRange($_POST['item_id'] ?? 0, 1);
$submittedOptions = $_POST['options'] ?? [];

if (!is_array($submittedOptions)) {
    $submittedOptions = [];
}

if ($itemId <= 0) {
    setFlashMessage('error', 'Invalid item.');
    header('Location: ' . BASE_URL . '/pages/cart.php');
    exit;
}

//DOES PRODUCT EXIST ?
try {
    $stmt = $pdo->prepare("SELECT id, title, price, type, cover_image FROM items WHERE id = ? AND is_active = 1 AND type IN ('product', 'digital_product', 'gallery') LIMIT 1");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    //DOES NOT EXIST OR OUT OF STOCK
    if (!$item) {
        setFlashMessage('error', 'Product is unavailable.');
        header('Location: ' . BASE_URL . '/pages/cart.php');
        exit;
    }

    //PULL REAL PRICES AND OPTIONS FROM DB
    $optionsStmt = $pdo->prepare("SELECT option_name, option_value, extra_price FROM item_options WHERE item_id = ? AND is_active = 1 ORDER BY option_name, sort_order");
    $optionsStmt->execute([$itemId]);
    $dbOptions = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);

    $allowedOptions = [];
    foreach ($dbOptions as $opt) {
        $optionName = sanitizeHtml($opt['option_name'] ?? '', 100);
        $optionValue = sanitizeHtml($opt['option_value'] ?? '', 150);

        if ($optionName === '' || $optionValue === '') {
            continue;
        }

        $allowedOptions[$optionName][$optionValue] = cleanFloat($opt['extra_price'] ?? 0, 0, 999999);
    }

    ksort($submittedOptions);

    $normalizedOptions = [];
    $extraTotal = 0.0;
    $optionSummaryParts = [];

    //LOOPS SELECTED ITEMS
    foreach ($submittedOptions as $optionName => $optionValue) {
        $optionName = sanitizeHtml((string)$optionName, 100);
        $optionValue = sanitizeHtml((string)$optionValue, 150);

        if ($optionName === '' || $optionValue === '') {
            continue;
        }

        //VERIFY THE MATCH
        if (isset($allowedOptions[$optionName][$optionValue])) {
            $normalizedOptions[$optionName] = $optionValue;
            $extraPrice = cleanFloat($allowedOptions[$optionName][$optionValue], 0, 999999);
            $extraTotal += $extraPrice;
            $optionSummaryParts[] = $optionName . ': ' . $optionValue;
        }
    }

    //CALCULATE FINAL PRICE
    $basePrice = cleanFloat($item['price'] ?? 0, 0, 999999);
    $finalUnitPrice = $basePrice + $extraTotal;
    $optionSummary = implode(' | ', $optionSummaryParts);

    //EACH PRODUCT/SERVICE + ITS OPTIONS = UNIQUE KEY, SAME SERVICE + DIFF OPTIONS = DIFF KEY
    $lineHash = md5(json_encode([
        'item_id' => (int)$item['id'],
        'options' => $normalizedOptions
    ]));
    $lineKey = 'line_' . $lineHash;

    //SAVE TO CART MEMORY

    //CART CREATOR
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    //MODIFIER
    if (isset($_SESSION['cart'][$lineKey])) {
        $currentQty = forceIntRange($_SESSION['cart'][$lineKey]['quantity'] ?? 1, 1, 100);
        $_SESSION['cart'][$lineKey]['quantity'] = min($currentQty + 1, 100);
    } else {
        $_SESSION['cart'][$lineKey] = [
            'line_key' => $lineKey,
            'item_id' => (int)$item['id'],
            'title' => sanitizeHtml($item['title'] ?? '', 200),
            'base_price' => $basePrice,
            'price' => $finalUnitPrice,
            'quantity' => 1,
            'type' => sanitizeHtml($item['type'] ?? '', 50),
            'selected_options' => $normalizedOptions,
            'option_summary' => $optionSummary,
            'image' => !empty($item['cover_image']) ? BASE_URL . '/' . ltrim($item['cover_image'], '/') : ''
        ];
    }

    //SECURITY RESET
    rotateCsrfToken();

    //SUCCES AND ERROR MESSAGE
    setFlashMessage('success', 'Item added to cart.');
} catch (PDOException $e) {
    setFlashMessage('error', 'A database error occurred while adding to cart. Please try again.');
}

header('Location: ' . BASE_URL . '/pages/cart.php');
exit;
