<?php

//PRODUCT CATEGORY CREATOR

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
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/categories.php');

//EXTRACT AND CLEAN DATA
$id = forceIntRange($_POST['id'] ?? 0);
$name = sanitizeHtml($_POST['name'] ?? '', 100);
$slug = sanitizeHtml($_POST['slug'] ?? '', 100);
$type = sanitizeHtml($_POST['type'] ?? '', 50);
$isActive = forceIntRange($_POST['is_active'] ?? 1, 0, 1);

//INPUT CHECKS
$allowedTypes = ['product', 'gallery', 'service'];

if ($name === '' || $slug === '' || !in_array($type, $allowedTypes, true)) {
    setFlashMessage('error', 'Please fill in the category fields correctly.');
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

//SAVE NEW DATA IN DB
try {
    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, type = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $type, $isActive, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, type, is_active) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $type, $isActive]);
    }

    //SECURITY RESET
    rotateCsrfToken();

    //SUCCES AND ERROR MESSAGE
    setFlashMessage('success', 'Category saved successfully.');
} catch (PDOException $e) {
    setFlashMessage('error', 'Category could not be saved.');
    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

header('Location: ' . BASE_URL . '/admin/categories.php');
exit;
