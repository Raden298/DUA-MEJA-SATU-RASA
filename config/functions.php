<?php
// config/functions.php

require_once __DIR__ . '/config.php';

// Contoh helper: ambil 3 menu signature untuk homepage
function getSignatureMenus(mysqli $conn): array {
    $data = [];
    $sql = "SELECT * FROM menu_items 
            WHERE is_signature = 1 AND is_active = 1
            ORDER BY id ASC
            LIMIT 3";
    if ($result = $conn->query($sql)) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $result->free();
    }
    return $data;
}
