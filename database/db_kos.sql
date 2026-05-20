-- KosManagement Database Dump
-- Host: localhost
-- Database: db_kos

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
CREATE DATABASE IF NOT EXISTS `db_kos` DEFAULT CHARACTER SET utf8mb4;
USE `db_kos`;

-- Table users
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role` enum('admin','penyewa') DEFAULT 'penyewa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` VALUES 
(1, 'Administrator', 'admin@kos.com', '$2y$10$l0TqO0ZJZqoXQy0O6QrJ6OQ9y6uGn6Y9Zz3d0L0J0f9kG1a8zG0Oe', '08123456789', 'Jl. Admin 1', 'admin', NOW()),
(2, 'Penyewa Demo', 'penyewa@demo.com', '$2y$10$l0TqO0ZJZqoXQy0O6QrJ6OQ9y6uGn6Y9Zz3d0L0J0f9kG1a8zG0Oe', '08111111111', 'Jl. Contoh', 'penyewa', NOW());
-- password: admin123

-- Table kamar
CREATE TABLE `kamar` (
  `id_kamar` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kamar` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `ukuran` varchar(50) DEFAULT NULL,
  `status_kamar` enum('tersedia','dipesan','terisi') DEFAULT 'tersedia',
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_kamar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kamar` VALUES 
(1,'Kamar Mawar','Kamar nyaman dengan ventilasi baik.',750000.00,'3x4 m','tersedia','default-kamar.jpg',NOW()),
(2,'Kamar Melati','Kamar premium pemandangan taman.',1200000.00,'4x5 m','tersedia','default-kamar.jpg',NOW()),
(3,'Kamar Anggrek','Kamar minimalis dekat pusat kota.',850000.00,'3x3 m','tersedia','default-kamar.jpg',NOW());

-- Table fasilitas
CREATE TABLE `fasilitas` (
  `id_fasilitas` int(11) NOT NULL AUTO_INCREMENT,
  `nama_fasilitas` varchar(100) NOT NULL,
  PRIMARY KEY (`id_fasilitas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fasilitas` VALUES (1,'WiFi'),(2,'AC'),(3,'Kamar Mandi Dalam'),(4,'Parkiran'),(5,'Lemari'),(6,'Kasur');

-- Table kamar_fasilitas
CREATE TABLE `kamar_fasilitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kamar` int(11) DEFAULT NULL,
  `id_fasilitas` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_kamar` (`id_kamar`),
  KEY `id_fasilitas` (`id_fasilitas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kamar_fasilitas` VALUES 
(1,1,1),(2,1,2),(3,1,3),
(4,2,1),(5,2,2),(6,2,3),(7,2,4),
(8,3,1),(9,3,5),(10,3,6);

-- Table booking
CREATE TABLE `booking` (
  `id_booking` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_kamar` int(11) DEFAULT NULL,
  `tanggal_booking` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_masuk` date DEFAULT NULL,
  `durasi_sewa` int(11) DEFAULT NULL,
  `total_harga` decimal(10,2) DEFAULT NULL,
  `status_booking` enum('pending','diterima','ditolak') DEFAULT 'pending',
  PRIMARY KEY (`id_booking`),
  KEY `id_user` (`id_user`),
  KEY `id_kamar` (`id_kamar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table pembayaran
CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `tanggal_bayar` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_verifikasi` enum('pending','valid','tidak_valid') DEFAULT 'pending',
  PRIMARY KEY (`id_pembayaran`),
  KEY `id_booking` (`id_booking`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table laporan (opsional)
CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) DEFAULT NULL,
  `bulan` varchar(7) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id_laporan`),
  KEY `id_booking` (`id_booking`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Foreign keys
ALTER TABLE `kamar_fasilitas` ADD CONSTRAINT `kamar_fasilitas_ibfk_1` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`) ON DELETE CASCADE;
ALTER TABLE `kamar_fasilitas` ADD CONSTRAINT `kamar_fasilitas_ibfk_2` FOREIGN KEY (`id_fasilitas`) REFERENCES `fasilitas` (`id_fasilitas`) ON DELETE CASCADE;
ALTER TABLE `booking` ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);
ALTER TABLE `booking` ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`id_kamar`) REFERENCES `kamar` (`id_kamar`);
ALTER TABLE `pembayaran` ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`);
ALTER TABLE `laporan` ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`id_booking`) REFERENCES `booking` (`id_booking`);
COMMIT;