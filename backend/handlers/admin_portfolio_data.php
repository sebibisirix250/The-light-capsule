<?php

//ACTIVE STORY DISPLAYER - PORTFOLIO

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

$portfolioProjects = [];

//FETCH STORIES, ORDER BY EVENT DATE
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
        WHERE is_active = 1
        ORDER BY event_date DESC, created_at DESC
    ";

    //EXECUTE AND FETCH
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $portfolioProjects = $stmt->fetchAll();

    //ERROR
} catch (PDOException $e) {
    error_log("Portfolio data fetch error: " . $e->getMessage());
    setFlashMessage('error', 'System error: Could not load the portfolio stories.');
}
