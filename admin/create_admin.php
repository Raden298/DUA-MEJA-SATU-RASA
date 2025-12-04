<?php
// create_admin.php

// FIX: path config
require __DIR__ . '/../config/config.php';

// ADMIN DEFAULT
$name  = 'Super Admin';
$email = 'admin@duameja.test';
$plainPassword = 'admin123';

// hash
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// insert ke tabel "admins"
$sql = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sss', $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo "<h2>Admin berhasil dibuat!</h2>";
    echo "<p>Email: <b>{$email}</b></p>";
    echo "<p>Password: <b>{$plainPassword}</b></p>";
    echo "<p><b>HAPUS FILE create_admin.php SETELAH LOGIN!</b></p>";
} else {
    echo "<p>Gagal: " . $stmt->error . "</p>";
}
