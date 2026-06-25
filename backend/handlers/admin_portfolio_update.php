<?php

//MODIFY STORY - PORTFOLIO

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

    //INPUT COLLECTING AND CLEANING
    $projectId = (int)($_POST['project_id'] ?? 0);
    $slug = sanitizeHtml($_POST['project_slug'] ?? '');
    $title = html_entity_decode($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $date = $_POST['event_date'] ?? null;
    $category = sanitizeHtml($_POST['category'] ?? '');
    $narrative = trim(htmlspecialchars($_POST['narrative'] ?? '', ENT_QUOTES, 'UTF-8'));
    $themeColor = sanitizeHtml($_POST['theme_color'] ?? '#ffffff');
    $deleteImageIds = $_POST['delete_images'] ?? [];
    $restoreImageIds = $_POST['restore_images'] ?? []; 
    $imageOrder = $_POST['image_order'] ?? '';

    //INPUT CHECK
    if ($projectId === 0 || empty($title) || empty($date)) {
        setFlashMessage('error', 'Invalid data submitted.');
        header('Location: ' . BASE_URL . '/admin/admin_portfolio.php');
        exit;
    }


    //EXECUTION
    try {
        $pdo->beginTransaction();

        //TEXTUAL CHANGES
        $updateStmt = $pdo->prepare("UPDATE projects SET title = ?, event_date = ?, category = ?, narrative_text = ?, theme_color = ? WHERE id = ?");
        $updateStmt->execute([$title, $date, $category, $narrative, $themeColor, $projectId]);

        //IMAGE MANAGEMENT
        if (!empty($imageOrder)) {
            $orderedIds = explode(',', $imageOrder);
            foreach ($orderedIds as $index => $imgId) {
                $cleanId = (int)$imgId;
                if ($cleanId > 0) {
                    $sortStmt = $pdo->prepare("UPDATE project_images SET sort_order = ? WHERE id = ? AND project_id = ?");
                    $sortStmt->execute([$index, $cleanId, $projectId]);
                }
            }
        }

        //SOFT DELETE LOGIC
        if (!empty($deleteImageIds) && is_array($deleteImageIds)) {
            foreach ($deleteImageIds as $imgId) {
                $delStmt = $pdo->prepare("UPDATE project_images SET is_active = 0 WHERE id = ? AND project_id = ?");
                $delStmt->execute([(int)$imgId, $projectId]);
            }
        }

        //RESTORE LOGIC
        if (!empty($restoreImageIds) && is_array($restoreImageIds)) {
            foreach ($restoreImageIds as $imgId) {
                $resStmt = $pdo->prepare("UPDATE project_images SET is_active = 1 WHERE id = ? AND project_id = ?");
                $resStmt->execute([(int)$imgId, $projectId]);
            }
        }

        //BULK IMAGE PROCESSING AND UPLOAD
        if (isset($_FILES['new_images']['name'][0]) && !empty($_FILES['new_images']['name'][0])) {
            $targetDir = __DIR__ . "/../../assets/images/portfolio/" . $slug . "/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $sortStmt = $pdo->prepare("SELECT MAX(sort_order) as max_sort FROM project_images WHERE project_id = ?");
            $sortStmt->execute([$projectId]);
            $sortData = $sortStmt->fetch();
            $nextSortOrder = ($sortData['max_sort'] !== null) ? (int)$sortData['max_sort'] + 1 : 0;

            $totalFiles = count($_FILES['new_images']['name']);
            for ($i = 0; $i < $totalFiles; $i++) {
                $tmpPath = $_FILES['new_images']['tmp_name'][$i];
                $error = $_FILES['new_images']['error'][$i];

                if ($error === UPLOAD_ERR_OK && is_uploaded_file($tmpPath)) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $tmpPath);
                    finfo_close($finfo);

                    if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                        $dimensions = getimagesize($tmpPath);
                        if ($dimensions !== false) {
                            $width = $dimensions[0];
                            $height = $dimensions[1];
                            $aspectRatio = round($width / $height, 3);

                            $newFilename = uniqid('img_', true) . '.webp';
                            $finalPath = $targetDir . $newFilename;
                            $dbPath = "/assets/images/portfolio/" . $slug . "/" . $newFilename;

                            $image = null;
                            if ($mime === 'image/jpeg') $image = imagecreatefromjpeg($tmpPath);
                            elseif ($mime === 'image/png') {
                                $image = imagecreatefrompng($tmpPath);
                                imagepalettetotruecolor($image);
                                imagealphablending($image, true);
                                imagesavealpha($image, true);
                            } elseif ($mime === 'image/webp') $image = imagecreatefromwebp($tmpPath);

                            if ($image !== null) {
                                $maxWidth = 1920;
                                $maxHeight = 1920;
                                if ($width > $maxWidth || $height > $maxHeight) {
                                    $ratio = min($maxWidth / $width, $maxHeight / $height);
                                    $newWidth = (int)($width * $ratio);
                                    $newHeight = (int)($height * $ratio);
                                } else {
                                    $newWidth = $width;
                                    $newHeight = $height;
                                }

                                $mainImage = imagecreatetruecolor($newWidth, $newHeight);
                                imagealphablending($mainImage, false);
                                imagesavealpha($mainImage, true);
                                $transparent = imagecolorallocatealpha($mainImage, 255, 255, 255, 127);
                                imagefilledrectangle($mainImage, 0, 0, $newWidth, $newHeight, $transparent);
                                imagecopyresampled($mainImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                                imagewebp($mainImage, $finalPath, 85);

                                $thumbWidth = 400;
                                $thumbHeight = (int)($newHeight * ($thumbWidth / $newWidth));
                                $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
                                imagealphablending($thumbImage, false);
                                imagesavealpha($thumbImage, true);
                                imagefilledrectangle($thumbImage, 0, 0, $thumbWidth, $thumbHeight, $transparent);
                                imagecopyresampled($thumbImage, $mainImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $newWidth, $newHeight);

                                $thumbFilename = str_replace('.webp', '_thumb.webp', $newFilename);
                                imagewebp($thumbImage, $targetDir . $thumbFilename, 80);

                                imagedestroy($thumbImage);
                                imagedestroy($mainImage);
                                imagedestroy($image);

                                $imgInsertStmt = $pdo->prepare("INSERT INTO project_images (project_id, file_path, sort_order, aspect_ratio, is_active) VALUES (?, ?, ?, ?, 1)");
                                $imgInsertStmt->execute([$projectId, $dbPath, $nextSortOrder, $aspectRatio]);
                                $nextSortOrder++;
                            }
                        }
                    }
                }
            }
        }

        //COVER IMAGE SELECTION
        $newCoverId = (int)($_POST['new_cover_id'] ?? 0);
        if ($newCoverId > 0) {
            $coverStmt = $pdo->prepare("SELECT file_path FROM project_images WHERE id = ? AND project_id = ? AND is_active = 1");
            $coverStmt->execute([$newCoverId, $projectId]);
            $newCoverData = $coverStmt->fetch();
            if ($newCoverData) {
                $updateCoverStmt = $pdo->prepare("UPDATE projects SET cover_image = ? WHERE id = ?");
                $updateCoverStmt->execute([$newCoverData['file_path'], $projectId]);
            }
        }

        //FINAL COMMIT
        $pdo->commit();

        //SUCCES & ERROR MESSAGES
        setFlashMessage('success', 'Story updated successfully!');
        header('Location: ' . BASE_URL . '/admin/admin_portfolio_edit.php?id=' . $projectId);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Portfolio Update Error: " . $e->getMessage());
        setFlashMessage('error', 'Failed to update story due to a system error.');
        header('Location: ' . BASE_URL . '/admin/admin_portfolio_edit.php?id=' . $projectId);
        exit;
    }
}
