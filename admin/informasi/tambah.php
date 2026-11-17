<?php
require_once '../../koneksi.php';

if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $isi = mysqli_real_escape_string($koneksi, $_POST['isi']);
    $tanggal = date('Y-m-d');

    // Proses upload gambar informasi
    $nama_file = $_FILES['gambar']['name'];
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $dir_upload = "../../uploads/informasi/";
    $gambar_db = "";

    if (!empty($nama_file)) {
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_valid = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ekstensi, $ekstensi_valid)) {
            $gambar_db = uniqid() . '.' . $ekstensi;
            move_uploaded_file($tmp_file, $dir_upload . $gambar_db);
        }
    }

    $query = "INSERT INTO informasi (judul, isi, tanggal, gambar) VALUES ('$judul', '$isi', '$tanggal', '$gambar_db')";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Informasi berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan informasi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Informasi</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body>
    <div class="container mt-4">
        <h3>Tambah Informasi Baru</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul Informasi</label>
                <input type="text" name="judul" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Isi Konten</label>
                <textarea name="isi" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Gambar Banner/Pendukung</label>
                <input type="file" name="gambar" class="form-control">
            </div>
            <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>