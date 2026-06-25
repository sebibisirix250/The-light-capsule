<?php

//LOGIN HANDLING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/login.php');

//INPUT CLEANING
$email = cleanEmail($_POST['email'] ?? '');
$password = (string)($_POST['password'] ?? '');
$rateIdentifier = strtolower($email);

//RATE LIMITING
if (isRateLimited('login', 5, 900, 900, $rateIdentifier)) {
    setFlashMessage('error', 'Too many login attempts. Please try again later.');
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

if ($email === '' || $password === '') {
    recordRateLimitAttempt('login', $rateIdentifier);
    setFlashMessage('error', 'Email and password are required.');
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

//USER DATA FETCH
$stmt = $pdo->prepare("
    SELECT id, 
           password_hash, 
           role, 
           full_name, 
           is_active 
    FROM users 
    WHERE email = ? 
    AND is_active = 1 
    LIMIT 1
");

$stmt->execute([$email]);
$user = $stmt->fetch();

//INPUT VALIDATION
if (!$user || !password_verify($password, $user['password_hash'])) {
    recordRateLimitAttempt('login', $rateIdentifier);
    setFlashMessage('error', 'Invalid email or password.');
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit;
}

clearRateLimit('login', $rateIdentifier);

//SECURITY RESET
rotateCsrfToken();

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_role'] = (string)$user['role'];
$_SESSION['user_name'] = (string)$user['full_name'];

//SUCCES
setFlashMessage('success', 'Login successful. Welcome back, ' . $user['full_name'] . '.');

//REDIRECT
if ($user['role'] === 'admin') {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/index.php');
}

exit;
