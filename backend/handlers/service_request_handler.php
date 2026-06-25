<?php

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/mail.php';
require_once __DIR__ . '/../../includes/validation.php';

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL);
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/pages/services.php');

//INPUT CLEANING
$serviceKey = sanitizeHtml($_POST['service_key'] ?? '', 50);
$contactName = sanitizeHtml($_POST['contact_name'] ?? '', 255);
$contactEmail = cleanEmail($_POST['contact_email'] ?? '');
$contactPhone = sanitizeHtml($_POST['contact_phone'] ?? '', 50);
$preferredDate = sanitizeHtml($_POST['preferred_date'] ?? '', 20);
$location = sanitizeHtml($_POST['location'] ?? '', 255);
$notes = sanitizeHtml($_POST['notes'] ?? '');
$selectedOptions = sanitizeHtml($_POST['selected_options'] ?? ''); // New field

//RATE LIMITING
$rateIdentifier = strtolower($contactEmail);

if (isRateLimited('service_request', 5, 3600, 3600, $rateIdentifier)) {
    setFlashMessage('error', 'Too many service requests sent. Please try again later.');
    header('Location: ' . BASE_URL . '/pages/service_request.php?service=' . urlencode($serviceKey));
    exit;
}

//SERVICE CATEGORY LIBRARY 
$serviceMap = [
    'individual'    => 'Individual Sessions',
    'group'         => 'Group Sessions',
    'weddings'      => 'Weddings',
    'automotive'    => 'Automotive',
    'realEstate'    => 'Real Estate',
    'advertisement' => 'Advertising',
    'baptism'       => 'Baptism',
    'sports'        => 'Sports',
    'events'        => 'Events',
    'landscapes'    => 'Landscapes',
    'wildlife'      => 'Wildlife',
    'aerial'        => 'Aerial Photography',
];

//INPUT VALIDATION
if (empty($serviceKey) || !isset($serviceMap[$serviceKey]) || empty($contactName) || empty($contactEmail) || empty($preferredDate)) {
    recordRateLimitAttempt('service_request', $rateIdentifier);
    setFlashMessage('error', 'Please complete all required fields and select a date.');
    header('Location: ' . BASE_URL . '/pages/service_request.php?service=' . urlencode($serviceKey));
    exit;
}

if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    recordRateLimitAttempt('service_request', $rateIdentifier);
    setFlashMessage('error', 'Invalid email address.');
    header('Location: ' . BASE_URL . '/pages/service_request.php?service=' . urlencode($serviceKey));
    exit;
}

//DOUBLE BOOKING PREVENTION
$searchString = "%Preferred Date: " . $preferredDate . "%";
$checkStmt = $pdo->prepare("SELECT id FROM orders WHERE order_type = 'service' AND status != 'cancelled' AND notes LIKE ?");
$checkStmt->execute([$searchString]);
if ($checkStmt->fetch()) {
    setFlashMessage('error', 'Sorry, that date was just booked by someone else. Please select another date.');
    header('Location: ' . BASE_URL . '/pages/service_request.php?service=' . urlencode($serviceKey));
    exit;
}

$userId = isLoggedIn() ? (int)currentUserId() : null;
$serviceTitle = $serviceMap[$serviceKey];

//COMPILE NOTES BLOCKS (REGEX)
$compiledNotes = "Service: " . $serviceTitle . "\n";
$compiledNotes .= "Preferred Date: " . $preferredDate . "\n";
if (!empty($selectedOptions)) {
    $compiledNotes .= "Selected Options: " . $selectedOptions . "\n";
}
if (!empty($location)) {
    $compiledNotes .= "Location: " . $location . "\n";
}
$compiledNotes .= "\nClient Message:\n" . $notes;

//EXECUTION
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, order_type, status, payment_status, total_price, notes, contact_name, contact_email, contact_phone, address_line
        ) VALUES (
            ?, 'service', 'pending', 'not_required', 0.00, ?, ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $userId,
        $compiledNotes,
        $contactName,
        $contactEmail,
        !empty($contactPhone) ? $contactPhone : null,
        !empty($location) ? $location : null
    ]);

    $orderId = (int)$pdo->lastInsertId();

    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, item_type, item_title, quantity, unit_price, line_total) 
        VALUES (?, 'service', ?, 1, 0.00, 0.00)
    ");
    $itemStmt->execute([$orderId, $serviceTitle]);

    //FINAL COMMIT
    $pdo->commit();
    recordRateLimitAttempt('service_request', $rateIdentifier);

    //SECURITY RESET
    rotateCsrfToken();

    //ERROR
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'Request could not be processed. Please try again.');
    header('Location: ' . BASE_URL . '/pages/service_request.php?service=' . urlencode($serviceKey));
    exit;
}

//E-MAIL
try {
    sendServiceRequestConfirmationEmail($contactEmail, $contactName, $serviceTitle, $orderId);
    sendAdminServiceRequestNotification($orderId, $serviceTitle, $contactName, $contactEmail, $compiledNotes);
} catch (Throwable $e) {
}

//SUCCES
setFlashMessage('success', 'Your request has been sent! We will contact you shortly.');

//REDIRECTION
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/account_orders.php');
} else {
    header('Location: ' . BASE_URL . '/index.php');
}
exit;
