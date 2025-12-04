<?php
session_start();
require_once dirname(__DIR__) . '/config/config.php';

// Cek login admin
if (empty($_SESSION['admin_id']) || empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

// Header admin (buka <html>, <body>, <main>)
require_once __DIR__ . '/includes/admin_header.php';
?>

<style>
/* ======= BACKGROUND ELEGANT ======= */
.admin-dashboard-wrapper {
    min-height: 100vh;
    padding: 60px 0;
    background: #0a0f1d;
    position: relative;
}
.admin-dashboard-wrapper::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image: url('../assets/img/fusion_bg.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 0.08;
    pointer-events: none;
}
.admin-title-box {
    text-align: center;
    margin-bottom: 50px;
}
.admin-title {
    font-family: 'Georgia', serif;
    color: #fff;
    font-size: 42px;
    margin-bottom: 10px;
}
.admin-subtitle {
    color: #cdd6f4;
    opacity: 0.8;
    font-size: 16px;
    margin-top: 0;
}
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    width: 90%;
    max-width: 1100px;
    margin: auto;
}
.admin-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    padding: 30px;
    border-radius: 18px;
    backdrop-filter: blur(6px);
    transition: 0.25s ease;
    cursor: pointer;
    text-decoration: none;
}
.admin-card:hover {
    background: rgba(255, 255, 255, 0.08);
    transform: translateY(-4px);
}
.admin-card-title {
    font-size: 22px;
    color: #fff;
    margin-bottom: 10px;
    font-family: 'Georgia', serif;
}
.admin-card-desc {
    color: #cdd6f4;
    opacity: 0.8;
    font-size: 14px;
    line-height: 1.5;
}
.icon-admin {
    font-size: 32px;
    margin-bottom: 15px;
    color: #00ffae;
}
</style>

<div class="admin-dashboard-wrapper">
    <div class="admin-title-box">
        <h1 class="admin-title">Dashboard Admin</h1>
        <p class="admin-subtitle">Kelola seluruh sistem restoran dengan cepat dan mudah.</p>
    </div>

    <div class="admin-grid">
        <a href="menu_manages.php" class="admin-card">
            <div class="icon-admin">🍽️</div>
            <h2 class="admin-card-title">Kelola Menu</h2>
            <p class="admin-card-desc">Tambah, edit, dan hapus menu makanan restoran.</p>
        </a>

        <a href="reservations_manages.php" class="admin-card">
            <div class="icon-admin">📅</div>
            <h2 class="admin-card-title">Kelola Reservasi</h2>
            <p class="admin-card-desc">Lihat daftar reservasi pelanggan dan tandai selesai.</p>
        </a>

        <a href="tables_manages.php" class="admin-card">
            <div class="icon-admin">🪑</div>
            <h2 class="admin-card-title">Kelola Meja</h2>
            <p class="admin-card-desc">Atur kapasitas meja dan kosongkan meja yang telah selesai digunakan.</p>
        </a>
    </div>
</div>

<?php
// Footer admin (nutup </main>, </body>, </html>)
require_once __DIR__ . '/includes/admin_footer.php';
?>
