
<?php
require_once __DIR__ . '/../fungsi.php';

require_role(['member'], 'loginMember.php');

$id = user()['id'];

// Mengambil 10 data pembayaran terakhir
$st = $koneksi->prepare(
    'SELECT *
     FROM payments
     WHERE user_id = ?
     ORDER BY tanggal DESC
     LIMIT 10'
);

$st->bind_param('i', $id);
$st->execute();

$rows = $st->get_result();

// Menampilkan layout halaman
layout_start('Notifikasi', 'member');
?>

<div class="card">

    <h2>Notifikasi Pembayaran</h2>

    <?php if (!$rows->num_rows): ?>

        <p>Belum ada notifikasi.</p>

    <?php endif; ?>

    <?php while ($r = $rows->fetch_assoc()): ?>

        <div class="card">

            <b>
                <?= e(ucfirst($r['jenis'])) ?>
                -
                <?= e($r['periode']) ?>
            </b>

            <p>
                Status pembayaran:
                <?= e($r['status']) ?>
            </p>

            <small>
                <?= e($r['tanggal']) ?>
            </small>

        </div>

    <?php endwhile; ?>

</div>

<?php
layout_end();
?>
