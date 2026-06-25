<?php

//SINGLE GALLERY IMPORT

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/validation.php';

//IMAGE PROCESSING, FORMAT CHECKING
function galleryLoadImageResource(string $path, string $mime)
{
    return match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($path),
        'image/png'  => @imagecreatefrompng($path),
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
        default      => false,
    };
}

function gallerySaveImageResource($image, string $path, string $mime): bool
{
    return match ($mime) {
        'image/jpeg' => imagejpeg($image, $path, 88),
        'image/png'  => imagepng($image, $path, 6),
        'image/webp' => function_exists('imagewebp') ? imagewebp($image, $path, 88) : false,
        default      => false,
    };
}

function galleryPrepareCanvas(int $width, int $height, string $mime)
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
function applyGalleryWatermark($image)
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
function galleryCreateVariant(string $sourcePath, string $destPath, string $mime, int $maxWidth, int $maxHeight): bool
{
    $imageInfo = @getimagesize($sourcePath);
    if (!$imageInfo) return false;
    $srcW = (int)$imageInfo[0];
    $srcH = (int)$imageInfo[1];
    $scale = min($maxWidth / $srcW, $maxHeight / $srcH, 1);
    $newW = max(1, (int)round($srcW * $scale));
    $newH = max(1, (int)round($srcH * $scale));

    $srcImg = galleryLoadImageResource($sourcePath, $mime);
    if (!$srcImg) return false;
    $dstImg = galleryPrepareCanvas($newW, $newH, $mime);
    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

    applyGalleryWatermark($dstImg);

    $saved = gallerySaveImageResource($dstImg, $destPath, $mime);
    imagedestroy($srcImg);
    imagedestroy($dstImg);
    return $saved;
}

//SECURITY CHECKS

//LOGIN, ADMIN VERIFICATION
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ' . BASE_URL);
    exit;
}

//ACCESED FROM BUTTON ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/gallery_items.php');
    exit;
}

//CSRF TOKEN VERIFICATION
verifyCsrfOrFail($_POST['csrf_token'] ?? null, BASE_URL . '/admin/gallery_items.php');

//INPUT EXTRACTION & CLEANING
$id      = forceIntRange($_POST['id'] ?? 0);
$title   = sanitizeHtml($_POST['title'] ?? '', 255);
$slug    = sanitizeHtml($_POST['slug'] ?? '', 255);
$short   = sanitizeHtml($_POST['short_description'] ?? '');
$full    = $_POST['full_description'] ?? '';
$price   = cleanFloat($_POST['price'] ?? 0);
$isActive = forceIntRange($_POST['is_active'] ?? 1, 0, 1);

$selectedTypes = $_POST['gallery_types'] ?? [];
if (!is_array($selectedTypes)) {
    $selectedTypes = [];
}
$selectedTypes = array_values(array_unique(array_filter(array_map('intval', $selectedTypes), fn($v) => $v > 0)));

//GATHER CHOSEN SETTINGS
$editStyle       = sanitizeHtml($_POST['edit_style'] ?? '', 100);
$captureLocation = sanitizeHtml($_POST['capture_location'] ?? '', 255);
$captureDate     = sanitizeHtml($_POST['capture_date'] ?? '', 20);
$isPrintable     = forceIntRange($_POST['is_printable'] ?? 1, 0, 1);
$isLicensed      = forceIntRange($_POST['is_licensed'] ?? 1, 0, 1);
$isDownloadable  = forceIntRange($_POST['is_downloadable'] ?? 1, 0, 1);
$sortOrder       = forceIntRange($_POST['sort_order'] ?? 0);

$optionNames  = (array)($_POST['option_name'] ?? []);
$optionValues = (array)($_POST['option_value'] ?? []);
$optionPrices = (array)($_POST['option_price'] ?? []);
$optionSorts  = (array)($_POST['option_sort'] ?? []);

if ($title === '' || $slug === '' || $price < 0) {
    setFlashMessage('error', 'Please fill in the gallery fields correctly.');
    header('Location: ' . BASE_URL . '/admin/gallery_items.php');
    exit;
}

//FILE HANDLING, CAMERA EXIF DATA EXTRACTION
$thumbImage       = null;
$modalImage       = null;
$fullImage        = null;
$widthPx          = null;
$heightPx         = null;
$aspectRatio      = null;
$orientation      = null;
$originalFilename = null;
$fileFormat       = null;
$fileSizeBytes    = null;
$cameraMake       = null;
$cameraModel      = null;
$lens             = null;
$focalLength      = null;
$aperture         = null;
$shutterSpeed     = null;
$isoValue         = null;

if (!empty($_FILES['image_file']['name'])) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $mimeType     = $_FILES['image_file']['type'] ?? '';

    if (!in_array($mimeType, $allowedTypes, true)) {
        setFlashMessage('error', 'Invalid image type.');
        header('Location: ' . BASE_URL . '/admin/gallery_items.php');
        exit;
    }

    $baseDir = __DIR__ . '/../../assets/uploads/gallery/';
    $dirs = ['thumbs/', 'modal/', 'full/'];
    foreach ($dirs as $sub) {
        if (!is_dir($baseDir . $sub)) {
            mkdir($baseDir . $sub, 0777, true);
        }
    }

    $originalFilename = basename($_FILES['image_file']['name']);
    $fileSizeBytes    = (int)$_FILES['image_file']['size'];
    $ext              = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    $fileFormat       = $ext;
    $baseName         = uniqid('gallery_', true);

    $fullPath  = $baseDir . 'full/' . $baseName . '.' . $ext;
    $thumbPath = $baseDir . 'thumbs/' . $baseName . '_thumb.' . $ext;
    $modalPath = $baseDir . 'modal/' . $baseName . '_modal.' . $ext;

    if (!move_uploaded_file($_FILES['image_file']['tmp_name'], $fullPath)) {
        setFlashMessage('error', 'Image upload failed.');
        header('Location: ' . BASE_URL . '/admin/gallery_items.php');
        exit;
    }

    galleryCreateVariant($fullPath, $thumbPath, $mimeType, 2000, 2000);
    galleryCreateVariant($fullPath, $modalPath, $mimeType, 2400, 2400);

    $thumbImage = 'assets/uploads/gallery/thumbs/' . $baseName . '_thumb.' . $ext;
    $modalImage = 'assets/uploads/gallery/modal/' . $baseName . '_modal.' . $ext;
    $fullImage  = 'assets/uploads/gallery/full/' . $baseName . '.' . $ext;

    $imageInfo = @getimagesize($fullPath);
    if ($imageInfo) {
        $widthPx  = (int)$imageInfo[0];
        $heightPx = (int)$imageInfo[1];
        if ($widthPx > 0 && $heightPx > 0) {
            $aspectRatio = (string)($widthPx / $heightPx);
            if ($widthPx > $heightPx) {
                $orientation = 'landscape';
            } elseif ($heightPx > $widthPx) {
                $orientation = 'portrait';
            } else {
                $orientation = 'square';
            }
        }
    }

    if (function_exists('exif_read_data') && in_array($ext, ['jpg', 'jpeg'], true)) {
        $exif = @exif_read_data($fullPath);
        if ($exif) {
            $cameraMake   = $exif['Make'] ?? null;
            $cameraModel  = $exif['Model'] ?? null;
            $lens         = $exif['UndefinedTag:0xA434'] ?? ($exif['LensModel'] ?? null);
            $focalLength  = isset($exif['FocalLength']) ? (string)$exif['FocalLength'] : null;
            $aperture     = $exif['COMPUTED']['ApertureFNumber'] ?? null;
            $shutterSpeed = $exif['ExposureTime'] ?? null;
            $isoValue     = $exif['ISOSpeedRatings'] ?? null;
            if ($captureDate === '' && !empty($exif['DateTimeOriginal'])) {
                $captureDate = date('Y-m-d', strtotime($exif['DateTimeOriginal']));
            }
        }
    }
}

//IMPORT EXECUTION - ALL STEPS OR FAIL
try {
    $pdo->beginTransaction();

    if ($id > 0) {
        $sql = "UPDATE items 
                SET title = ?, 
                    slug = ?, 
                    short_description = ?, 
                    full_description = ?, 
                    price = ?, 
                    is_active = ?" . ($thumbImage ? ", cover_image = ?" : "") . " 
                WHERE id = ?";

        $params = [$title, $slug, $short, $full, $price, $isActive];
        if ($thumbImage) {
            $params[] = $thumbImage;
        }
        $params[] = $id;

        $pdo->prepare($sql)->execute($params);
    } else {
        $sql = "INSERT INTO items (
                    type, code, title, slug, short_description, 
                    full_description, price, cover_image, is_active, delivery_mode
                ) VALUES (
                    'gallery', ?, ?, ?, ?, ?, ?, ?, ?, 'manual_email'
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'GALLERY-' . time(),
            $title,
            $slug,
            $short,
            $full,
            $price,
            $thumbImage,
            $isActive
        ]);
        $id = (int)$pdo->lastInsertId();
    }

    $existingQuery = $pdo->prepare("SELECT * FROM gallery_metadata WHERE item_id = ? LIMIT 1");
    $existingQuery->execute([$id]);
    $meta = $existingQuery->fetch();

    //CAMERA EXIF DATA EXTRACTION
    $metaData = [
        'orientation'      => $orientation ?? $meta['orientation'] ?? null,
        'width_px'         => $widthPx ?? $meta['width_px'] ?? null,
        'height_px'        => $heightPx ?? $meta['height_px'] ?? null,
        'aspect_ratio'     => $aspectRatio ?? $meta['aspect_ratio'] ?? null,
        'edit_style'       => ($editStyle !== '' ? $editStyle : ($meta['edit_style'] ?? null)),
        'thumb_image'      => $thumbImage ?? $meta['thumb_image'] ?? null,
        'modal_image'      => $modalImage ?? $meta['modal_image'] ?? null,
        'full_image'       => $fullImage ?? $meta['full_image'] ?? null,
        'original_filename' => $originalFilename ?? $meta['original_filename'] ?? null,
        'file_format'      => $fileFormat ?? $meta['file_format'] ?? null,
        'file_size_bytes'  => $fileSizeBytes ?? $meta['file_size_bytes'] ?? null,
        'capture_date'     => ($captureDate !== '' ? $captureDate : ($meta['capture_date'] ?? null)),
        'capture_location' => ($captureLocation !== '' ? $captureLocation : ($meta['capture_location'] ?? null)),
        'camera_make'      => $cameraMake ?? $meta['camera_make'] ?? null,
        'camera_model'     => $cameraModel ?? $meta['camera_model'] ?? null,
        'lens'             => $lens ?? $meta['lens'] ?? null,
        'focal_length'     => $focalLength ?? $meta['focal_length'] ?? null,
        'aperture'         => $aperture ?? $meta['aperture'] ?? null,
        'shutter_speed'    => $shutterSpeed ?? $meta['shutter_speed'] ?? null,
        'iso_value'        => $isoValue ?? $meta['iso_value'] ?? null,
        'is_printable'     => $isPrintable,
        'is_licensed'      => $isLicensed,
        'is_downloadable'  => $isDownloadable,
        'sort_order'       => $sortOrder,
        'item_id'          => $id
    ];

    if ($meta) {
        $metaSql = "UPDATE gallery_metadata 
                    SET orientation = ?, width_px = ?, height_px = ?, aspect_ratio = ?, 
                        edit_style = ?, thumb_image = ?, modal_image = ?, full_image = ?, 
                        original_filename = ?, file_format = ?, file_size_bytes = ?, 
                        capture_date = ?, capture_location = ?, camera_make = ?, 
                        camera_model = ?, lens = ?, focal_length = ?, aperture = ?, 
                        shutter_speed = ?, iso_value = ?, is_printable = ?, 
                        is_licensed = ?, is_downloadable = ?, sort_order = ? 
                    WHERE item_id = ?";
    } else {
        $metaSql = "INSERT INTO gallery_metadata (
                        orientation, width_px, height_px, aspect_ratio, edit_style, 
                        thumb_image, modal_image, full_image, original_filename, 
                        file_format, file_size_bytes, capture_date, capture_location, 
                        camera_make, camera_model, lens, focal_length, aperture, 
                        shutter_speed, iso_value, is_printable, is_licensed, 
                        is_downloadable, sort_order, item_id
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";
    }

    $pdo->prepare($metaSql)->execute(array_values($metaData));

    //CATEGORIES
    $pdo->prepare("DELETE FROM gallery_item_types WHERE item_id = ?")->execute([$id]);
    if ($selectedTypes) {
        $typeStmt = $pdo->prepare("INSERT INTO gallery_item_types (item_id, type_id) VALUES (?, ?)");
        foreach ($selectedTypes as $tid) {
            $typeStmt->execute([$id, $tid]);
        }
    }

    //OPTIONS
    $pdo->prepare("DELETE FROM item_options WHERE item_id = ?")->execute([$id]);
    $optStmt = $pdo->prepare("INSERT INTO item_options (
                    item_id, option_name, option_value, extra_price, is_active, sort_order
                ) VALUES (?, ?, ?, ?, 1, ?)");

    $maxOptions = max(count($optionNames), count($optionValues));
    for ($i = 0; $i < $maxOptions; $i++) {
        $oName = sanitizeHtml($optionNames[$i] ?? '');
        $oVal  = sanitizeHtml($optionValues[$i] ?? '');
        if ($oName !== '' && $oVal !== '') {
            $optStmt->execute([
                $id,
                $oName,
                $oVal,
                (float)($optionPrices[$i] ?? 0),
                (int)($optionSorts[$i] ?? 0)
            ]);
        }
    }

    //FINAL COMMIT
    $pdo->commit();

    //SECURITY RESET
    rotateCsrfToken();

    //SUCCES AND ERROR MESSAGE
    setFlashMessage('success', 'Gallery item saved successfully.');
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlashMessage('error', 'Save failed: ' . $e->getMessage());
}

header('Location: ' . BASE_URL . '/admin/gallery_items.php');
exit;
