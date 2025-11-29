<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/functions.php';

$lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? 'id');
if (!in_array($lang, ['id', 'en'])) {
    $lang = 'id';
}
$_SESSION['lang'] = $lang;
?>
<!DOCTYPE html>
<html lang="<?= esc($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dua Meja Satu Rasa</title>

    <!-- CSS utama -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="theme-dark">
    <header class="site-header">
        <div class="header-inner">

    <!-- LEFT: LOGO -->
    <a href="/index.php" class="brand-link">
        <img src="/assets/img/logo-dua-meja.png" class="brand-logo">
        <div class="brand-text">
            <span class="brand-line1">DUA MEJA</span>
            <span class="brand-line2">SATU RASA</span>
        </div>
    </a>

    <!-- MIDDLE: NAV -->
    <nav class="main-nav">
        <a href="/index.php" class="nav-link">Home</a>
        <a href="/pages/menu.php" class="nav-link">Menu</a>
        <a href="/pages/story.php" class="nav-link">Fusion Story</a>
        <a href="/pages/reservation.php" class="nav-link">Reservation</a>
        <a href="/pages/contact.php" class="nav-link">Contact</a>
        <a href="/pages/location.php" class="nav-link">Location</a>
    </nav>

    <!-- RIGHT: LOGIN + LANGUAGE + THEME -->
    <div class="nav-right">
        <a href="/auth/login.php" class="btn-login">Login</a>

        <div class="lang-switch">
            <a href="#" class="lang-link active">ID</a>
            <span>•</span>
            <a href="#" class="lang-link">EN</a>
        </div>

        <div class="theme-switch">
            <button class="theme-btn" data-theme="light">☀ </button>
            <button class="theme-btn" data-theme="dark">🌙 </button>
        </div>
    </div>

</div>

    </header>