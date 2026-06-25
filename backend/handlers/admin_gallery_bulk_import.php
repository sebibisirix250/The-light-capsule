<?php

//BULK GALLERY IMPORT

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//IMAGE PROCESSING, FORMAT CHECKING
function bulkLoadImageResource(string $path, string $mime)
{
    return match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/png'  => @imagecreatefrompng($path),
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        default => false,
    };
}

function bulkSaveImageResource($image, string $path, string $mime): bool
{
    return match ($mime) {
        'image/jpeg' => imagejpeg($image, $path, 88),
        'image/png'  => imagepng($image, $path, 6),
        'image/webp' => function_exists('imagewebp') ? imagewebp($image, $path, 88) : false,
        default => false,
    };
}

function bulkPrepareCanvas(int $width, int $height, string $mime)
{
    $canvas = imagecreatetruecolor($width, $height);

    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
    }

    return $canvas;
}

//WATERMARKING
function applyBulkWatermark($image)
{
    $w = imagesx($image);
    $h = imagesy($image);
    $text = "The light capsule";
    $fontSize = 5;
    $black = imagecolorallocate($image, 0, 0, 0);
    $tw = imagefontwidth($fontSize) * strlen($text);
    $th = imagefontheight($fontSize);
    imagestring($image, $fontSize, $w - $tw - 20, $h - $th - 20, $text, $black);
}

//RESIZER
function bulkCreateVariant(string $sourcePath, string $destPath, string $mime, int $maxWidth, int $maxHeight): bool
{
    $imageInfo = @getimagesize($sourcePath);
    if (!$imageInfo) return false;
    $srcWidth = (int)$imageInfo[0];
    $srcHeight = (int)$imageInfo[1];
    $scale = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1);
    $newWidth = max(1, (int)round($srcWidth * $scale));
    $newHeight = max(1, (int)round($srcHeight * $scale));

    $srcImage = bulkLoadImageResource($sourcePath, $mime);
    if (!$srcImage) return false;
    $dstImage = bulkPrepareCanvas($newWidth, $newHeight, $mime);
    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);

    applyBulkWatermark($dstImage);

    $saved = bulkSaveImageResource($dstImage, $destPath, $mime);
    imagedestroy($srcImage);
    imagedestroy($dstImage);
    return $saved;
}

//URL CLEANER
function makeSlug(string $text): string
{
    $text = trim($text);

    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($converted !== false) {
            $text = $converted;
        }
    }

    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');

    return $text !== '' ? $text : 'gallery-item';
}

//SECURITY CHECKS

//LOGIN, ADMIN VERIFICATION
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/gallery_bulk_import.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/gallery_bulk_import.php');

//CONFIG ERROR CATCHING
if (!function_exists('imagecreatetruecolor')) {
    setFlashMessage('error', 'GD image library is not available in PHP.');
    header('Location: ' . BASE_URL . '/admin/gallery_bulk_import.php');
    exit;
}

//CHECK INPUT
if (
    empty($_FILES['images']['name']) ||
    !is_array($_FILES['images']['name']) ||
    count($_FILES['images']['name']) === 0
) {
    setFlashMessage('error', 'Please select at least one image.');
    header('Location: ' . BASE_URL . '/admin/gallery_bulk_import.php');
    exit;
}

//GATHER CHOSEN SETTINGS
$price = cleanFloat($_POST['price'] ?? 0);
$editStyle = sanitizeHtml($_POST['edit_style'] ?? '', 100);
$captureLocation = sanitizeHtml($_POST['capture_location'] ?? '', 200);
$isPrintable = isset($_POST['is_printable']) ? 1 : 0;
$isLicensed = isset($_POST['is_licensed']) ? 1 : 0;
$isDownloadable = isset($_POST['is_downloadable']) ? 1 : 0;
$isActive = 1;
$templateId = forceIntRange($_POST['template_id'] ?? 0);

$selectedTypes = $_POST['gallery_types'] ?? [];
if (!is_array($selectedTypes)) {
    $selectedTypes = [];
}
$selectedTypes = array_map(fn($v) => forceIntRange($v), $selectedTypes);
$selectedTypes = array_values(array_unique(array_filter($selectedTypes, fn($v) => $v > 0)));

$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

$templateRows = [];
if ($templateId > 0) {
    $templateStmt = $pdo->prepare("SELECT option_name, option_value, extra_price, sort_order FROM option_template_items WHERE template_id = ? AND is_active = 1 ORDER BY option_name, sort_order");
    $templateStmt->execute([$templateId]);
    $templateRows = $templateStmt->fetchAll(PDO::FETCH_ASSOC);
}

$baseUploadDir = __DIR__ . '/../../assets/uploads/gallery/';
$thumbDir = $baseUploadDir . 'thumbs/';
$modalDir = $baseUploadDir . 'modal/';
$fullDir = $baseUploadDir . 'full/';

foreach ([$baseUploadDir, $thumbDir, $modalDir, $fullDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

$importedCount = 0;

//IMPORT EXECUTION - ALL IMAGES OR FAIL
try {
    $pdo->beginTransaction();

    $insertItemStmt = $pdo->prepare("INSERT INTO items (type, code, title, slug, short_description, full_description, price, cover_image, is_active, delivery_mode) VALUES ('gallery', ?, ?, ?, ?, ?, ?, ?, ?, 'manual_email')");

    $insertMetaStmt = $pdo->prepare("INSERT INTO gallery_metadata (item_id, gallery_type, orientation, width_px, height_px, aspect_ratio, edit_style, thumb_image, modal_image, full_image, original_filename, file_format, file_size_bytes, capture_date, capture_location, camera_make, camera_model, lens, focal_length, aperture, shutter_speed, iso_value, is_printable, is_licensed, is_downloadable, sort_order) VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $insertTypeStmt = $pdo->prepare("INSERT INTO gallery_item_types (item_id, type_id) VALUES (?, ?)");

    $insertOptionStmt = $pdo->prepare("INSERT INTO item_options (item_id, option_name, option_value, extra_price, is_active, sort_order) VALUES (?, ?, ?, ?, 1, ?)");

    $slugCheckStmt = $pdo->prepare("SELECT id FROM items WHERE slug = ? LIMIT 1");

    $fileCount = count($_FILES['images']['name']);

    //PROCESSING LOOP - RENAMING, VARIANT CREATION, ORIENTION MATH
    for ($i = 0; $i < $fileCount; $i++) {
        $originalName = $_FILES['images']['name'][$i] ?? '';
        $tmpName = $_FILES['images']['tmp_name'][$i] ?? '';
        $mimeType = $_FILES['images']['type'][$i] ?? '';
        $fileError = $_FILES['images']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        $fileSizeBytes = (int)($_FILES['images']['size'][$i] ?? 0);

        if ($fileError !== UPLOAD_ERR_OK || $originalName === '' || $tmpName === '') {
            continue;
        }

        if (!in_array($mimeType, $allowedTypes, true)) {
            continue;
        }

        $originalFilename = basename($originalName);
        $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
        $fileFormat = $ext;

        $baseName = uniqid('gallery_', true);
        $fullFilename = $baseName . '.' . $ext;
        $thumbFilename = $baseName . '_thumb.' . $ext;
        $modalFilename = $baseName . '_modal.' . $ext;

        $fullPath = $fullDir . $fullFilename;
        $thumbPath = $thumbDir . $thumbFilename;
        $modalPath = $modalDir . $modalFilename;

        if (!move_uploaded_file($tmpName, $fullPath)) {
            continue;
        }

        if (!bulkCreateVariant($fullPath, $thumbPath, $mimeType, 2000, 2000)) {
            continue;
        }

        if (!bulkCreateVariant($fullPath, $modalPath, $mimeType, 2400, 2400)) {
            continue;
        }

        $thumbImage = 'assets/uploads/gallery/thumbs/' . $thumbFilename;
        $modalImage = 'assets/uploads/gallery/modal/' . $modalFilename;
        $fullImage = 'assets/uploads/gallery/full/' . $fullFilename;

        $imageInfo = @getimagesize($fullPath);
        if (!$imageInfo) {
            continue;
        }

        $widthPx = (int)$imageInfo[0];
        $heightPx = (int)$imageInfo[1];
        $aspectRatio = ($widthPx > 0 && $heightPx > 0) ? (string)($widthPx / $heightPx) : null;

        $orientation = null;
        if ($widthPx > $heightPx) {
            $orientation = 'landscape';
        } elseif ($heightPx > $widthPx) {
            $orientation = 'portrait';
        } elseif ($widthPx === $heightPx && $widthPx > 0) {
            $orientation = 'square';
        }

        $cameraMake = $cameraModel = $lens = $focalLength = $aperture = $shutterSpeed = $isoValue = $captureDate = null;

        //CAMERA EXIF DATA EXTRACTION
        if (function_exists('exif_read_data') && in_array($ext, ['jpg', 'jpeg'], true)) {
            $exif = @exif_read_data($fullPath);
            if ($exif) {
                $cameraMake = $exif['Make'] ?? null;
                $cameraModel = $exif['Model'] ?? null;
                $lens = $exif['UndefinedTag:0xA434'] ?? ($exif['LensModel'] ?? null);
                $focalLength = isset($exif['FocalLength']) ? (string)$exif['FocalLength'] : null;
                $aperture = isset($exif['COMPUTED']['ApertureFNumber']) ? (string)$exif['COMPUTED']['ApertureFNumber'] : null;
                $shutterSpeed = isset($exif['ExposureTime']) ? (string)$exif['ExposureTime'] : null;
                $isoValue = isset($exif['ISOSpeedRatings']) ? (string)$exif['ISOSpeedRatings'] : null;
                if (!empty($exif['DateTimeOriginal'])) {
                    $captureDate = date('Y-m-d', strtotime($exif['DateTimeOriginal']));
                }
            }
        }

        $title = pathinfo($originalFilename, PATHINFO_FILENAME);
        $baseSlug = makeSlug($title);
        $slug = $baseSlug;
        $suffix = 2;

        //SLUG GENERATION, DUPLICATE PREVENTION
        while (true) {
            $slugCheckStmt->execute([$slug]);
            if (!$slugCheckStmt->fetch()) {
                break;
            }
            $slug = $baseSlug . '-' . $suffix;
            $suffix++;
        }

        $code = 'GALLERY-' . time() . '-' . $i;

        //DB INJECTION
        $insertItemStmt->execute([$code, $title, $slug, null, null, $price, $thumbImage, $isActive]);
        $itemId = (int)$pdo->lastInsertId();

        $insertMetaStmt->execute([
            $itemId,
            $orientation,
            $widthPx,
            $heightPx,
            $aspectRatio,
            ($editStyle !== '' ? $editStyle : null),
            $thumbImage,
            $modalImage,
            $fullImage,
            $originalFilename,
            $fileFormat,
            $fileSizeBytes,
            $captureDate,
            ($captureLocation !== '' ? $captureLocation : null),
            $cameraMake,
            $cameraModel,
            $lens,
            $focalLength,
            $aperture,
            $shutterSpeed,
            $isoValue,
            $isPrintable,
            $isLicensed,
            $isDownloadable,
            $i
        ]);

        foreach ($selectedTypes as $typeId) {
            $insertTypeStmt->execute([$itemId, $typeId]);
        }

        foreach ($templateRows as $row) {
            $insertOptionStmt->execute([$itemId, $row['option_name'], $row['option_value'], $row['extra_price'], $row['sort_order']]);
        }

        $importedCount++;
    }

    //FINAL COMMIT
    $pdo->commit();

    //SECURITY RESET
    rotateCsrfToken();

    //SUCCES AND ERROR MESSAGE
    setFlashMessage('success', 'Imported ' . $importedCount . ' gallery image(s).');

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'Bulk import failed due to a database error.');
    header('Location: ' . BASE_URL . '/admin/gallery_bulk_import.php');
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'Bulk import failed.');
    header('Location: ' . BASE_URL . '/admin/gallery_bulk_import.php');
    exit;
}

header('Location: ' . BASE_URL . '/admin/gallery_items.php');
exit;
