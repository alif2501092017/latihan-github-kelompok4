<?php
// Jalankan SEKALI lewat terminal untuk membuat akun admin pertama:
//   php buat_admin.php admin@pangerangym.test "Nama Admin" SandiRahasia123
// Setelah itu hapus file ini.
if (PHP_SAPI !== 'cli') {
    exit('Hanya bisa dijalankan lewat terminal.');
}
require_once __DIR__ . '/config/database.php';

[$_, $email, $nama, $sandi] = array_pad($argv, 4, null);
if (!$email || !$nama || !$sandi) {
    exit("Pemakaian: php buat_admin.php email \"Nama\" sandi\n");
}

$st = db()->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'admin')");
$st->execute([$nama, $email, password_hash($sandi, PASSWORD_DEFAULT)]);
echo "Admin dibuat: $email\n";
