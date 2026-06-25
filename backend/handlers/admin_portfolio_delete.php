<?php

//STORY DELETING - PORTFOLIO

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../middleware/require_admin.php';

//SECURITY CHECKS

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //CSRF TOKEN VERIFICATION
    verifyCsrfOrFail($_POST['csrf_token'] ?? '', BASE_URL . '/admin/admin_portfolio.php');

    //INPUT CLEANING
    $projectId = forceIntRange($_POST['project_id'] ?? 0, 1);

    if ($projectId > 0) {
        try {
            //SOFT DELETE
            $stmt = $pdo->prepare("UPDATE projects SET is_active = 0 WHERE id = ?");
            $stmt->execute([$projectId]);

            //CHECK ROW STATUS - UPDATED ?
            if ($stmt->rowCount() > 0) {
                setFlashMessage('success', 'Story successfully archived (soft deleted).');
            } else {
                setFlashMessage('info', 'Story not found or already archived.');
            }
        //ERROR
        } catch (Exception $e) {
            error_log("Soft delete story error: " . $e->getMessage());
            setFlashMessage('error', 'Failed to archive the story due to a system error.');
        }
    } else {
        setFlashMessage('error', 'Invalid project ID.');
    }
}

header('Location: ' . BASE_URL . '/admin/admin_portfolio.php');
exit;
