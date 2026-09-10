<?php
/**
 * Lokalink - Admin Authentication (stateless, Vercel-compatible)
 *
 * Uses signed cookies with HMAC-SHA256 — no server-side session needed.
 * Requires environment variables: ADMIN_USERNAME, ADMIN_PASSWORD_HASH, ADMIN_AUTH_SECRET.
 */

require_once __DIR__ . '/../src/helpers.php';

if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config.example.php';
}

define('ADMIN_COOKIE_NAME', 'lokalink_admin');
define('ADMIN_COOKIE_LIFETIME', 86400); // 24 hours

function admin_env(string $key): ?string
{
    $v = getenv($key);
    if ($v !== false && $v !== '') {
        return (string) $v;
    }
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    return null;
}

function admin_auth_secret(): string
{
    $secret = admin_env('ADMIN_AUTH_SECRET');
    if ($secret !== null && $secret !== '') {
        return $secret;
    }
    return csrf_secret();
}

function admin_set_cookie(string $username): void
{
    $payload = $username . '|' . (string) (time() + ADMIN_COOKIE_LIFETIME);
    $mac = hash_hmac('sha256', $payload, admin_auth_secret());
    $value = $payload . '|' . $mac;
    setcookie(ADMIN_COOKIE_NAME, $value, [
        'expires'  => time() + ADMIN_COOKIE_LIFETIME,
        'path'     => '/',
        'httponly'  => true,
        'secure'   => (!empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
        'samesite' => 'Strict',
    ]);
}

function admin_clear_cookie(): void
{
    if (isset($_COOKIE[ADMIN_COOKIE_NAME])) {
        setcookie(ADMIN_COOKIE_NAME, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly'  => true,
            'secure'   => (!empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'),
            'samesite' => 'Strict',
        ]);
    }
}

function admin_verify_cookie(): bool
{
    if (!isset($_COOKIE[ADMIN_COOKIE_NAME]) || $_COOKIE[ADMIN_COOKIE_NAME] === '') {
        return false;
    }
    $cookie = $_COOKIE[ADMIN_COOKIE_NAME];
    $parts = explode('|', $cookie);
    if (count($parts) !== 3) {
        return false;
    }
    [$username, $expiry, $mac] = $parts;
    if (!preg_match('/^\d{1,12}$/', $expiry)) {
        return false;
    }
    if ((int) $expiry < time()) {
        return false;
    }
    $expected = hash_hmac('sha256', $username . '|' . $expiry, admin_auth_secret());
    return hash_equals($expected, $mac);
}

function admin_get_user(): ?string
{
    if (!admin_verify_cookie()) {
        return null;
    }
    $parts = explode('|', $_COOKIE[ADMIN_COOKIE_NAME]);
    return $parts[0];
}

function admin_auth_require(): void
{
    if (admin_get_user() === null) {
        header('Location: /admin/login.php', true, 302);
        exit;
    }
}
