<?php
// Pastikan koneksi dan session sudah dimuat
require_once '../../koneksi.php';

// Cek apakah tombol submit ditekan
if (isset($_POST['simpan'])) {
    $nama_fasilitas = mysqli_real_escape_string($koneksi, $_POST['nama_fasilitas']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Proses upload gambar fasilitas jika ada
    $nama_file = $_FILES['gambar']['name'];
    $ukuran_file = $_FILES['gambar']['size'];
    $tmp_file = $_FILES['gambar']['tmp_name'];
    $dir_upload = "../../uploads/fasilitas/";

    if (!empty($nama_file)) {
        // Ambil ekstensi file
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            // Buat nama file unik untuk menghindari tabrakan nama
            $nama_file_baru = uniqid() . '.' . $ekstensi;

            if (move_uploaded_file($tmp_file, $dir_upload . $nama_file_baru)) {
                // Query insert dengan gambar
                $query = "INSERT INTO fasilitas (nama_fasilitas, deskripsi, gambar) VALUES ('$nama_fasilitas', '$deskripsi', '$nama_file_baru')";
            } else {
                echo "<script>alert('Gagal mengunggah gambar!');</script>";
                $query = "";
            }
        } else {
            echo "<script>alert('Ekstensi file gambar tidak diizinkan (Hanya JPG, JPEG, PNG, WEBP)!');</script>";
            $query = "";
        }
    } else {
        // Query insert tanpa gambar jika tidak diunggah
        $query = "INSERT INTO fasilitas (nama_fasilitas, deskripsi) VALUES ('$nama_fasilitas', '$deskripsi')";
    }

    // Eksekusi query jika valid
    if (!empty($query)) {
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Fasilitas berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan ke database.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Fasilitas Gym</title>
    <!-- Sesuaikan path CSS/Framework sesuai header admin project kalian -->
    <link rel="stylesheet" href="../../assets/style.css">
</head>

<body>
    <div class="container mt-4">
        <h3>Tambah Fasilitas Gym Baru</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_fasilitas" class="form-label">Nama Fasilitas</label>
                <input type="text" class="form-control" id="nama_fasilitas" name="nama_fasilitas" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="gambar" class="form-label">Foto Fasilitas (Opsional)</label>
                <input type="file" class="form-control" id="gambar" name="gambar">
                <small class="text-muted">Format yang didukung: JPG, JPEG, PNG, WEBP.</small>
            </div>
            <button type="submit" name="simpan" class="btn btn-success">Simpan Fasilitas</button>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>