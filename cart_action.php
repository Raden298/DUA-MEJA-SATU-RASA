<?php
// cart_action.php
require_once __DIR__ . '/config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kalau bukan POST, balikin ke menu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/menu.php');
    exit;
}

$action = $_POST['action'] ?? 'add';       // add / remove / clear
$menuId = (int)($_POST['menu_id'] ?? 0);
$qty    = (int)($_POST['qty'] ?? 1);
$qty    = $qty > 0 ? $qty : 1;

// Inisialisasi cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Kalau bukan clear dan nggak ada id → balik aja
if ($menuId <= 0 && $action !== 'clear') {
    header('Location: /pages/menu.php');
    exit;
}

switch ($action) {
    case 'add':
        if (isset($_SESSION['cart'][$menuId])) {
            $_SESSION['cart'][$menuId]['qty'] += $qty;
        } else {
            $stmt = $conn->prepare("
                SELECT id, name, price, image_path
                FROM menu_items
                WHERE id = ? AND is_active = 1
                LIMIT 1
            ");
            $stmt->bind_param('i', $menuId);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $_SESSION['cart'][$menuId] = [
                    'id'    => (int)$row['id'],
                    'name'  => $row['name'],
                    'price' => (float)$row['price'],
                    'qty'   => $qty,
                    'image' => $row['image_path'] ?? '',
                ];
            }
        }
        break;

    case 'remove':
        // Kurangi 1; kalau sisa 0, hapus
        if (isset($_SESSION['cart'][$menuId])) {
            if ($_SESSION['cart'][$menuId]['qty'] > 1) {
                $_SESSION['cart'][$menuId]['qty'] -= 1;
            } else {
                unset($_SESSION['cart'][$menuId]);
            }
        }
        break;

    case 'clear':
        // BENER-BENER KOSONGIN SEMUA
        $_SESSION['cart'] = [];
        break;
}

// Balik ke halaman sebelumnya / default menu
$redirect = $_POST['redirect'] ?? '/pages/menu.php';
header('Location: ' . $redirect);
exit;
