<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

// Kalau sudah login sebagai admin, lempar ke dashboard
if (!empty($_SESSION['admin_id']) && !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $admin = $res->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['is_admin'] = true;

            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Dua Meja Satu Rasa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Biar kartu login admin nongol di tengah full screen */
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
            <h1 class="auth-title">Login Admin</h1>
            <p class="auth-subtitle">Masuk untuk mengelola sistem restoran.</p>

            <?php if (!empty($error)): ?>
                <div class="auth-alert auth-alert-error">
                    <p><?= htmlspecialchars($error) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="auth-field">
                    <label>Username</label>
                    <input type="text" name="username" autocomplete="username">
                </div>

                <div class="auth-field">
                    <label>Password</label>
                    <input type="password" name="password" autocomplete="current-password">
                </div>

                <button type="submit" class="auth-submit-btn">
                    Login
                </button>

                <div class="auth-bottom-text" style="text-align:center; margin-top:12px;">
                    Belum punya akun admin?
                    <a href="register.php" class="auth-link">Register</a>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
