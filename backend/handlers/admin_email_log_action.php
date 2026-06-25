<?php

//EMAIL INBOX ACTIONS 

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';
require_once __DIR__ . '/../middleware/require_admin.php';

//SECURITY CHECKS

//LOGIN, ADMIN VERIFICATION
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/email_logs.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/email_logs.php');

//EXTRACT, CLEAN, CHECK INPUT AND FILE
$file = sanitizeHtml(basename($_POST['file'] ?? ''), 255);
$action = sanitizeHtml($_POST['action'] ?? '', 50);

if ($file === '') {
    setFlashMessage('error', 'Invalid file request.');
    header('Location: ' . BASE_URL . '/admin/email_logs.php');
    exit;
}

//LOCATE FILE
$logDir = realpath(__DIR__ . '/../../storage/emails') . '/';
$fullPath = $logDir . $file;

//EXIST ?
if (!is_file($fullPath)) {
    setFlashMessage('error', 'Log file not found.');
    header('Location: ' . BASE_URL . '/admin/email_logs.php');
    exit;
}

//MARK AS READ - EXECUTION
if ($action === 'mark_read') {
    if (!str_starts_with($file, 'read_')) {
        $newName = 'read_' . preg_replace('/^unread_/', '', $file);
        if (rename($fullPath, $logDir . $newName)) {
            rotateCsrfToken();
            setFlashMessage('success', 'Email log marked as read.');
            header('Location: ' . BASE_URL . '/admin/email_logs.php?file=' . urlencode($newName));
            exit;
        }
    }

    rotateCsrfToken();
    header('Location: ' . BASE_URL . '/admin/email_logs.php?file=' . urlencode($file));
    exit;
}

//DELETE - EXECUTION
if ($action === 'delete') {
    if (unlink($fullPath)) {
        rotateCsrfToken();
        setFlashMessage('success', 'Email log deleted successfully.');
        header('Location: ' . BASE_URL . '/admin/email_logs.php');
        exit;
    } else {
        setFlashMessage('error', 'Failed to delete the log file.');
    }
}

header('Location: ' . BASE_URL . '/admin/email_logs.php');
exit;
