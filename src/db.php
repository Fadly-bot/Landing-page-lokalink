<?php
/**
 * Lokalink - Koneksi database (PDO)
 *
 * PDO = PHP Data Objects, cara standar PHP untuk terhubung ke database.
 * Kita memakai prepared statements agar aman dari SQL Injection.
 *
 * Fungsi ini mengembalikan objek PDO, atau null jika koneksi gagal.
 * Detail error TIDAK ditampilkan ke pengunjung (hanya dicatat ke log server).
 */

// Fallback konfigurasi agar situs tetap jalan sebelum config.php dibuat.
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config.example.php';
}

function db_connect(): ?PDO
{
    if (!defined('DB_ENABLED') || !DB_ENABLED) {
        return null;
    }

    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Catat ke log server, jangan tampilkan detail ke user.
        error_log('[Lokalink] Koneksi database gagal: ' . $e->getMessage());
        return null;
    }
}
