<?php
/**
 * Lokalink - Konfigurasi utama
 * Salin file ini menjadi config/config.php lalu isi nilainya.
 * File config.php tidak di-commit ke repository (lihat .gitignore).
 */

// ---- Database (MySQL) ----
// Prioritas nilai (tinggi → rendah):
//   1. Environment Variable DB_* (production, mis. Vercel).
//   2. Nilai fallback local di bawah (untuk development, nilai lama).
//
// Vercel: set Environment Variables DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
// (opsional DB_ENABLED). JANGAN pernah menaruh credential production di file ini.
// Local: fallback 127.0.0.1/root tetap dipakai agar development tidak berubah.
// Keamanan production: jika berjalan di Vercel dan DB_HOST/DB_NAME/DB_USER
// belum tersedia dari Environment Variables, koneksi database otomatis
// DINONAKTIFKAN — production tidak akan diam-diam mencoba 127.0.0.1/root
// dengan password kosong.

if (!function_exists('lokalink_env')) {
    /**
     * Baca Environment Variable dari getenv()/$_ENV/$_SERVER.
     * Mengembalikan null jika variabel tidak diset atau string kosong.
     * (Pola sama dengan pembacaan CSRF_SECRET di src/helpers.php.)
     */
    function lokalink_env(string $key): ?string
    {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return (string) $value;
        }
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return (string) $_ENV[$key];
        }
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return (string) $_SERVER[$key];
        }
        return null;
    }
}

// Deteksi runtime serverless Vercel (VERCEL=1; VERCEL_ENV=production/preview/development).
$lokalinkOnVercel = lokalink_env('VERCEL') !== null;
$lokalinkEnvHost  = lokalink_env('DB_HOST');
$lokalinkEnvName  = lokalink_env('DB_NAME');
$lokalinkEnvUser  = lokalink_env('DB_USER');

// DB_ENABLED: env var lebih dulu (hanya '1','true','yes','on' = aktif).
// Di Vercel tanpa DB_* production env → matikan, jangan pakai kredensial lokal.
$lokalinkEnvEnabled = lokalink_env('DB_ENABLED');
if ($lokalinkEnvEnabled !== null) {
    $lokalinkDbEnabled = in_array(strtolower($lokalinkEnvEnabled), ['1', 'true', 'yes', 'on'], true);
} elseif ($lokalinkOnVercel && ($lokalinkEnvHost === null || $lokalinkEnvName === null || $lokalinkEnvUser === null)) {
    $lokalinkDbEnabled = false;
    error_log('[Lokalink] Database production belum dikonfigurasi: set Environment Variables DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS di Vercel (Lokalink).');
} else {
    $lokalinkDbEnabled = true;
}
if (!defined('DB_ENABLED')) {
    define('DB_ENABLED', $lokalinkDbEnabled);
}

// Nilai DB_*: Environment Variable (production) menang; fallback local hanya
// untuk development. DB_PASS boleh kosong (diset lewat env, bukan di sini).
if (!defined('DB_HOST')) {
    define('DB_HOST', $lokalinkEnvHost ?? '127.0.0.1');
}
if (!defined('DB_PORT')) {
    define('DB_PORT', lokalink_env('DB_PORT') ?? '3306');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', $lokalinkEnvName ?? 'lokalink');
}
if (!defined('DB_USER')) {
    define('DB_USER', $lokalinkEnvUser ?? 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', lokalink_env('DB_PASS') ?? '');
}

// ---- Informasi bisnis ----
// Nomor WhatsApp bisnis, format internasional tanpa tanda '+'
// contoh: 6285129984813
define('WHATSAPP_NUMBER', '6285129984813');
define('CONTACT_EMAIL', 'hello.lokalink@gmail.com');
define('BUSINESS_ADDRESS', 'Yogyakarta, Indonesia');

// ---- Situs ----
// Base URL tanpa garis miring di akhir, contoh: http://localhost:8000
// Biarkan kosong ('') untuk deteksi otomatis.
define('BASE_URL', '');

// ---- Keamanan (CSRF stateless untuk serverless, mis. Vercel) ----
// Secret untuk menandatangani CSRF token (HMAC-SHA256).
// WAJIB diset agar CSRF stateless benar-benar aman:
//   - Lokal: isi di config/config.php, contoh: define('CSRF_SECRET', 'string-acak-panjang-anda');
//   - Vercel: tambahkan Environment Variable bernama CSRF_SECRET.
// Generate nilai acak, contoh: php -r "echo bin2hex(random_bytes(32));"
define('CSRF_SECRET', '');
// Masa berlaku CSRF token dalam detik (opsional, default 7200 = 2 jam).
// define('CSRF_TOKEN_LIFETIME', 7200);
