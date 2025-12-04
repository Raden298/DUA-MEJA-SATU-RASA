<?php
// config/functions.php
// KUMPULAN FUNGSI HELPER (non-layout, non-HTML)

require_once __DIR__ . '/config.php';

/**
 * Redirect helper
 * NOTE: di config.php kamu sudah punya redirect(),
 * jadi di sini kita hanya bikin kalau belum ada.
 */
if (!function_exists('redirect')) {
    function redirect($url, $msg = null) {
    if ($msg) {
        $_SESSION['flash'] = $msg;
    }
    header("Location: $url");
    exit;
}
}

/**
 * Escape output ke HTML.
 * Lagi-lagi: kalau sudah ada di config.php, jangan duplikasi.
 */
if (!function_exists('esc')) {
    function esc(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Cek apakah user sudah login
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Ambil data user yang lagi login (kalau ada)
 */
function current_user(): ?array
{
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id'       => $_SESSION['user_id']      ?? null,
        'username' => $_SESSION['username']     ?? null,
        'email'    => $_SESSION['user_email']   ?? null,
        'role'     => $_SESSION['user_role']    ?? null,
    ];
}

/**
 * Paksa halaman tertentu hanya bisa diakses user login
 */
function require_login(): void
{
    if (!is_logged_in()) {
        // simpan pesan (optional, nanti bisa ditampilkan di halaman login)
        $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
        redirect('/auth/login.php');
    }
}

/**
 * Helper sederhana untuk SELECT satu user berdasarkan email / username.
 * Nanti bisa dipakai di login / register.
 */
function find_user_by_email_or_username(string $identifier): ?array
{
    global $conn;

    $sql = "SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    return $user ?: null;
}

/**
 * Helper untuk bikin user baru (register)
 */
function create_user(string $username, string $email, string $password): bool
{
    global $conn;

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('sss', $username, $email, $passwordHash);
    $ok = $stmt->execute();
    $stmt->close();

    return $ok;
}
