<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$slug = trim($_GET['slug'] ?? '');

if ($slug === '') {
    header('Location: ' . BASE_URL . '/pages/gallery.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        items.id, items.title, items.slug, items.short_description, items.full_description,
        items.price, items.is_active, gm.full_image, gm.edit_style, gm.capture_location,
        gm.capture_date, gm.lens, gm.focal_length, gm.aperture, gm.shutter_speed, gm.iso_value,
        GROUP_CONCAT(DISTINCT gt.name ORDER BY gt.sort_order ASC SEPARATOR ', ') AS type_names
    FROM items
    INNER JOIN gallery_metadata gm ON gm.item_id = items.id
    LEFT JOIN gallery_item_types git ON git.item_id = items.id
    LEFT JOIN gallery_types gt ON gt.id = git.type_id
    WHERE items.slug = ? AND items.type = 'gallery' AND items.is_active = 1
    GROUP BY items.id LIMIT 1
");

$stmt->execute([$slug]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: ' . BASE_URL . '/pages/gallery.php');
    exit;
}

$imageRelPath = (string)($item['full_image'] ?? '');
$imageFullPath = __DIR__ . '/../' . $imageRelPath;
$orientationClass = 'layout-portrait';

if (!empty($imageRelPath) && file_exists($imageFullPath)) {
    list($width, $height) = getimagesize($imageFullPath);
    if ($width > $height) {
        $orientationClass = 'layout-landscape';
    }
}

$pageTitle = htmlspecialchars((string)($item['title'] ?? 'Gallery Piece')) . ' | The Light Capsule';
$pageDescription = htmlspecialchars((string)($item['short_description'] ?? 'Fine art photography by Ontijt Sébastian.'));
$pageKeywords = htmlspecialchars((string)($item['type_names'] ?? 'photography')) . ', ' . htmlspecialchars((string)($item['capture_location'] ?? ''));
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_gallery_item.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';

$optionsStmt = $pdo->prepare("SELECT option_name, option_value, extra_price FROM item_options WHERE item_id = ? AND is_active = 1 ORDER BY option_name, sort_order");
$optionsStmt->execute([$item['id']]);
$options = $optionsStmt->fetchAll();
$groupedOptions = [];
foreach ($options as $opt) {
    $groupedOptions[$opt['option_name']][] = $opt;
}
?>

<main class="gallery-item-page <?= $orientationClass ?>" oncontextmenu="return false;">
    <div class="gallery-bg-blur" style="background-image: url('<?= BASE_URL ?>/<?= htmlspecialchars((string)$item['full_image']) ?>');"></div>

    <div class="gallery-item-container">

        <section class="visual-showcase">
            <div class="image-wrapper glass-panel">
                <img src="<?= BASE_URL ?>/<?= htmlspecialchars((string)$item['full_image']) ?>"
                    alt="<?= htmlspecialchars((string)$item['title']) ?>"
                    class="main-art-piece">
            </div>

            <div class="art-meta-bar">
                <?php if (!empty($item['capture_location'])): ?>
                    <div class="meta-badge">
                        <i data-lucide="map-pin"></i>
                        <?= htmlspecialchars((string)$item['capture_location']) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($item['capture_date'])): ?>
                    <div class="meta-badge">
                        <i data-lucide="calendar"></i>
                        <?= htmlspecialchars((string)$item['capture_date']) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($item['edit_style'])): ?>
                    <div class="meta-badge">
                        <i data-lucide="layers"></i>
                        <?= htmlspecialchars((string)$item['edit_style']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <aside class="item-details-sidebar">

            <section class="art-story-container glass-panel">
                <header class="item-header">
                    <span class="category-tag"><?= htmlspecialchars((string)($item['type_names'] ?: 'Original Piece')) ?></span>
                    <h1><?= htmlspecialchars((string)$item['title']) ?></h1>
                </header>

                <div class="item-description">
                    <div class="full-desc"><?= nl2br(htmlspecialchars((string)$item['full_description'])) ?></div>
                </div>

                <div class="exif-data-grid">
                    <div class="exif-item">
                        <span class="exif-label">Lens</span>
                        <span class="exif-value"><?= htmlspecialchars((string)($item['lens'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="exif-item">
                        <span class="exif-label">Focal length</span>
                        <span class="exif-value"><?= htmlspecialchars((string)($item['focal_length'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="exif-item">
                        <span class="exif-label">Aperture</span>
                        <span class="exif-value"><?= htmlspecialchars((string)($item['aperture'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="exif-item">
                        <span class="exif-label">Shutter speed</span>
                        <span class="exif-value"><?= htmlspecialchars((string)($item['shutter_speed'] ?? 'N/A')) ?></span>
                    </div>
                    <div class="exif-item">
                        <span class="exif-label">ISO</span>
                        <span class="exif-value"><?= htmlspecialchars((string)($item['iso_value'] ?? 'N/A')) ?></span>
                    </div>
                </div>
            </section>

            <div class="details-glass-card glass-panel">
                <div class="item-price">RON<?= number_format((float)($item['price'] ?? 0), 2) ?></div>

                <form method="POST" action="<?= BASE_URL ?>/backend/handlers/add_to_cart_handler.php" class="purchase-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">

                    <?php if (!empty($groupedOptions)): ?>
                        <?php foreach ($groupedOptions as $name => $values): ?>
                            <div class="option-group">
                                <label><?= htmlspecialchars(ucwords(str_replace('_', ' ', (string)$name))) ?></label>
                                <div class="select-wrapper">
                                    <select name="options[<?= htmlspecialchars((string)$name) ?>]" class="custom-select">
                                        <?php foreach ($values as $v): ?>
                                            <option value="<?= htmlspecialchars((string)$v['option_value']) ?>">
                                                <?= htmlspecialchars((string)$v['option_value']) ?>
                                                <?= $v['extra_price'] > 0 ? '(+€' . number_format((float)$v['extra_price'], 2) . ')' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <button type="submit" class="btn-add-to-cart">
                        <i data-lucide="shopping-cart"></i> Add to collection
                    </button>
                </form>
            </div>
        </aside>

    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>