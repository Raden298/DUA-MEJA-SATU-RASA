<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/functions.php';

$errors = [];
$infoMsg = '';

if (isset($_GET['registered'])) {
    $infoMsg = 'Akun berhasil dibuat. Silakan login.';
}

// ============ PROSES LOGIN ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';
    $next       = $_POST['next'] ?? ($_GET['next'] ?? '');

    if ($identifier === '' || $password === '') {
        $errors[] = 'Email/Username dan password wajib diisi.';
    } else {

        $stmt = $conn->prepare("
            SELECT id, username, email, password_hash
            FROM users
            WHERE email = ? OR username = ?
            LIMIT 1
        ");
        $stmt->bind_param('ss', $identifier, $identifier);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();

            if (password_verify($password, $user['password_hash'])) {

                // SET SESSION
                $_SESSION['user_id']    = (int)$user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['user_email'] = $user['email'];

                // === REDIRECT SESUAI KEBUTUHAN ===
                if ($next === 'reservation') {
                    redirect('/pages/reservation.php', 'Login berhasil! Silakan lanjutkan reservasi.');
                }

                redirect('/index.php', 'Login berhasil!');
            } else {
                $errors[] = 'Password salah.';
            }

        } else {
            $errors[] = 'Akun tidak ditemukan.';
        }
    }
}

?>

<!-- ====================== UI LOGIN ====================== -->

<main class="auth-main">
    <section class="auth-card">
        <p class="auth-kicker">Account</p>
        <h1 class="auth-title">Login ke Akun Anda</h1>
        <p class="auth-subtitle">
            Masuk untuk melanjutkan pemesanan, mengatur reservasi meja,
            dan menyelesaikan pembayaran.
        </p>

        <?php if ($infoMsg): ?>
            <div class="auth-alert auth-alert-info">
                <p><?= esc($infoMsg) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="auth-alert auth-alert-error">
                <?php foreach ($errors as $e): ?>
                    <p><?= esc($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="auth-form">

            <!-- NEXT HIDDEN FIELD -->
            <input type="hidden" name="next" value="<?= esc($_GET['next'] ?? '') ?>">

            <div class="auth-field">
                <label for="identifier">Email atau Username</label>
                <input
                    type="text"
                    id="identifier"
                    name="identifier"
                    required
                    value="<?= isset($_POST['identifier']) ? esc($_POST['identifier']) : '' ?>"
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

            <div class="auth-extra-line">
                <a href="#" class="auth-link">Lupa password?</a>
            </div>

            <button type="submit" class="auth-submit-btn">
                Login
            </button>
        </form>

        <p class="auth-bottom-text">
            Belum punya akun?
            <a href="/auth/register.php" class="auth-link">Daftar sekarang</a>
        </p>
    </section>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
