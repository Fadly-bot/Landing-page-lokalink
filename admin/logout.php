<?php
/**
 * Lokalink - Admin Logout (POST + CSRF only)
 */
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/');
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    redirect('/admin/');
}

admin_clear_cookie();
redirect('/admin/login.php');
