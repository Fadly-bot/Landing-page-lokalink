# Lokalink — Landing Page Promosi

Landing page untuk bisnis **Lokalink — Digital Presence untuk Bisnis Lokal**.
Dibangun dengan **PHP Native + Tailwind CSS (CDN) + MySQL (PDO)**, tanpa framework.

## Fitur

- Landing page lengkap: Hero, Masalah, Solusi, Layanan, Cara Kerja, Keunggulan, FAQ, CTA, Form Konsultasi
- Form konsultasi yang **benar-benar diproses PHP**: validasi server-side + CSRF + disimpan ke tabel `leads` (MySQL/PDO)
- Responsive (mobile-first), SEO dasar, aksesibilitas dasar
- JavaScript minimal (hanya menu mobile)

## Kebutuhan

- PHP 7.4+ (ekstensi `pdo_mysql`)
- MySQL / MariaDB
- Browser untuk melihat hasil

## Cara Menjalankan

### 1. Siapkan database

Impor `database/schema.sql` ke MySQL:

```
mysql -u root -p < database/schema.sql
```

Atau lewat phpMyAdmin: buat database `lokalink` lalu impor file tersebut.

### 2. Konfigurasi

Salin `config/config.example.php` menjadi `config/config.php`, lalu edit:

- `DB_ENABLED`, `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`
- `WHATSAPP_NUMBER` (format: `6281234567890`, tanpa tanda `+`)
- `CONTACT_EMAIL`, `BUSINESS_ADDRESS`

> `config/config.php` berisi kredensial, jadi ada di `.gitignore` dan tidak di-commit.

### 3. Jalankan server

**Cara A — PHP built-in server (paling cepat):**

```
cd Landing-page-lokalink
php -S localhost:8000
```

Buka http://localhost:8000

**Cara B — XAMPP/Laragon:**

Letakkan folder ini di `htdocs/` lalu buka `http://localhost/Landing-page-lokalink/`.

> Jika `config/config.php` belum dibuat, situs tetap terbuka memakai nilai
> default dari `config.example.php`, namun penyimpanan lead ke database
> tidak akan berhasil sampai konfigurasi database diisi.

## Struktur Folder

```
├── index.php                  # Landing page (semua section)
├── actions/
│   └── submit-lead.php        # Proses form: validasi + simpan ke DB
├── assets/
│   └── js/main.js             # JS minimal (menu mobile)
├── config/
│   ├── config.example.php     # Contoh konfigurasi
│   ├── config.php             # Konfigurasi asli (tidak di-commit)
│   └── README.md
├── database/
│   └── schema.sql             # Skema tabel leads
├── includes/
│   ├── header.php             # <head>, SEO meta, navbar
│   └── footer.php             # Footer
├── src/
│   ├── db.php                 # Koneksi PDO
│   ├── helpers.php            # Fungsi escaping & CSRF
│   └── img/logo/              # Logo & favicon
└── PRD.md
```

## Cara Mengubah Konten

- **Teks section** — edit langsung `index.php`. Setiap section ditandai komentar, contoh: `<!-- ===== LAYANAN ===== -->`.
- **Layanan** — edit bagian `<section id="layanan">` di `index.php`. Setiap layanan adalah satu `<article>`.
- **Logo** — ganti file `src/img/logo/logo.svg` dan `favicon.svg`. Ukuran navbar diatur lewat class `h-9`.
- **Nomor WhatsApp & kontak** — edit `config/config.php` (`WHATSAPP_NUMBER`, `CONTACT_EMAIL`, `BUSINESS_ADDRESS`).
- **Warna brand** — ubah palet `brand:` di `includes/header.php` (konfigurasi Tailwind).

## Testing

Cek syntax PHP (harus tidak ada output = aman):

```
php -l index.php
php -l actions/submit-lead.php
php -l src/db.php
php -l src/helpers.php
php -l includes/header.php
php -l includes/footer.php
```

Uji form secara manual:

1. Buka `http://localhost:8000` → isi form → submit.
   - Jika DB aktif: muncul pesan sukses dan baris baru di tabel `leads`.
   - Jika DB mati/salah kredensial: muncul pesan error aman (detail hanya di log server).
2. Kosongkan field wajib lalu submit → muncul daftar error validasi.
3. Isi nomor WhatsApp dengan huruf → ditolak validasi server.

## Keamanan yang Diterapkan

| Ancaman | Mitigasi |
|---|---|
| SQL Injection | Prepared statements PDO, kredensial tidak pernah digabung ke string query |
| CSRF | Token session `hash_equals`, diverifikasi server-side di `actions/submit-lead.php` |
| XSS | Semua output di-escape dengan `e()` (htmlspecialchars). Data disimpan apa adanya, escaping saat output |
| Bocornya detail internal | Error PDO hanya dicatat ke `error_log`, user hanya melihat pesan umum |
| Manipulasi form | Validasi server-side: wajib, panjang maksimum, whitelist nilai enum, normalisasi nomor WA |

## Catatan

- **Logo saat ini adalah placeholder.** Jika aset resmi Lokalink tersedia, cukup timpa file di `src/img/logo/`.
- Tailwind dimuat via CDN untuk kemudahan; untuk produksi disarankan kompilasi Tailwind agar file CSS lebih kecil.
- Untuk produksi: aktifkan HTTPS, dan pastikan `display_errors = Off` di PHP.
