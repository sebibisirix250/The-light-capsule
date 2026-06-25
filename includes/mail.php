<?php

//PULLS MAIL CONFIGURATION
function mailConfig(): array
{
    static $config = null;

    if ($config === null) {
        $config = require __DIR__ . '/mail_config.php';
    }

    return $config;
}

//DIRECTORY FOR LOCAL LOGS
function ensureMailLogDirectoryExists(string $dir): void
{
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

//LAYOUT
function buildEmailHtmlLayout(string $title, string $contentHtml): string
{
    return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#222;">
    <div style="max-width:640px;margin:30px auto;background:#ffffff;border:1px solid #ddd;">
        <div style="padding:24px 28px;background:#111;color:#fff;">
            <h1 style="margin:0;font-size:22px;">' . htmlspecialchars(mailConfig()['from_name'] ?? 'Website', ENT_QUOTES, 'UTF-8') . '</h1>
        </div>
        <div style="padding:28px;line-height:1.6;font-size:15px;">
            ' . $contentHtml . '
        </div>
    </div>
</body>
</html>';
}

//PLAIN TEXT -> HTML FORMATTER
function nl2p(string $text): string
{
    $paragraphs = preg_split("/\R{2,}/", trim($text));
    $html = '';

    foreach ($paragraphs as $paragraph) {
        $html .= '<p style="margin:0 0 16px 0;">' . nl2br(htmlspecialchars(trim($paragraph), ENT_QUOTES, 'UTF-8')) . '</p>';
    }

    return $html;
}

//SANDBOX UTILITY
function writeMailLog(array $payload): bool
{
    $config = mailConfig();
    $logDir = $config['log_dir'] ?? (__DIR__ . '/../emails/');

    ensureMailLogDirectoryExists($logDir);

    $filename = $logDir . 'unread_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.log';

    $content = '';
    $content .= "TO: " . ($payload['to'] ?? '') . "\n";
    $content .= "SUBJECT: " . ($payload['subject'] ?? '') . "\n";
    $content .= "FROM: " . ($payload['from_email'] ?? '') . "\n";
    $content .= "FROM_NAME: " . ($payload['from_name'] ?? '') . "\n";
    $content .= "TYPE: " . ($payload['type'] ?? '') . "\n";
    $content .= "CREATED_AT: " . date('Y-m-d H:i:s') . "\n";
    $content .= str_repeat('-', 60) . "\n";
    $content .= "TEXT VERSION:\n";
    $content .= ($payload['text_body'] ?? '') . "\n";
    $content .= str_repeat('-', 60) . "\n";
    $content .= "HTML VERSION:\n";
    $content .= ($payload['html_body'] ?? '') . "\n";

    return file_put_contents($filename, $content) !== false;
}

//LIVE SMTP CONFIGURATION
function sendMailViaSmtp(array $payload): bool
{
    $phpMailerClass = '\\PHPMailer\\PHPMailer\\PHPMailer';

    if (!class_exists($phpMailerClass)) {
        return false;
    }

    $config = mailConfig();
    $smtp = $config['smtp'] ?? [];

    try {
        $mail = new $phpMailerClass(true);

        $mail->isSMTP();
        $mail->Host = $smtp['host'] ?? '';
        $mail->Port = (int)($smtp['port'] ?? 587);
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['username'] ?? '';
        $mail->Password = $smtp['password'] ?? '';

        $encryption = $smtp['encryption'] ?? 'tls';
        if ($encryption === 'tls') {
            $mail->SMTPSecure = 'tls';
        } elseif ($encryption === 'ssl') {
            $mail->SMTPSecure = 'ssl';
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($payload['from_email'], $payload['from_name']);
        $mail->addAddress($payload['to']);
        $mail->Subject = $payload['subject'];
        $mail->Body = $payload['html_body'];
        $mail->AltBody = $payload['text_body'];
        $mail->isHTML(true);

        return $mail->send();
    } catch (Throwable $e) {
        return false;
    }
}

//MODE SWITCH
function sendMailMessage(
    string $to,
    string $subject,
    string $textBody,
    string $type = 'generic',
    ?string $htmlBody = null
): bool {
    $config = mailConfig();

    if ($htmlBody === null) {
        $htmlBody = buildEmailHtmlLayout($subject, nl2p($textBody));
    }

    $payload = [
        'to' => $to,
        'subject' => $subject,
        'text_body' => $textBody,
        'html_body' => $htmlBody,
        'type' => $type,
        'from_email' => $config['from_email'] ?? 'no-reply@example.com',
        'from_name' => $config['from_name'] ?? 'Website',
    ];

    $mode = $config['mode'] ?? 'disabled';

    if ($mode === 'disabled') {
        return true;
    }

    if ($mode === 'log') {
        return writeMailLog($payload);
    }

    if ($mode === 'smtp') {
        return sendMailViaSmtp($payload);
    }

    return false;
}

//EVENT BASED E-MAILS

//ORDERS - CLIENT
function sendOrderConfirmationEmail(
    string $to,
    string $contactName,
    int $orderId,
    string $orderType,
    float $totalPrice
): bool {
    $subject = "Order confirmation #{$orderId}";

    $text = "Hello {$contactName},\n\n";
    $text .= "Your order has been received successfully.\n\n";
    $text .= "Order ID: #{$orderId}\n";
    $text .= "Order Type: {$orderType}\n";
    $text .= "Total: RON" . number_format($totalPrice, 2) . "\n\n";
    $text .= "We will contact you if any further action is needed.\n\n";
    $text .= "Thank you.";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8') . ',</p>
         <p style="margin:0 0 16px 0;">Your order has been received successfully.</p>
         <p style="margin:0 0 16px 0;">
            <strong>Order ID:</strong> #' . $orderId . '<br>
            <strong>Order Type:</strong> ' . htmlspecialchars($orderType, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Total:</strong> RON' . number_format($totalPrice, 2) . '
         </p>
         <p style="margin:0 0 16px 0;">We will contact you if any further action is needed.</p>
         <p style="margin:0;">Thank you.</p>'
    );

    return sendMailMessage($to, $subject, $text, 'order_confirmation', $html);
}

//ORDERS - ADMIN
function sendAdminNewOrderNotification(
    int $orderId,
    string $orderType,
    string $customerName,
    string $customerEmail,
    float $totalPrice
): bool {
    $config = mailConfig();
    $to = $config['orders_email'] ?? ($config['admin_email'] ?? '');

    if ($to === '') {
        return true;
    }

    $subject = "New order received #{$orderId}";

    $text = "A new order was created.\n\n";
    $text .= "Order ID: #{$orderId}\n";
    $text .= "Order Type: {$orderType}\n";
    $text .= "Customer: {$customerName}\n";
    $text .= "Email: {$customerEmail}\n";
    $text .= "Total: RON" . number_format($totalPrice, 2) . "\n";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">A new order was created.</p>
         <p style="margin:0;">
            <strong>Order ID:</strong> #' . $orderId . '<br>
            <strong>Order Type:</strong> ' . htmlspecialchars($orderType, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Customer:</strong> ' . htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Email:</strong> ' . htmlspecialchars($customerEmail, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Total:</strong> RON' . number_format($totalPrice, 2) . '
         </p>'
    );

    return sendMailMessage($to, $subject, $text, 'admin_new_order', $html);
}

//SERVICE REQUEST - CLIENT
function sendServiceRequestConfirmationEmail(
    string $to,
    string $contactName,
    string $serviceTitle,
    int $orderId
): bool {
    $subject = "Service request received #{$orderId}";

    $text = "Hello {$contactName},\n\n";
    $text .= "Your service request has been received.\n\n";
    $text .= "Request ID: #{$orderId}\n";
    $text .= "Service: {$serviceTitle}\n\n";
    $text .= "We will review your request and get back to you.\n\n";
    $text .= "Thank you.";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8') . ',</p>
         <p style="margin:0 0 16px 0;">Your service request has been received.</p>
         <p style="margin:0 0 16px 0;">
            <strong>Request ID:</strong> #' . $orderId . '<br>
            <strong>Service:</strong> ' . htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8') . '
         </p>
         <p style="margin:0 0 16px 0;">We will review your request and get back to you.</p>
         <p style="margin:0;">Thank you.</p>'
    );

    return sendMailMessage($to, $subject, $text, 'service_request_confirmation', $html);
}

//SERVICE REQUEST - ADMIN
function sendAdminServiceRequestNotification(
    int $orderId,
    string $serviceTitle,
    string $contactName,
    string $contactEmail,
    string $notes
): bool {
    $config = mailConfig();
    $to = $config['bookings_email'] ?? ($config['admin_email'] ?? '');

    if ($to === '') {
        return true;
    }

    $subject = "New service request #{$orderId}";

    $text = "A new service request was submitted.\n\n";
    $text .= "Request ID: #{$orderId}\n";
    $text .= "Service: {$serviceTitle}\n";
    $text .= "Name: {$contactName}\n";
    $text .= "Email: {$contactEmail}\n\n";
    $text .= "Details:\n{$notes}\n";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">A new service request was submitted.</p>
         <p style="margin:0 0 16px 0;">
            <strong>Request ID:</strong> #' . $orderId . '<br>
            <strong>Service:</strong> ' . htmlspecialchars($serviceTitle, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Name:</strong> ' . htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Email:</strong> ' . htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8') . '
         </p>
         <div style="padding:14px;background:#f6f6f6;border:1px solid #ddd;white-space:pre-wrap;">' . htmlspecialchars($notes, ENT_QUOTES, 'UTF-8') . '</div>'
    );

    return sendMailMessage($to, $subject, $text, 'admin_service_request', $html);
}

//ORDER STATUS
function sendOrderStatusUpdateEmail(
    string $to,
    string $contactName,
    int $orderId,
    string $status,
    string $paymentStatus
): bool {
    $subject = "Order update #{$orderId}";

    $text = "Hello {$contactName},\n\n";
    $text .= "Your order has been updated.\n\n";
    $text .= "Order ID: #{$orderId}\n";
    $text .= "Status: {$status}\n";
    $text .= "Payment Status: {$paymentStatus}\n\n";
    $text .= "Thank you.";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8') . ',</p>
         <p style="margin:0 0 16px 0;">Your order has been updated.</p>
         <p style="margin:0 0 16px 0;">
            <strong>Order ID:</strong> #' . $orderId . '<br>
            <strong>Status:</strong> ' . htmlspecialchars($status, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Payment Status:</strong> ' . htmlspecialchars($paymentStatus, ENT_QUOTES, 'UTF-8') . '
         </p>
         <p style="margin:0;">Thank you.</p>'
    );

    return sendMailMessage($to, $subject, $text, 'order_status_update', $html);
}


//CONTACT
function sendContactInquiryConfirmationEmail(
    string $to,
    string $contactName,
    string $subjectLine
): bool {
    $subject = "We received your message";

    $text = "Hello {$contactName},\n\n";
    $text .= "We received your message successfully.\n\n";
    $text .= "Subject: {$subjectLine}\n\n";
    $text .= "We will get back to you as soon as possible.\n\n";
    $text .= "Thank you.";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8') . ',</p>
         <p style="margin:0 0 16px 0;">We received your message successfully.</p>
         <p style="margin:0 0 16px 0;"><strong>Subject:</strong> ' . htmlspecialchars($subjectLine, ENT_QUOTES, 'UTF-8') . '</p>
         <p style="margin:0 0 16px 0;">We will get back to you as soon as possible.</p>
         <p style="margin:0;">Thank you.</p>'
    );

    return sendMailMessage($to, $subject, $text, 'contact_confirmation', $html);
}


//CONTACT - ADMIN
function sendAdminContactInquiryNotification(
    string $contactName,
    string $contactEmail,
    string $subjectLine,
    string $message
): bool {
    $config = mailConfig();
    $to = $config['admin_email'] ?? '';

    if ($to === '') {
        return true;
    }

    $subject = "New contact inquiry";

    $text = "A new contact inquiry was submitted.\n\n";
    $text .= "Name: {$contactName}\n";
    $text .= "Email: {$contactEmail}\n";
    $text .= "Subject: {$subjectLine}\n\n";
    $text .= "Message:\n{$message}\n";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">A new contact inquiry was submitted.</p>
         <p style="margin:0 0 16px 0;">
            <strong>Name:</strong> ' . htmlspecialchars($contactName, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Email:</strong> ' . htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Subject:</strong> ' . htmlspecialchars($subjectLine, ENT_QUOTES, 'UTF-8') . '
         </p>
         <div style="padding:14px;background:#f6f6f6;border:1px solid #ddd;white-space:pre-wrap;">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>'
    );

    return sendMailMessage($to, $subject, $text, 'admin_contact_inquiry', $html);
}

//SIGN IN WELCOME EMAIL
function sendWelcomeEmail(
    string $to,
    string $fullName
): bool {
    $subject = "Welcome to " . (mailConfig()['from_name'] ?? 'Website');

    $text = "Hello {$fullName},\n\n";
    $text .= "Your account has been created successfully.\n\n";
    $text .= "You can now sign in and use your account.\n\n";
    $text .= "Welcome aboard.";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">Hello ' . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . ',</p>
         <p style="margin:0 0 16px 0;">Your account has been created successfully.</p>
         <p style="margin:0 0 16px 0;">You can now sign in and use your account.</p>
         <p style="margin:0;">Welcome aboard.</p>'
    );

    return sendMailMessage($to, $subject, $text, 'welcome_email', $html);
}

//NEW SIGN IN - ADMIN
function sendAdminNewUserNotification(
    string $fullName,
    string $email
): bool {
    $config = mailConfig();
    $to = $config['admin_email'] ?? '';

    if ($to === '') {
        return true;
    }

    $subject = "New user registration";

    $text = "A new user registered.\n\n";
    $text .= "Name: {$fullName}\n";
    $text .= "Email: {$email}\n";

    $html = buildEmailHtmlLayout(
        $subject,
        '<p style="margin:0 0 16px 0;">A new user registered.</p>
         <p style="margin:0;">
            <strong>Name:</strong> ' . htmlspecialchars($fullName, ENT_QUOTES, 'UTF-8') . '<br>
            <strong>Email:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '
         </p>'
    );

    return sendMailMessage($to, $subject, $text, 'admin_new_user', $html);
}
