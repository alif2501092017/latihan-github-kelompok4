<?php
require_once __DIR__ . '/includes/auth.php';

// Simpan pilihan portal; halaman login hanya bisa dibuka setelah memilih
$portal = $_GET['portal'] ?? '';
if (!in_array($portal, ['admin', 'member'], true)) {
    header('Location: index.php');
    exit;
}

$_SESSION['portal'] = $portal;
header('Location: ' . ($portal === 'admin' ? 'admin/login.php' : 'member/login.php'));
exit;
