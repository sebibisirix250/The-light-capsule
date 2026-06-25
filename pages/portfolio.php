<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$storySlug = $_GET['story'] ?? null;
$isStoryView = !empty($storySlug);

$pageCss = ['style_portfolio.css'];
$pageJs = ['portfolio.js'];

$pageTitle = 'Portfolio | The Light Capsule';
$metaAuthor = "The Light Capsule";
$metaDescription = "Explore the cinematic wedding and automotive visual stories captured by The Light Capsule.";
$metaKeywords = "photography, wedding photography, automotive photography, cinematic stories, the light capsule, visual portfolio";

if ($isStoryView) {

    $stmt = $pdo->prepare("SELECT * FROM projects WHERE slug = ? AND is_active = 1");
    $stmt->execute([$storySlug]);
    $currentStory = $stmt->fetch();

    if (!$currentStory) {
        header("Location: portfolio.php");
        exit;
    }

    $pageTitle = html_entity_decode($currentStory['title'], ENT_QUOTES, 'UTF-8') . " | Portfolio";

    $imgStmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC");
    $imgStmt->execute([$currentStory['id']]);
    $storyImages = $imgStmt->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT id, slug, title, category, event_date, theme_color, cover_image FROM projects WHERE is_active = 1 ORDER BY event_date DESC");
    $stmt->execute();
    $allStories = $stmt->fetchAll();
}

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="ambient-glow" id="ambientGlow" aria-hidden="true" <?= $isStoryView ? 'style="--current-theme: ' . htmlspecialchars($currentStory['theme_color']) . '"' : '' ?>></div>

<main class="portfolio-main">

    <?php if (!$isStoryView): ?>
        <header class="portfolio-header reveal-item">
            <h1>PORTFOLIO</h1>
            <p class="portfolio-subtitle">Selected stories and complete visual journeys</p>
        </header>

        <section class="editorial-index" id="editorialIndex">
            <img src="" alt="Preview" class="cursor-preview" id="cursorPreview" aria-hidden="true">
            <ul class="project-list">
                <?php foreach ($allStories as $story): ?>
                    <li class="project-item reveal-item"
                        data-cover="<?= BASE_URL . htmlspecialchars($story['cover_image']) ?>"
                        data-theme="<?= htmlspecialchars($story['theme_color']) ?>">

                        <a href="?story=<?= htmlspecialchars($story['slug']) ?>" class="project-link">
                            <span class="project-title"><?= html_entity_decode($story['title'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="project-meta"><?= htmlspecialchars($story['category']) ?> — <?= date('Y', strtotime($story['event_date'])) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

    <?php else: ?>
        <article class="story-detail">

            <header class="story-hero reveal-item">
                <a href="portfolio.php" class="back-link">← Back to index</a>

                <h1 class="playfair-heading"><?= html_entity_decode($currentStory['title'], ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="story-meta">
                    <span><?= date('F jS, Y', strtotime($currentStory['event_date'])) ?></span> |
                    <span><?= htmlspecialchars($currentStory['category']) ?></span>
                </div>
            </header>

            <?php if (!empty($currentStory['narrative_text'])): ?>
                <div class="story-narrative-wrapper reveal-item">
                    <div class="story-narrative">
                        <?php
                        $rawText = html_entity_decode($currentStory['narrative_text'], ENT_QUOTES, 'UTF-8');
                        $rawText = str_replace(["\r\n", "\r"], "\n", $rawText);
                        $paragraphs = preg_split('/\n\s*\n/', $rawText);

                        foreach ($paragraphs as $p) {
                            if (trim($p) !== '') {
                                echo '<p>' . nl2br(trim($p)) . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="cinematic-grid" id="cinematicGrid">
                <?php foreach ($storyImages as $img):
                    $ratio = round((float)$img['aspect_ratio'], 3);
                    if ($ratio <= 0) $ratio = 1;

                    $thumbPath = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '_thumb.webp', $img['file_path']);
                ?>
                    <div class="grid-item reveal-item" style="--ratio: <?= $ratio ?>;">
                        <img src="<?= BASE_URL . htmlspecialchars($thumbPath) ?>"
                            data-src="<?= BASE_URL . htmlspecialchars($img['file_path']) ?>"
                            alt="Story image"
                            class="blur-load"
                            loading="lazy"
                            decoding="async">
                    </div>
                <?php endforeach; ?>
            </div>

        </article>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>