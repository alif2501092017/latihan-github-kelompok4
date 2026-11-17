<?php
require_once '../../koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM fasilitas WHERE id_fasilitas = $id";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Fasilitas berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus fasilitas.'); window.location='index.php';</script>";
    }
}
?>