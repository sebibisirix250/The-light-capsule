<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/validation.php';

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/register.php');

//INPUT CLEANING
$fullName = sanitizeHtml($_POST['full_name'] ?? '', 255);
$email = cleanEmail($_POST['email'] ?? '');
$phone = sanitizeHtml($_POST['phone'] ?? '', 50);
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');

//INPUT VALIDATION
if ($fullName === '' || $email === '' || $password === '') {
    setFlashMessage('error', 'Please fill in all required fields.');
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'Invalid email address.');
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}

if ($password !== $passwordConfirm) {
    setFlashMessage('error', 'Passwords do not match.');
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}

if (strlen($password) < 8) {
    setFlashMessage('error', 'Password must be at least 8 characters long.');
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}

//FETCH EXISTING DB E-MAIL FOR VERIFICATION
$checkStmt = $pdo->prepare("
    SELECT id 
    FROM users 
    WHERE email = ? 
    LIMIT 1
");
$checkStmt->execute([$email]);

if ($checkStmt->fetch()) {
    setFlashMessage('error', 'A user is already registered with that email.');
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}

//PASSWORD HASHING
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

//INPUT GATHERING
try {
    $insertStmt = $pdo->prepare("
        INSERT INTO users (
            full_name, 
            email, 
            phone, 
            password_hash, 
            role, 
            is_active
        ) VALUES (?, ?, ?, ?, 'client', 1)
    ");
//INPUT INJECTION
    $insertStmt->execute([
        $fullName,
        $email,
        $phone !== '' ? $phone : null,
        $passwordHash
    ]);

    $userId = (int)$pdo->lastInsertId();

    //SECURITY RESET
    rotateCsrfToken();

    $_SESSION['user_id'] = $userId;
    $_SESSION['user_role'] = 'client';
    $_SESSION['user_name'] = $fullName;

    //E-MAIL INITIATION
    try {
        sendWelcomeEmail(
            $email,
            $fullName
        );

        sendAdminNewUserNotification(
            $fullName,
            $email
        );
    } catch (Throwable $e) {
        //SILENT FAIL ON E-MAIL
    }

    //SUCCES & ERROR MESSAGES
    setFlashMessage('success', 'Account created successfully. Welcome, ' . $fullName . '.');
    header('Location: ' . BASE_URL . '/index.php');
    exit;
} catch (Exception $e) {
    setFlashMessage('error', 'Account could not be created at this time.');
    header('Location: ' . BASE_URL . '/pages/register.php');
    exit;
}
