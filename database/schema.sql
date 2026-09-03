-- ============================================================
-- Lokalink - Skema database
-- Jalankan di MySQL/MariaDB, contoh:
--   mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS lokalink
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lokalink;

-- Tabel untuk menyimpan lead dari form konsultasi landing page.
CREATE TABLE IF NOT EXISTS leads (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(100)  NOT NULL,
  business_name   VARCHAR(150)  NOT NULL,
  business_type   VARCHAR(50)   NOT NULL,
  whatsapp        VARCHAR(30)   NOT NULL,
  needs           VARCHAR(100)  NULL,
  message         TEXT          NULL,
  source          VARCHAR(50)   NULL DEFAULT 'landing-page',
  created_at      TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
