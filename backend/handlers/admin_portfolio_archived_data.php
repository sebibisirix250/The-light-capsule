<?php

//INACTIVE PORTFOLIO STORY RETRIEVAL - FOR DISPLAY ONLY

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$archivedProjects = [];

//INACTIVE LOGIC
try {
    $sql = "
        SELECT 
            id, 
            slug, 
            title, 
            event_date, 
            category, 
            cover_image,
            created_at 
        FROM projects 
        WHERE is_active = 0
        ORDER BY event_date DESC, created_at DESC
    ";

    //EXECUTE AND FETCH
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $archivedProjects = $stmt->fetchAll();

    //ERROR
} catch (PDOException $e) {
    error_log("Archived Portfolio Fetch Error: " . $e->getMessage());
    setFlashMessage('error', 'Could not load archived stories.');
}
