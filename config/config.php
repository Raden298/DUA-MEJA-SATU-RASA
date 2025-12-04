<?php
// =========================================
// CONFIG.PHP — HANYA UNTUK KONFIGURASI
// =========================================

// BASE URL (sesuaikan lokasi project di XAMPP)
define('BASE_URL', 'http://localhost/PROJEK WEB DUA MEJA SATU RASA');

// START SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DATABASE CONNECTION
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dua_meja_satu_rasa";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// TIDAK ADA FUNCTION DI SINI.
