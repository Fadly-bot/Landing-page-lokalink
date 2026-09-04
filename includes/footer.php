</main>

<!-- ===== FOOTER ===== -->
<footer class="bg-slate-900 text-slate-300 mt-20">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12 grid gap-10 md:grid-cols-3">
        <div>
            <img src="src/img/logo/logo.png" alt="Logo Lokalink" class="h-9 w-auto mb-4 bg-white rounded-lg p-1">
            <p class="text-sm leading-relaxed">
                Lokalink membantu bisnis lokal membangun kehadiran digital agar lebih mudah
                ditemukan, dipahami, dipercaya, dan dihubungi oleh pelanggan.
            </p>
        </div>

        <div>
            <h2 class="text-white font-semibold mb-3">Navigasi</h2>
            <ul class="space-y-2 text-sm">
                <li><a href="index.php" class="hover:text-white">Beranda</a></li>
                <li><a href="#layanan" class="hover:text-white">Layanan</a></li>
                <li><a href="#cara-kerja" class="hover:text-white">Cara Kerja</a></li>
                <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                <li><a href="#kontak" class="hover:text-white">Konsultasi</a></li>
            </ul>
        </div>

        <div>
            <h2 class="text-white font-semibold mb-3">Kontak</h2>
            <ul class="space-y-2 text-sm">
                <li>
                    <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" class="hover:text-white" rel="noopener">
                        WhatsApp: +<?= e(WHATSAPP_NUMBER) ?>
                    </a>
                </li>
                <li><a href="mailto:<?= e(CONTACT_EMAIL) ?>" class="hover:text-white"><?= e(CONTACT_EMAIL) ?></a></li>
                <li><?= e(BUSINESS_ADDRESS) ?></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-800">
        <p class="max-w-6xl mx-auto px-4 sm:px-6 py-5 text-xs text-slate-400">
            &copy; <?= date('Y') ?> Lokalink. Seluruh hak cipta dilindungi.
        </p>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
