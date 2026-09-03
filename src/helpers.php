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
 * Ambil / buat CSRF token untuk session ini.
 * Token disimpan di session server-side dan dicocokkan saat form disubmit.
 */
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Field hidden CSRF untuk form.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Validasi CSRF token dari POST. Mengembalikan true jika cocok.
 */
function csrf_verify(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * URL dasar situs (untuk canonical & link internal).
 */
function base_url(): string
{
    if (defined('BASE_URL') && BASE_URL !== '') {
        return rtrim(BASE_URL, '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
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
