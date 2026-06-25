<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Privacy policy | The Light Capsule';
$pageDescription = 'Privacy Policy outlining data collection and usage for The Light Capsule.';
$pageKeywords = 'privacy policy, GDPR, data protection, The Light Capsule';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_legal.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="legal-page-container" oncontextmenu="return false;">
    <div class="legal-bg-overlay" aria-hidden="true"></div>
    <div class="legal-wrapper">
        <header class="legal-header">
            <h1>Privacy policy</h1>
            <p>Effective date: March 2026</p>
        </header>

        <section class="legal-content glass-panel">
            <h2>1. Information we collect</h2>
            <p>We collect information necessary to provide our photography services and process orders. This includes:</p>
            <ul>
                <li><strong>Personal data:</strong> Name, email address, and phone number when you create an account, contact us, or place an order.</li>
                <li><strong>Order data:</strong> Billing details and session requirements. Payment processing is handled securely by third-party gateways; we do not store raw credit card data on our servers.</li>
                <li><strong>Technical data:</strong> IP addresses, browser types, and usage data to ensure our site functions securely.</li>
            </ul>

            <h2>2. How we use your information</h2>
            <p>Your data is used strictly to fulfill your orders, communicate regarding your photography sessions, and maintain the security of your client account.</p>

            <h2>3. Cookies and tracking</h2>
            <p>We use essential cookies to maintain secure login sessions and keep track of items in your cart. We do not use aggressive third-party marketing trackers.</p>

            <h2>4. Data retention and deletion</h2>
            <p>We retain your data only for as long as necessary to fulfill the purposes outlined in this policy or to comply with legal obligations. You may request the deletion of your account and personal data at any time.</p>

            <h2>5. Contact</h2>
            <p>For data inquiries or deletion requests, please contact: <a href="mailto:info@thelightcapsule.com">info@thelightcapsule.com</a></p>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>