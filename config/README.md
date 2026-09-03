# Struktur folder konfigurasi

Folder ini berisi konfigurasi website.

## File

- `config.php` — konfigurasi utama (dimuat oleh `index.php`).
- `config.example.php` — contoh file konfigurasi untuk disalin.

## Cara setup

1. Salin `config.example.php` menjadi `config.php`:

   ```
   cp config/config.example.php config/config.php
   ```

2. Edit `config/config.php`:
   - Isi kredensial database MySQL Anda.
   - Ganti nomor WhatsApp di `WHATSAPP_NUMBER` dengan nomor bisnis Anda
     (format internasional tanpa tanda `+`, contoh: `6281234567890`).

> **Catatan keamanan:** `config.php` berisi kredensial database sehingga
> di-daftarkan di `.gitignore` dan TIDAK boleh di-commit ke repository.
