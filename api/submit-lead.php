<?php
/**
 * Lokalink - Vercel entry point untuk submit lead.
 *
 * Wrapper tipis: logika backend asli tetap di actions/submit-lead.php.
 * Pada Vercel, hanya fungsi di dalam direktori api/ yang dieksekusi,
 * jadi file ini hanya me-require handler aslinya.
 */

require __DIR__ . '/../actions/submit-lead.php';
