-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 09, 2026 at 07:16 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pangeran_gym`
--

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `keterangan` text,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama`, `keterangan`, `gambar`, `created_at`, `updated_at`) VALUES
(1, 'Area Fitness', 'Peralatan beban dan mesin lengkap.', NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41'),
(2, 'Ruang Ganti', 'Ruang ganti bersih dengan kamar mandi.', NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41'),
(3, 'Loker', 'Loker pribadi untuk menyimpan barang.', NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41');

-- --------------------------------------------------------

--
-- Table structure for table `informasi`
--

CREATE TABLE `informasi` (
  `id` bigint UNSIGNED NOT NULL,
  `judul` varchar(150) NOT NULL,
  `isi` text NOT NULL,
  `tanggal` date NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `dibuat_oleh` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `informasi`
--

INSERT INTO `informasi` (`id`, `judul`, `isi`, `tanggal`, `gambar`, `dibuat_oleh`, `created_at`, `updated_at`) VALUES
(1, 'Promo Member Baru', 'Daftar bulan ini dan dapatkan potongan biaya pendaftaran.', '2026-11-09', NULL, NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41'),
(2, 'Jadwal Kelas Zumba', 'Kelas Zumba setiap Sabtu pukul 08.00 di area latihan.', '2026-11-09', NULL, NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41');

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `paket_id` int UNSIGNED DEFAULT NULL,
  `no_member` varchar(20) NOT NULL,
  `status` enum('menunggu','aktif','jatuh_tempo','nonaktif') NOT NULL DEFAULT 'menunggu',
  `tanggal_daftar` date NOT NULL,
  `aktif_sampai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(60) NOT NULL,
  `tipe` enum('transfer','ewallet','tunai') NOT NULL,
  `no_rekening` varchar(40) DEFAULT NULL,
  `atas_nama` varchar(100) DEFAULT NULL,
  `qr_gambar` varchar(255) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id`, `nama`, `tipe`, `no_rekening`, `atas_nama`, `qr_gambar`, `aktif`) VALUES
(1, 'Transfer Bank', 'transfer', '0000000000', 'Pangeran Gym', NULL, 1),
(2, 'E-Wallet', 'ewallet', '0800000000', 'Pangeran Gym', NULL, 1),
(3, 'Tunai di Kasir', 'tunai', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` bigint UNSIGNED NOT NULL,
  `member_id` bigint UNSIGNED NOT NULL,
  `pembayaran_id` bigint UNSIGNED DEFAULT NULL,
  `tipe` enum('pengingat_h7','pengingat_h3','pengingat_h1','pembayaran_terverifikasi','pembayaran_ditolak') NOT NULL,
  `kanal` enum('aplikasi','whatsapp','email') NOT NULL DEFAULT 'aplikasi',
  `jatuh_tempo` date DEFAULT NULL,
  `judul` varchar(120) NOT NULL,
  `isi` text NOT NULL,
  `status_kirim` enum('terjadwal','terkirim','gagal') NOT NULL DEFAULT 'terjadwal',
  `dikirim_pada` datetime DEFAULT NULL,
  `dibaca_pada` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE `paket` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(60) NOT NULL,
  `durasi_bulan` tinyint UNSIGNED NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `paket`
--

INSERT INTO `paket` (`id`, `nama`, `durasi_bulan`, `harga`, `aktif`) VALUES
(1, 'Paket 1 Bulan', 1, '150000.00', 1),
(2, 'Paket 3 Bulan', 3, '400000.00', 1),
(3, 'Paket 6 Bulan', 6, '750000.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(120) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint UNSIGNED NOT NULL,
  `member_id` bigint UNSIGNED NOT NULL,
  `paket_id` int UNSIGNED DEFAULT NULL,
  `metode_id` int UNSIGNED NOT NULL,
  `jenis` enum('pertama','bulanan') NOT NULL,
  `periode_mulai` date NOT NULL,
  `periode_akhir` date NOT NULL,
  `biaya_pendaftaran` decimal(12,2) NOT NULL DEFAULT '0.00',
  `jumlah` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `alasan_tolak` varchar(255) DEFAULT NULL,
  `jatuh_tempo` date DEFAULT NULL,
  `dibuat_oleh` bigint UNSIGNED DEFAULT NULL,
  `diverifikasi_oleh` bigint UNSIGNED DEFAULT NULL,
  `diverifikasi_pada` datetime DEFAULT NULL,
  `no_struk` varchar(30) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `kunci` varchar(60) NOT NULL,
  `nilai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`kunci`, `nilai`) VALUES
('biaya_pendaftaran', '50000');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_pengingat`
--

CREATE TABLE `pengaturan_pengingat` (
  `id` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `kirim_h7` tinyint(1) NOT NULL DEFAULT '1',
  `kirim_h3` tinyint(1) NOT NULL DEFAULT '1',
  `kirim_h1` tinyint(1) NOT NULL DEFAULT '1',
  `via_aplikasi` tinyint(1) NOT NULL DEFAULT '1',
  `via_whatsapp` tinyint(1) NOT NULL DEFAULT '1',
  `via_email` tinyint(1) NOT NULL DEFAULT '1',
  `template_pesan` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `pengaturan_pengingat`
--

INSERT INTO `pengaturan_pengingat` (`id`, `kirim_h7`, `kirim_h3`, `kirim_h1`, `via_aplikasi`, `via_whatsapp`, `via_email`, `template_pesan`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 'Halo {nama}, iuran Pangeran Gym Anda jatuh tempo pada {tanggal}. Silakan lakukan pembayaran.', '2026-11-09 06:48:41');

-- --------------------------------------------------------

--
-- Table structure for table `personal_trainer`
--

CREATE TABLE `personal_trainer` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `biodata` text,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `personal_trainer`
--

INSERT INTO `personal_trainer` (`id`, `nama`, `biodata`, `foto`, `created_at`, `updated_at`) VALUES
(1, 'Coach Contoh 1', 'Berpengalaman melatih penurunan berat badan dan kebugaran umum.', NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41'),
(2, 'Coach Contoh 2', 'Spesialis latihan kekuatan dan pembentukan otot.', NULL, '2026-11-09 06:48:41', '2026-11-09 06:48:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir','member') NOT NULL DEFAULT 'member',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '081234567890', '$2y$10$DcMs8IZ3yQ5em7JdY4kCB.WvLWh4tNi2lPb9NojQRV.xu/L9UPMTq', 'admin', '2026-11-09 07:15:12', '2026-11-09 07:15:12');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_member_jatuh_tempo`
-- (See below for the actual view)
--
CREATE TABLE `v_member_jatuh_tempo` (
`aktif_sampai` date
,`email` varchar(120)
,`member_id` bigint unsigned
,`nama` varchar(100)
,`no_hp` varchar(20)
,`sisa_hari` int
);

-- --------------------------------------------------------

--
-- Structure for view `v_member_jatuh_tempo`
--
DROP TABLE IF EXISTS `v_member_jatuh_tempo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_member_jatuh_tempo`  AS SELECT `m`.`id` AS `member_id`, `u`.`nama` AS `nama`, `u`.`no_hp` AS `no_hp`, `u`.`email` AS `email`, `m`.`aktif_sampai` AS `aktif_sampai`, (to_days(`m`.`aktif_sampai`) - to_days(curdate())) AS `sisa_hari` FROM (`members` `m` join `users` `u` on((`u`.`id` = `m`.`user_id`))) WHERE ((`m`.`status` in ('aktif','jatuh_tempo')) AND (`m`.`aktif_sampai` between curdate() and (curdate() + interval 7 day)))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `informasi`
--
ALTER TABLE `informasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_info_user` (`dibuat_oleh`),
  ADD KEY `idx_info_tanggal` (`tanggal`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `no_member` (`no_member`),
  ADD KEY `fk_members_paket` (`paket_id`),
  ADD KEY `idx_members_status` (`status`),
  ADD KEY `idx_members_aktif_sampai` (`aktif_sampai`);

--
-- Indexes for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_notif_pengingat` (`member_id`,`tipe`,`kanal`,`jatuh_tempo`),
  ADD KEY `fk_notif_bayar` (`pembayaran_id`),
  ADD KEY `idx_notif_member` (`member_id`,`dibaca_pada`);

--
-- Indexes for table `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_struk` (`no_struk`),
  ADD KEY `fk_bayar_paket` (`paket_id`),
  ADD KEY `fk_bayar_metode` (`metode_id`),
  ADD KEY `fk_bayar_kasir` (`dibuat_oleh`),
  ADD KEY `fk_bayar_admin` (`diverifikasi_oleh`),
  ADD KEY `idx_bayar_status` (`status`),
  ADD KEY `idx_bayar_member` (`member_id`,`created_at`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`kunci`);

--
-- Indexes for table `pengaturan_pengingat`
--
ALTER TABLE `pengaturan_pengingat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_trainer`
--
ALTER TABLE `personal_trainer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `informasi`
--
ALTER TABLE `informasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paket`
--
ALTER TABLE `paket`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_trainer`
--
ALTER TABLE `personal_trainer`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `informasi`
--
ALTER TABLE `informasi`
  ADD CONSTRAINT `fk_info_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `fk_members_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_members_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notif_bayar` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_notif_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_bayar_admin` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bayar_kasir` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_bayar_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bayar_metode` FOREIGN KEY (`metode_id`) REFERENCES `metode_pembayaran` (`id`),
  ADD CONSTRAINT `fk_bayar_paket` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
