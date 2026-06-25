<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'Terms & conditions | The Light Capsule';
$pageDescription = 'Terms and Conditions for using The Light Capsule website and services.';
$pageKeywords = 'terms and conditions, website terms, The Light Capsule';
$pageAuthor = 'Ontijt Sébastian';

$pageCss = ['style_legal.css'];

require_once __DIR__ . '/../includes/page_start.php';
require_once __DIR__ . '/../includes/header.php';
?>

<main class="legal-page-container" oncontextmenu="return false;">
    <div class="legal-bg-overlay" aria-hidden="true"></div>
    <div class="legal-wrapper">
        <header class="legal-header">
            <h1>Terms & conditions</h1>
            <p>Effective date: March 2026</p>
        </header>

        <section class="legal-content glass-panel">
            <h2>1. Agreement to terms</h2>
            <p>By accessing or using The Light Capsule website, booking a session, or purchasing digital prints, you agree to be bound by these Terms and Conditions.</p>

            <h2>2. User accounts</h2>
            <p>You are responsible for maintaining the confidentiality of your account credentials. You agree to accept responsibility for all activities that occur under your account.</p>

            <h2>3. Photography sessions & bookings</h2>
            <p>All bookings are subject to availability and confirmation by Ontijt Sébastian. Specific session terms, delivery timelines, and usage rights will be defined in your individual client agreement.</p>

            <h2>4. Purchases and payments</h2>
            <p>All prices are listed in Euros (€). We reserve the right to refuse or cancel any order if fraud or an unauthorized transaction is suspected.</p>

            <h2>5. Limitation of liability</h2>
            <p>The Light Capsule shall not be liable for any indirect, incidental, or consequential damages resulting from the use of our website or services.</p>
        </section>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>