<?php
// Jalankan SEKALI lewat terminal untuk membuat akun admin pertama:
//   php loginAdmin.php admin@pangerangym.test "Nama Admin" SandiRahasia123
// Setelah itu hapus file ini.
if (PHP_SAPI !== 'cli') {
    exit('Hanya bisa dijalankan lewat terminal.');
}
require_once __DIR__ . '/koneksi.php';

[$_, $email, $nama, $sandi] = array_pad($argv, 4, null);
if (!$email || !$nama || !$sandi) {
    exit("Pemakaian: php loginAdmin.php email \"Nama\" sandi\n");
}

$st = $koneksi->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')");
$passwordHash = password_hash($sandi, PASSWORD_DEFAULT);
$st->bind_param("sss", $nama, $email, $passwordHash);
$st->execute();
echo "Admin dibuat: $email\n";