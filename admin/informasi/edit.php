<?php
require_once '../../koneksi.php';

$id = $_GET['id'];
$query = "SELECT * FROM informasi WHERE id_informasi = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);

    $nama_file = $_FILES['gambar']['name'];
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $dir_upload = "../../uploads/informasi/";

    if (!empty($nama_file)) {
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $gambar_baru = uniqid() . '.' . $ekstensi;

        // Hapus gambar lama jika ada
        if (!empty($data['gambar']) && file_exists($dir_upload . $data['gambar'])) {
            unlink($dir_upload . $data['gambar']);
        }

        move_uploaded_file($tmp_file, $dir_upload . $gambar_baru);
        $update_gambar = ", gambar = '$gambar_baru'";
    } else {
        $update_gambar = "";
    }

    $update_query = "UPDATE informasi SET judul = '$judul', isi = '$isi' $update_gambar WHERE id_informasi = $id";
    if (mysqli_query($koneksi, $update_query)) {
        echo "<script>alert('Informasi berhasil diperbarui!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui informasi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Informasi</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body>
    <div class="container mt-4">
        <h3>Edit Informasi</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul Informasi</label>
                <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']); ?>"
                    required>
            </div>
            <div class="mb-3">
                <label class="form-label">Isi Konten</label>
                <textarea name="isi" class="form-control" rows="5"
                    required><?= htmlspecialchars($data['isi']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Gambar Saat Ini:</label><br>
                <?php if (!empty($data['gambar'])): ?>
                    <img src="../../uploads/informasi/<?= $data['gambar']; ?>" width="120" class="mb-2"><br>
                <?php else: ?>
                    <p>Tidak ada gambar</p>
                <?php endif; ?>
                <input type="file" name="gambar" class="form-control">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
            </div>
            <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>