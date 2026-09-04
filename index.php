<?php
/**
 * Lokalink - Landing Page
 *
 * Halaman utama promosi. Semua konten section bisa diubah langsung di file ini.
 * Konfigurasi (nomor WhatsApp, kontak) ada di config/config.php.
 */

require_once __DIR__ . '/src/helpers.php';

// Muat konfigurasi: gunakan config.php jika ada, jika belum ada
// pakai config.example.php (nilai default aman) supaya situs tetap jalan.
if (file_exists(__DIR__ . '/config/config.php')) {
    require_once __DIR__ . '/config/config.php';
} else {
    require_once __DIR__ . '/config/config.example.php';
}

session_start();

// Status hasil form (dari actions/submit-lead.php)
$formStatus = $_GET['status'] ?? '';
$formReason = $_GET['reason'] ?? '';

require __DIR__ . '/includes/header.php';
?>

<!-- ===== HERO ===== -->
<section class="bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 sm:py-24 grid gap-10 md:grid-cols-2 items-center">
        <div>
            <p class="inline-block bg-brand-100 text-brand-700 text-sm font-semibold px-3 py-1 rounded-full mb-4">
                Digital Presence untuk Bisnis Lokal
            </p>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 leading-tight">
                Bisnis Anda mudah ditemukan, dipahami, dan dihubungi pelanggan.
            </h1>
            <p class="mt-5 text-lg text-slate-600 leading-relaxed">
                Lokalink membantu UMKM dan bisnis lokal membangun kehadiran digital —
                website sederhana, QR menu, sampai profil Google yang rapi.
                Tanpa ribet, tanpa perlu jago teknis.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <a href="#kontak" class="inline-block text-center bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3.5 rounded-xl transition">
                    Mulai Konsultasi Gratis
                </a>
                <a href="#layanan" class="inline-block text-center border border-slate-300 hover:border-brand-600 hover:text-brand-700 text-slate-700 font-semibold px-6 py-3.5 rounded-xl transition">
                    Lihat Layanan
                </a>
            </div>
            <p class="mt-4 text-sm text-slate-500">
                Konsultasi lewat WhatsApp — balasan cepat, tanpa komitmen.
            </p>
        </div>

        <!-- Ilustrasi preview sederhana -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-5">
            <div class="flex gap-1.5 mb-4" aria-hidden="true">
                <span class="w-3 h-3 rounded-full bg-red-400"></span>
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                <span class="w-3 h-3 rounded-full bg-green-400"></span>
            </div>
            <div class="space-y-3" aria-hidden="true">
                <!-- Mini header: logo + menu -->
                <div class="flex items-center justify-between bg-brand-50 border border-brand-100 rounded-lg px-3 py-2">
                    <span class="flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded bg-brand-600"></span>
                        <span class="h-2 w-12 bg-brand-300 rounded"></span>
                    </span>
                    <span class="hidden sm:flex items-center gap-2">
                        <span class="h-1.5 w-8 bg-slate-300 rounded"></span>
                        <span class="h-1.5 w-8 bg-slate-300 rounded"></span>
                        <span class="h-4 w-14 bg-brand-600 rounded-full"></span>
                    </span>
                </div>
                <!-- Judul + deskripsi -->
                <div class="pt-1 text-center">
                    <div class="h-4 w-3/4 mx-auto bg-slate-800 rounded"></div>
                    <div class="mt-2 space-y-1.5">
                        <div class="h-2 w-full bg-slate-200 rounded"></div>
                        <div class="h-2 w-5/6 mx-auto bg-slate-200 rounded"></div>
                    </div>
                </div>
                <!-- Kartu konten -->
                <div class="grid grid-cols-3 gap-3 pt-1">
                    <div class="rounded-lg border border-slate-200 p-2 space-y-1.5">
                        <div class="h-8 bg-brand-100 rounded"></div>
                        <div class="h-1.5 w-4/5 bg-slate-200 rounded"></div>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-2 space-y-1.5">
                        <div class="h-8 bg-brand-100 rounded"></div>
                        <div class="h-1.5 w-3/5 bg-slate-200 rounded"></div>
                    </div>
                    <div class="rounded-lg border border-slate-200 p-2 space-y-1.5">
                        <div class="h-8 bg-brand-100 rounded"></div>
                        <div class="h-1.5 w-2/3 bg-slate-200 rounded"></div>
                    </div>
                </div>
                <div class="text-center pt-1">
                    <span class="inline-block h-8 w-36 bg-brand-600 rounded-lg"></span>
                </div>
            </div>
            <p class="mt-4 text-xs text-slate-400 text-center">Contoh tampilan website bisnis sederhana</p>
        </div>
    </div>
</section>

<!-- ===== MASALAH BISNIS LOKAL ===== -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">
        Masalah yang Sering Dialami Bisnis Lokal
    </h2>
    <p class="mt-3 text-center text-slate-600 max-w-2xl mx-auto">
        Kalau hal ini terasa familiar, bisnis Anda mungkin kehilangan banyak pelanggan potensial.
    </p>
    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <p class="text-3xl mb-3" aria-hidden="true">🔍</p>
            <h3 class="font-semibold text-slate-900">Sulit ditemukan pelanggan</h3>
            <p class="mt-2 text-sm text-slate-600">Pelanggan mencari di Google, tapi bisnis Anda tidak muncul.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <p class="text-3xl mb-3" aria-hidden="true">🌐</p>
            <h3 class="font-semibold text-slate-900">Belum punya website</h3>
            <p class="mt-2 text-sm text-slate-600">Informasi bisnis hanya tersimpan di chat dan akun sosial media.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <p class="text-3xl mb-3" aria-hidden="true">📍</p>
            <h3 class="font-semibold text-slate-900">Informasi tersebar</h3>
            <p class="mt-2 text-sm text-slate-600">Menu, harga, jam buka, dan lokasi tidak ada dalam satu tempat yang jelas.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
            <p class="text-3xl mb-3" aria-hidden="true">💬</p>
            <h3 class="font-semibold text-slate-900">Susah dihubungi</h3>
            <p class="mt-2 text-sm text-slate-600">Pelanggan tertarik, tapi bingung harus menghubungi lewat mana.</p>
        </div>
    </div>
</section>

<!-- ===== SOLUSI LOKALINK ===== -->
<section class="bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">Solusi dari Lokalink</h2>
        <p class="mt-3 text-center text-slate-600 max-w-2xl mx-auto">
            Kami merapikan kehadiran digital bisnis Anda menjadi satu paket yang sederhana dan mudah dipahami pelanggan.
        </p>
        <div class="mt-10 grid gap-6 md:grid-cols-3">
            <div class="bg-white rounded-xl p-6 border border-slate-200">
                <h3 class="font-semibold text-brand-700">Ditemukan</h3>
                <p class="mt-2 text-sm text-slate-600">Website dan profil Google yang rapi, agar pelanggan menemukan bisnis Anda saat mencari.</p>
            </div>
            <div class="bg-white rounded-xl p-6 border border-slate-200">
                <h3 class="font-semibold text-brand-700">Dipahami &amp; Dipercaya</h3>
                <p class="mt-2 text-sm text-slate-600">Layanan, harga, jam buka, dan lokasi tampil jelas dalam satu halaman profesional.</p>
            </div>
            <div class="bg-white rounded-xl p-6 border border-slate-200">
                <h3 class="font-semibold text-brand-700">Dihubungi</h3>
                <p class="mt-2 text-sm text-slate-600">Tombol WhatsApp di tempat yang mudah dilihat, jadi pelanggan bisa langsung chat.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== LAYANAN ===== -->
<section id="layanan" class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">Layanan Kami</h2>
    <p class="mt-3 text-center text-slate-600 max-w-2xl mx-auto">
        Ambil yang dibutuhkan saja, atau gabungkan sesuai kebutuhan bisnis Anda.
    </p>
    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <article class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
            <h3 class="font-bold text-lg text-slate-900">Website Bisnis</h3>
            <p class="mt-2 text-sm text-slate-600">Website sederhana berisi layanan, harga, jam buka, lokasi, dan kontak. Tampil rapi di HP dan nyaman dibaca pelanggan.</p>
        </article>
        <article class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
            <h3 class="font-bold text-lg text-slate-900">QR Review Card</h3>
            <p class="mt-2 text-sm text-slate-600">Kartu berisi kode QR yang membawa pelanggan langsung ke halaman review Google bisnis Anda — memudahkan mereka yang memang ingin memberi ulasan.</p>
            <p class="mt-2 text-xs text-slate-500">Catatan: kartu ini hanya mempermudah pelanggan membuka halaman review. Lokalink tidak menjamin isi, jumlah, atau rating review.</p>
        </article>
        <article class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
            <h3 class="font-bold text-lg text-slate-900">Google Business Profile Setup</h3>
            <p class="mt-2 text-sm text-slate-600">Bantuan menyiapkan profil Google bisnis Anda agar informasi alamat, jam buka, dan foto tampil lengkap dan akurat.</p>
        </article>
        <article class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
            <h3 class="font-bold text-lg text-slate-900">QR Menu</h3>
            <p class="mt-2 text-sm text-slate-600">Menu kedai atau restoran dalam bentuk QR — pelanggan cukup scan untuk melihat daftar menu dan harga terbaru.</p>
        </article>
        <article class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
            <h3 class="font-bold text-lg text-slate-900">Integrasi WhatsApp</h3>
            <p class="mt-2 text-sm text-slate-600">Tombol WhatsApp terpasang di website dan QR, sehingga pelanggan terarah langsung ke chat bisnis Anda.</p>
        </article>
        <article class="bg-brand-600 text-white rounded-xl p-6 flex flex-col justify-center">
            <h3 class="font-bold text-lg">Butuh kombinasi layanan?</h3>
            <p class="mt-2 text-sm text-brand-100">Ceritakan kebutuhan bisnis Anda, kami bantu rekomendasikan yang paling pas.</p>
            <a href="#kontak" class="mt-4 inline-block text-center bg-white text-brand-700 font-semibold px-4 py-2.5 rounded-lg hover:bg-brand-50 transition">
                Konsultasi Sekarang
            </a>
        </article>
    </div>
</section>

<!-- ===== CARA KERJA ===== -->
<section id="cara-kerja" class="bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">Cara Kerja</h2>
        <p class="mt-3 text-center text-slate-600 max-w-2xl mx-auto">
            Prosesnya sederhana dan transparan. Anda selalu tahu pekerjaan sampai tahap mana.
        </p>
        <ol class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-5">
            <li class="bg-white rounded-xl p-6 border border-slate-200">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-600 text-white font-bold mb-3" aria-hidden="true">1</span>
                <h3 class="font-semibold text-slate-900">Konsultasi</h3>
                <p class="mt-2 text-sm text-slate-600">Ngobrol santai lewat WhatsApp atau form di bawah tentang bisnis Anda.</p>
            </li>
            <li class="bg-white rounded-xl p-6 border border-slate-200">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-600 text-white font-bold mb-3" aria-hidden="true">2</span>
                <h3 class="font-semibold text-slate-900">Tentukan Kebutuhan</h3>
                <p class="mt-2 text-sm text-slate-600">Kami bantu pilih layanan yang pas beserta estimasi biaya dan waktu.</p>
            </li>
            <li class="bg-white rounded-xl p-6 border border-slate-200">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-600 text-white font-bold mb-3" aria-hidden="true">3</span>
                <h3 class="font-semibold text-slate-900">Pengerjaan</h3>
                <p class="mt-2 text-sm text-slate-600">Kami kerjakan hasilnya, Anda tinggal menyiapkan info bisnis dan foto.</p>
            </li>
            <li class="bg-white rounded-xl p-6 border border-slate-200">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-600 text-white font-bold mb-3" aria-hidden="true">4</span>
                <h3 class="font-semibold text-slate-900">Review</h3>
                <p class="mt-2 text-sm text-slate-600">Anda cek hasilnya, ada revisi kami rapikan dulu.</p>
            </li>
            <li class="bg-white rounded-xl p-6 border border-slate-200 sm:col-span-2 lg:col-span-1">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-brand-600 text-white font-bold mb-3" aria-hidden="true">5</span>
                <h3 class="font-semibold text-slate-900">Siap Digunakan</h3>
                <p class="mt-2 text-sm text-slate-600">Website/produk diterima lengkap dengan panduan singkat.</p>
            </li>
        </ol>
    </div>
</section>

<!-- ===== KEUNGGULAN ===== -->
<section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">Kenapa Pilih Lokalink?</h2>
    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900">Bahasa manusia, bukan bahasa teknis</h3>
            <p class="mt-2 text-sm text-slate-600">Kami jelaskan semua dengan sederhana, tanpa istilah yang membingungkan.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900">Harga jelas di awal</h3>
            <p class="mt-2 text-sm text-slate-600">Biaya disepakati sebelum pengerjaan. Tidak ada biaya kejutan.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900">Cepat &amp; sederhana</h3>
            <p class="mt-2 text-sm text-slate-600">Fokus pada yang penting: pelanggan paham bisnis Anda dan bisa menghubungi.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900">Nyaman di HP</h3>
            <p class="mt-2 text-sm text-slate-600">Mayoritas pelanggan membuka lewat HP — tampilan selalu kami utamakan rapi di layar kecil.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900">Ramah UMKM</h3>
            <p class="mt-2 text-sm text-slate-600">Dibuat sesuai kebutuhan bisnis kecil: sederhana, jelas, dan mudah dirawat.</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-xl p-6">
            <h3 class="font-semibold text-slate-900">Dukungan setelah selesai</h3>
            <p class="mt-2 text-sm text-slate-600">Ada pertanyaan setelah serah terima? Tinggal chat WhatsApp kami.</p>
        </div>
    </div>
</section>

<!-- ===== PREVIEW / CONTOH HASIL ===== -->
<section id="preview" class="bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">Contoh Tampilan</h2>
        <p class="mt-3 text-center text-slate-600 max-w-2xl mx-auto">
            Ilustrasi sederhana bagaimana bisnis lokal tampil setelah dikerjakan Lokalink.
            Desain akhir menyesuaikan isi bisnis Anda.
        </p>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">

            <!-- Contoh: Barbershop -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-4 py-2 border-b border-slate-200 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                </div>
                <div class="p-5">
                    <p class="font-bold text-slate-900">Barbershop Rapi Pangkas</p>
                    <p class="mt-1 text-xs text-slate-500">Jam buka, harga layanan, lokasi, tombol WhatsApp</p>
                    <!-- Mini preview: barbershop -->
                    <div class="mt-3 rounded-lg border border-slate-200 overflow-hidden" aria-hidden="true">
                        <div class="flex items-center justify-between bg-slate-900 px-3 py-2">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 rounded-full bg-amber-400"></span>
                                <span class="h-1.5 w-14 bg-slate-400 rounded"></span>
                            </span>
                            <span class="hidden sm:block h-4 w-14 bg-amber-400 rounded-full"></span>
                        </div>
                        <div class="p-3 space-y-2">
                            <div class="h-2.5 w-3/4 bg-slate-800 rounded"></div>
                            <div class="h-1.5 w-full bg-slate-200 rounded"></div>
                            <div class="grid grid-cols-3 gap-2 pt-1">
                                <div class="rounded border border-slate-200 p-1.5 space-y-1">
                                    <div class="h-1.5 w-full bg-amber-300 rounded"></div>
                                    <div class="h-1.5 w-2/3 bg-slate-200 rounded"></div>
                                </div>
                                <div class="rounded border border-slate-200 p-1.5 space-y-1">
                                    <div class="h-1.5 w-full bg-amber-300 rounded"></div>
                                    <div class="h-1.5 w-1/2 bg-slate-200 rounded"></div>
                                </div>
                                <div class="rounded border border-slate-200 p-1.5 space-y-1">
                                    <div class="h-1.5 w-full bg-amber-300 rounded"></div>
                                    <div class="h-1.5 w-3/4 bg-slate-200 rounded"></div>
                                </div>
                            </div>
                            <span class="inline-block h-5 w-24 bg-amber-400 rounded-full"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contoh: Kedai -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-4 py-2 border-b border-slate-200 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                </div>
                <div class="p-5">
                    <p class="font-bold text-slate-900">Kedai Kopi Sudut</p>
                    <p class="mt-1 text-xs text-slate-500">QR Menu, menu dan harga terbaru, profil Google</p>
                    <!-- Mini preview: kedai kopi -->
                    <div class="mt-3 rounded-lg border border-slate-200 overflow-hidden" aria-hidden="true">
                        <div class="flex items-center justify-between bg-amber-900 px-3 py-2">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 rounded-full bg-amber-100"></span>
                                <span class="h-1.5 w-14 bg-amber-200/70 rounded"></span>
                            </span>
                            <span class="hidden sm:flex gap-1.5">
                                <span class="h-1.5 w-6 bg-amber-200/60 rounded"></span>
                                <span class="h-1.5 w-6 bg-amber-200/60 rounded"></span>
                            </span>
                        </div>
                        <div class="p-3 space-y-2">
                            <div class="h-2.5 w-2/3 bg-slate-800 rounded"></div>
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between rounded bg-amber-50 border border-amber-100 px-2 py-1.5">
                                    <span class="h-1.5 w-16 bg-amber-800/60 rounded"></span>
                                    <span class="h-1.5 w-6 bg-amber-800/40 rounded"></span>
                                </div>
                                <div class="flex items-center justify-between rounded bg-amber-50 border border-amber-100 px-2 py-1.5">
                                    <span class="h-1.5 w-12 bg-amber-800/60 rounded"></span>
                                    <span class="h-1.5 w-6 bg-amber-800/40 rounded"></span>
                                </div>
                                <div class="flex items-center justify-between rounded bg-amber-50 border border-amber-100 px-2 py-1.5">
                                    <span class="h-1.5 w-14 bg-amber-800/60 rounded"></span>
                                    <span class="h-1.5 w-6 bg-amber-800/40 rounded"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-0.5">
                                <span class="inline-block h-5 w-24 bg-amber-700 rounded-full"></span>
                                <span class="w-7 h-7 rounded-lg bg-emerald-500"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contoh: Jasa/Bengkel -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm sm:col-span-2 lg:col-span-1">
                <div class="px-4 py-2 border-b border-slate-200 flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-300" aria-hidden="true"></span>
                </div>
                <div class="p-5">
                    <p class="font-bold text-slate-900">Jasa &amp; Bengkel Terpercaya</p>
                    <p class="mt-1 text-xs text-slate-500">Daftar jasa, galeri pekerjaan, kontak WhatsApp</p>
                    <!-- Mini preview: jasa/bengkel -->
                    <div class="mt-3 rounded-lg border border-slate-200 overflow-hidden" aria-hidden="true">
                        <div class="flex items-center justify-between bg-blue-950 px-3 py-2">
                            <span class="flex items-center gap-1.5">
                                <span class="w-3.5 h-3.5 rounded bg-blue-400"></span>
                                <span class="h-1.5 w-14 bg-blue-200/70 rounded"></span>
                            </span>
                            <span class="hidden sm:block h-4 w-14 bg-blue-500 rounded-full"></span>
                        </div>
                        <div class="p-3 space-y-2">
                            <div class="h-2.5 w-3/5 bg-slate-800 rounded"></div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="rounded bg-blue-50 border border-blue-100 p-1.5 space-y-1">
                                    <div class="h-5 bg-blue-200 rounded"></div>
                                    <div class="h-1.5 w-4/5 bg-slate-200 rounded"></div>
                                </div>
                                <div class="rounded bg-blue-50 border border-blue-100 p-1.5 space-y-1">
                                    <div class="h-5 bg-blue-200 rounded"></div>
                                    <div class="h-1.5 w-3/5 bg-slate-200 rounded"></div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <div class="h-1.5 w-full bg-slate-200 rounded"></div>
                                <div class="h-1.5 w-2/3 bg-slate-200 rounded"></div>
                            </div>
                            <span class="inline-block h-5 w-24 bg-emerald-500 rounded-full"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FAQ ===== -->
<section id="faq" class="bg-slate-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-slate-900">Pertanyaan yang Sering Diajukan</h2>
        <div class="mt-10 space-y-4">
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-semibold text-slate-900 cursor-pointer">Berapa biayanya?</summary>
                <p class="mt-3 text-sm text-slate-600">Biaya tergantung layanan yang dipilih. Setelah konsultasi, Anda akan menerima rincian harga yang jelas sebelum pengerjaan dimulai. Tidak ada biaya tersembunyi.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-semibold text-slate-900 cursor-pointer">Berapa lama prosesnya?</summary>
                <p class="mt-3 text-sm text-slate-600">Untuk kebutuhan standar, biasanya beberapa hari kerja. Waktu pastinya tergantung kelengkapan informasi bisnis Anda dan akan kami sampaikan di awal.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-semibold text-slate-900 cursor-pointer">Saya tidak paham teknis, apa bisa?</summary>
                <p class="mt-3 text-sm text-slate-600">Sangat bisa. Justru itu fokus Lokalink: Anda cukup cerita tentang bisnis Anda, urusan teknis kami yang selesaikan. Panduan singkat juga disertakan.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-semibold text-slate-900 cursor-pointer">Apakah QR Review Card menjamin rating Google naik?</summary>
                <p class="mt-3 text-sm text-slate-600">Tidak. QR Review Card hanya mempermudah pelanggan membuka halaman review Google bisnis Anda. Isi dan jumlah review sepenuhnya ditentukan oleh pelanggan Anda sendiri.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-semibold text-slate-900 cursor-pointer">Setelah jadi, bagaimana kalau ingin mengubah isi website?</summary>
                <p class="mt-3 text-sm text-slate-600">Anda bisa hubungi kami lewat WhatsApp. Perubahan kecil (jam buka, harga, nomor kontak) dibantu dengan cepat. Panduan pengelolaan dasar juga kami sediakan.</p>
            </details>
            <details class="bg-white rounded-xl border border-slate-200 p-5">
                <summary class="font-semibold text-slate-900 cursor-pointer">Apakah bisa revisi?</summary>
                <p class="mt-3 text-sm text-slate-600">Bisa. Sebelum serah terima, Anda akan melihat hasilnya dan kami siap merapikan bagian yang kurang sesuai.</p>
            </details>
        </div>
    </div>
</section>

<!-- ===== CTA AKHIR + FORM KONSULTASI ===== -->
<section id="kontak" class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <div class="bg-brand-700 rounded-2xl p-8 sm:p-12 text-white">
        <h2 class="text-2xl sm:text-3xl font-bold text-center">
            Mulai Tampil Lebih Baik Secara Digital
        </h2>
        <p class="mt-3 text-center text-brand-100 max-w-2xl mx-auto">
            Ceritakan bisnis Anda. Kami bantu rekomendasikan kebutuhan digital yang paling sesuai — tanpa paksaan.
        </p>

        <div class="mt-10 grid gap-8 lg:grid-cols-5 items-start">

            <!-- Info kontak -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-semibold">Atau chat langsung</h3>
                <p class="mt-2 text-sm text-brand-100">Lebih suka ngobrol dulu? Hubungi kami lewat WhatsApp.</p>
                <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>"
                   class="mt-4 inline-block bg-white text-brand-700 font-semibold px-5 py-3 rounded-xl hover:bg-brand-50 transition"
                   rel="noopener">
                    Chat via WhatsApp
                </a>
                <p class="mt-6 text-sm text-brand-100">
                    Email: <a href="mailto:<?= e(CONTACT_EMAIL) ?>" class="underline hover:text-white"><?= e(CONTACT_EMAIL) ?></a>
                </p>
            </div>

            <!-- Form konsultasi -->
            <div class="lg:col-span-3 bg-white rounded-xl p-6 sm:p-8 text-slate-800">
                <h3 class="text-lg font-bold text-slate-900">Form Konsultasi</h3>

                <?php if ($formStatus === 'success'): ?>
                    <div class="mt-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm" role="status">
                        <strong>Terima kasih!</strong> Pesan Anda sudah kami terima dan akan ditindaklanjuti secepatnya lewat WhatsApp.
                    </div>
                <?php elseif ($formStatus === 'error'): ?>
                    <div class="mt-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm" role="alert">
                        <?php if ($formReason === 'csrf'): ?>
                            Sesi Anda sudah habis atau tidak valid. Silakan isi ulang form.
                        <?php else: ?>
                            Maaf, terjadi kendala saat memproses. Silakan coba lagi atau hubungi kami via WhatsApp.
                        <?php endif; ?>
                    </div>
                <?php elseif ($formStatus === 'invalid'): ?>
                    <div class="mt-4 rounded-lg bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 text-sm" role="alert">
                        <?= e(urldecode($formReason)) ?>
                    </div>
                <?php endif; ?>

                <form action="/api/submit-lead.php" method="post" class="mt-5 space-y-4">
                    <?= csrf_field() ?>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Nama <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" required maxlength="100"
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2.5 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none"
                                   autocomplete="name">
                        </div>
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-slate-700">Nama Bisnis <span class="text-red-500">*</span></label>
                            <input type="text" id="business_name" name="business_name" required maxlength="150"
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none"
                                   autocomplete="organization">
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="business_type" class="block text-sm font-medium text-slate-700">Jenis Bisnis <span class="text-red-500">*</span></label>
                            <select id="business_type" name="business_type" required
                                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none">
                                <option value="" disabled selected>Pilih jenis bisnis</option>
                                <option value="umkm-toko">UMKM / Toko</option>
                                <option value="kuliner">Kedai / Restoran</option>
                                <option value="jasa">Jasa Lokal</option>
                                <option value="barbershop-salon">Barbershop / Salon</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-slate-700">Nomor WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" id="whatsapp" name="whatsapp" required maxlength="20"
                                   pattern="[0-9+\-\s.()]{9,20}"
                                   title="Contoh: 081234567890"
                                   class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none"
                                   autocomplete="tel" inputmode="tel" placeholder="081234567890">
                        </div>
                    </div>

                    <div>
                        <label for="needs" class="block text-sm font-medium text-slate-700">Kebutuhan</label>
                        <select id="needs" name="needs"
                                class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 bg-white focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none">
                            <option value="">— Pilih kebutuhan (opsional) —</option>
                            <option value="website-bisnis">Website Bisnis</option>
                            <option value="qr-review-card">QR Review Card</option>
                            <option value="google-business">Google Business/Profile Setup</option>
                            <option value="qr-menu">QR Menu</option>
                            <option value="whatsapp">Integrasi WhatsApp</option>
                            <option value="belum-tahu">Belum tahu, butuh saran</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-slate-700">Pesan</label>
                        <textarea id="message" name="message" rows="4" maxlength="1000"
                                  placeholder="Ceritakan sedikit tentang bisnis Anda..."
                                  class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-brand-600 focus:ring-2 focus:ring-brand-200 outline-none"></textarea>
                    </div>

                    <button type="submit"
                            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold px-6 py-3.5 rounded-xl transition">
                        Kirim Permintaan Konsultasi
                    </button>
                    <p class="text-xs text-slate-500 text-center">Data Anda hanya digunakan untuk keperluan konsultasi.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
