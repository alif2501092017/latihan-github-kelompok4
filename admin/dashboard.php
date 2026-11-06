<?php
// FILE 2: Login Admin/Kasir (tabel users, role = admin atau kasir)
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

// ---- Koneksi database (sesuaikan) ----
$pdo = new PDO('mysql:host=localhost;dbname=pangeran_gym;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Wajib lewat halaman pilih portal
if (($_SESSION['portal'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}
// Sudah login sebagai admin/kasir
if (in_array($_SESSION['auth']['role'] ?? '', ['admin', 'kasir'], true)) {
    header('Location: dashboard_admin.php');
    exit;
}

function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f = $_SESSION['gagal'] ?? ['n' => 0, 't' => 0];
    if (time() - $f['t'] >= 300) { $f = ['n' => 0, 't' => 0]; }

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $error = 'Sesi tidak valid. Muat ulang halaman.';
    } elseif ($f['n'] >= 5) {
        $error = 'Terlalu banyak percobaan. Coba lagi dalam 5 menit.';
    } else {
        $email = trim($_POST['identitas'] ?? '');
        $pass  = $_POST['password'] ?? '';

        $st = $pdo->prepare("SELECT id, nama, password, role FROM users
                              WHERE email = ? AND role IN ('admin','kasir') LIMIT 1");
        $st->execute([$email]);
        $u = $st->fetch();

        // verifikasi tetap dijalankan walau akun tidak ada (waktu respons sama)
        $hash = $u['password'] ?? password_hash('dummy', PASSWORD_DEFAULT);
        if ($u && password_verify($pass, $hash)) {
            session_regenerate_id(true);
            $_SESSION['auth'] = ['id' => (int) $u['id'], 'nama' => $u['nama'], 'role' => $u['role']];
            unset($_SESSION['gagal']);
            header('Location: dashboard_admin.php');
            exit;
        }
        $_SESSION['gagal'] = ['n' => $f['n'] + 1, 't' => time()];
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
<style>
*{box-sizing:border-box}body{margin:0;background:#eee;font:16px/1.4 Arial,sans-serif;color:#444}a{color:#444}
.frame{max-width:520px;margin:40px auto;background:#fff;box-shadow:0 1px 4px rgba(0,0,0,.25)}
.bar{height:30px;background:#ddd;display:flex;align-items:center;padding-left:10px}
.bar::before{content:"";width:9px;height:9px;border-radius:50%;background:#bbb;box-shadow:16px 0 #bbb,32px 0 #bbb}
.content{padding:30px 40px;text-align:center}
.logo{width:160px;height:140px;margin:0 auto 20px;border:1px solid #555;display:flex;align-items:center;justify-content:center;background:linear-gradient(to top right,transparent calc(50% - .5px),#777 50%,transparent calc(50% + .5px)),linear-gradient(to bottom right,transparent calc(50% - .5px),#777 50%,transparent calc(50% + .5px))}
form{text-align:left}label{display:block;margin-top:18px;font-size:15px}
input[type=text],input[type=password]{display:block;width:100%;height:40px;margin-top:4px;padding:0 10px;border:1px solid #444;border-radius:0;font:inherit}
.pw{position:relative}.pw input{padding-right:80px}.eye{position:absolute;right:4px;top:8px;border:0;background:none;color:#444;cursor:pointer;font-size:13px}
.btn{display:block;margin:24px auto 0;background:#444;color:#fff;border:0;border-radius:4px;padding:10px 44px;font:700 14px Arial;cursor:pointer}
.err{border:1px solid #555;padding:8px;margin-bottom:8px;text-align:left}.kecil{font-size:13px}
hr{border:0;border-top:1px solid #555;margin:0 14px}.foot{text-align:center;padding:14px;margin:0;font-size:15px}
</style>
</head>
<body>
<div class="frame">
  <div class="bar"></div>
  <div class="content">
    <div class="logo">Logo Pangeran Gym</div>
    <?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
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
    <p><a href="index.php">&larr; Kembali ke Pilihan Portal</a></p>
  </div>
  <hr>
  <p class="foot">Butuh bantuan Hubungi CS: 0812-xxxx-xxxx</p>
</div>
</body>
</html>