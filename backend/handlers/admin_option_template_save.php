<?php

//OPTIONS AND PRICING - GALLERY ITEMS

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//SECURITY CHECKS

//LOGIN, ADMIN VERIFICATION
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/option_templates.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/option_templates.php');

//INPUT CLEANING
$id = forceIntRange($_POST['id'] ?? 0);
$name = sanitizeHtml($_POST['name'] ?? '', 255);
$itemType = sanitizeHtml($_POST['item_type'] ?? '');
$isActive = forceIntRange($_POST['is_active'] ?? 1, 0, 1);

$allowedTypes = ['gallery', 'digital_product', 'service'];

$optionNames = (array)($_POST['option_name'] ?? []);
$optionValues = (array)($_POST['option_value'] ?? []);
$optionPrices = (array)($_POST['option_price'] ?? []);
$optionSorts = (array)($_POST['option_sort'] ?? []);

//INPUT CHECK
if ($name === '' || !in_array($itemType, $allowedTypes, true)) {
    setFlashMessage('error', 'Please fill in the template correctly.');
    header('Location: ' . BASE_URL . '/admin/option_templates.php');
    exit;
}

//UPDATE/INSERT/DELETE EXECUTION
try {
    $pdo->beginTransaction();

    //UPDATE
    if ($id > 0) {
        $stmt = $pdo->prepare("
            UPDATE option_templates
            SET name = ?, 
                item_type = ?, 
                is_active = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $name,
            $itemType,
            $isActive,
            $id
        ]);
    //INSERT
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO option_templates (
                name, 
                item_type, 
                is_active
            ) VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $name,
            $itemType,
            $isActive
        ]);
        $id = (int)$pdo->lastInsertId();
    }

    //DELETE
    $deleteStmt = $pdo->prepare("
        DELETE FROM option_template_items
        WHERE template_id = ?
    ");
    $deleteStmt->execute([$id]);

    $insertStmt = $pdo->prepare("
        INSERT INTO option_template_items (
            template_id,
            option_name,
            option_value,
            extra_price,
            sort_order,
            is_active
        ) VALUES (?, ?, ?, ?, ?, 1)
    ");

    $count = max(
        count($optionNames),
        count($optionValues),
        count($optionPrices),
        count($optionSorts)
    );

    for ($i = 0; $i < $count; $i++) {
        $optName = sanitizeHtml($optionNames[$i] ?? '');
        $optVal = sanitizeHtml($optionValues[$i] ?? '');

        if ($optName === '' || $optVal === '') {
            continue;
        }

        $extraPrice = (float)($optionPrices[$i] ?? 0);
        $sortOrder = (int)($optionSorts[$i] ?? 0);

        $insertStmt->execute([
            $id,
            $optName,
            $optVal,
            $extraPrice,
            $sortOrder
        ]);
    }

    //FINAL COMMIT
    $pdo->commit();

    //SECURITY RESET
    rotateCsrfToken();

    //SUCCES & ERROR MESSAGE
    setFlashMessage('success', 'Template saved successfully.');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    setFlashMessage('error', 'Template could not be saved.');
}

header('Location: ' . BASE_URL . '/admin/option_templates.php');
exit;
