<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

$pageTitle = 'Book a service | The Light Capsule';
$pageCss = ['style_service_request.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';

$serviceKey = trim($_GET['service'] ?? '');

$serviceMap = [
    'individual' => 'Individual Sessions',
    'group' => 'Group Sessions',
    'weddings' => 'Weddings',
    'automotive' => 'Automotive',
    'realEstate' => 'Real Estate',
    'advertisement' => 'Advertising',
    'baptism' => 'Baptism',
    'sports' => 'Sports',
    'events' => 'Events',
    'landscapes' => 'Landscapes',
    'wildlife' => 'Wildlife',
    'aerial' => 'Aerial Photography',
];

if ($serviceKey === '' || !isset($serviceMap[$serviceKey])) {
    echo '<main class="book-fullscreen"><div class="glass-panel text-center"><h2>Service not found.</h2><a href="' . BASE_URL . '/pages/prices.php" class="btn-primary">Return to pricing</a></div></main>';
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$user = null;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT full_name, email, phone FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([currentUserId()]);
    $user = $stmt->fetch();
}
?>

<main class="book-fullscreen" oncontextmenu="return false;">

    <div id="service-data"
        data-service="<?= htmlspecialchars($serviceKey) ?>"
        data-baseurl="<?= BASE_URL ?>"
        style="display:none;"></div>

    <div class="book-container">

        <section class="glass-panel config-panel">
            <h2 class="section-title">Schedule & options</h2>

            <div class="calendar-wrapper">
                <div class="calendar-header">
                    <button type="button" id="prev-month"><i data-lucide="chevron-left"></i></button>
                    <h3 id="month-year-display">Loading...</h3>
                    <button type="button" id="next-month"><i data-lucide="chevron-right"></i></button>
                </div>
                <div class="calendar-weekdays">
                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                </div>
                <div id="calendar-grid" class="calendar-grid">
                </div>
            </div>

            <div class="addons-wrapper" id="dynamic-addons">
            </div>
        </section>

        <section class="glass-panel form-panel">
            <h1 class="book-title">Request: <?= htmlspecialchars($serviceMap[$serviceKey]) ?></h1>
            <p class="book-subtitle">Confirm your details and project scope.</p>

            <form method="POST" action="<?= BASE_URL ?>/backend/handlers/service_request_handler.php" id="booking-form" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="service_key" value="<?= htmlspecialchars($serviceKey) ?>">
                <input type="hidden" name="preferred_date" id="hidden_date" required>
                <input type="hidden" name="selected_options" id="hidden_options">

                <div class="input-group">
                    <i data-lucide="user" class="input-icon" aria-hidden="true"></i>
                    <input type="text" id="contact_name" name="contact_name" class="auth-input" placeholder=" " value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                    <label for="contact_name" class="auth-label">Full Name</label>
                </div>

                <div class="input-group">
                    <i data-lucide="mail" class="input-icon" aria-hidden="true"></i>
                    <input type="email" id="contact_email" name="contact_email" class="auth-input" placeholder=" " value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    <label for="contact_email" class="auth-label">Email Address</label>
                </div>

                <div class="input-group">
                    <i data-lucide="phone" class="input-icon" aria-hidden="true"></i>
                    <input type="text" id="contact_phone" name="contact_phone" class="auth-input" placeholder=" " value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    <label for="contact_phone" class="auth-label">Phone</label>
                </div>

                <div class="input-group">
                    <i data-lucide="map-pin" class="input-icon" aria-hidden="true"></i>
                    <input type="text" id="location" name="location" class="auth-input" placeholder=" ">
                    <label for="location" class="auth-label">Location (City/Venue)</label>
                </div>

                <div class="input-group textarea-group">
                    <textarea id="notes" name="notes" class="auth-input" placeholder=" " rows="4" required></textarea>
                    <label for="notes" class="auth-label">Project Details (Style, timing, requests...)</label>
                </div>

                <div class="live-price-box">
                    <span>Estimated Total:</span>
                    <strong id="live-total">...</strong>
                </div>

                <button type="submit" class="btn-primary auth-submit" id="submit-btn" disabled>Select a Date to Book</button>
            </form>
        </section>

    </div>
</main>

<script src="<?= BASE_URL ?>/js/book_service.js?v=<?= time() ?>" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>