<?php
// config/config.php

// --- SETTING DATABASE ---
$DB_HOST = 'localhost';
$DB_NAME = 'dua_meja_satu_rasa';
$DB_USER = 'root';   // default XAMPP
$DB_PASS = '';       // default XAMPP: kosong, kalau pakai password ganti di sini

// BASE_URL = nama folder project di htdocs
// contoh: kalau path lo http://localhost/PROJEK WEB DUA MEJA SATU RASA/
// maka isi sesuai:
define('BASE_URL', '/PROJEK WEB DUA MEJA SATU RASA');
// kalo folder lo namanya lain, ganti string di atas

// --- KONEKSI KE DATABASE (MYSQLI) ---
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Cek error koneksi
if ($conn->connect_error) {
    die('Koneksi database gagal: ' . $conn->connect_error);
}

// Set karakter supaya aman (emoji, aksen, dll)
$conn->set_charset('utf8mb4');

// --- START SESSION ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper untuk escape output ke HTML
function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
// GANTI sesuai nama folder project di htdocs
// dari log tadi: "PROJEK WEB DUA MEJA SATU RASA"
if (!defined('BASE_URL')) {
    define('BASE_URL', '/PROJEK WEB DUA MEJA SATU RASA');
}
