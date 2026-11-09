<?php
// Login Admin/Kasir: tabel users, role = admin atau kasir
require_once __DIR__ . '/../fungsi.php';

// Wajib lewat halaman pilih portal
if (($_SESSION['portal'] ?? '') !== 'admin') {
    header('Location: ../index.php');
    exit;
}
// Sudah login
if (in_array($_SESSION['auth']['role'] ?? '', ['admin', 'kasir'], true)) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    if (terlalu_banyak_gagal()) {
        $error = 'Terlalu banyak percobaan. Coba lagi dalam 5 menit.';
    } else {
        $email = trim($_POST['identitas'] ?? '');
        $pass  = $_POST['password'] ?? '';

        $st = $koneksi->prepare(
            "SELECT id, nama, password, role FROM users
             WHERE email = ? AND role IN ('admin','kasir') LIMIT 1"
        );
        $st->bind_param('s', $email);
        $st->execute();
        $u = $st->get_result()->fetch_assoc();

        if ($u && password_verify($pass, $u['password'])) {
            masuk($u);
            header('Location: dashboard.php');
            exit;
        }
        if (!$u) { password_verify($pass, hash_palsu()); }   // samakan waktu respons
        catat_gagal();
        $error = 'ID/email atau sandi salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Pangeran Gym</title>
    <link rel="stylesheet" href="../assets/style.css">
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
            <label for="password">Masukan Sandi</label>
            <div class="pw">
                <input id="password" name="password" type="password" required>
                <button type="button" class="eye" onclick="var i=document.getElementById('password');var s=i.type==='password';i.type=s?'text':'password';this.textContent=s?'Sembunyi':'Lihat'">Lihat</button>
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
        <p class="kecil">[!] Area Terbatas untuk Staf Resmi</p>
        <p><a href="../index.php">&larr; Kembali ke Pilihan Portal</a></p>
    </div>
    <hr>
    <p class="foot">Butuh bantuan Hubungi CS: 082279974645</p>
</div>
</body>
</html>
