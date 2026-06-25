<?php

//DETECT USER ACTIVITY

function getUsers($pdo, $search, $filter)
{
    $query = "SELECT *, 
              (last_activity > NOW() - INTERVAL 5 MINUTE) AS is_online 
              FROM users WHERE 1=1";

    $params = [];

    if (!empty($search)) {
        $query .= " AND (full_name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($filter === 'active') {
        $query .= " AND is_active = 1";
    } elseif ($filter === 'inactive') {
        $query .= " AND is_active = 0";
    }

    $query .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll();
}
