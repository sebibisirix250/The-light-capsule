<?php

//DYNAMIC GALLERY FEED ASYNCHRONOUS API

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//OUTPUT TYPE
header('Content-Type: application/json; charset=utf-8');

//INPUT CLEANING
$typeFilter = sanitizeHtml($_GET['type'] ?? '');
$resolutionFilter = sanitizeHtml($_GET['resolution'] ?? '');
$editFilter = sanitizeHtml($_GET['edit'] ?? '');

//PAGINATION & OFFSET CALCULATIONS
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = max(1, min(60, (int)($_GET['limit'] ?? 24)));
$offset = ($page - 1) * $limit;

//MULTI-CONDITION QUERY BUILDER

//IMAGE IS ACTIVE ?
$where = [
    "items.type = 'gallery'",
    "items.is_active = 1"
];
$params = [];

//FILTER EXISTS ?
if ($typeFilter !== '') {
    $where[] = "EXISTS (
        SELECT 1
        FROM gallery_item_types git2
        INNER JOIN gallery_types gt2 ON gt2.id = git2.type_id
        WHERE git2.item_id = items.id
        AND gt2.slug = ?
    )";
    $params[] = $typeFilter;
}

//RESOLUTION PARSING
if ($resolutionFilter !== '') {
    if ($resolutionFilter === 'Other') {
        $where[] = "(
            gm.width_px IS NULL
            OR gm.height_px IS NULL
            OR CONCAT(gm.width_px, 'x', gm.height_px) NOT IN ('6000x4000', '4000x6000')
        )";
    } else {
        $where[] = "CONCAT(gm.width_px, 'x', gm.height_px) = ?";
        $params[] = $resolutionFilter;
    }
}

if ($editFilter !== '') {
    $where[] = "gm.edit_style = ?";
    $params[] = $editFilter;
}

$whereSql = implode(' AND ', $where);

//COUNT & PARTIAL FETCH BASED ON REQUIRMENTS
try {
    $countSql = "
        SELECT COUNT(DISTINCT items.id) AS total_count
        FROM items
        INNER JOIN gallery_metadata gm
            ON gm.item_id = items.id
        WHERE $whereSql
    ";

    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = (int)($countStmt->fetchColumn() ?: 0);

    //FETCH
    $sql = "
        SELECT
            items.id,
            items.slug,
            items.title,
            items.short_description,
            items.full_description,
            gm.thumb_image,
            gm.modal_image,
            gm.full_image,
            gm.width_px,
            gm.height_px,
            gm.edit_style,
            GROUP_CONCAT(DISTINCT gt.slug ORDER BY gt.sort_order ASC, gt.name ASC SEPARATOR '|') AS type_slugs,
            GROUP_CONCAT(DISTINCT gt.name ORDER BY gt.sort_order ASC, gt.name ASC SEPARATOR ', ') AS type_names
        FROM items
        INNER JOIN gallery_metadata gm
            ON gm.item_id = items.id
        LEFT JOIN gallery_item_types git
            ON git.item_id = items.id
        LEFT JOIN gallery_types gt
            ON gt.id = git.type_id
        WHERE $whereSql
        GROUP BY
            items.id,
            items.slug,
            items.title,
            items.short_description,
            items.full_description,
            gm.thumb_image,
            gm.modal_image,
            gm.full_image,
            gm.width_px,
            gm.height_px,
            gm.edit_style,
            gm.sort_order,
            items.created_at
        ORDER BY gm.sort_order ASC, items.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    //TRANSLATE IMAGES DATA INTO CLEAN TEXT
    $items = [];
    foreach ($rows as $row) {
        $resolution = 'Other';
        if (!empty($row['width_px']) && !empty($row['height_px'])) {
            $resolution = $row['width_px'] . 'x' . $row['height_px'];
        }

        $typeSlugs = [];
        if (!empty($row['type_slugs'])) {
            $typeSlugs = explode('|', $row['type_slugs']);
        }

        $items[] = [
            'id' => (int)$row['id'],
            'slug' => (string)$row['slug'],
            'title' => (string)$row['title'],
            'short_description' => (string)$row['short_description'],
            'full_description' => (string)$row['full_description'],
            'src' => (string)$row['thumb_image'],
            'modal_src' => (string)$row['modal_image'],
            'full_src' => (string)$row['full_image'],
            'resolution' => $resolution,
            'edit' => (string)($row['edit_style'] ?? ''),
            'types' => $typeSlugs,
            'type_names' => (string)($row['type_names'] ?? '')
        ];
    }

    $loadedCount = min($offset + count($items), $totalCount);
    $hasMore = $loadedCount < $totalCount;

    echo json_encode([
        'items' => $items,
        'page' => $page,
        'limit' => $limit,
        'loaded_count' => $loadedCount,
        'total_count' => $totalCount,
        'has_more' => $hasMore
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    
    //ERROR
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load gallery feed.'
    ]);
}
