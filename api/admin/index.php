<?php
/**
 * Lokalink - Vercel entry point for admin pages.
 *
 * Routes /admin/* requests to the appropriate admin PHP file via allowlist.
 * This file must be registered in vercel.json with src: "^/admin/(.*)$".
 */

$root = dirname(__DIR__, 2);
chdir($root);

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = '/' . ltrim($uri, '/');

// Allowlist: only these exact paths are routed to admin files.
$adminMap = [
    '/admin/login.php'   => $root . '/admin/login.php',
    '/admin/logout.php'  => $root . '/admin/logout.php',
    '/admin/detail.php'  => $root . '/admin/detail.php',
    '/admin/index.php'   => $root . '/admin/index.php',
    '/admin/'            => $root . '/admin/index.php',
    '/admin'             => $root . '/admin/index.php',
];

if (isset($adminMap[$uri])) {
    require $adminMap[$uri];
    exit;
}

// Unknown /admin/* path → 404.
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404</title></head><body><p>Halaman tidak ditemukan.</p></body></html>';
