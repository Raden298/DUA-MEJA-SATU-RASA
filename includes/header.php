<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'id');
if (!in_array($lang, ['id', 'en'])) {
    $lang = 'id';
}
$_SESSION['lang'] = $lang;

// hitung total item di cart
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int)($item['qty'] ?? 0);
    }
}
?>

<!DOCTYPE html>
<html lang="<?= esc($lang) ?>">

<head>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&display=swap">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dua Meja Satu Rasa</title>

    <link rel="stylesheet" href="/assets/css/style.css">

    <style>
        .brand-logo {
            max-width: 52px;
            height: auto;
        }
    </style>
</head>

<body class="theme-dark">
    <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash-message">
            <?= esc($_SESSION['flash']); ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <header class="site-header">
        <div class="header-inner">

            <a href="/index.php" class="brand-link">
                <img src="/assets/img/logo-dua-meja.png" class="brand-logo" alt="Logo"
                    style="width: 52px; height: 52px; object-fit: cover; border-radius: 50%;">
                <div class="brand-text">
                    <span class="brand-line1">DUA MEJA</span>
                    <span class="brand-line2">SATU RASA</span>
                </div>
            </a>

            <?php
            $current = basename($_SERVER['SCRIPT_NAME']);
            ?>

            <nav class="main-nav">
                <a href="/index.php" class="nav-link <?= $current === 'index.php' ? 'is-active' : '' ?>">
                    Home
                </a>

                <a href="/pages/menu.php" class="nav-link <?= $current === 'menu.php' ? 'is-active' : '' ?>">
                    Menu
                </a>

                <a href="/pages/fusion_story.php"
                    class="nav-link <?= $current === 'fusion_story.php' ? 'is-active' : '' ?>">
                    Fusion Story
                </a>

                <a href="/pages/reservation.php"
                    class="nav-link <?= $current === 'reservation.php' ? 'is-active' : '' ?>">
                    Reservation
                </a>

                <a href="/pages/contact.php" class="nav-link <?= $current === 'contact.php' ? 'is-active' : '' ?>">
                    Contact
                </a>

                    <a href="/pages/location.php" class="nav-link">Location</a>

            </nav>


            <div class="nav-right">

                <a href="/pages/cart.php" class="btn-cart-icon" title="Lihat Keranjang">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <span class="cart-badge"><?= $cartCount ?></span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- USER LOGGED IN -->
                    <div class="user-menu">
                        <button type="button" class="user-menu-btn">
                            <span class="user-avatar">
                                <?php
                                $uname = $_SESSION['username'] ?? 'User';
                                echo strtoupper(mb_substr($uname, 0, 1));
                                ?>
                            </span>
                            <span class="user-name">
                                <?= esc($uname) ?>
                            </span>
                            <span class="user-caret">▾</span>
                        </button>

                        <div class="user-menu-dropdown">
                            <!-- nanti kalau mau tambah "Profile" tinggal tambah 1 link di sini -->
                            <form action="/auth/logout.php" method="post">
                                <button type="submit" class="user-menu-link logout-link">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- BELUM LOGIN -->
                    <a href="/auth/login.php" class="btn-login">Login</a>
                <?php endif; ?>

                <div class="theme-switch">
                    <button class="theme-btn" data-theme="light">☀ </button>
                    <button class="theme-btn" data-theme="dark">🌙 </button>
                </div>
            </div>

        </div>
    </header>