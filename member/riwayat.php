
<?php
require_once __DIR__ . '/../fungsi.php';

require_role(['member'], 'loginMember.php');

$id = user()['id'];

// Mengambil seluruh riwayat pembayaran member
$st = $koneksi->prepare(
    'SELECT *
     FROM payments
     WHERE user_id = ?
     ORDER BY tanggal DESC'
);

$st->bind_param('i', $id);
$st->execute();

$rows = $st->get_result();

// Menampilkan layout halaman
layout_start('Riwayat Pembayaran', 'member');
?>

<div class="card">

    <h2>Riwayat Pembayaran</h2>

    <div class="table-wrap">

        <table>

            <tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Periode</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Status</th>
            </tr>

            <?php
            $i = 1;

            while ($r = $rows->fetch_assoc()):
            ?>

                <tr>

                    <td><?= $i++ ?></td>

                    <td>
                        <?= e($r['jenis']) ?>
                    </td>

                    <td>
                        <?= e($r['periode']) ?>
                    </td>

                    <td>
                        <?= rupiah($r['jumlah']) ?>
                    </td>

                    <td>
                        <?= e($r['tanggal']) ?>
                    </td>

                    <td>
                        <?= e($r['status']) ?>
                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

<?php
layout_end();
?>
