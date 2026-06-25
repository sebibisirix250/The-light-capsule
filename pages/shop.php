<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'The studio shop | The Light Capsule';
$pageDescription = 'Explore our exclusive collection of premium digital photography packs, presets, and cinematic assets.';
$pageKeywords = 'digital packs, photography presets, lightroom presets, cinematic luts, The Light Capsule';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_shop.css'];
$pageJs = ['shop.js'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/db.php';

$selectedCategoryId = (int)($_GET['category'] ?? 0);

$catStmt = $pdo->prepare("SELECT id, name FROM categories WHERE type = 'product' AND is_active = 1 ORDER BY name");
$catStmt->execute();
$categories = $catStmt->fetchAll();

$query = "
    SELECT 
        items.id, items.title, items.slug, items.short_description, 
        items.price, items.cover_image, categories.name AS category_name
    FROM items
    LEFT JOIN item_categories ON item_categories.item_id = items.id
    LEFT JOIN categories ON categories.id = item_categories.category_id
    WHERE (items.type = 'product' OR items.type = 'digital_product') AND items.is_active = 1
";

$params = [];
if ($selectedCategoryId > 0) {
    $query .= " AND categories.id = ?";
    $params[] = $selectedCategoryId;
}

$query .= " ORDER BY items.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
if ($isAjax) {
    require __DIR__ . '/../partials/shop_grid.php'; 
    exit;
}

require_once __DIR__ . '/../includes/header.php';
?>

<main class="shop-container">

    <header class="shop-hero">
        <div class="hero-content">
            <h1 class="hero-title">The studio shop</h1>
            <p class="hero-subtitle">Premium editing tools, prints, and curated photography accessories.</p>
        </div>
        <div class="hero-glow" aria-hidden="true"></div>
    </header>

    <nav class="category-nav-wrapper" aria-label="Product categories">
        <ul class="category-pills" id="categoryNav">
            <li>
                <button class="pill-btn <?= $selectedCategoryId === 0 ? 'active' : '' ?>" data-category-id="0">
                    All collections
                </button>
            </li>
            <?php foreach ($categories as $category): ?>
                <li>
                    <button class="pill-btn <?= $selectedCategoryId === (int)$category['id'] ? 'active' : '' ?>"
                        data-category-id="<?= (int)$category['id'] ?>">
                        <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div id="productGridContainer" class="grid-transition-wrapper">
        <?php require __DIR__ . '/../partials/shop_grid.php'; ?>
    </div>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>