<?php
// pakai header biar CSS + nav kepanggil
require_once __DIR__ . '/../includes/header.php';

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // validasi dasar
    if ($username === '') {
        $errors[] = 'Username wajib diisi.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    }

    if (empty($errors)) {
        // cek sudah dipakai atau belum
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $errors[] = 'Username atau email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $ins = $conn->prepare("
                INSERT INTO users (username, email, password_hash)
                VALUES (?, ?, ?)
            ");
            $ins->bind_param('sss', $username, $email, $hash);

            if ($ins->execute()) {
                // setelah register, lempar ke login
                header('Location: /auth/login.php?registered=1');
                exit;
            } else {
                $errors[] = 'Gagal menyimpan akun. Coba lagi beberapa saat.';
            }
        }
    }
}
?>

<main class="auth-main">
    <section class="auth-card">
        <p class="auth-kicker">Account</p>
        <h1 class="auth-title">Buat Akun Baru</h1>
        <p class="auth-subtitle">
            Daftarkan akun untuk menyimpan pesanan, mengatur reservasi meja,
            dan menyelesaikan pembayaran dengan lebih mudah.
        </p>

        <?php if (!empty($errors)): ?>
            <div class="auth-alert auth-alert-error">
                <?php foreach ($errors as $e): ?>
                    <p><?= esc($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <div class="auth-field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    value="<?= isset($_POST['username']) ? esc($_POST['username']) : '' ?>"
                >
            </div>

            <div class="auth-field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    value="<?= isset($_POST['email']) ? esc($_POST['email']) : '' ?>"
                >
            </div>

            <div class="auth-field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit" class="auth-submit-btn">
                Buat Akun
            </button>
        </form>

        <p class="auth-bottom-text">
            Sudah punya akun?
            <a href="/auth/login.php" class="auth-link">Login di sini</a>
        </p>
    </section>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
