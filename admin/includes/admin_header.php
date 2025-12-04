<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pakai config utama project
require_once dirname(__DIR__) . '/../config/config.php';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - Dua Meja Satu Rasa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="theme-dark">
<header class="admin-topbar">
    <!-- isi topbar admin kalau mau -->
</header>
<main class="admin-main">
