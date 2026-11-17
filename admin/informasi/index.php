<?php
require_once '../../koneksi.php';

$query = "SELECT * FROM informasi ORDER BY id_informasi DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Informasi - Pangeran Gym</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body>
    <div class="container mt-4">
        <h3>Daftar Informasi & Pengumuman</h3>
        <a href="tambah.php" class="btn btn-primary mb-3">Tambah Informasi Baru</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Gambar</th>
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
                            <?= htmlspecialchars($row['judul']); ?>
                        </td>
                        <td>
                            <?= $row['tanggal']; ?>
                        </td>
                        <td>
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="../../uploads/informasi/<?= $row['gambar']; ?>" width="80" alt="Gambar">
                            <?php else: ?>
                                <span>Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id_informasi']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= $row['id_informasi']; ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin ingin menghapus informasi ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="../dashboard.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
    </div>
</body>

</html>