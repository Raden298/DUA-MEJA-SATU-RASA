<?php
require_once __DIR__ . '/../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function esc_html($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

$cart    = $_SESSION['cart'] ?? [];
$isEmpty = empty($cart);

$cartItems   = [];
$totalItems  = 0;
$totalAmount = 0;

// Kalau ada isi keranjang, lengkapi data (harga, gambar, subtotal)
if (!$isEmpty) {
    foreach ($cart as $menuId => $item) {
        $menuId = (int)$menuId;
        $qty    = (int)($item['qty'] ?? 0);
        if ($qty < 1) continue;

        // Ambil data terbaru dari DB
        $stmt = $conn->prepare("
            SELECT name, price, image_path
            FROM menu_items
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->bind_param('i', $menuId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        $name      = $row['name']  ?? ($item['name']  ?? 'Menu');
        $price     = isset($row['price']) ? (float)$row['price'] : (float)($item['price'] ?? 0);
        $imagePath = $row['image_path'] ?? ($item['image'] ?? null);

        // Path gambar
        $filename     = !empty($imagePath) ? $imagePath : $name . '.jpg';
        $physicalPath = __DIR__ . '/../assets/img/' . $filename;

        if (is_file($physicalPath)) {
            $imgSrc = '../assets/img/' . rawurlencode($filename);
        } else {
            $imgSrc = 'https://placehold.co/600x400/020617/FFF?text=' . rawurlencode($name);
        }

        $subtotal     = $price * $qty;
        $totalItems  += $qty;
        $totalAmount += $subtotal;

        $cartItems[] = [
            'id'        => $menuId,
            'name'      => $name,
            'qty'       => $qty,
            'price'     => $price,
            'subtotal'  => $subtotal,
            'image_src' => $imgSrc,
        ];
    }
}
?>

<main class="cart-main">

    <!-- HEADER CART -->
    <header class="cart-header <?= $isEmpty ? 'cart-header-center' : '' ?>">
        <p class="cart-kicker">Pesanan Anda</p>
        <h1 class="cart-title">Keranjang</h1>
        <p class="cart-subtitle">
            Tinjau kembali hidangan yang sudah Anda pilih sebelum melanjutkan ke reservasi.
        </p>
    </header>

    <?php if ($isEmpty): ?>

        <!-- KERANJANG KOSONG -->
        <section class="cart-empty-section">
            <div class="cart-empty-card">
                <div class="cart-empty-icon">🍽️</div>
                <h2 class="cart-empty-title">Keranjang Anda masih kosong</h2>
                <p class="cart-empty-text">
                    Belum ada hidangan yang dipilih. Mulai dari menu signature kami dan temukan
                    paduan rasa Indonesia &amp; Italia yang ingin Anda coba malam ini.
                </p>
                <a href="/pages/menu.php" class="cart-empty-btn">
                    Lihat Menu
                </a>
            </div>
        </section>

    <?php else: ?>

        <!-- ADA ISI -->
        <section class="cart-layout">
            <!-- LIST ITEM KIRI -->
            <div class="cart-items-column">
                <?php foreach ($cartItems as $row): ?>
                    <!-- PENTING: id ADA DI DALAM TAG, BUKAN KEPRINT SEBAGAI TEKS -->
                    <article class="cart-item-card" id="cart-item-<?= (int)$row['id'] ?>">
                        <div class="cart-item-thumb">
                            <img src="<?= esc_html($row['image_src']) ?>"
                                 alt="<?= esc_html($row['name']) ?>">
                        </div>

                        <div class="cart-item-body">
                            <h2 class="cart-item-title">
                                <?= esc_html($row['name']) ?>
                            </h2>

                            <div class="cart-item-meta">
                                <div class="cart-item-meta-left">
                                    <span class="cart-item-price">
                                        Rp <?= number_format($row['price'], 0, ',', '.') ?>
                                    </span>

                                    <!-- KONTROL QTY (+ / -) -->
                                    <div class="cart-item-qty-group">
                                        <!-- MINUS: kurangi 1 -->
                                        <form method="post" action="../cart_action.php">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="menu_id" value="<?= (int)$row['id'] ?>">
                                            <input type="hidden" name="redirect"
                                                   value="/pages/cart.php#cart-item-<?= (int)$row['id'] ?>">
                                            <button type="submit" class="cart-qty-btn cart-qty-btn-minus">
                                                &minus;
                                            </button>
                                        </form>

                                        <!-- ANGKA QTY -->
                                        <span class="cart-item-qty-number">
                                            <?= (int)$row['qty'] ?>
                                        </span>

                                        <!-- PLUS: tambah 1 -->
                                        <form method="post" action="../cart_action.php">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="menu_id" value="<?= (int)$row['id'] ?>">
                                            <input type="hidden" name="qty" value="1">
                                            <input type="hidden" name="redirect"
                                                   value="/pages/cart.php#cart-item-<?= (int)$row['id'] ?>">
                                            <button type="submit" class="cart-qty-btn cart-qty-btn-plus">
                                                +
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="cart-item-meta-right">
                                    <span class="cart-item-subtotal-label">Subtotal:</span>
                                    <span class="cart-item-subtotal">
                                        Rp <?= number_format($row['subtotal'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <!-- RINGKASAN KANAN -->
            <aside class="cart-summary-column">
                <div class="cart-summary-card">
                    <h2 class="cart-summary-title">Ringkasan Pesanan</h2>

                    <div class="cart-summary-row">
                        <span>Total item</span>
                        <span><?= (int)$totalItems ?></span>
                    </div>

                    <div class="cart-summary-row cart-summary-row-total">
                        <span>Total</span>
                        <span>Rp <?= number_format($totalAmount, 0, ',', '.') ?></span>
                    </div>

                    <a href="/pages/reservation.php" class="cart-summary-primary-btn">
                        Lanjut ke Reservasi
                    </a>

                    <!-- KOSONGKAN KERANJANG -->
                    <form method="post" action="../cart_action.php" class="cart-summary-clear-form">
                        <input type="hidden" name="action" value="clear">
                        <input type="hidden" name="redirect" value="/pages/cart.php">
                        <button type="submit" class="cart-summary-clear-btn">
                            Kosongkan Keranjang
                        </button>
                    </form>

                    <!-- KEMBALI KE MENU -->
                    <a href="/pages/menu.php" class="cart-back-btn">
                        ← Kembali ke Menu
                    </a>
                </div>
            </aside>
        </section>

    <?php endif; ?>

</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>

<!-- ===== SIMPAN & BALIKIN POSISI SCROLL BIAR GAK LONCAT KE ATAS ===== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // sebelum submit form cart, simpan posisi scroll
    document.querySelectorAll('form[action="../cart_action.php"]').forEach(function (form) {
        form.addEventListener('submit', function () {
            sessionStorage.setItem('cart_scroll_y', window.scrollY || window.pageYOffset || 0);
        });
    });

    // setelah halaman kebuka lagi, balikin ke posisi sebelumnya
    const savedY = sessionStorage.getItem('cart_scroll_y');
    if (savedY !== null) {
        window.scrollTo(0, parseInt(savedY, 10));
        sessionStorage.removeItem('cart_scroll_y');
    }
});
</script>
