<?php

//PROFILE UPDATE

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//SECURITY CHECKS

//LOGGED IN ?
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

//USING THE FORM?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/account.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/account.php');

//GRAB USER ID
$userId = currentUserId();

//CLEAN USER DATA
$fullName = sanitizeHtml($_POST['full_name'] ?? '', 150);
$email = cleanEmail($_POST['email'] ?? '');
$phone = sanitizeHtml($_POST['phone'] ?? '', 30);

//INPUT REQUIRMENT
if ($fullName === '' || $email === '') {
    setFlashMessage('error', 'Full name and a valid email are required.');
    header('Location: ' . BASE_URL . '/pages/account.php');
    exit;
}

//SAVE NEW DATA, DISPLAY ERRORS
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1");
    $stmt->execute([$email, $userId]);

    if ($stmt->fetch()) {
        setFlashMessage('error', 'That email address is already in use.'); //ERROR 1
        header('Location: ' . BASE_URL . '/pages/account.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ? LIMIT 1");
    $success = $stmt->execute([
        $fullName,
        $email,
        $phone !== '' ? $phone : null,
        $userId
    ]);

    if ($success) {
        $_SESSION['user_name'] = $fullName;
        rotateCsrfToken();
        setFlashMessage('success', 'Account updated successfully.');
    } else {
        setFlashMessage('error', 'Failed to update account. Please try again.'); //ERROR 2
    }
} catch (PDOException $e) {
    setFlashMessage('error', 'A database error occurred.'); //ERROR 3
}

header('Location: ' . BASE_URL . '/pages/account.php');
exit;
