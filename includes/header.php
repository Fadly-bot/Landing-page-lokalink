<?php
// Header: <head>, SEO meta, Open Graph, dan navbar.
// Dipanggil dari index.php setelah config & helpers dimuat.
require_once __DIR__ . '/../src/helpers.php';

$canonical = base_url() . '/';
$ogImage   = base_url() . '/src/img/logo/logo.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokalink — Digital Presence untuk Bisnis Lokal | Website UMKM & QR Menu</title>
    <meta name="description" content="Lokalink membantu UMKM dan bisnis lokal membangun kehadiran digital: website bisnis, QR menu, QR review card, Google Business Profile, dan integrasi WhatsApp.">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <!-- Open Graph dasar -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Lokalink — Digital Presence untuk Bisnis Lokal">
    <meta property="og:description" content="Website bisnis, QR menu, dan QR review card untuk UMKM. Sederhana, cepat, dan terjangkau.">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta property="og:image:width" content="856">
    <meta property="og:image:height" content="291">
    <meta property="og:image:alt" content="Logo Lokalink">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="theme-color" content="#0d9488">
    <meta name="google-site-verification" content="rz4jnPTh8oPi4eXDaIkvYkMCTGojhIaGm3oS7H2oZuM" />

    <?php
    // JSON-LD structured data (Organization, WebSite, FAQPage).
    // Konten FAQ mengambil dari pertanyaan yang sama di index.php (jangan diubah terpisah).
    $siteUrl     = base_url() . '/';
    $orgLogo     = base_url() . '/src/img/logo/logo.png';
    $jsonLdFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    $jsonLdOrganization = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Organization',
        'name'        => 'Lokalink',
        'url'         => $siteUrl,
        'logo'        => $orgLogo,
        'areaServed'  => 'Cimahi, Indonesia',
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'contactType' => 'customer service',
            'telephone'   => '+' . e(WHATSAPP_NUMBER),
            'email'       => e(CONTACT_EMAIL),
            'url'         => 'https://wa.me/' . e(WHATSAPP_NUMBER),
        ],
    ];

    $jsonLdWebSite = [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'Lokalink',
        'url'      => $siteUrl,
    ];

    $jsonLdFaqPage = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => 'Berapa biayanya?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Biaya tergantung layanan yang dipilih. Setelah konsultasi, Anda akan menerima rincian harga yang jelas sebelum pengerjaan dimulai. Tidak ada biaya tersembunyi.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Berapa lama prosesnya?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Untuk kebutuhan standar, biasanya beberapa hari kerja. Waktu pastinya tergantung kelengkapan informasi bisnis Anda dan akan kami sampaikan di awal.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Saya tidak paham teknis, apa bisa?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Sangat bisa. Justru itu fokus Lokalink: Anda cukup cerita tentang bisnis Anda, urusan teknis kami yang selesaikan. Panduan singkat juga disertakan.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Apakah QR Review Card menjamin rating Google naik?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Tidak. QR Review Card hanya mempermudah pelanggan membuka halaman review Google bisnis Anda. Isi dan jumlah review sepenuhnya ditentukan oleh pelanggan Anda sendiri.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Setelah jadi, bagaimana kalau ingin mengubah isi website?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Anda bisa hubungi kami lewat WhatsApp. Perubahan kecil (jam buka, harga, nomor kontak) dibantu dengan cepat. Panduan pengelolaan dasar juga kami sediakan.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Apakah bisa revisi?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Bisa. Sebelum serah terima, Anda akan melihat hasilnya dan kami siap merapikan bagian yang kurang sesuai.',
                ],
            ],
        ],
    ];
    ?>
    <script type="application/ld+json"><?= json_encode($jsonLdOrganization, $jsonLdFlags) ?></script>
    <script type="application/ld+json"><?= json_encode($jsonLdWebSite, $jsonLdFlags) ?></script>
    <script type="application/ld+json"><?= json_encode($jsonLdFaqPage, $jsonLdFlags) ?></script>

    <link rel="icon" type="image/png" href="src/img/logo/favicon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4',
                            400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e',
                            900: '#134e4a'
                        }
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-white text-slate-800 antialiased">

<!-- Skip link untuk navigasi keyboard -->
<a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:bg-brand-600 focus:text-white focus:px-4 focus:py-2 focus:rounded">Langsung ke konten</a>

<!-- ===== NAVBAR ===== -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
    <nav class="max-w-6xl mx-auto px-4 sm:px-6" aria-label="Navigasi utama">
        <div class="flex items-center justify-between h-16">
            <a href="index.php" class="flex items-center" aria-label="Beranda Lokalink">
                <img src="src/img/logo/logo.png" alt="Logo Lokalink" class="h-9 w-auto">
            </a>

            <!-- Menu desktop -->
            <ul class="hidden md:flex items-center gap-6 text-sm font-medium">
                <li><a href="index.php" class="text-slate-700 hover:text-brand-600">Beranda</a></li>
                <li><a href="#layanan" class="text-slate-700 hover:text-brand-600">Layanan</a></li>
                <li><a href="#cara-kerja" class="text-slate-700 hover:text-brand-600">Cara Kerja</a></li>
                <li><a href="#faq" class="text-slate-700 hover:text-brand-600">FAQ</a></li>
                <li>
                    <a href="#kontak" class="inline-block bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        Mulai Konsultasi
                    </a>
                </li>
            </ul>

            <!-- Tombol menu mobile -->
            <button id="menu-toggle" type="button"
                    class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100"
                    aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu navigasi">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <!-- Menu mobile -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <ul class="flex flex-col gap-1 text-base font-medium">
                <li><a href="index.php" class="block px-3 py-2 rounded-lg hover:bg-slate-100">Beranda</a></li>
                <li><a href="#layanan" class="block px-3 py-2 rounded-lg hover:bg-slate-100">Layanan</a></li>
                <li><a href="#cara-kerja" class="block px-3 py-2 rounded-lg hover:bg-slate-100">Cara Kerja</a></li>
                <li><a href="#faq" class="block px-3 py-2 rounded-lg hover:bg-slate-100">FAQ</a></li>
                <li class="pt-2">
                    <a href="#kontak" class="block text-center bg-brand-600 hover:bg-brand-700 text-white px-4 py-3 rounded-lg font-semibold">
                        Mulai Konsultasi
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<main id="konten">
