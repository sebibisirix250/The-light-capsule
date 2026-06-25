<?php
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../middleware/require_admin.php';

//SECURITY CHECKS

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //CSRF TOKEN VERIFICATION
    verifyCsrfOrFail($_POST['csrf_token'] ?? '', BASE_URL . '/admin/admin_portfolio_archived.php');

    //INPUT CLEANING
    $projectId = forceIntRange($_POST['project_id'] ?? 0, 1);

    //EXECUTION
    if ($projectId > 0) {
        try {
            //RESTORE THE SOFT DELETE (ACTIVE = 1)
            $stmt = $pdo->prepare("UPDATE projects SET is_active = 1 WHERE id = ?");
            $stmt->execute([$projectId]);

            //SUCCES & ERROR MESSAGES
            if ($stmt->rowCount() > 0) {
                setFlashMessage('success', 'Story successfully restored to the live portfolio!');
            } else {
                setFlashMessage('info', 'Story not found or already active.');
            }
        } catch (Exception $e) {
            error_log("Restore Story Error: " . $e->getMessage());
            setFlashMessage('error', 'Failed to restore the story due to a system error.');
        }
    } else {
        setFlashMessage('error', 'Invalid project ID.');
    }
}

header('Location: ' . BASE_URL . '/admin/admin_portfolio_archived.php');
exit;
