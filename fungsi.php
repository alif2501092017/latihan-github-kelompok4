<?php
// Fungsi bantu bersama: sesi, keamanan, dan pengecekan peran.
require_once __DIR__ . '/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

/** Escape output agar aman dari XSS */
function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Sesi tidak valid. Muat ulang halaman lalu coba lagi.');
    }
}

function user_login(): ?array
{
    return $_SESSION['auth'] ?? null;
}

/** Batasi halaman untuk peran tertentu, selain itu kembali ke $tujuan */
function wajib_peran(array $peran, string $tujuan): void
{
    $u = user_login();
    if (!$u || !in_array($u['role'], $peran, true)) {
        header('Location: ' . $tujuan);
        exit;
    }
}

function masuk(array $u): void
{
    session_regenerate_id(true);
    $_SESSION['auth'] = ['id' => (int) $u['id'], 'nama' => $u['nama'], 'role' => $u['role']];
    unset($_SESSION['gagal']);
}

/** 5 kali gagal = tunggu 5 menit */
function terlalu_banyak_gagal(): bool
{
    $f = $_SESSION['gagal'] ?? ['n' => 0, 't' => 0];
    if (time() - $f['t'] >= 300) {
        $_SESSION['gagal'] = ['n' => 0, 't' => 0];
        return false;
    }
    return $f['n'] >= 5;
}

function catat_gagal(): void
{
    $f = $_SESSION['gagal'] ?? ['n' => 0, 't' => 0];
    $_SESSION['gagal'] = ['n' => $f['n'] + 1, 't' => time()];
}

/** Hash palsu agar waktu respons sama saat akun tidak ditemukan */
function hash_palsu(): string
{
    static $h = null;
    return $h ??= password_hash('dummy-password', PASSWORD_DEFAULT);
}
