-- ============================================================
-- Struktur database: Sistem Informasi Pangeran Gym (MySQL / MariaDB)
-- Cara pakai:  mysql -u root -p < pangeran_gym.sql
-- Atau lewat phpMyAdmin: tab Import, pilih file ini.
-- ============================================================

CREATE DATABASE IF NOT EXISTS pangeran_gym
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE pangeran_gym;

-- Hapus tabel lama (urutan anak ke induk) agar skrip bisa dijalankan ulang
SET FOREIGN_KEY_CHECKS = 0;
DROP VIEW  IF EXISTS v_member_jatuh_tempo;
DROP TABLE IF EXISTS notifikasi, pembayaran, members, paket, metode_pembayaran,
                     pengaturan_pengingat, pengaturan, informasi, fasilitas,
                     personal_trainer, password_reset_tokens, users;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. users: akun login admin, kasir, dan member
--    (Login Admin/Kasir, Login Member, Pendaftaran Member)
-- ------------------------------------------------------------
CREATE TABLE users (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL,
  email       VARCHAR(120) NOT NULL UNIQUE,
  no_hp       VARCHAR(20)  NULL,
  password    VARCHAR(255) NOT NULL,              -- simpan hash, bukan teks asli
  role        ENUM('admin','kasir','member') NOT NULL DEFAULT 'member',
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_users_role (role)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. password_reset_tokens: Lupa Sandi
-- ------------------------------------------------------------
CREATE TABLE password_reset_tokens (
  email       VARCHAR(120) NOT NULL PRIMARY KEY,
  token       VARCHAR(255) NOT NULL,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. paket: pilihan paket keanggotaan (Pembayaran Pertama - Pilih Paket)
-- ------------------------------------------------------------
CREATE TABLE paket (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama          VARCHAR(60)  NOT NULL,
  durasi_bulan  TINYINT UNSIGNED NOT NULL,
  harga         DECIMAL(12,2) NOT NULL,
  aktif         TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. members: data keanggotaan
--    (Data Member, Dashboard Member, Dashboard Admin)
-- ------------------------------------------------------------
CREATE TABLE members (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id         BIGINT UNSIGNED NOT NULL UNIQUE,
  paket_id        INT UNSIGNED NULL,
  no_member       VARCHAR(20) NOT NULL UNIQUE,
  status          ENUM('menunggu','aktif','jatuh_tempo','nonaktif') NOT NULL DEFAULT 'menunggu',
  tanggal_daftar  DATE NOT NULL,
  aktif_sampai    DATE NULL,                        -- dasar pengingat dan status
  created_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_members_user  FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
  CONSTRAINT fk_members_paket FOREIGN KEY (paket_id) REFERENCES paket(id)  ON DELETE SET NULL,
  INDEX idx_members_status (status),
  INDEX idx_members_aktif_sampai (aktif_sampai)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. metode_pembayaran: transfer bank, e-wallet, tunai (kasir)
--    (Pembayaran Pertama - Bayar, Iuran Bulanan, Pembayaran Tunai)
-- ------------------------------------------------------------
CREATE TABLE metode_pembayaran (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama         VARCHAR(60) NOT NULL,
  tipe         ENUM('transfer','ewallet','tunai') NOT NULL,
  no_rekening  VARCHAR(40) NULL,
  atas_nama    VARCHAR(100) NULL,
  qr_gambar    VARCHAR(255) NULL,
  aktif        TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. pembayaran: pembayaran pertama, iuran bulanan, dan tunai
--    (Pembayaran Pertama, Iuran Bulanan, Riwayat, Verifikasi, Kasir)
-- ------------------------------------------------------------
CREATE TABLE pembayaran (
  id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id           BIGINT UNSIGNED NOT NULL,
  paket_id            INT UNSIGNED NULL,
  metode_id           INT UNSIGNED NOT NULL,
  jenis               ENUM('pertama','bulanan') NOT NULL,
  periode_mulai       DATE NOT NULL,
  periode_akhir       DATE NOT NULL,
  biaya_pendaftaran   DECIMAL(12,2) NOT NULL DEFAULT 0,   -- hanya untuk jenis 'pertama'
  jumlah              DECIMAL(12,2) NOT NULL,              -- iuran paket
  total               DECIMAL(12,2) NOT NULL,
  bukti_transfer      VARCHAR(255) NULL,                   -- kosong jika tunai
  status              ENUM('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  alasan_tolak        VARCHAR(255) NULL,
  jatuh_tempo         DATE NULL,
  dibuat_oleh         BIGINT UNSIGNED NULL,                -- kasir jika input tunai
  diverifikasi_oleh   BIGINT UNSIGNED NULL,
  diverifikasi_pada   DATETIME NULL,
  no_struk            VARCHAR(30) NULL UNIQUE,
  created_at          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bayar_member FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE,
  CONSTRAINT fk_bayar_paket  FOREIGN KEY (paket_id)  REFERENCES paket(id)   ON DELETE SET NULL,
  CONSTRAINT fk_bayar_metode FOREIGN KEY (metode_id) REFERENCES metode_pembayaran(id),
  CONSTRAINT fk_bayar_kasir  FOREIGN KEY (dibuat_oleh)       REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_bayar_admin  FOREIGN KEY (diverifikasi_oleh) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_bayar_status (status),
  INDEX idx_bayar_member (member_id, created_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. pengaturan_pengingat: kapan dan lewat apa pengingat dikirim
--    (Pengaturan Pengingat Bayar). Satu baris saja.
-- ------------------------------------------------------------
CREATE TABLE pengaturan_pengingat (
  id              TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
  kirim_h7        TINYINT(1) NOT NULL DEFAULT 1,
  kirim_h3        TINYINT(1) NOT NULL DEFAULT 1,
  kirim_h1        TINYINT(1) NOT NULL DEFAULT 1,
  via_aplikasi    TINYINT(1) NOT NULL DEFAULT 1,
  via_whatsapp    TINYINT(1) NOT NULL DEFAULT 1,
  via_email       TINYINT(1) NOT NULL DEFAULT 1,
  template_pesan  TEXT NOT NULL,
  updated_at      TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_pengingat_satu_baris CHECK (id = 1)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 8. pengaturan: nilai umum, misalnya biaya pendaftaran
-- ------------------------------------------------------------
CREATE TABLE pengaturan (
  kunci  VARCHAR(60) NOT NULL PRIMARY KEY,
  nilai  VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 9. notifikasi: pengingat H-7, H-3, H-1 dan konfirmasi verifikasi
--    (Notifikasi Pengingat Bayar, Dashboard Member)
-- ------------------------------------------------------------
CREATE TABLE notifikasi (
  id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id      BIGINT UNSIGNED NOT NULL,
  pembayaran_id  BIGINT UNSIGNED NULL,
  tipe           ENUM('pengingat_h7','pengingat_h3','pengingat_h1',
                      'pembayaran_terverifikasi','pembayaran_ditolak') NOT NULL,
  kanal          ENUM('aplikasi','whatsapp','email') NOT NULL DEFAULT 'aplikasi',
  jatuh_tempo    DATE NULL,                         -- jatuh tempo yang diingatkan
  judul          VARCHAR(120) NOT NULL,
  isi            TEXT NOT NULL,
  status_kirim   ENUM('terjadwal','terkirim','gagal') NOT NULL DEFAULT 'terjadwal',
  dikirim_pada   DATETIME NULL,
  dibaca_pada    DATETIME NULL,
  created_at     TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notif_member FOREIGN KEY (member_id)     REFERENCES members(id)    ON DELETE CASCADE,
  CONSTRAINT fk_notif_bayar  FOREIGN KEY (pembayaran_id) REFERENCES pembayaran(id) ON DELETE SET NULL,
  -- cegah pengingat ganda untuk member, tipe, kanal, dan jatuh tempo yang sama
  UNIQUE KEY uq_notif_pengingat (member_id, tipe, kanal, jatuh_tempo),
  INDEX idx_notif_member (member_id, dibaca_pada)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 10. informasi: Informasi Terbaru (CRUD admin, tampil di Beranda)
-- ------------------------------------------------------------
CREATE TABLE informasi (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul        VARCHAR(150) NOT NULL,
  isi          TEXT NOT NULL,
  tanggal      DATE NOT NULL,
  gambar       VARCHAR(255) NULL,
  dibuat_oleh  BIGINT UNSIGNED NULL,
  created_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_info_user FOREIGN KEY (dibuat_oleh) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_info_tanggal (tanggal)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 11. fasilitas: Fasilitas yang Tersedia (CRUD admin)
-- ------------------------------------------------------------
CREATE TABLE fasilitas (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL,
  keterangan  TEXT NULL,
  gambar      VARCHAR(255) NULL,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 12. personal_trainer: Personal Trainer (CRUD admin)
-- ------------------------------------------------------------
CREATE TABLE personal_trainer (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(100) NOT NULL,
  biodata     TEXT NULL,
  foto        VARCHAR(255) NULL,
  created_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- View: member yang jatuh tempo dalam 7 hari (Dashboard Admin)
-- ------------------------------------------------------------
CREATE VIEW v_member_jatuh_tempo AS
SELECT m.id AS member_id, u.nama, u.no_hp, u.email, m.aktif_sampai,
       DATEDIFF(m.aktif_sampai, CURDATE()) AS sisa_hari
FROM members m
JOIN users u ON u.id = m.user_id
WHERE m.status IN ('aktif','jatuh_tempo')
  AND m.aktif_sampai BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY);

-- ------------------------------------------------------------
-- Data awal (CONTOH, sesuaikan dengan kebutuhan)
-- ------------------------------------------------------------
INSERT INTO paket (nama, durasi_bulan, harga) VALUES
  ('Paket 1 Bulan', 1, 150000),
  ('Paket 3 Bulan', 3, 400000),
  ('Paket 6 Bulan', 6, 750000);

INSERT INTO metode_pembayaran (nama, tipe, no_rekening, atas_nama) VALUES
  ('Transfer Bank', 'transfer', '0000000000', 'Pangeran Gym'),
  ('E-Wallet',      'ewallet',  '0800000000', 'Pangeran Gym'),
  ('Tunai di Kasir','tunai',    NULL,         NULL);

INSERT INTO pengaturan (kunci, nilai) VALUES ('biaya_pendaftaran', '50000');

INSERT INTO pengaturan_pengingat (id, template_pesan) VALUES
  (1, 'Halo {nama}, iuran Pangeran Gym Anda jatuh tempo pada {tanggal}. Silakan lakukan pembayaran.');

-- Akun admin awal: ganti hash di bawah dengan hasil password_hash() / bcrypt milik Anda
-- INSERT INTO users (nama, email, password, role)
-- VALUES ('Admin', 'admin@pangerangym.test', '<HASH_PASSWORD>', 'admin');
