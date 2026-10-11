CREATE DATABASE IF NOT EXISTS perpustakaan_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
 
USE perpustakaan_db;
 
CREATE TABLE IF NOT EXISTS buku_tamu (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nama          VARCHAR(100) NOT NULL,
    email         VARCHAR(100) NOT NULL,
    pesan         TEXT         NOT NULL,
    tanggal_kirim DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tanggal_kirim (tanggal_kirim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;