<?php
/**
 * Lokalink - Koneksi database (PDO)
 *
 * PDO = PHP Data Objects, cara standar PHP untuk terhubung ke database.
 * Kita memakai prepared statements agar aman dari SQL Injection.
 *
 * Fungsi ini mengembalikan objek PDO, atau null jika koneksi gagal.
 * Detail error TIDAK ditampilkan ke pengunjung (hanya dicatat ke log server).
 *
 * TLS/SSL (Aiven): jika Environment Variable DB_SSL_CA berisi CA certificate
 * (multiline), certificate ditulis ke file sementara dengan permission 0600
 * di direktori temp sistem dan dihapus setelah koneksi dibuat. Certificate
 * TIDAK pernah disimpan di repository — cukup set DB_SSL_CA sebagai
 * Environment Variable (mis. di Vercel/Aiven).
 */

// Fallback konfigurasi agar situs tetap jalan sebelum config.php dibuat.
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config.example.php';
}

/**
 * Baca Environment Variable DB_SSL_CA (CA certificate multiline).
 * Mengembalikan null jika tidak diset/kosong.
 */
function db_ssl_ca_env(): ?string
{
    if (function_exists('lokalink_env')) {
        return lokalink_env('DB_SSL_CA');
    }
    $value = getenv('DB_SSL_CA');
    if ($value !== false && $value !== '') {
        return (string) $value;
    }
    return null;
}

/**
 * Siapkan file CA certificate untuk PDO (MYSQL_ATTR_SSL_CA butuh path file).
 *
 * DB_SSL_CA berisi isi certificate (multiline) dari Environment Variable,
 * jadi kita tulis ke file sementara:
 *   - lokasi: sys_get_temp_dir() (di luar repository / document root),
 *   - permission: 0600 (hanya owner yang bisa baca),
 *   - pemilik file dihapus setelah koneksi PDO dibuat (lihat db_connect()).
 *
 * Mengembalikan path file sementara, atau null jika SSL tidak dikonfigurasi
 * atau certificate tidak valid (dengan pesan di log server).
 */
function db_ssl_ca_path(): ?string
{
    $ca = db_ssl_ca_env();
    if ($ca === null) {
        return null;
    }

    $ca = trim($ca);
    if ($ca === '') {
        return null;
    }

    if (strpos($ca, '-----BEGIN CERTIFICATE-----') === false) {
        error_log('[Lokalink] DB_SSL_CA diset tetapi tidak berisi certificate PEM yang valid (---BEGIN CERTIFICATE--- tidak ditemukan). Koneksi dilanjutkan tanpa SSL CA.');
        return null;
    }

    $path = tempnam(sys_get_temp_dir(), 'lokalink-ca-');
    if ($path === false) {
        error_log('[Lokalink] Gagal membuat file sementara untuk DB_SSL_CA. Koneksi dilanjutkan tanpa SSL CA.');
        return null;
    }

    $ok = file_put_contents($path, $ca . "\n") !== false;
    if ($ok) {
        $ok = chmod($path, 0600);
    }
    if (!$ok) {
        @unlink($path);
        error_log('[Lokalink] Gagal menulis DB_SSL_CA ke file sementara. Koneksi dilanjutkan tanpa SSL CA.');
        return null;
    }

    return $path;
}

/**
 * Opsi PDO tambahan untuk TLS/SSL. dipanggil dengan path CA (boleh null).
 * Server cert selalu diverifikasi terhadap CA agar identitas server Aiven
 * terjamin. Konstanta SSL PDO di-resolve via Pdo\Mysql (PHP 8.4+) dengan
 * fallback PDO::MYSQL_ATTR_* untuk PHP lebih lama.
 */
function db_ssl_pdo_options(?string $caFile): array
{
    if ($caFile === null) {
        return [];
    }
    return [
        db_mysql_ssl_option('SSL_CA')                => $caFile,
        db_mysql_ssl_option('SSL_VERIFY_SERVER_CERT') => true,
    ];
}

/**
 * Resolve konstanta opsi SSL PDO MySQL lintas versi PHP.
 * PHP 8.4+: Pdo\Mysql::ATTR_SSL_CA / ATTR_SSL_VERIFY_SERVER_CERT.
 * PHP lama : PDO::MYSQL_ATTR_SSL_CA / PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT.
 */
function db_mysql_ssl_option(string $name): int
{
    if (defined('Pdo\\Mysql::ATTR_' . $name)) {
        return (int) constant('Pdo\\Mysql::ATTR_' . $name);
    }
    return (int) constant('PDO::MYSQL_ATTR_' . $name);
}

function db_connect(): ?PDO
{
    if (!defined('DB_ENABLED') || !DB_ENABLED) {
        // Bedakan di log: "belum dikonfigurasi/dinonaktifkan" vs "koneksi gagal".
        error_log('[Lokalink] db_connect(): koneksi database dinonaktifkan (DB_ENABLED=false). Jika ini production, pastikan Environment Variables DB_* sudah diset.');
        return null;
    }

    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

    // TLS/SSL (Aiven): jika DB_SSL_CA diset, siapkan file CA sementara.
    $caFile = db_ssl_ca_path();
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ] + db_ssl_pdo_options($caFile);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Catat ke log server, jangan tampilkan detail ke user.
        error_log('[Lokalink] Koneksi database gagal: ' . $e->getMessage());
        return null;
    } finally {
        // File CA sementara sudah tidak dibutuhkan setelah koneksi dibuat
        // (berhasil maupun gagal) — pastikan tidak tertinggal di disk.
        if ($caFile !== null) {
            @unlink($caFile);
        }
    }
}
