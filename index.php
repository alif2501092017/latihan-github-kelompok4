<?php
// Halaman Landing Page Pengunjung - Pangeran Gym
// Menggabungkan Use Case "Melihat Informasi" dan "Melihat Fasilitas"
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda - Pangeran Gym</title>
    <!-- Menggunakan file CSS utama Anda -->
    <link rel="stylesheet" href="assets/style.css">
    <style>
        /* CSS Tambahan khusus tata letak landing page agar sesuai dengan wireframe */
        .landing-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: var(--bg-frame, #161616);
            border-bottom: 1px solid var(--border-color, #441010);
        }
        .btn-login-corner {
            background: var(--btn-bg, #8b0000);
            color: #fff;
            padding: 6px 14px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: bold;
        }
        .btn-login-corner:hover {
            background: var(--btn-hover, #b71c1c);
        }
        .section-title {
            text-align: left;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0 10px 0;
            color: var(--text-color, #e0e0e0);
            border-left: 3px solid var(--btn-bg, #8b0000);
            padding-left: 8px;
        }
        /* Grid untuk Fasilitas ber-Gambar */
        .facility-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .facility-card {
            background: var(--input-bg, #1f1f1f);
            border: 1px solid var(--border-color, #441010);
            border-radius: 4px;
            overflow: hidden;
            text-align: center;
        }
        .facility-img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            background-color: #333; /* Warna cadangan jika gambar belum ada */
            border-bottom: 1px solid var(--border-color, #441010);
        }
        .facility-name {
            padding: 8px;
            font-size: 13px;
            font-weight: bold;
            color: var(--text-color, #e0e0e0);
        }
        /* Kotak Informasi Terbaru */
        .info-card {
            background: var(--input-bg, #1f1f1f);
            border: 1px solid var(--border-color, #441010);
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            text-align: left;
        }
        .info-date {
            font-size: 11px;
            color: var(--text-muted, #aaa);
            margin-bottom: 4px;
        }
        .info-text {
            font-size: 14px;
            margin: 0;
        }
    </style>
</head>
<body>

<div class="frame" style="max-width: 560px;">
    
<!-- Tombol Login di Sudut Kiri Atas (Sesuai Permintaan) -->
<div class="landing-header">
    <a href="login.php" class="btn-login-corner">Login / Masuk</a>
    <span style="font-size: 12px; color: var(--text-muted, #888);">Pangeran Gym Portal</span>
</div>

<div class="content" style="padding: 20px 30px;">
    
    <!-- Logo & Judul Sambutan -->
    <div class="logo" style="margin: 0 auto 15px;">
        <img src="logopangeran.png" alt="Logo Pangeran GYM" style="max-width:100%; height:auto;">
    </div>
    <h2 style="margin-bottom: 5px;">Selamat Datang di Pangeran Gym</h2>
    <p class="kecil">Pusat kebugaran terbaik dengan fasilitas lengkap dan modern.</p>

    <hr style="margin: 15px 0;">

    <!-- ================= BAGIAN 1: INFORMASI & PROMO (Use Case 10) ================= -->
    <div class="section-title">Informasi & Promo Terbaru</div>
    
    <!-- Informasi 1 -->
    <div class="info-card">
        <div class="info-date">📅 10 November 2026</div>
        <p class="info-text"><b>Promo Spesial Mahasiswa hanya 120.000:</b> Diskon pendaftaran khusus paket 3 bulan untuk Umum!</p>
    </div>

    <!-- Informasi 2 -->
    <div class="info-card">
        <p class="info-text"><b>Jadwal GYM sepuasnya untuk semua member</b></p>
    </div>


    <!-- ================= BAGIAN 2: FASILITAS GYM & GAMBAR ================= -->
    <div class="section-title">Fasilitas Pangeran Gym</div>
    
    <div class="facility-grid">
        
        <!-- Fasilitas 1 -->
        <div class="facility-card">
            <img src="assets/img/area_fitnes.jpg" alt="Area Fitness" class="facility-img" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'><rect width=\'100%\' height=\'100%\' fill=\'%23222\'/><text x=\'50%\' y=\'50%\' fill=\'%23aaa\' text-anchor=\'middle\' dominant-baseline=\'central\' font-size=\'12\'>Area Fitness</text></svg>'">
            <div class="facility-name">Area Fitness & Beban</div>
        </div>

        <!-- Fasilitas 2 -->
        <div class="facility-card">
            <img src="assets/img/ruang_ganti.jpg" alt="Ruang Ganti" class="facility-img" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'><rect width=\'100%\' height=\'100%\' fill=\'%23222\'/><text x=\'50%\' y=\'50%\' fill=\'%23aaa\' text-anchor=\'middle\' dominant-baseline=\'central\' font-size=\'12\'>Ruang Ganti</text></svg>'">
            <div class="facility-name">Ruang Ganti & Toilet</div>
        </div>

        <!-- Fasilitas 3 -->
        <div class="facility-card">
            <div class="facility-name">Loker Aman</div>
        </div>

    </div>

    <p style="margin-top: 20px; font-size: 14px;">
        Ingin menikmati fasilitas lengkap ini? <a class="u" href="member/daftar.php">Daftar Member Sekarang</a>
    </p>

</div>

<hr>
<p class="foot">Butuh bantuan Hubungi CS: 082279974645</p>
</div>

</body>
</html>