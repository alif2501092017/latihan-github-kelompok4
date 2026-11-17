<?php
require_once '../../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data gambar untuk dihapus dari folder
    $query = "SELECT gambar FROM informasi WHERE id_informasi = $id";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);

    if (!empty($data['gambar'])) {
        $path_gambar = "../../uploads/informasi/" . $data['gambar'];
        if (file_exists($path_gambar)) {
            unlink($path_gambar);
        }
    }

    // Hapus record dari database
    $delete_query = "DELETE FROM informasi WHERE id_informasi = $id";
    if (mysqli_query($koneksi, $delete_query)) {
        echo "<script>alert('Informasi berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus informasi.'); window.location='index.php';</script>";
    }
}
?>