-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for pangeran_gym
CREATE DATABASE IF NOT EXISTS `pangeran_gym` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pangeran_gym`;

-- Dumping structure for table pangeran_gym.fasilitas
CREATE TABLE IF NOT EXISTS `fasilitas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.fasilitas: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.informasi
CREATE TABLE IF NOT EXISTS `informasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dibuat_oleh` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_info_user` (`dibuat_oleh`),
  KEY `idx_info_tanggal` (`tanggal`),
  CONSTRAINT `fk_info_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.informasi: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.members
CREATE TABLE IF NOT EXISTS `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `paket_id` int unsigned DEFAULT NULL,
  `no_member` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('menunggu','aktif','jatuh_tempo','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `tanggal_daftar` date NOT NULL,
  `aktif_sampai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `no_member` (`no_member`),
  KEY `fk_members_paket` (`paket_id`),
  KEY `idx_members_status` (`status`),
  KEY `idx_members_aktif_sampai` (`aktif_sampai`),
  CONSTRAINT `fk_members_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.members: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.metode_pembayaran
CREATE TABLE IF NOT EXISTS `metode_pembayaran` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('transfer','ewallet','tunai') COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_rekening` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atas_nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_gambar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.metode_pembayaran: ~3 rows (approximately)
INSERT INTO `metode_pembayaran` (`id`, `nama`, `tipe`, `no_rekening`, `atas_nama`, `qr_gambar`, `aktif`) VALUES
	(1, 'Transfer Bank', 'transfer', '0000000000', 'Pangeran Gym', NULL, 1),
	(2, 'E-Wallet', 'ewallet', '0800000000', 'Pangeran Gym', NULL, 1),
	(3, 'Tunai di Kasir', 'tunai', NULL, NULL, NULL, 1);

-- Dumping structure for table pangeran_gym.notifikasi
CREATE TABLE IF NOT EXISTS `notifikasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `pembayaran_id` bigint unsigned DEFAULT NULL,
  `tipe` enum('pengingat_h7','pengingat_h3','pengingat_h1','pembayaran_terverifikasi','pembayaran_ditolak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kanal` enum('aplikasi','whatsapp','email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aplikasi',
  `jatuh_tempo` date DEFAULT NULL,
  `judul` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_kirim` enum('terjadwal','terkirim','gagal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terjadwal',
  `dikirim_pada` datetime DEFAULT NULL,
  `dibaca_pada` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_notif_pengingat` (`member_id`,`tipe`,`kanal`,`jatuh_tempo`),
  KEY `fk_notif_bayar` (`pembayaran_id`),
  KEY `idx_notif_member` (`member_id`,`dibaca_pada`),
  CONSTRAINT `fk_notif_bayar` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_notif_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.notifikasi: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.paket
CREATE TABLE IF NOT EXISTS `paket` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `durasi_bulan` tinyint unsigned NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.paket: ~3 rows (approximately)
INSERT INTO `paket` (`id`, `nama`, `durasi_bulan`, `harga`, `aktif`) VALUES
	(1, 'Paket 1 Bulan', 1, 150000.00, 1),
	(2, 'Paket 3 Bulan', 3, 400000.00, 1),
	(3, 'Paket 6 Bulan', 6, 750000.00, 1);

-- Dumping structure for table pangeran_gym.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `paket_id` int unsigned DEFAULT NULL,
  `metode_id` int unsigned NOT NULL,
  `jenis` enum('pertama','bulanan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `biaya_pendaftaran` decimal(12,2) NOT NULL DEFAULT '0.00',
  `jumlah` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `bukti_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `alasan_tolak` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `dibuat_oleh` bigint unsigned DEFAULT NULL,
  `diverifikasi_oleh` bigint unsigned DEFAULT NULL,
  `diverifikasi_pada` datetime DEFAULT NULL,
  `no_struk` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_struk` (`no_struk`),
  KEY `fk_bayar_paket` (`paket_id`),
  KEY `fk_bayar_metode` (`metode_id`),
  KEY `fk_bayar_kasir` (`dibuat_oleh`),
  KEY `fk_bayar_admin` (`diverifikasi_oleh`),
  KEY `idx_bayar_status` (`status`),
  KEY `idx_bayar_member` (`member_id`,`created_at`),
  CONSTRAINT `fk_bayar_admin` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_bayar_kasir` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_bayar_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_bayar_metode` FOREIGN KEY (`metode_id`) REFERENCES `metode_pembayaran` (`id`),
  CONSTRAINT `fk_bayar_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.pembayaran: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.pengaturan
CREATE TABLE IF NOT EXISTS `pengaturan` (
  `kunci` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`kunci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.pengaturan: ~1 rows (approximately)
INSERT INTO `pengaturan` (`kunci`, `nilai`) VALUES
	('biaya_pendaftaran', '50000');

-- Dumping structure for table pangeran_gym.pengaturan_pengingat
CREATE TABLE IF NOT EXISTS `pengaturan_pengingat` (
  `id` tinyint unsigned NOT NULL DEFAULT '1',
  `kirim_h7` tinyint(1) NOT NULL DEFAULT '1',
  `kirim_h3` tinyint(1) NOT NULL DEFAULT '1',
  `kirim_h1` tinyint(1) NOT NULL DEFAULT '1',
  `via_aplikasi` tinyint(1) NOT NULL DEFAULT '1',
  `via_whatsapp` tinyint(1) NOT NULL DEFAULT '1',
  `via_email` tinyint(1) NOT NULL DEFAULT '1',
  `template_pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `chk_pengingat_satu_baris` CHECK ((`id` = 1))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.pengaturan_pengingat: ~1 rows (approximately)
INSERT INTO `pengaturan_pengingat` (`id`, `kirim_h7`, `kirim_h3`, `kirim_h1`, `via_aplikasi`, `via_whatsapp`, `via_email`, `template_pesan`, `updated_at`) VALUES
	(1, 1, 1, 1, 1, 1, 1, 'Halo {nama}, iuran Pangeran Gym Anda jatuh tempo pada {tanggal}. Silakan lakukan pembayaran.', '2026-11-04 10:11:43');

-- Dumping structure for table pangeran_gym.personal_trainer
CREATE TABLE IF NOT EXISTS `personal_trainer` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `biodata` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.personal_trainer: ~0 rows (approximately)

-- Dumping structure for table pangeran_gym.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','kasir','member') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table pangeran_gym.users: ~1 rows (approximately)
INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `password`, `role`, `created_at`, `updated_at`) VALUES
	(5, 'Admin', 'admin@pangerangym.test', NULL, '$2y$10$DcMs8IZ3yQ5em7JdY4kCB.WvLWh4tNi2lPb9NojQRV.xu/L9UPMTq', 'admin', '2026-11-04 10:38:31', '2026-11-04 10:38:31');

-- Dumping structure for view pangeran_gym.v_member_jatuh_tempo
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `v_member_jatuh_tempo` (
	`member_id` BIGINT(20) UNSIGNED NOT NULL,
	`nama` VARCHAR(100) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`no_hp` VARCHAR(20) NULL COLLATE 'utf8mb4_unicode_ci',
	`email` VARCHAR(120) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`aktif_sampai` DATE NULL,
	`sisa_hari` INT(10) NULL
) ENGINE=MyISAM;

-- Dumping structure for view pangeran_gym.v_member_jatuh_tempo
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `v_member_jatuh_tempo`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_member_jatuh_tempo` AS select `m`.`id` AS `member_id`,`u`.`nama` AS `nama`,`u`.`no_hp` AS `no_hp`,`u`.`email` AS `email`,`m`.`aktif_sampai` AS `aktif_sampai`,(to_days(`m`.`aktif_sampai`) - to_days(curdate())) AS `sisa_hari` from (`members` `m` join `users` `u` on((`u`.`id` = `m`.`user_id`))) where ((`m`.`status` in ('aktif','jatuh_tempo')) and (`m`.`aktif_sampai` between curdate() and (curdate() + interval 7 day)));

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
