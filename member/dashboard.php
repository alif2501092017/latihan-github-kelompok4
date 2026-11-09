
<?php
require_once __DIR__ . '/../fungsi.php';

require_role(['member'], 'loginMember.php');

$id = user()['id'];

// Mengambil data member
$st = $koneksi->prepare(
    'SELECT m.*, u.email
     FROM members m
     JOIN users u ON u.id = m.user_id
     WHERE m.user_id = ?'
);

$st->bind_param('i', $id);
$st->execute();

$m = $st->get_result()->fetch_assoc();

// Mengambil pembayaran terakhir
$p = $koneksi->prepare(
    'SELECT *
     FROM payments
     WHERE user_id = ?
     ORDER BY id DESC
     LIMIT 1'
);

$p->bind_param('i', $id);
$p->execute();

$last = $p->get_result()->fetch_assoc();

// Menampilkan layout dashboard
layout_start('Dashboard Member', 'member');
?>

<div class="card">

    <h2>Halo, <?= e(user()['nama']) ?>!</h2>

    <p>
        Nomor Member:
        <b><?= e($m['no_member'] ?? '-') ?></b>
    </p>

    <p>
        Status:
        <span class="status">
            <?= e($m['status'] ?? 'menunggu') ?>
        </span>
    </p>

    <p>
        Aktif sampai:
        <?= e($m['aktif_sampai'] ?? '-') ?>
    </p>

</div>

<div class="grid">

    <div class="card">

        <h3>Pengingat Iuran</h3>

        <p>
            <?= $m && $m['aktif_sampai']
                ? 'Masa aktif sampai ' . e($m['aktif_sampai'])
                : 'Menunggu pembayaran pertama diverifikasi.'
            ?>
        </p>

        <a class="btn" href="pembayaran.php">
            Bayar Sekarang
        </a>

    </div>

    <div class="card">

        <h3>Pembayaran Terakhir</h3>

        <p>
            <?= e($last['status'] ?? 'Belum ada pembayaran') ?>
        </p>

        <a href="riwayat.php">
            Lihat riwayat
        </a>

    </div>

    <div class="card">

        <h3>Informasi Gym</h3>

        <a href="../landing.php">
            Lihat informasi dan fasilitas
        </a>

    </div>

</div>

<?php
layout_end();
?>
