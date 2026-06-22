CREATE DATABASE IF NOT EXISTS inventaris CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventaris;

-- TABEL: user
CREATE TABLE IF NOT EXISTS user (
    id_user    INT AUTO_INCREMENT PRIMARY KEY,
    nama       VARCHAR(100) NULL,
    nim        VARCHAR(30)  NULL UNIQUE,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABEL: alat
CREATE TABLE IF NOT EXISTS alat (
    id_alat     INT AUTO_INCREMENT PRIMARY KEY,
    nama_alat   VARCHAR(100) NOT NULL,
    kode_alat   VARCHAR(30)  NULL UNIQUE,
    kategori    ENUM('Komputer','Elektronika','Jaringan','Lainnya') NOT NULL DEFAULT 'Lainnya',
    kondisi     VARCHAR(50)  NULL,
    lokasi      VARCHAR(100) NULL,
    deskripsi   TEXT         NULL,
    status_alat ENUM('tersedia','rusak') NOT NULL DEFAULT 'tersedia',
    stok        INT NOT NULL DEFAULT 0,
    foto        VARCHAR(255) NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABEL: peminjaman
CREATE TABLE IF NOT EXISTS peminjaman (
    id_pinjam          INT AUTO_INCREMENT PRIMARY KEY,
    id_user             INT NOT NULL,
    id_alat             INT NOT NULL,
    jumlah              INT NOT NULL DEFAULT 1,
    status               ENUM('menunggu','dipinjam','dikembalikan','terlambat','ditolak') NOT NULL DEFAULT 'menunggu',
    tgl_pinjam          DATE NOT NULL,
    tgl_kembali         DATE NOT NULL,
    tgl_kembali_aktual  DATE NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_alat) REFERENCES alat(id_alat) ON DELETE CASCADE
) ENGINE=InnoDB;

-- DATA AWAL: user

-- Password: admin123
INSERT INTO user (nama, nim, username, password, role) VALUES
('Administrator Lab', NULL, 'admin', '$2y$10$Mg06ngoBHpgpqsgvmcxDruCsp0G/MWelQrh5a/.TNF6jQb3KmM0Yu', 'admin');

-- Password: user123
INSERT INTO user (nama, nim, username, password, role) VALUES
('Saya', 'H1A024001', 'user1', '$2y$10$GHcWyNlx/THpBrgo/sM8g.xPqOZ1dZFVU3MB.KFbpJ0k7cIksKJ8S', 'user');

-- DATA AWAL: alat 
INSERT INTO alat
(nama_alat, kode_alat, kategori, kondisi, lokasi, deskripsi, status_alat, stok)
VALUES

('Sensor DHT22', 'ALT-001', 'Elektronika', 'Baik', 'Lemari C - Rak 2',
'Sensor suhu dan kelembaban digital dengan akurasi tinggi yang digunakan pada praktikum IoT, monitoring lingkungan, dan sistem otomasi berbasis mikrokontroler.',
'tersedia', 10),

('ESP32 DevKit V1', 'ALT-002', 'Komputer', 'Baik', 'Lemari C - Rak 1',
'Board mikrokontroler ESP32 dengan fitur Wi-Fi dan Bluetooth bawaan yang digunakan untuk praktikum Internet of Things (IoT), embedded system, dan komunikasi data.',
'tersedia', 8),

('Dioda Kit', 'ALT-003', 'Elektronika', 'Baik', 'Lemari B - Rak 2',
'Kit dioda berisi berbagai jenis dioda seperti 1N4007 dan 1N4148 yang digunakan untuk praktikum penyearah, proteksi rangkaian, dan elektronika dasar.',
'tersedia', 15),

('Solder + Besi Solder', 'ALT-004', 'Elektronika', 'Baik', 'Lemari B - Rak 1',
'Perangkat untuk menyambung komponen elektronika pada PCB menggunakan timah panas dalam kegiatan perakitan dan perbaikan rangkaian.',
'tersedia', 10),

('Breadboard', 'ALT-005', 'Elektronika', 'Baik', 'Lemari B - Rak 1',
'Papan rangkaian tanpa solder yang digunakan untuk merancang dan menguji rangkaian elektronika sementara.',
'tersedia', 15),

('Arduino Uno R3', 'ALT-006', 'Komputer', 'Baik', 'Lemari C - Rak 1',
'Board mikrokontroler berbasis ATmega328P yang digunakan dalam praktikum mikrokontroler, sistem tertanam, dan otomasi.',
'tersedia', 8),

('Push Button (Pack)', 'ALT-007', 'Elektronika', 'Baik', 'Lemari C - Rak 2',
'Paket tombol tekan (push button) yang digunakan sebagai input digital pada rangkaian elektronika dan proyek mikrokontroler.',
'tersedia', 20),

('Kabel Jumper (Set)', 'ALT-008', 'Elektronika', 'Baik', 'Lemari B - Rak 2',
'Set kabel jumper male-male, male-female, dan female-female untuk menghubungkan komponen pada breadboard dan modul elektronik.',
'tersedia', 20),

('Resistor 220ohm', 'ALT-009', 'Elektronika', 'Baik', 'Lemari B - Rak 3',
'Resistor dengan nilai hambatan 220 Ohm yang digunakan untuk pembatas arus pada LED dan rangkaian elektronika dasar.',
'tersedia', 10),

('LED Assorted Pack', 'ALT-010', 'Elektronika', 'Baik', 'Lemari B - Rak 3',
'Paket LED berbagai warna dan ukuran yang digunakan sebagai indikator visual dan praktikum elektronika dasar.',
'tersedia', 30),

('Transistor BC547 (Pack)', 'ALT-011', 'Elektronika', 'Baik', 'Lemari B - Rak 3',
'Transistor NPN serbaguna yang digunakan pada praktikum penguat sinyal dan rangkaian switching sederhana.',
'tersedia', 20),

('IC 555 Timer', 'ALT-012', 'Elektronika', 'Baik', 'Lemari B - Rak 4',
'Integrated Circuit (IC) timer yang digunakan untuk praktikum osilator, pulse generator, dan rangkaian pewaktu.',
'tersedia', 20),

('LCD 16x2', 'ALT-013', 'Elektronika', 'Baik', 'Lemari C - Rak 2',
'Modul layar LCD 16x2 karakter untuk menampilkan data, menu, dan informasi pada proyek mikrokontroler.',
'tersedia', 6),

('Sensor Ultrasonik HC-SR04', 'ALT-014', 'Elektronika', 'Baik', 'Lemari C - Rak 2',
'Sensor jarak berbasis gelombang ultrasonik yang digunakan untuk pengukuran jarak pada proyek robotika dan otomasi.',
'tersedia', 8),

('Motor Servo SG90', 'ALT-015', 'Elektronika', 'Baik', 'Lemari C - Rak 2',
'Motor servo mini dengan sudut rotasi hingga 180 derajat yang digunakan pada proyek robotika dan sistem kendali.',
'tersedia', 7),

('Switch Hub 8 Port', 'ALT-016', 'Jaringan', 'Baik', 'Lemari D - Rak 1',
'Perangkat jaringan yang digunakan untuk menghubungkan beberapa komputer dalam satu jaringan LAN pada praktikum jaringan komputer.',
'tersedia', 6),

('Kabel LAN UTP (Roll)', 'ALT-017', 'Jaringan', 'Baik', 'Lemari D - Rak 1',
'Gulungan kabel UTP Cat6 yang digunakan untuk praktikum instalasi jaringan dan pembuatan kabel LAN.',
'tersedia', 5),

('MikroTik RouterBoard', 'ALT-018', 'Jaringan', 'Baik', 'Lemari D - Rak 2',
'Perangkat router MikroTik yang digunakan untuk praktikum konfigurasi routing, manajemen jaringan, firewall, dan hotspot.',
'tersedia', 3),

('Tang Crimping RJ45', 'ALT-019', 'Jaringan', 'Baik', 'Lemari D - Rak 2',
'Alat untuk memasang konektor RJ45 pada kabel UTP saat praktikum instalasi dan perawatan jaringan komputer.',
'tersedia', 4),

('Kapasitor Kit', 'ALT-020', 'Elektronika', 'Baik', 'Lemari B - Rak 4',
'Kit kapasitor keramik dan elektrolit dengan berbagai nilai yang digunakan dalam praktikum elektronika dan mikrokontroler.',
'tersedia', 10),

('Konektor RJ45 (Pack)', 'ALT-021', 'Jaringan', 'Baik', 'Lemari D - Rak 2',
'Paket konektor RJ45 yang digunakan untuk terminasi kabel UTP pada praktikum pembuatan kabel jaringan.',
'tersedia', 30),

('LAN Cable Tester', 'ALT-022', 'Jaringan', 'Baik', 'Lemari D - Rak 3',
'Alat penguji kabel LAN UTP dengan indikator LED 1-8 untuk memeriksa urutan kabel, mendeteksi kabel putus, short, atau kesalahan pemasangan konektor RJ45.',
'tersedia', 3),

('Potensiometer (Pack)', 'ALT-023', 'Elektronika', 'Baik', 'Lemari B - Rak 3',
'Komponen resistor variabel yang digunakan untuk mengatur tegangan, arus, dan input analog pada berbagai rangkaian elektronika.',
'tersedia', 15),

('Resistor 1kohm', 'ALT-024', 'Elektronika', 'Baik', 'Lemari B - Rak 3',
'Resistor dengan nilai hambatan 1 Kilo Ohm yang digunakan pada rangkaian elektronika dan mikrokontroler.',
'tersedia', 20),

('Resistor 2.2Kohm', 'ALT-025', 'Elektronika', 'Baik', 'Lemari B - Rak 3',
'Resistor dengan nilai hambatan 2.2 Kilo Ohm yang umum digunakan pada rangkaian sensor dan pembagi tegangan.',
'tersedia', 20);

-- DATA AWAL: peminjaman
INSERT INTO peminjaman (id_user, id_alat, jumlah, status, tgl_pinjam, tgl_kembali) VALUES
-- Dipinjam, jatuh tempo besok (H-1)
(2, 1, 1, 'dipinjam',     DATE_SUB(CURDATE(), INTERVAL 6 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY)),
-- Dipinjam,ada sisa waktu
(2, 6, 2, 'dipinjam',     CURDATE(),                            DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
-- Menunggu persetujuan
(2, 3, 1, 'menunggu',     CURDATE(),                            DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
-- Sudah dikembalikan
(2, 5, 1, 'dikembalikan', DATE_SUB(CURDATE(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY));