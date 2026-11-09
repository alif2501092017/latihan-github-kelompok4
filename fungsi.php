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


// =========================================================================
// ALIAS & FUNGSI PENDUKUNG (Untuk kompatibilitas dengan folder member/)
// =========================================================================

/** Alias untuk user_login() */
function user(): ?array
{
    return user_login();
}

/** Alias untuk wajib_peran() */
function require_role($role, $redirect = '../login.php'): void
{
    $roles = is_array($role) ? $role : [$role];
    wajib_peran($roles, $redirect);
}

/** Redireksi halaman */
function go(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Format angka ke Rupiah */
function rupiah($angka): string
{
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/** Notifikasi pesan kilat (flash message) */
function flash($key = '', $message = '')
{
    if (!empty($key) && !empty($message)) {
        $_SESSION['flash'][$key] = $message;
    } elseif (!empty($key) && empty($message)) {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }
}

/** Layout bagian atas */
function layout_start(string $title = 'Pangeran Gym'): void
{
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>' . e($title) . '</title></head><body>';
}

/** Layout bagian bawah */
function layout_end(): void
{
    echo '</body></html>';
}

/** 
 * Handler upload berkas yang fleksibel
 * Menerima tipe 'array' ($_FILES['bukti']) atau 'string' nama input ('bukti')
 */
function upload_file($fileOrKey, string $destinationDir = '../uploads/'): ?string
{
    $file = null;

    if (is_string($fileOrKey)) {
        if (!isset($_FILES[$fileOrKey])) {
            return null;
        }
        $file = $_FILES[$fileOrKey];
    } elseif (is_array($fileOrKey)) {
        $file = $fileOrKey;
    }

    if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid('img_', true) . '.' . strtolower($ext);

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destinationDir . $newName)) {
        return $newName;
    }

    return null;
}