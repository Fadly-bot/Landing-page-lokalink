<?php
/**
 * Lokalink - Proses form konsultasi (submit lead)
 *
 * Alur keamanan:
 * 1. Ambil input dari $_POST (dianggap tidak terpercaya).
 * 2. Verifikasi CSRF token (server-side).
 * 3. Normalisasi & validasi server-side (field wajib, panjang, format WA).
 * 4. Simpan dengan prepared statement PDO (aman dari SQL Injection).
 * 5. Redirect dengan status (pesanan/error di tampilkan via query string).
 *
 * Data asli disimpan apa adanya; escaping dilakukan saat output (lihat helpers.php).
 */

require_once __DIR__ . '/../src/helpers.php';

// Fallback konfigurasi agar situs tetap jalan sebelum config.php dibuat.
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
} else {
    require_once __DIR__ . '/../config/config.example.php';
}

require_once __DIR__ . '/../src/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php#kontak');
}

// ---- 1. CSRF protection ----
if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    redirect('index.php?status=error&reason=csrf#kontak');
}

// ---- 2. Ambil input & normalisasi ----
$name         = trim((string) ($_POST['name'] ?? ''));
$businessName = trim((string) ($_POST['business_name'] ?? ''));
$businessType = trim((string) ($_POST['business_type'] ?? ''));
$whatsapp     = trim((string) ($_POST['whatsapp'] ?? ''));
$needs        = trim((string) ($_POST['needs'] ?? ''));
$message      = trim((string) ($_POST['message'] ?? ''));

$errors = [];

// ---- 3. Validasi server-side ----
if ($name === '') {
    $errors[] = 'Nama wajib diisi.';
} elseif (mb_strlen($name) > 100) {
    $errors[] = 'Nama maksimal 100 karakter.';
}

if ($businessName === '') {
    $errors[] = 'Nama bisnis wajib diisi.';
} elseif (mb_strlen($businessName) > 150) {
    $errors[] = 'Nama bisnis maksimal 150 karakter.';
}

$allowedTypes = ['umkm-toko', 'kuliner', 'jasa', 'barbershop-salon', 'lainnya'];
if ($businessType === '' || !in_array($businessType, $allowedTypes, true)) {
    $errors[] = 'Jenis bisnis tidak valid.';
}

// Nomor WhatsApp: hanya angka, 9-16 digit setelah normalisasi awalan 0/62/+.
$waNormalized = preg_replace('/[\s\-\.\(\)]+/', '', $whatsapp);
$waDigits     = preg_replace('/^\+/', '', $waNormalized);
$waDigits     = preg_replace('/\D/', '', $waDigits);
if (preg_match('/^0/', $waDigits)) {
    $waDigits = '62' . substr($waDigits, 1); // ubah awalan 0 menjadi 62
}
if ($waDigits === '' || strlen($waDigits) < 9 || strlen($waDigits) > 16) {
    $errors[] = 'Nomor WhatsApp tidak valid. Contoh: 081234567890.';
}

$allowedNeeds = ['website-bisnis', 'qr-review-card', 'google-business', 'qr-menu', 'whatsapp', 'belum-tahu'];
if ($needs !== '' && !in_array($needs, $allowedNeeds, true)) {
    $errors[] = 'Pilihan kebutuhan tidak valid.';
}

if (mb_strlen($message) > 1000) {
    $errors[] = 'Pesan maksimal 1000 karakter.';
}

// Jika ada error validasi, kirim kembali dengan daftar error di query string.
if (!empty($errors)) {
    $reason = rawurlencode(implode(' ', $errors));
    redirect('index.php?status=invalid&reason=' . $reason . '#kontak');
}

// ---- 4. Simpan ke database (prepared statement) ----
$pdo = db_connect();

if ($pdo === null) {
    // Database tidak tersedia / kredensial salah.
    // Pesan aman untuk user, detail error hanya di log server.
    redirect('index.php?status=error&reason=db#kontak');
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO leads (name, business_name, business_type, whatsapp, needs, message, source)
         VALUES (:name, :business_name, :business_type, :whatsapp, :needs, :message, :source)'
    );
    $stmt->execute([
        ':name'           => $name,
        ':business_name'  => $businessName,
        ':business_type'  => $businessType,
        ':whatsapp'       => $waDigits,
        ':needs'          => $needs !== '' ? $needs : null,
        ':message'        => $message !== '' ? $message : null,
        ':source'         => 'landing-page',
    ]);
} catch (PDOException $e) {
    error_log('[Lokalink] Gagal menyimpan lead: ' . $e->getMessage());
    redirect('index.php?status=error&reason=db#kontak');
}

// ---- 5. Sukses ----
redirect('index.php?status=success#kontak');
