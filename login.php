<?php
// Login GABUNGAN untuk Admin, Kasir, dan Member.
// Taruh di folder utama proyek (sejajar dengan index.php dan fungsi.php).
require_once __DIR__ . '/fungsi.php';

// Sudah login: arahkan sesuai peran
if ($u = user_login()) {
    header('Location: ' . ($u['role'] === 'member' ? 'member/dashboard.php' : 'admin/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (terlalu_banyak_gagal()) {
        $error = 'Terlalu banyak percobaan. Coba lagi dalam 5 menit.';
    } else {
        $id   = trim($_POST['identitas'] ?? '');
        $pass = $_POST['password'] ?? '';

        // Email berlaku untuk semua peran, nomor member hanya cocok dengan member
        $st = $koneksi->prepare(
            "SELECT u.id, u.nama, u.password, u.role, m.status
             FROM users u LEFT JOIN members m ON m.user_id = u.id
             WHERE u.email = ? OR m.no_member = ?
             LIMIT 1"
        );
        $st->bind_param('ss', $id, $id);
        $st->execute();
        $u = $st->get_result()->fetch_assoc();

        if ($u && password_verify($pass, $u['password'])) {
            if ($u['role'] === 'member' && ($u['status'] ?? '') === 'nonaktif') {
                $error = 'Akun Anda nonaktif. Hubungi CS Pangeran Gym.';
            } else {
                masuk($u);
                // Tujuan ditentukan oleh peran di database, bukan oleh pilihan pengguna
                header('Location: ' . ($u['role'] === 'member' ? 'member/dashboard.php' : 'admin/dashboard.php'));
                exit;
            }
        } else {
            if (!$u) { password_verify($pass, hash_palsu()); }   // samakan waktu respons
            catat_gagal();
            $error = 'ID/email atau sandi salah.';   // pesan sama untuk semua kasus
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Pangeran Gym</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="frame">
    <div class="bar"></div>
    <div class="content">
        <img src="logopangeran.png" alt="Logo Pangeran GYM" width="150" height="auto">
        <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <label for="identitas">Masukan Id atau Email Anda</label>
            <input id="identitas" name="identitas" type="text" required value="<?= e($_POST['identitas'] ?? '') ?>">
            <label for="password">Masukan sandi</label>
            <div class="pw">
                <input id="password" name="password" type="password" required>
                <button type="button" class="eye" onclick="var i=document.getElementById('password');var s=i.type==='password';i.type=s?'text':'password';this.textContent=s?'Sembunyi':'Lihat'">Lihat</button>
            </div>
            <div class="kanan"><a href="member/lupa_sandi.php">Lupa sandi?</a></div>
            <button class="btn" type="submit">Login</button>
        </form>
        <p>Belum Punya Akun? <a class="u" href="member/daftar.php">[Ayo daftar sekarang juga]</a></p>
        <p><a href="index.php">&larr; Kembali ke Beranda</a></p>
    </div>
    <hr>
    <p class="foot">Butuh bantuan Hubungi CS: 082279974645</p>
</div>
</body>
</html>