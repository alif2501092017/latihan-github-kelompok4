
<?php
require_once __DIR__ . '/../fungsi.php';

// Mengambil ID pengguna
$uid = user()['id'] ?? ($_SESSION['pending_user'] ?? 0);

if (!$uid) {
    go('daftar.php');
}

// Menentukan jenis pembayaran
$pertama = isset($_GET['pertama']) || isset($_SESSION['pending_user']);

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_check();

    try {

        // Mengunggah bukti pembayaran
        $bukti = upload_file('bukti', 'bukti');

        if (!$bukti) {
            throw new RuntimeException(
                'Bukti transfer wajib diunggah.'
            );
        }

        // Menentukan jenis dan periode pembayaran
        $jenis = $pertama ? 'pertama' : 'bulanan';

        $periode = $pertama
            ? 'Pendaftaran'
            : date('Y-m');

        // Menentukan jumlah pembayaran
        $jumlah = $pertama ? 250000 : 150000;

        $metode = $_POST['metode'] ?? 'Transfer';

        // Menyimpan data pembayaran ke database
        $st = $koneksi->prepare(
            "INSERT INTO payments(
                user_id,
                jenis,
                periode,
                jumlah,
                metode,
                bukti,
                status
            )
            VALUES(?, ?, ?, ?, ?, ?, 'menunggu')"
        );

        $st->bind_param(
            'issdss',
            $uid,
            $jenis,
            $periode,
            $jumlah,
            $metode,
            $bukti
        );

        $st->execute();

        // Menghapus sesi pendaftaran sementara
        unset($_SESSION['pending_user']);

        flash(
            'success',
            'Pembayaran terkirim dan menunggu verifikasi admin.'
        );

        go(user() ? 'dashboard.php' : 'loginMember.php');

    } catch (Throwable $e) {

        $err = $e->getMessage();

    }
}
?>

<!doctype html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pembayaran</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

    <div class="auth">

        <h2>
            <?= $pertama
                ? 'Pembayaran Pertama'
                : 'Pembayaran Iuran Bulanan'
            ?>
        </h2>

        <p>
            Biaya:
            <b>
                <?= rupiah($pertama ? 250000 : 150000) ?>
            </b>
        </p>

        <?php if ($err): ?>

            <div class="alert error">
                <?= e($err) ?>
            </div>

        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrf_token()) ?>"
            >

            <label>Metode Pembayaran</label>

            <select name="metode">

                <option>Transfer Bank</option>
                <option>QRIS</option>
                <option>Tunai</option>

            </select>

            <label>
                Bukti Pembayaran (jpg/png/webp, maks. 3 MB)
            </label>

            <input
                type="file"
                name="bukti"
                accept="image/*"
                required
            >

            <button class="btn">
                Kirim Pembayaran
            </button>

        </form>

        <p>
            <a href="../index.php">Kembali</a>
        </p>

    </div>

</body>

</html>
