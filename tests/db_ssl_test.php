<?php
/**
 * Lokalink - Test konfigurasi SSL/TLS database (Aiven)
 *
 * Test mandiri tanpa framework (project ini tidak memakai Composer/PHPUnit).
 * Jalankan: php tests/db_ssl_test.php
 *
 * Yang diuji (hanya konfigurasi/SSL, tidak mengubah behavior lain):
 *   1. DB_SSL_CA tidak diset        → tanpa opsi SSL (perilaku lama).
 *   2. DB_SSL_CA kosong             → tanpa opsi SSL.
 *   3. DB_SSL_CA berisi PEM multiline → file CA sementara dibuat dengan
 *      permission 0600 dan dihapus lagi (tidak tertinggal di disk).
 *   4. DB_SSL_CA bukan certificate   → null (fallback tanpa SSL + log).
 *   5. db_connect() dengan SSL tetap gagal secara graceful (null) saat
 *      server tidak tersedia — tidak ada fatal error.
 */

require_once __DIR__ . '/../src/db.php';

$failures = 0;

function check(string $name, bool $condition): void
{
    global $failures;
    if ($condition) {
        echo "PASS: {$name}\n";
    } else {
        $failures++;
        echo "FAIL: {$name}\n";
    }
}

const TEST_CA = "-----BEGIN CERTIFICATE-----\nMIIBszCCAVmgAwIBAgIUXdUMMYTESTCERTaivenCA0=\n-----END CERTIFICATE-----\n";

// --- Bersihkan env sebelum setiap skenario ---

function unset_db_ssl_ca(): void
{
    putenv('DB_SSL_CA');
    unset($_ENV['DB_SSL_CA'], $_SERVER['DB_SSL_CA']);
}

// 1. DB_SSL_CA tidak diset → tanpa SSL (perilaku lama tetap).
unset_db_ssl_ca();
check('Skenario 1: env tidak diset → db_ssl_ca_path() = null', db_ssl_ca_path() === null);
check('Skenario 1: env tidak diset → tanpa opsi SSL', db_ssl_pdo_options(null) === []);

// 2. DB_SSL_CA kosong → tanpa SSL.
putenv('DB_SSL_CA=');
check('Skenario 2: env kosong → db_ssl_ca_path() = null', db_ssl_ca_path() === null);

// 3. DB_SSL_CA berisi PEM multiline → file sementara 0600, dibersihkan.
putenv('DB_SSL_CA=' . TEST_CA);
$caFile = db_ssl_ca_path();
check('Skenario 3: PEM multiline → path file sementara dibuat', is_string($caFile) && is_file($caFile));
if (is_string($caFile)) {
    check('Skenario 3: file berada di temp sistem (di luar repo)', strpos($caFile, sys_get_temp_dir()) === 0);
    $perms = fileperms($caFile) & 0777;
    check('Skenario 3: permission file 0600', $perms === 0600);
    check('Skenario 3: isi file = certificate dari env', file_get_contents($caFile) === TEST_CA . "\n" || file_get_contents($caFile) === trim(TEST_CA) . "\n");
    check('Skenario 3: opsi PDO berisi opsi SSL_CA = path', db_ssl_pdo_options($caFile)[db_mysql_ssl_option('SSL_CA')] === $caFile);
    check('Skenario 3: opsi PDO memverifikasi server cert', db_ssl_pdo_options($caFile)[db_mysql_ssl_option('SSL_VERIFY_SERVER_CERT')] === true);
    // Simulasi pembersihan yang dilakukan db_connect() (finally).
    @unlink($caFile);
    check('Skenario 3: file sementara dihapus setelah dipakai', !is_file($caFile));
}

// 4. DB_SSL_CA bukan certificate PEM → null, tanpa file tertinggal.
$before = glob(sys_get_temp_dir() . '/lokalink-ca-*') ?: [];
putenv('DB_SSL_CA=bukan-certificate');
$invalid = db_ssl_ca_path();
check('Skenario 4: nilai bukan PEM → db_ssl_ca_path() = null', $invalid === null);
$after = glob(sys_get_temp_dir() . '/lokalink-ca-*') ?: [];
check('Skenario 4: tidak ada file sementara tertinggal', count($after) === count($before));

// 5. db_connect() dengan SSL tetap graceful (null) saat server tak tersedia.
putenv('DB_SSL_CA=' . TEST_CA);
$connected = db_connect();
check('Skenario 5: db_connect() gagal secara graceful (null), tanpa fatal error', $connected === null);
$leftover = glob(sys_get_temp_dir() . '/lokalink-ca-*') ?: [];
check('Skenario 5: file CA sementara dibersihkan oleh db_connect()', count($leftover) === count($before));
unset_db_ssl_ca();

echo "\n" . ($failures === 0 ? "SEMUA TEST SSL LULUS\n" : "{$failures} TEST GAGAL\n");
exit($failures === 0 ? 0 : 1);
