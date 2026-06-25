<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Refund policy | The Light Capsule';
$pageDescription = 'Refund and cancellation policy for digital products and photography sessions.';
$pageKeywords = 'refund policy, cancellation policy, The Light Capsule';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_legal.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="legal-page-container" oncontextmenu="return false;">
    <div class="legal-bg-overlay" aria-hidden="true"></div>
    <div class="legal-wrapper">
        <header class="legal-header">
            <h1>Refund & cancellation policy</h1>
            <p>Effective date: March 2026</p>
        </header>

        <section class="legal-content glass-panel">
            <h2>1. Digital products</h2>
            <p>Due to the nature of digital goods (e.g., fine art prints, presets, digital downloads), all sales are final. We do not offer refunds once the digital file has been accessed or downloaded.</p>

            <h2>2. Photography sessions</h2>
            <ul>
                <li><strong>Cancellations:</strong> Sessions cancelled with less than 48 hours' notice may forfeit the initial deposit.</li>
                <li><strong>Rescheduling:</strong> We allow one complimentary reschedule if requested at least 48 hours prior to the session time.</li>
                <li><strong>No shows:</strong> Failure to arrive at the agreed location without prior notice will result in the cancellation of the session without a refund.</li>
            </ul>

            <h2>3. Exceptions</h2>
            <p>If a session cannot be fulfilled due to extreme weather or circumstances entirely on our end, a full refund or priority reschedule will be offered.</p>

            <h2>4. Contact for disputes</h2>
            <p>If you believe there has been an error with your billing, please contact us immediately at <a href="mailto:info@thelightcapsule.com">info@thelightcapsule.com</a>.</p>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>