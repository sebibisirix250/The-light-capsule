<?php

//STORY UPLOAD - PORTFOLIO

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../middleware/require_admin.php';

//SECURITY CHECKS

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //CSRF TOKEN VERIFICATION
    verifyCsrfOrFail($_POST['csrf_token'] ?? '', BASE_URL . '/admin/admin_portfolio_create.php');

    //INPUT CLEANING
    $rawTitle = html_entity_decode($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8');
    $date = $_POST['event_date'] ?? null;
    $category = sanitizeHtml($_POST['category'] ?? '');
    $narrative = trim(htmlspecialchars($_POST['narrative'] ?? '', ENT_QUOTES, 'UTF-8'));
    $theme_color = sanitizeHtml($_POST['theme_color'] ?? '#ffffff');

    if (empty($rawTitle) || empty($date)) {
        setFlashMessage('error', 'Title and Date are required.');
        header('Location: ' . BASE_URL . '/admin/admin_portfolio_create.php');
        exit;
    }

    $slugBase = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $rawTitle)));
    $slug = $slugBase . '-' . time();

    //EXECUTION
    try {
        $pdo->beginTransaction();

        //DATA INJECTION
        $stmt = $pdo->prepare("INSERT INTO projects (slug, title, event_date, category, narrative_text, theme_color, cover_image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$slug, $rawTitle, $date, $category, $narrative, $theme_color, 'pending']);
        $projectId = $pdo->lastInsertId();

        //IMAGES PATH
        $targetDir = __DIR__ . "/../../assets/images/portfolio/" . $slug . "/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $coverImagePath = '';
        $sortOrder = 0;
        $uploadedCount = 0;

        //IMAGE PROCESSING AND UPLOAD
        if (isset($_FILES['gallery_images']['name']) && is_array($_FILES['gallery_images']['name'])) {
            $totalFiles = count($_FILES['gallery_images']['name']);

            for ($i = 0; $i < $totalFiles; $i++) {
                $tmpPath = $_FILES['gallery_images']['tmp_name'][$i];
                $error = $_FILES['gallery_images']['error'][$i];

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

                                if ($sortOrder === 0) {
                                    $coverImagePath = $dbPath;
                                }

                                $imgStmt = $pdo->prepare("INSERT INTO project_images (project_id, file_path, sort_order, aspect_ratio) VALUES (?, ?, ?, ?)");
                                $imgStmt->execute([$projectId, $dbPath, $sortOrder, $aspectRatio]);

                                $sortOrder++;
                                $uploadedCount++;
                            }
                        }
                    }
                }
            }
        }

        //COVER IMAGE SELECTION
        if ($coverImagePath !== '') {
            $updateStmt = $pdo->prepare("UPDATE projects SET cover_image = ? WHERE id = ?");
            $updateStmt->execute([$coverImagePath, $projectId]);
        }

        //FINAL COMMIT
        $pdo->commit();

        //SUCCES & ERROR MESSAGES
        setFlashMessage('success', "Story created! {$uploadedCount} optimized images and thumbnails generated.");
        header('Location: ' . BASE_URL . '/admin/admin_portfolio.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Portfolio Upload Error: " . $e->getMessage());
        setFlashMessage('error', 'System error occurred during upload.');
        header('Location: ' . BASE_URL . '/admin/admin_portfolio_create.php');
        exit;
    }
} else {
    header('Location: ' . BASE_URL . '/admin/admin_portfolio.php');
    exit;
}
