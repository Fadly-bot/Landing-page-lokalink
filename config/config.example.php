<?php
/**
 * Lokalink - Konfigurasi utama
 * Salin file ini menjadi config/config.php lalu isi nilainya.
 * File config.php tidak di-commit ke repository (lihat .gitignore).
 */

// ---- Database (MySQL) ----
// Jika database belum disiapkan, isi DB_ENABLED = false.
// Form tetap berfungsi (validasi jalan) namun data tidak disimpan.
define('DB_ENABLED', true);
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'lokalink');
define('DB_USER', 'root');
define('DB_PASS', '');

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
