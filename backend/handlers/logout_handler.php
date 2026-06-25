<?php

//LOGIN OFF HANDLING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}

session_start();
session_regenerate_id(true);

setFlashMessage('success', 'You have been logged out successfully.');

header('Location: ' . BASE_URL . '/index.php');
exit;
