<?php

//FETCH UNAVAILABLE DATES FROM DB FOR SERVICES

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT notes 
        FROM orders 
        WHERE order_type = 'service' 
        AND status != 'cancelled'
    ");
    $stmt->execute();
    $notesArray = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $bookedDates = [];
    $today = strtotime(date('Y-m-d'));

    foreach ($notesArray as $note) {
        if (preg_match('/Preferred Date:\s*([0-9]{4}-[0-9]{2}-[0-9]{2})/', $note, $matches)) {
            $date = $matches[1];
            if (strtotime($date) >= $today) {
                $bookedDates[] = $date;
            }
        }
    }

    echo json_encode(['status' => 'success', 'dates' => $bookedDates]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to load calendar data.']);
}
exit;
