<?php

//PRODUCT CREATION

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//SECURITY CHECKS

//LOGIN, ADMIN VERIFICATION
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/products.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/products.php');

//INPUT CLEANING
$id = forceIntRange($_POST['id'] ?? 0);
$title = sanitizeHtml($_POST['title'] ?? '', 255);
$slug = sanitizeHtml($_POST['slug'] ?? '', 255);
$short = sanitizeHtml($_POST['short_description'] ?? '');
$full = $_POST['full_description'] ?? '';
$price = cleanFloat($_POST['price'] ?? 0);
$isActive = forceIntRange($_POST['is_active'] ?? 1, 0, 1);
$categoryId = forceIntRange($_POST['category_id'] ?? 0);

$isPhysical = forceIntRange($_POST['is_physical'] ?? 0, 0, 1);
$stockQuantity = forceIntRange($_POST['stock_quantity'] ?? 0);
$isLimitedEdition = forceIntRange($_POST['is_limited_edition'] ?? 0, 0, 1);

//INPUT CHECK
if ($title === '' || $slug === '' || $price < 0) {
    setFlashMessage('error', 'Please fill in the required product fields correctly.');
    header('Location: ' . BASE_URL . '/admin/products.php');
    exit;
}

//IMAGE PATH
$uploadDir = __DIR__ . '/../../assets/uploads/products/';
$galleryDir = $uploadDir . 'gallery/';

if (!is_dir($galleryDir)) {
    mkdir($galleryDir, 0777, true);
}

//COVER IMAGE UPLOAD
$coverImagePath = null;
if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {

    $err = $_FILES['cover_image']['error'];
    if ($err !== UPLOAD_ERR_OK) {
        setFlashMessage('error', 'Cover image upload error code: ' . $err);
        header('Location: ' . BASE_URL . '/admin/products.php');
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $mimeType = $_FILES['cover_image']['type'] ?? '';

    if (!in_array($mimeType, $allowedTypes, true)) {
        setFlashMessage('error', 'Invalid cover image type. Only JPG, PNG, WEBP allowed.');
        header('Location: ' . BASE_URL . '/admin/products.php');
        exit;
    }

    $fileName = uniqid('prod_', true) . '_' . basename($_FILES['cover_image']['name']);
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetPath)) {
        setFlashMessage('error', 'Cover image failed to move to folder.');
        header('Location: ' . BASE_URL . '/admin/products.php');
        exit;
    }

    $coverImagePath = 'assets/uploads/products/' . $fileName;
}

try {
    $pdo->beginTransaction();

    //CORE PRODUCT DATA SAVING
    if ($id > 0) {
        $updateQuery = "
            UPDATE items
            SET title = ?, slug = ?, short_description = ?, full_description = ?, 
                price = ?, is_active = ?, is_physical = ?, stock_quantity = ?, is_limited_edition = ?";

        $params = [$title, $slug, $short, $full, $price, $isActive, $isPhysical, $stockQuantity, $isLimitedEdition];

        if ($coverImagePath) {
            $updateQuery .= ", cover_image = ?";
            $params[] = $coverImagePath;
        }

        $updateQuery .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($updateQuery);
        $stmt->execute($params);
    } else {
        $code = 'PACK-' . time();
        $stmt = $pdo->prepare("
            INSERT INTO items (
                type, code, title, slug, short_description, full_description, 
                price, is_active, is_physical, stock_quantity, is_limited_edition, 
                cover_image, delivery_mode
            ) VALUES (
                'product', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'manual_email'
            )
        ");
        $stmt->execute([
            $code,
            $title,
            $slug,
            $short,
            $full,
            $price,
            $isActive,
            $isPhysical,
            $stockQuantity,
            $isLimitedEdition,
            $coverImagePath
        ]);
        $id = (int)$pdo->lastInsertId();
    }

    //UPDATE CATEGORIES
    $pdo->prepare("DELETE FROM item_categories WHERE item_id = ?")->execute([$id]);

    if ($categoryId > 0) {
        $insertCat = $pdo->prepare("INSERT INTO item_categories (item_id, category_id) VALUES (?, ?)");
        $insertCat->execute([$id, $categoryId]);
    }

    //SECONDARY GALLERY LOOP
    if (isset($_FILES['gallery_images']) && $_FILES['gallery_images']['error'][0] !== UPLOAD_ERR_NO_FILE) {

        $galleryAllowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        $orderStmt = $pdo->prepare("SELECT MAX(display_order) FROM item_images WHERE item_id = ?");
        $orderStmt->execute([$id]);
        $currentOrder = (int)$orderStmt->fetchColumn();

        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmpName) {
            $err = $_FILES['gallery_images']['error'][$key];
            if ($err !== UPLOAD_ERR_OK) continue;

            $mime = $_FILES['gallery_images']['type'][$key];
            if (in_array($mime, $galleryAllowedTypes, true)) {
                $galFileName = uniqid('prod_gal_', true) . '_' . basename($_FILES['gallery_images']['name'][$key]);
                $galTargetPath = $galleryDir . $galFileName;

                if (move_uploaded_file($tmpName, $galTargetPath)) {
                    $currentOrder++;
                    $galPath = 'assets/uploads/products/gallery/' . $galFileName;

                    $insertGal = $pdo->prepare("
                        INSERT INTO item_images (item_id, image_path, display_order) 
                        VALUES (?, ?, ?)
                    ");
                    $insertGal->execute([$id, $galPath, $currentOrder]);
                }
            }
        }
    }

    //GALLERY RE-ORDERING AND DELETATION
    $existingIds = $_POST['existing_gallery_id'] ?? [];
    $existingDeletes = $_POST['existing_gallery_delete'] ?? [];

    foreach ($existingIds as $index => $imgId) {
        $imgId = (int)$imgId;
        $isDeleted = (int)($existingDeletes[$index] ?? 0);

        if ($isDeleted === 1) {
            //DELETE PHYSICAL FILE FROM SERVER 
            $stmt = $pdo->prepare("SELECT image_path FROM item_images WHERE id = ? AND item_id = ?");
            $stmt->execute([$imgId, $id]);
            if ($row = $stmt->fetch()) {
                $filePath = __DIR__ . '/../../' . $row['image_path'];
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
            //WIPE IT FROM DATABASE
            $pdo->prepare("DELETE FROM item_images WHERE id = ?")->execute([$imgId]);
        } else {
            //UPDATE DISPLAY ORDER
            $newOrder = $index + 1;
            $pdo->prepare("UPDATE item_images SET display_order = ? WHERE id = ?")->execute([$newOrder, $imgId]);
        }
    }

    //FINAL COMMIT
    $pdo->commit();

    //SECURITY RESET
    rotateCsrfToken();

    //SUCCES & ERROR MESSAGES
    setFlashMessage('Succes !','Product saved successfully.');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'CRITICAL DB ERROR: ' . $e->getMessage());
}

header('Location: ' . BASE_URL . '/admin/products.php');
exit;
