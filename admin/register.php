<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

// Kalau sudah login sebagai admin, lempar ke dashboard
if (!empty($_SESSION['admin_id']) && !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: index.php');
    exit;
}

$errors = [];

// Proses form register
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username'] ?? '');
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validasi sederhana
    if ($username === '' || $password === '' || $password_confirm === '') {
        $errors[] = 'Semua kolom wajib diisi.';
    }

    if (strlen($username) < 3) {
        $errors[] = 'Username minimal 3 karakter.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    // Cek username sudah dipakai atau belum
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->fetch_assoc()) {
            $errors[] = 'Username sudah digunakan.';
        }
        $stmt->close();
    }

    // Insert ke tabel admins
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $hash);

        if ($stmt->execute()) {
            // Auto login admin setelah register
            $_SESSION['admin_id']       = $stmt->insert_id;
            $_SESSION['admin_username'] = $username;
            $_SESSION['is_admin']       = true;

            $stmt->close();
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data admin.';
        }

        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Admin - Dua Meja Satu Rasa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #020617;
        }
    </style>
</head>
<body class="theme-dark">

<div class="admin-auth-wrapper">
    <main class="auth-main">
        <div class="auth-card">
            <p class="auth-kicker">ADMIN AREA</p>
            <h1 class="auth-title">Register Admin</h1>
            <p class="auth-subtitle">
                Buat akun admin untuk mengelola menu, reservasi, dan meja.
            </p>

            <?php if (!empty($errors)): ?>
                <div class="auth-alert auth-alert-error">
                    <?php foreach ($errors as $msg): ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="auth-field">
                    <label>Username</label>
                    <input
                        type="text"
                        name="username"
                        value="<?= isset($username) ? htmlspecialchars($username) : '' ?>"
                        autocomplete="username"
                    >
                </div>

                <div class="auth-field">
                    <label>Password</label>
                    <input
                        type="password"
                        name="password"
                        autocomplete="new-password"
                    >
                </div>

                <div class="auth-field">
                    <label>Konfirmasi Password</label>
                    <input
                        type="password"
                        name="password_confirm"
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="auth-submit-btn">
                    Register
                </button>

                <div class="auth-bottom-text" style="text-align:center; margin-top:12px;">
                    Sudah punya akun admin?
                    <a href="login.php" class="auth-link">Login</a>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
