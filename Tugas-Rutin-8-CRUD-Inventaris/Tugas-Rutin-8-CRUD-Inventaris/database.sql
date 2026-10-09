-- =========================================================
-- TUGAS RUTIN 8
-- CRUD INVENTARIS BARANG
-- Database: inventaris_db
-- =========================================================

CREATE DATABASE IF NOT EXISTS inventaris_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE inventaris_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS supplier;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------
-- Tabel kategori
-- ---------------------------------------------------------
CREATE TABLE kategori (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(60) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel supplier
-- ---------------------------------------------------------
CREATE TABLE supplier (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telepon VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabel produk
-- ---------------------------------------------------------
CREATE TABLE produk (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(30) NOT NULL UNIQUE,
    nama_produk VARCHAR(100) NOT NULL,
    kategori_id INT UNSIGNED NOT NULL,
    supplier_id INT UNSIGNED NOT NULL,
    stok INT UNSIGNED NOT NULL DEFAULT 0,
    harga DECIMAL(14,2) UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_produk_kategori
        FOREIGN KEY (kategori_id)
        REFERENCES kategori(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_produk_supplier
        FOREIGN KEY (supplier_id)
        REFERENCES supplier(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Seed: minimal 5 data per tabel
-- ---------------------------------------------------------

INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'),
('Alat Tulis'),
('Perlengkapan Kantor'),
('Aksesori Komputer'),
('Perangkat Jaringan');

INSERT INTO supplier (nama_supplier, email, telepon) VALUES
('PT Sinar Teknologi', 'sinar.teknologi@example.com', '081234567801'),
('CV Maju Bersama', 'maju.bersama@example.com', '081234567802'),
('PT Digital Nusantara', 'digital.nusantara@example.com', '081234567803'),
('CV Sarana Komputer', 'sarana.komputer@example.com', '081234567804'),
('PT Solusi Data', 'solusi.data@example.com', '081234567805');

INSERT INTO produk
(kode_produk, nama_produk, kategori_id, supplier_id, stok, harga)
VALUES
('EL-001', 'Keyboard Mechanical', 4, 1, 15, 425000),
('EL-002', 'Mouse Wireless', 4, 2, 28, 185000),
('AT-001', 'Buku Catatan A5', 2, 3, 40, 35000),
('KO-001', 'Kursi Kerja Ergonomis', 3, 4, 8, 1250000),
('JN-001', 'Router WiFi Dual Band', 5, 5, 12, 575000),
('EL-003', 'Webcam Full HD', 1, 1, 10, 495000),
('AT-002', 'Paket Pulpen Gel', 2, 2, 60, 45000),
('JN-002', 'Kabel LAN Cat6 10m', 5, 5, 25, 85000);

-- Verifikasi data seed
SELECT 'kategori' AS tabel, COUNT(*) AS jumlah_data FROM kategori
UNION ALL
SELECT 'supplier', COUNT(*) FROM supplier
UNION ALL
SELECT 'produk', COUNT(*) FROM produk;
