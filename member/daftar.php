
<?php
require_once __DIR__ . '/../fungsi.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $nama  = trim($_POST['nama'] ?? '');
    $hp    = trim($_POST['hp'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (strlen($pass) < 8) {
        $err = 'Password minimal 8 karakter.';
    } else {
        try {
            $koneksi->begin_transaction();

            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $st = $koneksi->prepare(
                "INSERT INTO users(nama, email, no_hp, password, role)
                 VALUES(?, ?, ?, ?, 'member')"
            );

            $st->bind_param('ssss', $nama, $email, $hp, $hash);
            $st->execute();

            $uid = $koneksi->insert_id;

            $no = 'PG' . date('ym') . str_pad(
                (string)$uid,
                5,
                '0',
                STR_PAD_LEFT
            );

            $m = $koneksi->prepare(
                "INSERT INTO members(user_id, no_member, status)
                 VALUES(?, ?, 'menunggu')"
            );

            $m->bind_param('is', $uid, $no);
            $m->execute();

            $koneksi->commit();

            $_SESSION['pending_user'] = $uid;

            flash(
                'success',
                'Pendaftaran berhasil. Silakan lakukan pembayaran pertama.'
            );

            go('pembayaran.php?pertama=1');

        } catch (Throwable $e) {
            $koneksi->rollback();

            $err = 'Email sudah digunakan atau pendaftaran gagal.';
        }
    }
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Member</title>

    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

    <div class="auth">

        <h2>Daftar Member Baru</h2>

        <?php if ($err): ?>
            <div class="alert error">
                <?= e($err) ?>
            </div>
        <?php endif; ?>

        <form method="post">

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >

            <label>Nama Lengkap</label>
            <input
                type="text"
                name="nama"
                required
            >

            <label>No. HP / WhatsApp</label>
            <input
                type="text"
                name="hp"
                required
            >

            <label>Email</label>
            <input
                type="email"
                name="email"
                required
            >

            <label>Password (min. 8 karakter)</label>
            <input
                type="password"
                name="password"
                minlength="8"
                required
            >

            <button class="btn">
                Lanjut ke Pembayaran
            </button>

        </form>

        <p>
            <a href="loginMember.php">
                Sudah punya akun? Login
            </a>
        </p>

    </div>

</body>
</html>
