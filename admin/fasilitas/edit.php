<?php
require_once '../../koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM fasilitas WHERE id_fasilitas = $id";
$result = mysqli_query($koneksi, $query);
$fasilitas = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_fasilitas']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $update_query = "UPDATE fasilitas SET nama_fasilitas = '$nama', deskripsi = '$deskripsi' WHERE id_fasilitas = $id";

    if (mysqli_query($koneksi, $update_query)) {
        echo "<script>alert('Fasilitas berhasil diperbarui!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui fasilitas.');</script>";
    }
}
?>

<!-- Form Edit -->
<div class="container">
    <h3>Edit Fasilitas</h3>
    <form action="" method="POST">
        <div class="mb-3">
            <label>Nama Fasilitas</label>
            <input type="text" name="nama_fasilitas" class="form-control"
                value="<?= htmlspecialchars($fasilitas['nama_fasilitas']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"
                required><?= htmlspecialchars($fasilitas['deskripsi']); ?></textarea>
        </div>
        <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>