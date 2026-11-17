<?php
// Pastikan koneksi dan session sudah dimuat
require_once '../../koneksi.php';

// Query untuk membaca data fasilitas
$query = "SELECT * FROM fasilitas";
$result = mysqli_query($koneksi, $query);
?>

<!-- Contoh Bagian Tabel untuk Read Fasilitas -->
<div class="container-fluid">
    <h3>Daftar Fasilitas Gym</h3>
    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Fasilitas</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Fasilitas</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>
                        <?= $no++; ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['nama_fasilitas']); ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($row['deskripsi']); ?>
                    </td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_fasilitas']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="hapus.php?id=<?= $row['id_fasilitas']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus fasilitas ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>