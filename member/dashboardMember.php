<?php
require_once __DIR__ . '/../fungsi.php';
wajib_peran(['member'], 'loginMember.php');
$user = user_login();

$st = $koneksi->prepare('SELECT no_member, status, aktif_sampai FROM members WHERE user_id = ?');
$st->bind_param('i', $user['id']);
$st->execute();
$m = $st->get_result()->fetch_assoc() ?: ['no_member' => '-', 'status' => '-', 'aktif_sampai' => null];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Member - Pangeran Gym</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="frame">
    <div class="bar"></div>
    <div class="content">
        <h2>Halo, <?= e($user['nama']) ?></h2>
        <p>No. Member: <?= e($m['no_member']) ?></p>
        <p>Status: <?= e($m['status']) ?></p>
        <p>Aktif sampai: <?= e($m['aktif_sampai'] ?? '-') ?></p>
        <form method="post" action="../logout.php">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <button class="btn" type="submit">Logout</button>
        </form>
    </div>
</div>
</body>
</html>
