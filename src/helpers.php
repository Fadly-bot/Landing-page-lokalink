<?php
/**
 * Lokalink - Helper functions
 * Berisi fungsi keamanan: escaping output & CSRF token.
 */

/**
 * Escape output untuk konteks HTML.
 * Prinsip: VALIDATE INPUT -> STORE DATA -> ESCAPE OUTPUT.
 * Data disimpan apa adanya di database, dan di-escape saat ditampilkan.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Polyfill sederhana: jika ekstensi mbstring tidak aktif, pakai strlen.
// (Cukup untuk kebutuhan validasi panjang di situs ini.)
if (!function_exists('mb_strlen')) {
    function mb_strlen(string $value): int
    {
        return strlen($value);
    }
}

/**
 * Secret untuk HMAC CSRF token.
 *
 * STATELESS CSRF (aman untuk serverless seperti Vercel):
 * tidak menggunakan $_SESSION. Token terdiri dari:
 *   {nonce}.{expiry}.{hmac_sha256(nonce.expiry, secret)}
 * Server cukup memverifikasi HMAC dan masa berlaku token, tanpa state.
 *
 * Sumber secret (urutan prioritas):
 *   1. Konstanta CSRF_SECRET (didefinisikan di config/config.php).
 *   2. Environment variable CSRF_SECRET (getenv / $_ENV, mis. di Vercel).
 *
 * JANGAN hardcode secret di source code.
 */
function csrf_secret(): string
{
    if (defined('CSRF_SECRET') && CSRF_SECRET !== '') {
        return (string) CSRF_SECRET;
    }
    $env = getenv('CSRF_SECRET');
    if ($env !== false && $env !== '') {
        return (string) $env;
    }
    if (isset($_ENV['CSRF_SECRET']) && $_ENV['CSRF_SECRET'] !== '') {
        return (string) $_ENV['CSRF_SECRET'];
    }
    // Fallback darurat agar situs tetap berjalan di lokal tanpa config:
    // diturunkan dari kredensial aplikasi, bukan secret yang di-hardcode.
    $fallback = (defined('DB_HOST') ? DB_HOST : '')
        . '|' . (defined('DB_NAME') ? DB_NAME : '')
        . '|' . (defined('WHATSAPP_NUMBER') ? WHATSAPP_NUMBER : '')
        . '|lokalink-csrf-fallback';
    return hash('sha256', $fallback);
}

/**
 * Masa berlaku CSRF token (detik). Default 2 jam.
 */
function csrf_token_lifetime(): int
{
    return defined('CSRF_TOKEN_LIFETIME') ? (int) CSRF_TOKEN_LIFETIME : 7200;
}

/**
 * Buat CSRF token stateless:
 * nonce kriptografis acak + waktu kedaluwarsa, ditandatangani HMAC-SHA256.
 */
function csrf_token(): string
{
    $nonce  = bin2hex(random_bytes(32));
    $expiry = (string) (time() + csrf_token_lifetime());
    $payload = $nonce . '.' . $expiry;
    $mac = hash_hmac('sha256', $payload, csrf_secret());
    return $payload . '.' . $mac;
}

/**
 * Field hidden CSRF untuk form.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Validasi CSRF token dari POST tanpa session.
 * Token ditolak jika: format salah, HMAC tidak cocok, atau sudah kedaluwarsa.
 */
function csrf_verify(?string $token): bool
{
    if ($token === null || $token === '') {
        return false;
    }
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }
    [$nonce, $expiry, $mac] = $parts;

    // Nonce & expiry harus heksadesimal / numerik agar format terkendali.
    if (!preg_match('/^[0-9a-f]{64}$/', $nonce) || !preg_match('/^\d{1,12}$/', $expiry)) {
        return false;
    }
    // Kedaluwarsa: token lama otomatis ditolak.
    if ((int) $expiry < time()) {
        return false;
    }
    $expected = hash_hmac('sha256', $nonce . '.' . $expiry, csrf_secret());
    return hash_equals($expected, $mac);
}

/**
 * URL dasar situs (untuk canonical & link internal).
 */
function base_url(): string
{
    if (defined('BASE_URL') && BASE_URL !== '') {
        return rtrim(BASE_URL, '/');
    }
    // Deteksi HTTPS: perhatikan juga header proxy (mis. Vercel/Cloudflare)
    // yang meneruskan protokol asli via X-Forwarded-Proto.
    $forwardedProto = strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $forwardedProto === 'https'
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
    $scheme = $isHttps ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base   = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    return $scheme . '://' . $host . $base;
}

/**
 * Redirect lalu berhenti.
 */
function redirect(string $url): void
{
    header('Location: ' . $url, true, 302);
    exit;
}
