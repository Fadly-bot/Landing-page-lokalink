-- ============================================================
-- Lokalink - Migration: tambahkan status & admin_notes ke tabel leads
-- Jalankan manual: mysql -u root -p < database/migration-admin.sql
-- ============================================================

USE lokalink;

-- Tambah kolom status dengan default 'Baru' agar lead lama tetap valid.
ALTER TABLE leads
    ADD COLUMN IF NOT EXISTS status VARCHAR(30) NOT NULL DEFAULT 'Baru'
    AFTER source;

-- Tambah kolom admin_notes (opsional, nullable).
ALTER TABLE leads
    ADD COLUMN IF NOT EXISTS admin_notes TEXT NULL
    AFTER status;
