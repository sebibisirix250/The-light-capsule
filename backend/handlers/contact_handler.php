<?php

//PUBLIC CONTACT & RATE LIMITING

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/validation.php';

//JSON HELPER
function contactJsonResponse(bool $success, string $message, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $success,
        'message' => $message,
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    contactJsonResponse(false, 'Invalid request method.', 405);
}

//CSRF TOKEN VERIFICATION
$csrfToken = $_POST['csrf_token'] ?? null;
$sessionToken = $_SESSION['csrf_token'] ?? '';

$isValidCsrf = is_string($csrfToken)
    && is_string($sessionToken)
    && $csrfToken !== ''
    && $sessionToken !== ''
    && hash_equals($sessionToken, $csrfToken);

if (!$isValidCsrf) {
    contactJsonResponse(false, 'Invalid request. Please refresh the page and try again.', 403);
}

//INPUT CLEANING
$email = sanitizeHtml($_POST['email'] ?? '', 255);
$rateIdentifier = strtolower($email);

//RATE LIMITING
if (isRateLimited('contact', 5, 1800, 1800, $rateIdentifier)) {
    contactJsonResponse(false, 'Too many messages sent. Please try again later.', 429);
}

//MORE INPUT CLEANING
$name = sanitizeHtml($_POST['name'] ?? '', 255);
$subject = sanitizeHtml($_POST['subject'] ?? '', 255);
$message = sanitizeHtml($_POST['message'] ?? '');

//INPUT VALIDATION
if ($name === '' || $email === '' || $subject === '' || $message === '') {
    recordRateLimitAttempt('contact', $rateIdentifier);
    contactJsonResponse(false, 'Please complete all required fields.', 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    recordRateLimitAttempt('contact', $rateIdentifier);
    contactJsonResponse(false, 'Invalid email address.', 422);
}

//E-MAIL INITIATION
try {
    sendContactInquiryConfirmationEmail(
        $email,
        $name,
        $subject
    );

    sendAdminContactInquiryNotification(
        $name,
        $email,
        $subject,
        $message
    );
} catch (Throwable $e) {
    //SILENTLY FAILS ON E-MAIL
}

//RATE LIMITING AND SECURITY RESET
recordRateLimitAttempt('contact', $rateIdentifier);
rotateCsrfToken();

//SUCCESS
contactJsonResponse(true, 'Message sent successfully.');
