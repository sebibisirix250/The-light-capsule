<?php

//FLASH MESSAGES
function setFlashMessage(string $type, string $message): void
{
    $_SESSION['flash_message'] = [
        'type' => $type,
        'text' => $message
    ];
}

function getFlashMessage(): ?array
{
    if (!isset($_SESSION['flash_message'])) {
        return null;
    }

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

    return $flash;
}

//CSRF PROTECTION - SECURITY
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verifyCsrfOrFail(?string $token, string $redirectUrl = ''): void
{
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    $isValid = is_string($token)
        && $token !== ''
        && hash_equals($sessionToken, $token);

    if ($isValid) {
        return;
    }

    setFlashMessage('error', 'Security token invalid or expired. Please try again.');

    $location = ($redirectUrl !== '') ? $redirectUrl : BASE_URL . '/index.php';
    header('Location: ' . $location);
    exit;
}

function rotateCsrfToken(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

//RATE LIMITING - SECURITY
function rateLimitStorageDir(): string
{
    return __DIR__ . '/../storage/rate_limits/';
}

function ensureRateLimitDirectoryExists(): void
{
    $dir = rateLimitStorageDir();
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

function getClientIpAddress(): string
{
    $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $value = trim((string)$_SERVER[$key]);
            if ($key === 'HTTP_X_FORWARDED_FOR') {
                $parts = explode(',', $value);
                $value = trim((string)($parts[0] ?? ''));
            }
            if ($value !== '') return $value;
        }
    }
    return '127.0.0.1';
}

function buildRateLimitKey(string $action, string $identifier = ''): string
{
    $ip = getClientIpAddress();
    return hash('sha256', $action . '|' . $ip . '|' . trim($identifier));
}

function rateLimitFilePath(string $action, string $identifier = ''): string
{
    ensureRateLimitDirectoryExists();
    return rateLimitStorageDir() . buildRateLimitKey($action, $identifier) . '.json';
}

function getRateLimitData(string $action, string $identifier = ''): array
{
    $path = rateLimitFilePath($action, $identifier);

    if (!is_file($path)) {
        return ['attempts' => [], 'blocked_until' => 0];
    }

    $data = json_decode((string)file_get_contents($path), true);
    return is_array($data) ? $data : ['attempts' => [], 'blocked_until' => 0];
}

function saveRateLimitData(string $action, string $identifier, array $data): void
{
    $path = rateLimitFilePath($action, $identifier);
    file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function isRateLimited(string $action, int $maxAttempts, int $windowSeconds, int $blockSeconds, string $identifier = ''): bool
{
    $now = time();
    $data = getRateLimitData($action, $identifier);

    if (($data['blocked_until'] ?? 0) > $now) {
        return true;
    }

    $attempts = array_filter(
        (array)($data['attempts'] ?? []),
        fn($ts) => is_numeric($ts) && $ts > ($now - $windowSeconds)
    );

    if (count($attempts) >= $maxAttempts) {
        $data['attempts'] = array_values($attempts);
        $data['blocked_until'] = $now + $blockSeconds;
        saveRateLimitData($action, $identifier, $data);
        return true;
    }

    return false;
}

function recordRateLimitAttempt(string $action, string $identifier = ''): void
{
    $data = getRateLimitData($action, $identifier);
    $data['attempts'][] = time();
    saveRateLimitData($action, $identifier, $data);
}

function clearRateLimit(string $action, string $identifier = ''): void
{
    $path = rateLimitFilePath($action, $identifier);
    if (is_file($path)) unlink($path);
}


//DATA SANITIZATION
function sanitizeHtml(string $data, int $maxLen = 0): string
{
    $data = trim($data);
    if ($maxLen > 0) $data = mb_substr($data, 0, $maxLen);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}


function forceIntRange($data, ?int $min = null, ?int $max = null): int
{
    $val = (int)$data;
    if ($min !== null && $val < $min) $val = $min;
    if ($max !== null && $val > $max) $val = $max;
    return $val;
}

