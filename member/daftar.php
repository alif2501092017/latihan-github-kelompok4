<?php
// Pendaftaran member baru: buat akun (users) + data keanggotaan (members), lalu ke pembayaran pertama
require_once __DIR__ . '/../fungsi.php';

// Sudah login: tidak perlu daftar lagi
if ($u = user_login()) {
    header('Location: ' . ($u['role'] === 'member' ? 'dashboard.php' : '../admin/dashboard.php'));
    exit;
}

$err = '';
$nama = $hp = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $nama  = trim($_POST['nama'] ?? '');
    $hp    = trim($_POST['hp'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    // Validasi di server (validasi di browser bisa dilewati)
    if ($nama === '' || mb_strlen($nama) > 100) {
        $err = 'Nama wajib diisi (maksimal 100 karakter).';
    } elseif (!preg_match('/^[0-9+\- ]{8,20}$/', $hp)) {
        $err = 'No. HP tidak valid (8-20 digit).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
        $err = 'Format email tidak valid.';
    } elseif (strlen($pass) < 8) {
        $err = 'Password minimal 8 karakter.';
    } else {
        try {
            $koneksi->begin_transaction();

            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $st = $koneksi->prepare(
                "INSERT INTO users (nama, email, no_hp, password, role)
                 VALUES (?, ?, ?, ?, 'member')"
            );
            $st->bind_param('ssss', $nama, $email, $hp, $hash);
            $st->execute();
            $uid = $koneksi->insert_id;

            // Kolom tanggal_daftar wajib diisi (NOT NULL tanpa nilai bawaan)
            $no = 'PG' . date('ym') . str_pad((string) $uid, 5, '0', STR_PAD_LEFT);
            $m = $koneksi->prepare(
                "INSERT INTO members (user_id, no_member, status, tanggal_daftar)
                 VALUES (?, ?, 'menunggu', CURDATE())"
            );
            $m->bind_param('is', $uid, $no);
            $m->execute();

            $koneksi->commit();

            // Langsung login agar halaman pembayaran tahu siapa yang mendaftar
            masuk(['id' => $uid, 'nama' => $nama, 'role' => 'member']);
            header('Location: pembayaran.php?pertama=1');
            exit;

        } catch (mysqli_sql_exception $ex) {
            $koneksi->rollback();
            // 1062 = nilai unik sudah ada (email dobel)
            $err = $ex->getCode() === 1062
                ? 'Email sudah terdaftar. Silakan login.'
                : 'Pendaftaran gagal. Coba lagi beberapa saat lagi.';
            if ($ex->getCode() !== 1062) { error_log($ex->getMessage()); }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Member - Pangeran Gym</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="frame">
    <div class="bar"></div>
    <div class="content">
        <img src="logopangeran.png" alt="Logo Pangeran GYM" width="150" height="auto">
        <h2>Daftar Member Baru</h2>

        <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>

        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

            <label for="nama">Nama Lengkap</label>
            <input id="nama" type="text" name="nama" maxlength="100" required value="<?= e($nama) ?>">

            <label for="hp">No. HP / WhatsApp</label>
            <input id="hp" type="text" name="hp" maxlength="20" required value="<?= e($hp) ?>">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" maxlength="120" required value="<?= e($email) ?>">

            <label for="password">Password (min. 8 karakter)</label>
            <input id="password" type="password" name="password" minlength="8" required>

            <button class="btn" type="submit">Daftar &amp; Lanjut Bayar</button>
        </form>

        <p>Sudah punya akun? <a class="u" href="../login.php">Login</a></p>
        <p><a href="../index.php">&larr; Kembali ke Beranda</a></p>
    </div>
    <hr>
    <p class="foot">Butuh bantuan Hubungi CS: 0812-xxxx-xxxx</p>
</div>
</body>
</html>