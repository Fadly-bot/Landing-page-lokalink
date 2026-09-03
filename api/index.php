<?php
/**
 * Vercel entrypoint — thin wrapper for the existing root index.php.
 *
 * Vercel's PHP runtime (vercel-php) only executes files under /api,
 * so this file acts as the serverless entrypoint and forwards execution
 * to the original landing page. It does NOT duplicate any logic.
 */

// __DIR__ = <project>/api  → project root is one level up.
$root = dirname(__DIR__);

// Make relative asset paths (src/img/..., assets/..., includes/...) resolve
// correctly, because the PHP runtime's working directory differs from the
// document root on Vercel.
chdir($root);

// Execute the existing root index.php unchanged.
require $root . '/index.php';
